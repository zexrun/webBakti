<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\Supervisor;
use App\Models\Student;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard');
    }

    public function index()
    {
        $supervisor = Auth::user()->supervisor;

        $students = $supervisor->students()
            ->with([
                'user',
                'finalAssessment',
                'documents' => function ($query) { // Muat HANYA dokumen yang relevan
                    $query->whereIn('type', ['proposal', 'laporan_akhir']);
            }
            ])
            ->paginate(10);

        return view('supervisor.students.list.index', compact('students'));
    }


    public function generateGrade(Student $student)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $submissions = $student->submissions()
            ->whereNotNull('grade')
            ->with('task')
            ->get();


        $data = [
            'student' => $student,
            'supervisor' => Auth::user(),
            'submissions' => $submissions,
            'date' => date('d M Y')
        ];

        $pdf = Pdf::loadView('supervisor.pdf.grades-pdf', $data);

        return $pdf->stream('rekap-nilai-' . Str::slug($student->user->name) . '.pdf');
    }

    public function showDocuments(Student $student)
    {
        // Validasi dengan cara yang sama seperti di generateGrade
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke dokumen mahasiswa ini.');
        }

        // Ambil dokumen
        $documents = $student->documents()->latest()->get();

        // Tampilkan view
        return view('supervisor.students.documents.index', compact('student', 'documents'));
    }
    /* 
    public function myStudents()
    {
        $supervisor = Auth::user()->supervisor;

        // Ambil semua mahasiswa bimbingan dan muat relasi yang dibutuhkan
        $students = $supervisor->students()
            ->with('user')
            // Kita juga memuat submission yang terhubung dengan tugas tipe 'akhir'
            ->with(['submissions' => function ($query) {
                $query->whereHas('task', function ($q) {
                    $q->where('type', 'akhir');
                });
            }])
            ->paginate(10);

        return view('supervisor.my-students', compact('students'));
    }


    public function viewStudent()
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $students = $supervisor->students()->with('user')->paginate(10);

        return view('supervisor.view-student', compact('students'));
    } */
}
