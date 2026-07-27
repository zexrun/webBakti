<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ServerFaceVerificationService
{
    /**
     * Extract face descriptor from image using face-api.js via Node.js
     */
    public static function extractFaceDescriptor(string $imageBase64): ?array
    {
        try {
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }

            $imageId = Str::uuid();
            $imagePath = $tmpDir . '/face_' . $imageId . '.jpg';
            $outputPath = $tmpDir . '/descriptor_' . $imageId . '.json';

            // Decode and save image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64));
            if (!$imageData) {
                throw new Exception('Invalid base64 image data');
            }

            file_put_contents($imagePath, $imageData);

            // Run Node script to extract descriptor
            $scriptPath = base_path('resources/face-verification/extract-descriptor.cjs');

            $env = array_filter([
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'windir' => getenv('windir') ?: 'C:\\Windows',
                'PATH' => getenv('PATH'),
            ]);

            $result = Process::timeout(30)
                ->env($env)
                ->run(['node', $scriptPath, $imagePath, $outputPath]);

            if (!$result->successful()) {
                throw new Exception('Face extraction failed: ' . $result->errorOutput());
            }

            if (!file_exists($outputPath)) {
                throw new Exception('Descriptor output file not created');
            }

            $descriptor = json_decode(file_get_contents($outputPath), true);

            // Cleanup
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            return $descriptor;
        } catch (Exception $e) {
            logger()->error('Face descriptor extraction failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Compare two face descriptors and return distance
     * Lower distance = more similar faces
     * Distance < 0.6 typically means same person
     */
    public static function compareFaceDescriptors(
        array $descriptor1,
        array $descriptor2,
        float $threshold = 0.6
    ): array {
        if (empty($descriptor1) || empty($descriptor2)) {
            return [
                'distance' => 1.0,
                'match' => false,
                'reason' => 'Empty descriptors',
            ];
        }

        if (count($descriptor1) !== count($descriptor2)) {
            return [
                'distance' => 1.0,
                'match' => false,
                'reason' => 'Descriptor size mismatch',
            ];
        }

        $distance = 0.0;
        $count = count($descriptor1);

        for ($i = 0; $i < $count; $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $distance += $diff * $diff;
        }

        $distance = sqrt($distance / $count);

        return [
            'distance' => round($distance, 3),
            'match' => $distance < $threshold,
            'threshold' => $threshold,
        ];
    }

    /**
     * Verify attendance with face + location validation
     */
    public static function verifyAttendance(
        string $photoBase64,
        array $currentLocation,
        array $geofenceCenter,
        float $maxDistance = 50
    ): array {
        $user = auth()->user();

        // Step 1: Extract descriptor dari foto yang di-submit
        $submittedDescriptor = self::extractFaceDescriptor($photoBase64);

        if (!$submittedDescriptor) {
            return [
                'success' => false,
                'reason' => 'Wajah tidak terdeteksi dalam foto. Pastikan wajah Anda jelas terlihat.',
                'code' => 'FACE_NOT_DETECTED',
            ];
        }

        // Step 2: Get stored descriptor dari user profile
        $storedDescriptor = json_decode($user->face_descriptor ?? '[]', true);

        if (empty($storedDescriptor)) {
            return [
                'success' => false,
                'reason' => 'Foto profil wajah belum terdaftar. Silakan update profil Anda terlebih dahulu.',
                'code' => 'NO_STORED_FACE',
            ];
        }

        // Step 3: Compare face descriptors
        $faceComparison = self::compareFaceDescriptors($submittedDescriptor, $storedDescriptor);

        if (!$faceComparison['match']) {
            return [
                'success' => false,
                'reason' => sprintf(
                    'Wajah tidak cocok dengan profil Anda (similarity: %.1f%%). Pastikan Anda adalah orang yang benar.',
                    (1 - $faceComparison['distance']) * 100
                ),
                'code' => 'FACE_MISMATCH',
                'distance' => $faceComparison['distance'],
            ];
        }

        // Step 4: Verify location (Haversine distance)
        $locationDistance = self::haversineDistance(
            $currentLocation['latitude'],
            $currentLocation['longitude'],
            $geofenceCenter['latitude'],
            $geofenceCenter['longitude']
        );

        if ($locationDistance > $maxDistance) {
            return [
                'success' => false,
                'reason' => sprintf(
                    'Lokasi Anda terlalu jauh dari kantor (%d meter, limit: %d meter). Pastikan Anda berada di lokasi yang benar.',
                    round($locationDistance),
                    $maxDistance
                ),
                'code' => 'LOCATION_OUT_OF_RANGE',
                'location_distance' => round($locationDistance),
            ];
        }

        // Step 5: All validations passed
        return [
            'success' => true,
            'face_distance' => $faceComparison['distance'],
            'location_distance' => round($locationDistance),
            'verified_at' => now(),
        ];
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    private static function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $R = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    /**
     * Get office geofence center coordinates
     * Can be moved to config if needed
     */
    public static function getOfficeLocation(): array
    {
        return [
            'latitude' => config('app.office_latitude', -6.2088),
            'longitude' => config('app.office_longitude', 106.8057),
        ];
    }
}
