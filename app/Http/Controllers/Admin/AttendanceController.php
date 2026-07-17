<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\AttendanceSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $status = $request->get('status');
        
        $query = Attendance::with('user')->where('date', $date);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $attendances = $query->paginate(20);
        
        $stats = [
            'total' => Attendance::where('date', $date)->count(),
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
        ];
        
        return view('admin.attendance.index', compact('attendances', 'stats', 'date', 'status'));
    }

    public function approvals()
    {
        $pendingAttendances = Attendance::with('user')
            ->where('supervisor_approval', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $pendingExceptions = AttendanceException::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.attendance.approvals', compact('pendingAttendances', 'pendingExceptions'));
    }

    public function approve(Request $request, $type, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if ($type === 'attendance') {
            $item = Attendance::with('user.student')->findOrFail($id);

            // Authorization: Supervisor must supervise this student
            if ($user->role === 'supervisor' && $item->user->student?->supervisor_id !== $user->id) {
                abort(403, 'Anda tidak berhak mengapprove attendance student ini.');
            }

            $item->update([
                'supervisor_approval' => $request->action === 'approve' ? 'approved' : 'rejected',
                'supervisor_notes' => $request->notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        } else {
            $item = AttendanceException::with('user.student')->findOrFail($id);

            // Authorization: Supervisor must supervise this student
            if ($user->role === 'supervisor' && $item->user->student?->supervisor_id !== $user->id) {
                abort(403, 'Anda tidak berhak mengapprove exception student ini.');
            }

            $item->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'supervisor_notes' => $request->notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        }

        $message = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Item berhasil {$message}!");
    }

    public function settings()
    {
        $settings = AttendanceSetting::getSettings();
        return view('admin.attendance.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'work_start_time' => 'required|date_format:H:i',
                'work_end_time' => 'required|date_format:H:i|after:work_start_time',
                'late_tolerance_minutes' => 'required|integer|min:0|max:120',
                'location_radius_meters' => 'required|integer|min:10|max:5000',
                'require_photo' => 'nullable',
                'require_location' => 'nullable',
                'office_latitude' => 'nullable|numeric|between:-90,90',
                'office_longitude' => 'nullable|numeric|between:-180,180',
                'office_address' => 'nullable|string|max:500',
            ]);

            $updateData = [
                'work_start_time' => $validated['work_start_time'] . ':00',
                'work_end_time' => $validated['work_end_time'] . ':00',
                'late_tolerance_minutes' => (int) $validated['late_tolerance_minutes'],
                'location_radius_meters' => (int) $validated['location_radius_meters'],
                'require_photo' => $request->has('require_photo'),
                'require_location' => $request->has('require_location'),
                'office_latitude' => $validated['office_latitude'] ? (float) $validated['office_latitude'] : null,
                'office_longitude' => $validated['office_longitude'] ? (float) $validated['office_longitude'] : null,
                'office_address' => $validated['office_address'] ?? null,
            ];

            $settings = AttendanceSetting::first();
            
            if (!$settings) {
                AttendanceSetting::create($updateData);
            } else {
                $settings->update($updateData);
            }

            return redirect()
                ->route('admin.attendance.settings')
                ->with('success', 'Pengaturan berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengaturan.');
        }
    }

    public function reports(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = Attendance::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();
        $users = User::where('role', 'student')->get();

        $summary = $attendances->groupBy('user_id')->map(function ($userAttendances) {
            return [
                'user' => $userAttendances->first()->user,
                'total_days' => $userAttendances->count(),
                'present' => $userAttendances->where('status', 'present')->count(),
                'late' => $userAttendances->where('status', 'late')->count(),
                'absent' => $userAttendances->where('status', 'absent')->count(),
                'avg_hours' => $userAttendances->where('check_out', '!=', null)->avg(function ($att) {
                    return $att->working_hours;
                }),
            ];
        });

        return view('admin.attendance.reports', compact('summary', 'users', 'month', 'year', 'userId'));
    }
}
