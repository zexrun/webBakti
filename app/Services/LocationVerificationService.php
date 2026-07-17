<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationVerificationService
{
    /**
     * Radius deviation threshold (meters)
     * If server IP location differs by more than this from claimed location, flag it
     */
    private const IP_GEOLOCATION_THRESHOLD = 5000; // 5km

    /**
     * EXIF coordinate deviation threshold (meters)
     * If photo EXIF location differs from claimed location, flag it
     */
    private const EXIF_DEVIATION_THRESHOLD = 100; // 100m

    /**
     * Verify location using multiple methods
     * Returns array with verification result and risk level
     */
    public function verifyAttendanceLocation(
        Request $request,
        float $officeLatitude,
        float $officeLongitude,
        float $officeRadius
    ): array {
        $requestLat = $request->latitude;
        $requestLon = $request->longitude;

        $result = [
            'verified' => true,
            'risk_level' => 'low', // low, medium, high
            'issues' => [],
            'details' => [],
            'spoofing_score' => 0, // 0-100, higher = more suspicious
        ];

        // 1. Server-side IP Geolocation Check
        $ipGeoResult = $this->verifyIPGeolocation($request, $officeLatitude, $officeLongitude);
        if (!$ipGeoResult['valid']) {
            $result['issues'][] = 'IP location mismatch';
            $result['spoofing_score'] += 30;
        }
        $result['details']['ip_geolocation'] = $ipGeoResult;

        // 2. Photo EXIF Verification (if photo provided)
        if ($request->hasFile('photo')) {
            $exifResult = $this->verifyPhotoExif($request->file('photo'), $requestLat, $requestLon);
            if (!$exifResult['valid']) {
                $result['issues'][] = $exifResult['message'];
                $result['spoofing_score'] += 20;
            }
            $result['details']['exif_verification'] = $exifResult;
        }

        // 3. Distance Validation (existing check)
        $distance = $this->calculateDistance($requestLat, $requestLon, $officeLatitude, $officeLongitude);
        if ($distance > $officeRadius) {
            $result['verified'] = false;
            $result['issues'][] = "Outside office radius: {$distance}m vs {$officeRadius}m";
        }
        $result['details']['distance_check'] = [
            'distance' => round($distance, 2),
            'radius' => $officeRadius,
            'within_radius' => $distance <= $officeRadius,
        ];

        // 4. Determine risk level
        if ($result['spoofing_score'] >= 50) {
            $result['risk_level'] = 'high';
        } elseif ($result['spoofing_score'] >= 30) {
            $result['risk_level'] = 'medium';
        }

        // Log suspicious activity
        if (!empty($result['issues'])) {
            Log::warning('Suspicious attendance location detected', [
                'user_id' => auth()->id(),
                'claimed_lat' => $requestLat,
                'claimed_lon' => $requestLon,
                'office_lat' => $officeLatitude,
                'office_lon' => $officeLongitude,
                'distance' => round($distance, 2),
                'issues' => $result['issues'],
                'spoofing_score' => $result['spoofing_score'],
                'ip' => $request->ip(),
            ]);
        }

        return $result;
    }

    /**
     * Verify IP-based geolocation
     * Returns basic validation (limited without paid GeoIP service)
     */
    private function verifyIPGeolocation(Request $request, float $officeLat, float $officeLon): array
    {
        // NOTE: This is a basic implementation
        // In production, use a paid GeoIP service like:
        // - MaxMind GeoIP2
        // - IP2Location
        // - GeoIP.io
        //
        // For now, we log the IP for manual review

        return [
            'valid' => true, // Can't verify without paid service
            'note' => 'IP geolocation verification requires paid GeoIP service',
            'ip' => $request->ip(),
            'recommendation' => 'Consider adding MaxMind or IP2Location API for production',
        ];
    }

    /**
     * Verify photo EXIF GPS data
     * Check if photo's GPS coordinates match claimed location
     */
    private function verifyPhotoExif($photoFile, float $claimedLat, float $claimedLon): array
    {
        try {
            $path = $photoFile->getRealPath();

            // Try to read EXIF data
            if (!extension_loaded('exif')) {
                return [
                    'valid' => true,
                    'note' => 'EXIF extension not available - skipping verification',
                ];
            }

            $exif = @exif_read_data($path);

            if (!$exif || !isset($exif['GPS'])) {
                return [
                    'valid' => false,
                    'message' => 'Photo does not contain GPS metadata',
                    'has_exif' => !empty($exif),
                    'note' => 'Student should enable location in camera app',
                ];
            }

            $gpsData = $exif['GPS'];
            $photoLat = $this->convertExifGPSToDecimal($gpsData['GPSLatitude'], $gpsData['GPSLatitudeRef'] ?? 'N');
            $photoLon = $this->convertExifGPSToDecimal($gpsData['GPSLongitude'], $gpsData['GPSLongitudeRef'] ?? 'E');

            if ($photoLat === null || $photoLon === null) {
                return [
                    'valid' => false,
                    'message' => 'Could not parse GPS coordinates from photo',
                ];
            }

            // Check if photo coordinates match claimed location
            $deviation = $this->calculateDistance($photoLat, $photoLon, $claimedLat, $claimedLon);

            if ($deviation > self::EXIF_DEVIATION_THRESHOLD) {
                return [
                    'valid' => false,
                    'message' => "Photo GPS location differs from claimed location by {$deviation}m",
                    'photo_coordinates' => ['lat' => $photoLat, 'lon' => $photoLon],
                    'claimed_coordinates' => ['lat' => $claimedLat, 'lon' => $claimedLon],
                    'deviation' => round($deviation, 2),
                ];
            }

            return [
                'valid' => true,
                'message' => 'Photo GPS coordinates verified',
                'photo_coordinates' => ['lat' => $photoLat, 'lon' => $photoLon],
                'claimed_coordinates' => ['lat' => $claimedLat, 'lon' => $claimedLon],
                'deviation' => round($deviation, 2),
            ];

        } catch (\Exception $e) {
            Log::error('EXIF verification error: ' . $e->getMessage());

            return [
                'valid' => false,
                'message' => 'Error reading photo metadata',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert EXIF GPS format to decimal degrees
     * EXIF format: array(degrees, minutes, seconds)
     */
    private function convertExifGPSToDecimal($exifCoord, $hemi): ?float
    {
        if (!is_array($exifCoord) || count($exifCoord) < 3) {
            return null;
        }

        try {
            $degrees = $this->parseRational($exifCoord[0]);
            $minutes = $this->parseRational($exifCoord[1]);
            $seconds = $this->parseRational($exifCoord[2]);

            $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

            if ($hemi === 'S' || $hemi === 'W') {
                $decimal = -$decimal;
            }

            return $decimal;
        } catch (\Exception $e) {
            Log::error('EXIF GPS parsing error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse EXIF rational number format (numerator/denominator)
     */
    private function parseRational($rational): float
    {
        if (is_string($rational) && strpos($rational, '/') !== false) {
            [$numerator, $denominator] = explode('/', $rational);
            if ($denominator != 0) {
                return (float)$numerator / (float)$denominator;
            }
        }
        return (float)$rational;
    }

    /**
     * Calculate distance between two GPS coordinates (Haversine formula)
     * Returns distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * asin(sqrt($a));
        return $earthRadius * $c;
    }
}
