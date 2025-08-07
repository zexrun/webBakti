<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard mahasiswa beserta data tugasnya.
     */
    public function index()
    {
        // 1. Dapatkan model 'student' dari user yang sedang login
        $student = Auth::user()->student;

        // 2. Ambil SEMUA tugas yang ditugaskan ke mahasiswa ini
        // Urutkan berdasarkan tenggat waktu terdekat
        $tasks = $student->task()
            ->with('supervisor.user')
            ->orderBy('due_date', 'asc')
            ->get();

        // 3. Kirim data tasks ke view
        return view('student.dashboard', [
            'tasks' => $tasks,
        ]);
    }
}
