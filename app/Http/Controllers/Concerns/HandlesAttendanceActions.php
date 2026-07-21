<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Attendance;
use App\Models\AttendanceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Shared attendance approve/review/export logic used by both
 * Admin\AttendanceController (unscoped) and Supervisor\AttendanceController
 * (scoped to the supervisor's own students). Each caller is responsible
 * for authorization/scoping *before* calling these methods — the trait
 * itself only performs the ownership check already present in
 * approveAttendanceOrException() (supervisor-role guard) and adds the
 * same guard to reviewSuspiciousAttendance(), which previously had none.
 */
trait HandlesAttendanceActions
{
    protected function approveAttendanceOrException(Request $request, string $type, int $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if ($type === 'attendance') {
            $item = Attendance::with('user.student')->findOrFail($id);

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

    protected function reviewSuspiciousAttendance(Request $request, Attendance $attendance)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $attendance->loadMissing('user.student');

        if ($user->role === 'supervisor' && $attendance->user->student?->supervisor_id !== $user->id) {
            abort(403, 'Anda tidak berhak me-review attendance student ini.');
        }

        $attendance->update([
            'requires_manual_review' => false,
            'location_verification_status' => $request->action === 'approve' ? 'verified' : 'flagged',
            'location_notes' => $request->notes,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $message = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Kehadiran berhasil di-review dan {$message}!");
    }

    protected function exportDetailedCsv($handle, $attendances): void
    {
        $headers = [
            'Tanggal', 'Nama Mahasiswa', 'Nim', 'Pembimbing', 'Direktorat',
            'Waktu Masuk', 'Waktu Keluar', 'Jam Kerja', 'Status', 'Lokasi', 'Catatan',
        ];

        fputcsv($handle, $headers);

        foreach ($attendances as $attendance) {
            $checkInTime = $attendance->check_in_time
                ? Carbon::parse($attendance->check_in_time)->format('H:i:s')
                : '-';
            $checkOutTime = $attendance->check_out_time
                ? Carbon::parse($attendance->check_out_time)->format('H:i:s')
                : '-';

            fputcsv($handle, [
                Carbon::parse($attendance->date)->format('d-m-Y'),
                $attendance->user->name,
                $attendance->user->student->nim ?? '-',
                $attendance->user->student?->supervisor?->user->name ?? '-',
                $attendance->user->student?->supervisor?->direktorat ?? '-',
                $checkInTime,
                $checkOutTime,
                $attendance->working_hours ?? '-',
                ucfirst($attendance->status),
                $attendance->location_name ?? '-',
                $attendance->location_notes ?? '-',
            ]);
        }
    }

    protected function exportSummaryCsv($handle, $attendances): void
    {
        $headers = [
            'Nama Mahasiswa', 'NIM', 'Pembimbing', 'Total Hari Kerja',
            'Hadir', 'Terlambat', 'Tidak Hadir', 'Rata-rata Jam Kerja',
        ];

        fputcsv($handle, $headers);

        $summary = $attendances->groupBy('user_id')->map(function ($userAttendances) {
            $totalHours = 0;
            $countWithHours = 0;

            foreach ($userAttendances->where('check_out_time', '!=', null) as $att) {
                $totalHours += $att->working_hours ?? 0;
                $countWithHours++;
            }

            $avgHours = $countWithHours > 0 ? round($totalHours / $countWithHours, 2) : 0;

            return [
                'user' => $userAttendances->first()->user,
                'total_days' => $userAttendances->count(),
                'present' => $userAttendances->where('status', 'present')->count(),
                'late' => $userAttendances->where('status', 'late')->count(),
                'absent' => $userAttendances->where('status', 'absent')->count(),
                'avg_hours' => $avgHours,
            ];
        });

        foreach ($summary as $item) {
            fputcsv($handle, [
                $item['user']->name,
                $item['user']->student->nim ?? '-',
                $item['user']->student?->supervisor?->user->name ?? '-',
                $item['total_days'],
                $item['present'],
                $item['late'],
                $item['absent'],
                $item['avg_hours'] . ' jam',
            ]);
        }
    }
}
