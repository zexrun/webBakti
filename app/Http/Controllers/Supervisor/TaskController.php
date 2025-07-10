<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Supervisor; // Import model Supervisor
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
        'assignment_type' => 'required|in:general,specific',
        'student_id' => 'required_if:assignment_type,specific|exists:students,id',
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

    // 3. Logika untuk assign tugas
    if ($request->assignment_type === 'general') {
        // Jika general, assign ke semua mahasiswa bimbingan supervisor ini
        $studentIds = $supervisor->students()->pluck('id');
        $task->students()->attach($studentIds);
    } else {
        // Jika spesifik, assign hanya ke satu mahasiswa yang dipilih
        $task->students()->attach($request->student_id);
    }

    // ... (redirect dengan pesan sukses) ...
    return redirect()->route('supervisor.tasks.index')->with('success', 'Tugas berhasil dibuat dan ditugaskan!');
    }

    public function show(Task $task)
    {
        $task->load('submissions.tasks.user');

        return view('supervisor.tasks.show', compact('task'));
    }
}