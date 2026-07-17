<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Task;
use App\Notifications\TaskSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $student = Auth::user()->student;

        $tasks = $student
            ? $student->tasks()->with('supervisor.user')->latest()->paginate(10)
            : Task::whereRaw('0 = 1')->paginate(10);

        $tasks->getCollection()->transform(function (Task $task) use ($student) {
            $task->is_submitted = $student
                ? $task->submissions()->where('student_id', $student->id)->exists()
                : false;

            return $task;
        });

        return Inertia::render('Student/Tasks/Index', compact('tasks'));
    }

    public function show(Task $task): Response
    {
        $submission = $task->submissions()
            ->where('student_id', Auth::user()->student->id)
            ->first();

        return Inertia::render('Student/Tasks/Show', compact('task', 'submission'));
    }

    public function submit(Request $request, Task $task)
    {
        try {
            // Validasi input
            $request->validate([
                'content' => 'required|string',
                'file' => 'nullable|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240',
            ]);

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('submissions', 'public');
            }

            $submission = Submission::create([
                'task_id' => $task->id,
                'student_id' => Auth::user()->student->id,
                'content' => $request->content,
                'file_path' => $filePath,
            ]);

            $submission->load('task.supervisor.user', 'student.user');
            $supervisorUser = $submission->task->supervisor->user;
            $supervisorUser->notify(new TaskSubmitted($submission));

            return redirect()->route('student.tasks.show', $task->id)
                ->with('success', 'Tugas berhasil dikumpulkan');
        } catch (\Exception $e) {
            Log::error('Task submission failed', [
                'task_id' => $task->id,
                'student_id' => Auth::user()->student->id,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengumpulkan tugas. Silahkan coba lagi.');
        }
    }
}
