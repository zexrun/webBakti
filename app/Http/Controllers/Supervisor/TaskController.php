<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Supervisor; // Import model Supervisor
use App\Models\Student;
use App\Notifications\NewTaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Menampilkan form untuk membuat tugas baru.
     */

    public function index(){
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $tasks = Task::where('supervisor_id', $supervisor->id)
            ->latest()
            ->paginate(10); // Ambil 10 tugas terbaru
        
        
        return view('supervisor.tasks.index', compact('tasks'));
    }


    public function create()
    {
        $supervisor = Supervisor::where('user_id', auth()->id())->firstOrFail();
        $students = $supervisor->students()->with('user')->get();

        return view('supervisor.tasks.create', compact('students'));
    }

    /**
     * Menyimpan tugas baru ke database.
     */
    public function store(Request $request)
    {
    // 1. Validasi, tambahkan assignment_type dan student_id
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:harian,akhir',
        'due_date' => 'nullable|date',
        'file' => 'nullable|file|mimes:pdf,docx,pptx,zip,rar|max:10240',
        // 'assignment_type' => 'required|in:general,specific',
        'student_ids' => 'required|array    ',
        'student_ids.*' => 'exists:students,id',
    ]);

    // ... (kode untuk upload file dan mencari supervisor tetap sama) ...
    $filePath = $request->hasFile('file') ? $request->file('file')->store('task_attachments', 'public') : null;
    $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

    // 2. Buat tugas baru
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

    // 3. Logika untuk assign tugas
/*     if ($request->assignment_type === 'general') {
        // Jika general, assign ke semua mahasiswa bimbingan supervisor ini
        $studentIds = $supervisor->students()->pluck('id');
        $task->students()->attach($studentIds);
    } else {
        // Jika spesifik, assign hanya ke satu mahasiswa yang dipilih
        $task->students()->attach($request->student_id);
    } */

    // ... (redirect dengan pesan sukses) ...
    return redirect()->route('supervisor.tasks.index')->with('success', 'Tugas berhasil dibuat dan ditugaskan!');
    }

    public function show(Task $task)
    {
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