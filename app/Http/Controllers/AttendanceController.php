<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
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
            
        return view('student.attendance.index', compact(
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

            // Validate location if required
            if ($settings->require_location && $settings->office_latitude && $settings->office_longitude) {
                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $settings->office_latitude,
                    $settings->office_longitude
                );

                if ($distance > $settings->location_radius_meters) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar radius kantor! Jarak: ' . round($distance) . 'm'
                    ]);
                }
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance/check-in', 'public');
            }

            $now = Carbon::now();
            $workStart = Carbon::parse($settings->work_start_time);

            // Determine status based on time
            $isLate = $now->gt($workStart->addMinutes($settings->late_tolerance_minutes));
            $status = $isLate ? 'late' : 'present';

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

    public function history(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $exceptions = AttendanceException::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        return view('student.attendance.history', compact('attendances', 'exceptions', 'month', 'year'));
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
