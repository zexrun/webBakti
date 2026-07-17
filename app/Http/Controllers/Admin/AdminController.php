<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Task;
use App\Models\Directorate;
use App\Models\User;
use App\Models\Submission;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', $this->dashboardData());
    }

    /**
     * Versi Inertia/React dari dashboard admin (dalam migrasi UI baru).
     */
    public function dashboardInertia(): Response
    {
        return Inertia::render('Admin/Dashboard', $this->dashboardData());
    }

    private function dashboardData(): array
    {
        // Basic counts
        $userCount = User::whereNotNull('username')->where('username', '!=', '')->count();
        $directorateCount = Directorate::count();
        $adminCount = User::where('role', 'admin')->count();
        $studentCount = Student::count();
        $supervisorCount = Supervisor::count();
        $taskCount = Task::count();

        // Task Statistics
        $totalSubmissions = Submission::count();
        $submissionsWithGrades = Submission::whereNotNull('grade')->count();
        $pendingSubmissions = $totalSubmissions - $submissionsWithGrades;
        $submissionGradeRate = $totalSubmissions > 0
            ? round(($submissionsWithGrades / $totalSubmissions) * 100, 1)
            : 0;

        // Attendance Statistics
        $totalAttendance = Attendance::count();
        $approvedAttendance = Attendance::where('status', 'approved')->count();
        $pendingAttendance = Attendance::where('status', 'pending')->count();
        $attendanceApprovalRate = $totalAttendance > 0
            ? round(($approvedAttendance / $totalAttendance) * 100, 1)
            : 0;

        // Today's Activity
        $todayAttendance = Attendance::whereDate('created_at', today())->count();
        $todaySubmissions = Submission::whereDate('created_at', today())->count();
        $todayApprovals = Attendance::whereDate('updated_at', today())->where('status', 'approved')->count();

        // This Month Statistics
        $thisMonthStudents = Student::whereMonth('created_at', now()->month)->count();
        $thisMonthTasks = Task::whereMonth('created_at', now()->month)->count();
        $thisMonthAttendance = Attendance::whereMonth('created_at', now()->month)->count();

        // Performance Metrics
        $averageStudentsPerSupervisor = $supervisorCount > 0
            ? round($studentCount / $supervisorCount, 1)
            : 0;

        $averageTasksPerSupervisor = $supervisorCount > 0
            ? round($taskCount / $supervisorCount, 1)
            : 0;

        $studentWithoutSupervisor = Student::whereNull('supervisor_id')->count();

        // Activity Trends (Last 7 days)
        $attendanceTrend = Attendance::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->pluck('count', 'date')
            ->values()
            ->toArray();

        $submissionTrend = Submission::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->pluck('count', 'date')
            ->values()
            ->toArray();

        // Department Performance
        $departmentStats = Directorate::with('supervisors', 'supervisors.students')
            ->get()
            ->map(fn($dir) => [
                'name' => $dir->name,
                'supervisors' => $dir->supervisors->count(),
                'students' => $dir->supervisors->sum(fn($s) => $s->students->count()),
            ])
            ->sortByDesc('students')
            ->values()
            ->toArray();

        return [
            'userCount' => $userCount,
            'directorateCount' => $directorateCount,
            'adminCount' => $adminCount,
            'studentCount' => $studentCount,
            'supervisorCount' => $supervisorCount,
            'taskCount' => $taskCount,
            'totalSubmissions' => $totalSubmissions,
            'submissionsWithGrades' => $submissionsWithGrades,
            'pendingSubmissions' => $pendingSubmissions,
            'submissionGradeRate' => $submissionGradeRate,
            'totalAttendance' => $totalAttendance,
            'approvedAttendance' => $approvedAttendance,
            'pendingAttendance' => $pendingAttendance,
            'attendanceApprovalRate' => $attendanceApprovalRate,
            'todayAttendance' => $todayAttendance,
            'todaySubmissions' => $todaySubmissions,
            'todayApprovals' => $todayApprovals,
            'thisMonthStudents' => $thisMonthStudents,
            'thisMonthTasks' => $thisMonthTasks,
            'thisMonthAttendance' => $thisMonthAttendance,
            'averageStudentsPerSupervisor' => $averageStudentsPerSupervisor,
            'averageTasksPerSupervisor' => $averageTasksPerSupervisor,
            'studentWithoutSupervisor' => $studentWithoutSupervisor,
            'attendanceTrend' => $attendanceTrend,
            'submissionTrend' => $submissionTrend,
            'departmentStats' => $departmentStats,
        ];
    }

    public function plotting()
    {
        $students = Student::with('user', 'supervisor.user')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();

        $supervisors = Supervisor::with('user')->get();

        return view('admin.plotting', [
            'students' => $students,
            'supervisors' => $supervisors,
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
        ]);

        $student = Student::find($request->student_id);
        
        if ($request->filled('supervisor_id')) {
            $supervisor = Supervisor::find($request->supervisor_id);
            $student->supervisor_id = $supervisor->id;
            $student->direktorat = $supervisor->direktorat;
        } else {
            $student->supervisor_id = null;
            $student->direktorat = null;
        }

        $student->save();

        return redirect()->route('admin.plotting')
            ->with('success', 'Status pembimbing mahasiswa berhasil di update.');
    }
}