<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Task;
use App\Models\Directorate;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() 
    {
        $userCount = User::whereNotNull('username')->where('username', '!=', '')->count();
        $directorateCount = Directorate::count();
        $adminCount = User::where('role', 'admin')->count();
        $studentCount = Student::count();
        $supervisorCount = Supervisor::count();
        $taskCount = Task::count();

        return view('admin.dashboard', [
            'userCount' => $userCount,
            'directorateCount' => $directorateCount,
            'adminCount' => $adminCount,
            'studentCount' => $studentCount,
            'supervisorCount' => $supervisorCount,
            'taskCount' => $taskCount,
        ]);
    }

    public function plotting()
    {
        $students = Student::with('user', 'supervisor.user')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();

        $supervisors = Supervisor::with('user')->get();

        return view('admin.plotting', [
            'students' => $students,
            'supervisors' => $supervisors,
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
        ]);

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

        return redirect()->route('admin.plotting')
            ->with('success', 'Status pembimbing mahasiswa berhasil di update.');
    }
}