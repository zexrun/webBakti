<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Task;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\App;

class AdminController extends Controller
{
    public function dashboard() 
    {
        // Ambil data statistik untuk ditampilkan di kartu
        $studentCount = Student::count();
        $supervisorCount = Supervisor::count();
        $taskCount = Task::count();

        // Kirim data ke view
        return view('admin.dashboard', [
            'studentCount' => $studentCount,
            'supervisorCount' => $supervisorCount,
            'taskCount' => $taskCount,
        ]);
    }
    public function plotting()
    {
        $students = Student::with('user', 'supervisor.user')
        ->get()
        ->sortBy('user.name');
        $supervisors = Supervisor::with('user')->get();

        return view('admin.plotting', [
            'students' => $students,
            'supervisors' => $supervisors,   
        ]);
    }

    public function assign(Request $request)
    {
        // Validasi input
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
        ]);

        // Temukan student dan supervisor
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

        return redirect()->route('admin.plotting')->with('success', 'Status pembimbing mahasiswa berhasil di update.');
    }
}
