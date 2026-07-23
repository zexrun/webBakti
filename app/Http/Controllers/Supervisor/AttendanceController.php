<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesAttendanceActions;
use App\Models\Attendance;
use App\Models\AttendanceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    use HandlesAttendanceActions;

    /**
     * Scope any Attendance/AttendanceException query builder to only the
     * students supervised by the currently logged-in supervisor.
     */
    private function scopeToSupervisedStudents($query)
    {
        $supervisorId = Auth::user()->supervisor->id;

        return $query->whereHas('user.student', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        });
    }

    public function index(Request $request): Response
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $status = $request->get('status');

        $query = $this->scopeToSupervisedStudents(Attendance::with('user')->where('date', $date));

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(20)->withQueryString();

        $baseStatsQuery = fn () => $this->scopeToSupervisedStudents(Attendance::query())->where('date', $date);

        $stats = [
            'total' => $baseStatsQuery()->count(),
            'present' => $baseStatsQuery()->where('status', 'present')->count(),
            'late' => $baseStatsQuery()->where('status', 'late')->count(),
            'absent' => $baseStatsQuery()->where('status', 'absent')->count(),
            'suspicious' => $baseStatsQuery()->where('requires_manual_review', true)->count(),
        ];

        return Inertia::render('Supervisor/Attendance/Index', compact('attendances', 'stats', 'date', 'status'));
    }

    public function approvals(): Response
    {
        $pendingAttendances = $this->scopeToSupervisedStudents(
            Attendance::with('user')->where('supervisor_approval', 'pending')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $pendingExceptions = $this->scopeToSupervisedStudents(
            AttendanceException::with('user')->where('status', 'pending')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Supervisor/Attendance/Approvals', compact('pendingAttendances', 'pendingExceptions'));
    }

    public function approve(Request $request, $type, $id)
    {
        return $this->approveAttendanceOrException($request, $type, (int) $id);
    }

    public function suspicious(): Response
    {
        $suspiciousAttendances = $this->scopeToSupervisedStudents(
            Attendance::where('requires_manual_review', true)->with('user')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Supervisor/Attendance/Suspicious', compact('suspiciousAttendances'));
    }

    public function reviewSuspicious(Request $request, Attendance $attendance)
    {
        return $this->reviewSuspiciousAttendance($request, $attendance);
    }

    public function reports(Request $request): Response
    {
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = $this->scopeToSupervisedStudents(
            Attendance::with('user')->whereMonth('date', $month)->whereYear('date', $year)
        );

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();

        $users = Auth::user()->supervisor->students()->with('user')->get()->map(function ($student) {
            $user = $student->user;
            $user->setRelation('student', $student);
            return $user;
        });

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
        })->values();

        return Inertia::render('Supervisor/Attendance/Reports', compact('summary', 'users', 'month', 'year', 'userId'));
    }

    public function exportCsv(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');
        $exportType = $request->get('type', 'detail');

        $query = $this->scopeToSupervisedStudents(
            Attendance::with('user.student.supervisor.user')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
        );

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('date', 'asc')->orderBy('user_id')->get();

        $filename = "attendance_export_" . now()->format('Y-m-d_H-i-s') . ".csv";

        $response = new StreamedResponse(function () use ($attendances, $exportType) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($exportType === 'detail') {
                $this->exportDetailedCsv($handle, $attendances);
            } else {
                $this->exportSummaryCsv($handle, $attendances);
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
