<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Supervisor;
use App\Models\Student;
use App\Notifications\NewTaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'due_date' => 'nullable|date',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'file' => 'nullable|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_attachments', 'public');
        } else if ($request->hasFile('file')) {
            dd('File not valid');
        }

        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        //dd($request->file('file'));

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
        if (Auth::user() === 'admin') {
            return view('supervisor.tasks.show', [
                'task' => $task,
                'assignedStudents' => $assignedStudents,
                'submissions' => $submissions,
            ]);
        }
        // 1. Ambil semua mahasiswa yang terhubung dengan tugas ini
        $assignedStudents = $task->students()->with('user')->get();

        // 2. Ambil semua submission untuk tugas ini dan kelompokkan berdasarkan student_id
        $submissions = $task->submissions()->with('student.user')->get()->keyBy('student_id');

        // 3. Kirim kedua data tersebut ke view
        return view('supervisor.tasks.show', [
            'task' => $task,
            'assignedStudents' => $assignedStudents,
            'submissions' => $submissions,
        ]);
    }

    public function grade(Request $request, Submission $submission)
    {
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
