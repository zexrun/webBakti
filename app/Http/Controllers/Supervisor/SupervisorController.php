<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\Supervisor;
use App\Models\Student;
use App\Models\Message;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard', $this->dashboardData());
    }

    /**
     * Versi Inertia/React dari dashboard pembimbing (dalam migrasi UI baru).
     */
    public function dashboardInertia(): Response
    {
        return Inertia::render('Supervisor/Dashboard', $this->dashboardData());
    }

    private function dashboardData(): array
    {
        $supervisor = Auth::user()->supervisor;

        $totalStudents = $supervisor->students()->count();
        $totalTasks = $supervisor->tasks()->count();
        $pendingAssessments = $supervisor->students()->whereDoesntHave('finalAssessment')->count();
        $completedInternships = $supervisor->students()->whereHas('finalAssessment')->count();

        $recentStudents = $supervisor->students()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $unreadMessages = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return [
            'stats' => [
                'totalStudents' => $totalStudents,
                'totalTasks' => $totalTasks,
                'pendingAssessments' => $pendingAssessments,
                'completedInternships' => $completedInternships,
            ],
            'recentStudents' => $recentStudents,
            'unreadMessages' => $unreadMessages,
        ];
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
}
