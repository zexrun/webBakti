<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\AttendanceSetting;
use App\Services\LocationVerificationService;
use App\Services\FaceVerificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
            
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();
            
        $pendingExceptions = AttendanceException::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
            
        return Inertia::render('Student/Attendance/Index', compact(
            'todayAttendance',
            'recentAttendances',
            'pendingExceptions'
        ));
    }

    public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|image|max:2048',
                'notes' => 'nullable|string|max:500',
                'face_descriptor' => 'nullable|string',
            ]);

            $user = Auth::user();
            $today = Carbon::today();
            $settings = AttendanceSetting::getSettings();

            $existing = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if ($existing && $existing->check_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-in hari ini!'
                ]);
            }

            $locationService = new LocationVerificationService();
            $photoPath = null;
            $verificationResult = null;

            // Store photo
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance/check-in', 'public');
            }

            // Validate location if required
            $locationVerificationStatus = 'unverified';
            $spoofingScore = 0;
            $requiresManualReview = false;

            if ($settings->require_location && $settings->office_latitude && $settings->office_longitude) {
                $verificationResult = $locationService->verifyAttendanceLocation(
                    $request,
                    $settings->office_latitude,
                    $settings->office_longitude,
                    $settings->location_radius_meters
                );

                $locationVerificationStatus = $verificationResult['risk_level'] === 'high' ? 'suspicious' :
                                              (count($verificationResult['issues']) > 0 ? 'flagged' : 'verified');
                $spoofingScore = $verificationResult['spoofing_score'];
                $requiresManualReview = $verificationResult['risk_level'] === 'high' || !$verificationResult['verified'];

                // If high risk or outside radius, reject immediately
                if ($verificationResult['risk_level'] === 'high') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lokasi terindikasi mencurigakan. Hubungi supervisor Anda.',
                        'details' => $verificationResult['issues'],
                    ], 403);
                }

                // If outside radius, reject
                if (!$verificationResult['verified']) {
                    $distance = $verificationResult['details']['distance_check']['distance'];
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor! Jarak: ' . round($distance) . 'm'
                    ]);
                }
            }

            $now = Carbon::now();
            $workStart = Carbon::parse($settings->work_start_time);

            // Determine status based on time
            $isLate = $now->gt($workStart->addMinutes($settings->late_tolerance_minutes));
            $status = $isLate ? 'late' : 'present';

            $checkInDescriptor = null;
            if ($request->filled('face_descriptor')) {
                $decoded = json_decode($request->input('face_descriptor'), true);
                if (is_array($decoded)) {
                    $checkInDescriptor = $decoded;
                }
            }

            $faceService = new FaceVerificationService();
            $faceResult = $faceService->verify($checkInDescriptor, $user->face_descriptor, $user->id);

            if ($faceResult['status'] === 'mismatch') {
                $requiresManualReview = true;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'check_in' => $now,
                    'check_in_latitude' => $request->latitude,
                    'check_in_longitude' => $request->longitude,
                    'check_in_photo' => $photoPath,
                    'notes' => $request->notes,
                    'status' => $status,
                    'location_verification_status' => $locationVerificationStatus,
                    'location_spoofing_score' => $spoofingScore,
                    'location_verification_details' => $verificationResult,
                    'requires_manual_review' => $requiresManualReview,
                    'face_verification_status' => $faceResult['status'],
                    'face_match_distance' => $faceResult['distance'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil!',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            Log::error('CheckIn Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat check-in.'
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|image|max:2048',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();
            $today = Carbon::today();

            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->check_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan check-in hari ini!'
                ]);
            }

            if ($attendance->check_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-out hari ini!'
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance/check-out', 'public');
            }

            $updateData = [
                'check_out' => Carbon::now(),
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_out_photo' => $photoPath,
            ];

            if ($request->notes) {
                $existingNotes = $attendance->notes ?: '';
                $updateData['notes'] = $existingNotes . ($existingNotes ? "\n\nCheck-out: " : "Check-out: ") . $request->notes;
            }

            $attendance->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil!',
                'data' => $attendance->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('CheckOut Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat check-out.'
            ], 500);
        }
    }

    public function history(Request $request): Response
    {
        $user = Auth::user();
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $exceptions = AttendanceException::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        return Inertia::render('Student/Attendance/History', compact('attendances', 'exceptions', 'month', 'year'));
    }

    public function requestException(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date|before_or_equal:today',
                'type' => 'required|in:sick,leave,permit,official',
                'reason' => 'required|string|max:500',
                'attachment' => 'nullable|file|max:2048',
            ]);

            $user = Auth::user();

            $existing = AttendanceException::where('user_id', $user->id)
                ->where('date', $request->date)
                ->first();

            if ($existing) {
                return back()->with('error', 'Pengajuan untuk tanggal ini sudah ada!');
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attendance/exceptions', 'public');
            }

            AttendanceException::create([
                'user_id' => $user->id,
                'date' => $request->date,
                'type' => $request->type,
                'reason' => $request->reason,
                'attachment' => $attachmentPath,
            ]);

            return back()->with('success', 'Pengajuan berhasil disubmit!');

        } catch (\Exception $e) {
            Log::error('Exception Request Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengajukan permohonan.');
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
