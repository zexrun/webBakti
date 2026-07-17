<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard mahasiswa yang dipersonalisasi dengan berbagai widgets.
     */
    public function index()
    {
        $student = Auth::user()->student;

        // 1. Upcoming tasks (due in next 7 days)
        $upcomingTasks = $student->tasks()
            ->with('supervisor.user')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // 2. All tasks grouped by status
        $allTasks = $student->tasks()
            ->with('supervisor.user', 'submissions')
            ->orderBy('due_date', 'asc')
            ->get();

        $taskStats = [
            'total' => $allTasks->count(),
            'completed' => $allTasks->filter(fn($t) => $t->submissions()->where('student_id', $student->id)->exists())->count(),
            'pending' => $allTasks->filter(fn($t) => !$t->submissions()->where('student_id', $student->id)->exists())->count(),
            'graded' => $allTasks->flatMap(fn($t) => $t->submissions)->where('student_id', $student->id)->where('grade', '!=', null)->count(),
        ];

        // 3. Pending submissions (not yet graded)
        $pendingSubmissions = $student->submissions()
            ->whereNull('grade')
            ->with('task.supervisor.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Recent grades received
        $recentGrades = $student->submissions()
            ->whereNotNull('grade')
            ->with('task.supervisor.user')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // 5. Attendance stats
        $attendance = $student->attendances()
            ->where('created_at', '>=', now()->startOfMonth())
            ->get();

        $attendanceStats = [
            'present' => $attendance->where('check_out_time', '!=', null)->count(),
            'absent' => $attendance->where('check_in_time', null)->count(),
            'pending' => $attendance->where('status', 'pending')->count(),
            'rate' => $attendance->count() > 0
                ? round(($attendance->where('check_out_time', '!=', null)->count() / $attendance->count()) * 100, 1)
                : 0,
        ];

        // 6. Supervisor info
        $supervisor = $student->supervisor->user;

        // 7. Logbooks count this month
        $logbooksThisMonth = $student->logbooks()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        // 8. Progress calculation
        $completionPercentage = $taskStats['total'] > 0
            ? round(($taskStats['completed'] / $taskStats['total']) * 100, 1)
            : 0;

        return view('student.dashboard', compact(
            'student',
            'upcomingTasks',
            'allTasks',
            'taskStats',
            'pendingSubmissions',
            'recentGrades',
            'attendanceStats',
            'supervisor',
            'logbooksThisMonth',
            'completionPercentage'
        ));
    }
}
