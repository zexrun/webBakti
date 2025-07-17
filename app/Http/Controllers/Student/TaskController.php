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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Task $task)
    {
        $submission = $task->submissions()
                            ->where('student_id', auth()->user()->student->id)
                            ->first();

        return view('student.tasks.show', compact('task', 'submission'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function submit(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf, docs, pptx, zip, rar|max:20480',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions');
        }
        
        $submission =  Submission::create([
            'task_id' => $task->id,
            'student_id' => Auth::user()->student->id,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);
        
        $supervisorUser = $task->supervisor->user;
        $supervisorUser->notify(new TaskSubmitted($submission));

        return redirect()->route('student.tasks.show', $task->id)->with('success', 'Tugas berhasil dikumpulkan');

    }
}
