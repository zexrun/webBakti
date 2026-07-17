<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        $supervisor = Auth::user()->supervisor;
        $students = $supervisor->students;

        // Overall Statistics
        $totalStudents = $students->count();
        $totalTasks = $supervisor->tasks()->count();
        $totalSubmissions = $supervisor->tasks()->with('submissions')->get()->flatMap(fn($t) => $t->submissions)->count();
        $gradedSubmissions = $supervisor->tasks()->with('submissions')->get()
            ->flatMap(fn($t) => $t->submissions)
            ->filter(fn($s) => $s->grade !== null)
            ->count();

        // Grade Statistics
        $submissions = $supervisor->tasks()->with('submissions')->get()->flatMap(fn($t) => $t->submissions)->filter(fn($s) => $s->grade !== null);
        $grades = $submissions->pluck('grade')->map(fn($g) => (float)$g)->toArray();
        $gradeStats = [
            'average' => count($grades) > 0 ? round(array_sum($grades) / count($grades), 2) : 0,
            'highest' => count($grades) > 0 ? round(max($grades), 2) : 0,
            'lowest' => count($grades) > 0 ? round(min($grades), 2) : 0,
            'median' => $this->calculateMedian($grades),
        ];

        // Grade Distribution
        $gradeDistribution = [
            'A (90-100)' => $submissions->filter(fn($s) => (float)$s->grade >= 90)->count(),
            'B (80-89)' => $submissions->filter(fn($s) => (float)$s->grade >= 80 && (float)$s->grade < 90)->count(),
            'C (70-79)' => $submissions->filter(fn($s) => (float)$s->grade >= 70 && (float)$s->grade < 80)->count(),
            'D (60-69)' => $submissions->filter(fn($s) => (float)$s->grade >= 60 && (float)$s->grade < 70)->count(),
            'E (<60)' => $submissions->filter(fn($s) => (float)$s->grade < 60)->count(),
        ];

        // Task Performance
        $taskPerformance = $supervisor->tasks()->with('submissions')->get()->map(function ($task) {
            $submissions = $task->submissions;
            $gradedSubmissions = $submissions->filter(fn($s) => $s->grade !== null);
            $gradedCount = $gradedSubmissions->count();
            $totalCount = $submissions->count();
            $grades = $gradedSubmissions->pluck('grade')->map(fn($g) => (float)$g)->toArray();
            $avgGrade = count($grades) > 0 ? round(array_sum($grades) / count($grades), 2) : 0;

            return [
                'id' => $task->id,
                'title' => $task->title,
                'submitted' => $totalCount,
                'graded' => $gradedCount,
                'completion_rate' => $totalCount > 0 ? round(($totalCount / $this->countAssignedStudents($task)) * 100, 1) : 0,
                'average_grade' => $avgGrade,
            ];
        });

        // Student Performance Trend (Last 30 days)
        $studentTrend = $this->getStudentPerformanceTrend($supervisor, 30);

        // Top Performing Students
        $topStudents = $this->getTopPerformingStudents($supervisor, 5);

        // Students Needing Attention
        $atRiskStudents = $this->getAtRiskStudents($supervisor);

        return view('supervisor.analytics.dashboard', compact(
            'totalStudents',
            'totalTasks',
            'totalSubmissions',
            'gradedSubmissions',
            'gradeStats',
            'gradeDistribution',
            'taskPerformance',
            'studentTrend',
            'topStudents',
            'atRiskStudents'
        ));
    }

    public function studentReport($studentId)
    {
        $supervisor = Auth::user()->supervisor;
        $student = $supervisor->students()->findOrFail($studentId);

        // Student Info
        $studentInfo = [
            'name' => $student->user->name,
            'nim' => $student->nim,
            'universitas' => $student->universitas,
            'program_studi' => $student->program_studi,
            'supervisor' => $supervisor->user->name,
        ];

        // Task & Submission Stats
        $tasks = $supervisor->tasks()->whereHas('students', fn($q) => $q->where('students.id', $studentId))->with('submissions')->get();
        $submissions = $tasks->flatMap(fn($t) => $t->submissions)->filter(fn($s) => $s->student_id === $studentId);

        $taskStats = [
            'assigned' => $tasks->count(),
            'submitted' => $submissions->count(),
            'graded' => $submissions->filter(fn($s) => $s->grade !== null)->count(),
            'completion_rate' => $tasks->count() > 0 ? round(($submissions->count() / $tasks->count()) * 100, 1) : 0,
        ];

        // Grade Performance
        $gradedSubmissions = $submissions->filter(fn($s) => $s->grade !== null);
        $studentGrades = $gradedSubmissions->pluck('grade')->map(fn($g) => (float)$g)->toArray();
        $gradePerformance = [
            'average_grade' => count($studentGrades) > 0 ? round(array_sum($studentGrades) / count($studentGrades), 2) : 0,
            'highest_grade' => count($studentGrades) > 0 ? round(max($studentGrades), 2) : 0,
            'lowest_grade' => count($studentGrades) > 0 ? round(min($studentGrades), 2) : 0,
            'total_graded' => $gradedSubmissions->count(),
        ];

        // Submission Timeline
        $submissionTimeline = $submissions->sortBy('created_at')->map(function ($submission) use ($tasks) {
            $task = $tasks->firstWhere('id', $submission->task_id);
            return [
                'task_title' => $task->title ?? 'Unknown',
                'submitted_at' => $submission->created_at,
                'grade' => $submission->grade,
                'feedback' => $submission->feedback,
                'status' => $submission->grade !== null ? 'Graded' : 'Pending',
            ];
        });

        // Attendance Stats
        $attendance = $student->attendances()
            ->where('attendances.created_at', '>=', now()->startOfMonth())
            ->get();

        $attendanceStats = [
            'present' => $attendance->where('status', 'present')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'rate' => $attendance->count() > 0 ? round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 1) : 0,
        ];

        // Final Assessment
        $finalAssessment = $student->finalAssessment;

        return view('supervisor.analytics.student-report', compact(
            'student',
            'studentInfo',
            'taskStats',
            'gradePerformance',
            'submissionTimeline',
            'attendanceStats',
            'finalAssessment'
        ));
    }

    public function taskAnalytics($taskId)
    {
        $supervisor = Auth::user()->supervisor;
        $task = $supervisor->tasks()->findOrFail($taskId);

        $submissions = $task->submissions;

        // Task Info
        $taskInfo = [
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'created_at' => $task->created_at,
        ];

        // Submission Stats
        $submissionStats = [
            'total_assigned' => $task->students()->count(),
            'submitted' => $submissions->count(),
            'not_submitted' => $task->students()->count() - $submissions->count(),
            'graded' => $submissions->filter(fn($s) => $s->grade !== null)->count(),
            'pending_grade' => $submissions->filter(fn($s) => $s->grade === null)->count(),
            'submission_rate' => $task->students()->count() > 0 ? round(($submissions->count() / $task->students()->count()) * 100, 1) : 0,
        ];

        // Grade Distribution
        $gradedSubmissions = $submissions->filter(fn($s) => $s->grade !== null);
        $gradeDistribution = [
            'A (90-100)' => $gradedSubmissions->filter(fn($s) => (float)$s->grade >= 90)->count(),
            'B (80-89)' => $gradedSubmissions->filter(fn($s) => (float)$s->grade >= 80 && (float)$s->grade < 90)->count(),
            'C (70-79)' => $gradedSubmissions->filter(fn($s) => (float)$s->grade >= 70 && (float)$s->grade < 80)->count(),
            'D (60-69)' => $gradedSubmissions->filter(fn($s) => (float)$s->grade >= 60 && (float)$s->grade < 70)->count(),
            'E (<60)' => $gradedSubmissions->filter(fn($s) => (float)$s->grade < 60)->count(),
        ];

        // Grade Statistics
        $taskGrades = $gradedSubmissions->pluck('grade')->map(fn($g) => (float)$g)->toArray();
        $gradeStats = [
            'average' => count($taskGrades) > 0 ? round(array_sum($taskGrades) / count($taskGrades), 2) : 0,
            'highest' => count($taskGrades) > 0 ? round(max($taskGrades), 2) : 0,
            'lowest' => count($taskGrades) > 0 ? round(min($taskGrades), 2) : 0,
            'median' => $this->calculateMedian($taskGrades),
        ];

        // Student Performance on this Task
        $studentPerformance = $submissions->map(function ($submission) {
            return [
                'student_name' => $submission->student->user->name,
                'student_id' => $submission->student_id,
                'submitted_at' => $submission->created_at,
                'grade' => $submission->grade,
                'feedback' => $submission->feedback,
                'status' => $submission->grade !== null ? 'Graded' : 'Pending',
                'grade_numeric' => $submission->grade !== null ? (float)$submission->grade : 0,
            ];
        })->sortByDesc('grade_numeric')->values();

        // Not Submitted Students
        $notSubmittedStudents = $task->students()
            ->whereNotIn('id', $submissions->pluck('student_id'))
            ->get()
            ->map(fn($s) => ['name' => $s->user->name, 'nim' => $s->nim]);

        return view('supervisor.analytics.task-analytics', compact(
            'task',
            'taskInfo',
            'submissionStats',
            'gradeDistribution',
            'gradeStats',
            'studentPerformance',
            'notSubmittedStudents'
        ));
    }

    private function calculateMedian($grades)
    {
        if (empty($grades)) return 0;

        sort($grades);
        $count = count($grades);
        $middle = intdiv($count, 2);

        return $count % 2 === 0
            ? round(($grades[$middle - 1] + $grades[$middle]) / 2, 2)
            : $grades[$middle];
    }

    private function countAssignedStudents($task)
    {
        return $task->students()->count();
    }

    private function getStudentPerformanceTrend($supervisor, $days)
    {
        $tasks = $supervisor->tasks()->with('submissions')->get();
        $trendData = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $submissionsOnDate = $tasks->flatMap(fn($t) => $t->submissions)
                ->filter(fn($s) => $s->created_at->format('Y-m-d') === $date)
                ->filter(fn($s) => $s->grade !== null);

            $trendGrades = $submissionsOnDate->pluck('grade')->map(fn($g) => (float)$g)->toArray();
            $avgGrade = count($trendGrades) > 0
                ? round(array_sum($trendGrades) / count($trendGrades), 2)
                : 0;

            $trendData[] = [
                'date' => $date,
                'average_grade' => $avgGrade,
                'submissions' => $submissionsOnDate->count(),
            ];
        }

        return $trendData;
    }

    private function getTopPerformingStudents($supervisor, $limit = 5)
    {
        $tasks = $supervisor->tasks()->with('submissions')->get();
        $studentGrades = collect();

        foreach ($tasks->flatMap(fn($t) => $t->submissions)->filter(fn($s) => $s->grade !== null) as $submission) {
            $studentGrades->push([
                'student_name' => $submission->student->user->name,
                'student_id' => $submission->student_id,
                'grade' => $submission->grade,
            ]);
        }

        return $studentGrades->groupBy('student_id')
            ->map(fn($grades) => [
                'student_name' => $grades[0]['student_name'],
                'student_id' => $grades[0]['student_id'],
                'average_grade' => round(array_sum(array_map(fn($g) => (float)$g['grade'], $grades->all())) / count($grades), 2),
                'submission_count' => $grades->count(),
            ])
            ->sortByDesc('average_grade')
            ->take($limit)
            ->values();
    }

    private function getAtRiskStudents($supervisor)
    {
        $tasks = $supervisor->tasks()->with('submissions')->get();
        $atRisk = collect();

        foreach ($supervisor->students as $student) {
            $submissions = $tasks->flatMap(fn($t) => $t->submissions)->filter(fn($s) => $s->student_id === $student->id);
            $gradedSubmissions = $submissions->filter(fn($s) => $s->grade !== null);

            if ($submissions->count() > 0) {
                $submissionRate = ($submissions->count() / $tasks->count()) * 100;
                $riskGrades = $gradedSubmissions->pluck('grade')->map(fn($g) => (float)$g)->toArray();
                $avgGrade = count($riskGrades) > 0 ? array_sum($riskGrades) / count($riskGrades) : 0;

                if ($submissionRate < 50 || $avgGrade < 60) {
                    $atRisk->push([
                        'student_name' => $student->user->name,
                        'student_id' => $student->id,
                        'submission_rate' => round($submissionRate, 1),
                        'average_grade' => round($avgGrade, 2),
                        'reason' => $submissionRate < 50 ? 'Low submission rate' : 'Low grades',
                    ]);
                }
            }
        }

        return $atRisk->sortBy('average_grade')->take(5);
    }
}
