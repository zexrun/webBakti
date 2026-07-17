<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Supervisor;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $query = Submission::whereHas('task', function ($q) use ($supervisor) {
            $q->where('supervisor_id', $supervisor->id);
        })->with(['student.user', 'task']);

        // Filter berdasarkan status grading
        $status = $request->get('status');
        if ($status === 'graded') {
            $query->whereNotNull('grade');
        } elseif ($status === 'pending') {
            $query->whereNull('grade');
        }

        // Filter berdasarkan task
        $taskId = $request->get('task_id');
        if ($taskId) {
            $query->where('task_id', $taskId);
        }

        // Filter berdasarkan student
        $studentId = $request->get('student_id');
        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['created_at', 'updated_at', 'grade'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $submissions = $query->paginate(15);

        // Get tasks dan students untuk filter
        $tasks = $supervisor->tasks()->get();
        $students = $supervisor->students()->with('user')->get();

        // Statistics
        $totalSubmissions = Submission::whereHas('task', function ($q) use ($supervisor) {
            $q->where('supervisor_id', $supervisor->id);
        })->count();

        $gradedCount = Submission::whereHas('task', function ($q) use ($supervisor) {
            $q->where('supervisor_id', $supervisor->id);
        })->whereNotNull('grade')->count();

        $pendingCount = $totalSubmissions - $gradedCount;

        return view('supervisor.submissions.index', compact(
            'submissions',
            'tasks',
            'students',
            'totalSubmissions',
            'gradedCount',
            'pendingCount',
            'status',
            'taskId',
            'studentId',
            'sortBy',
            'sortOrder'
        ));
    }

    public function edit(Submission $submission)
    {
        if ($submission->task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('supervisor.submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        if ($submission->task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'grade' => 'required|string|max:10',
            'comments' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'comments' => $request->comments,
        ]);

        return redirect()
            ->route('supervisor.submissions.index')
            ->with('success', 'Nilai berhasil disimpan!');
    }

    public function quickUpdate(Request $request, Submission $submission)
    {
        if ($submission->task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'grade' => 'required|string|max:10',
            'comments' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'comments' => $request->comments,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil diperbarui',
            'submission' => $submission,
        ]);
    }

    public function bulkGrade(Request $request)
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'submissions' => 'required|array',
            'submissions.*.id' => 'required|exists:submissions,id',
            'submissions.*.grade' => 'required|string|max:10',
            'submissions.*.comments' => 'nullable|string|max:1000',
        ]);

        $gradedCount = 0;
        foreach ($request->submissions as $submissionData) {
            $submission = Submission::find($submissionData['id']);

            if ($submission->task->supervisor_id === $supervisor->id) {
                $submission->update([
                    'grade' => $submissionData['grade'],
                    'comments' => $submissionData['comments'] ?? null,
                ]);
                $gradedCount++;
            }
        }

        return redirect()
            ->route('supervisor.submissions.index')
            ->with('success', "Nilai untuk $gradedCount submission berhasil disimpan!");
    }
}
