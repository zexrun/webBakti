<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\Supervisor;

use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function showStudent2(Student $student)
    {
        // Muat semua relasi yang dibutuhkan secara efisien
        $student->load([
            'user',
            'supervisor.user',
            'tasks',
            'logbooks',
            'documents',
            'finalAssessment'
        ]);

        return view('admin.monitoring.show', compact('student'));
    }

    
    public function index2()
    {
        // Ambil semua supervisor, beserta relasi user dan mahasiswa bimbingannya
        // (yang juga dimuat relasi user-nya)
        $supervisors = Supervisor::with('user', 'students.user')->get();

        return view('admin.monitoring.index', compact('supervisors'));
    }

    public function index()
    {
        $supervisors = Supervisor::with(['user', 'students.user'])->get();
        
        return view('admin.monitoring.index', compact('supervisors'));
    }
    
    public function showSupervisor(Supervisor $supervisor)
    {
        $supervisor->load(['user', 'students.user']);
        
        return view('admin.monitoring.supervisor-detail', compact('supervisor'));
    }
    
    public function showStudent(Student $student)
    {
        $student->load(['user', 'supervisor.user']);
        
        return view('admin.monitoring.student-detail', compact('student'));
    }
}
