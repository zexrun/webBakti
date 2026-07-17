<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Supervisor;
use App\Models\Student;
use App\Notifications\NewTaskAssigned;
use App\Notifications\TaskDeadlineReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Menampilkan form untuk membuat tugas baru.
     */

    public function index()
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $students = $supervisor->students()
            ->with('user', 'tasks')
            ->paginate(5);

        return view('supervisor.tasks.index', compact('students'));
    }

    public function create()
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();
        $students = $supervisor->students()->with('user')->get();

        return view('supervisor.tasks.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:harian,akhir',
            'due_date' => 'nullable|date|after_or_equal:today',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'file' => 'nullable|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_attachments', 'public');
        }

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $task = Task::create([
            'supervisor_id' => $supervisor->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'due_date' => $request->due_date,
            'file_path' => $filePath,
        ]);

        if ($request->has('student_ids')) {
            $task->students()->attach($request->student_ids);

            $assignedStudents = Student::whereIn('id', $request->student_ids)->with('user')->get();
            foreach ($assignedStudents as $student) {
                $student->user->notify(new NewTaskAssigned($task));
            }
        }

        return redirect()->route('supervisor.tasks.index')->with('success', 'Tugas berhasil dibuat dan ditugaskan!');
    }

    public function show(Task $task)
    {
        if ($task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $assignedStudents = $task->students()->with('user')->get();
        $submissions = $task->submissions()->with('student.user')->get()->keyBy('student_id');

        return view('supervisor.tasks.show', [
            'task' => $task,
            'assignedStudents' => $assignedStudents,
            'submissions' => $submissions,
        ]);
    }

    public function edit(Task $task)
    {
        if ($task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();
        $students = $supervisor->students()->with('user')->get();
        $assignedStudents = $task->students()->pluck('id')->toArray();

        return view('supervisor.tasks.edit', compact('task', 'students', 'assignedStudents'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:harian,akhir',
            'due_date' => 'nullable|date|after_or_equal:today',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'file' => 'nullable|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240',
        ]);

        $filePath = $task->file_path;
        if ($request->hasFile('file')) {
            if ($task->file_path && \Storage::disk('public')->exists($task->file_path)) {
                \Storage::disk('public')->delete($task->file_path);
            }
            $filePath = $request->file('file')->store('task_attachments', 'public');
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'due_date' => $request->due_date,
            'file_path' => $filePath,
        ]);

        $task->students()->sync($request->student_ids);

        return redirect()->route('supervisor.tasks.show', $task)->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        if ($task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        if ($task->submissions()->exists()) {
            return redirect()
                ->route('supervisor.tasks.index')
                ->with('error', 'Tugas tidak dapat dihapus karena sudah ada submission dari siswa.');
        }

        if ($task->file_path && \Storage::disk('public')->exists($task->file_path)) {
            \Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();

        return redirect()->route('supervisor.tasks.index')->with('success', 'Tugas berhasil dihapus!');
    }

    public function grade(Request $request, Submission $submission)
    {
        if ($submission->task->supervisor_id !== Auth::user()->supervisor->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'grade' => 'required|string|max:10',
            'comments' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'comments' => $request->comments,
        ]);

        return redirect()
            ->route('supervisor.tasks.show', $submission->task_id)
            ->with('success', 'Nilai dan komentar berhasil diberikan');
    }
}
