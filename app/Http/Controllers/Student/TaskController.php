<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Task;
use App\Notifications\TaskSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        // Kode ini sudah bagus, tidak perlu diubah.
        $student = Auth::user()->student;

        if ($student) {
            $tasks = $student->tasks()
                ->with('supervisor.user')
                ->latest()
                ->paginate(10);
        } else {
            $tasks = collect();
        }
        return view('student.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        // Kode ini sudah bagus, tidak perlu diubah.
        $submission = $task->submissions()
            ->where('student_id', Auth::user()->student->id)
            ->first();

        return view('student.tasks.show', compact('task', 'submission'));
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
            // Tangkap dan kembalikan error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengumpulkan tugas: ' . $e->getMessage());
        }
    }
}
