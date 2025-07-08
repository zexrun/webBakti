<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;


use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() { return view('admin.dashboard'); }

    public function plotting()
    {
        $students = Student::with('user', 'supervisor.user')->get();
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
            'supervisor_id' => 'required|exists:supervisors,id',
        ]);

        // Temukan student dan supervisor
        $student = Student::find($request->student_id);

        // Assign supervisor ke student
        $student->supervisor_id = $request->supervisor_id;
        $student->save();

        return redirect()->route('admin.plotting')->with('success', 'Pembimbing berhasil ditugaskan ke mahasiswa.');
    }
}
