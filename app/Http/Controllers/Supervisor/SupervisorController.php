<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\Supervisor;
use App\Models\Student;
use App\Models\Message;
use App\Helpers\DateHelper;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;
use Inertia\Response;

class SupervisorController extends Controller
{
    public function dashboard(): Response
    {
        // UI baru sudah di-cutover penuh: dashboard utama kini me-render React.
        return $this->dashboardInertia();
    }

    /**
     * Versi Inertia/React dari dashboard pembimbing. Alias route
     * *.dashboard.new tetap dipertahankan agar tautan lama tidak putus.
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
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Supervisor/Students/Index', [
            'students' => $students,
        ]);
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
            ->get()
            ->map(fn($s) => [
                'taskTitle' => $s->task->title ?? '-',
                'grade' => $s->grade ?? '-',
                'comment' => $s->comment ?? '-',
            ]);

        $supervisor = Auth::user()->supervisor;
        $reportNumber = str_pad(($student->id * 37 + now()->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
        $reportMonth = now()->format('m/Y');

        $data = [
            'studentName' => $student->user->name,
            'studentNim' => $student->nim ?? '-',
            'studentUniversitas' => $student->university ?? '-',
            'studentProgramStudi' => $student->study_program ?? '-',
            'periodePeriode' => $student->period_start && $student->period_end
                ? DateHelper::formatDateIndonesian($student->period_start) . ' s.d. ' . DateHelper::formatDateIndonesian($student->period_end)
                : 'Tidak tercantum',
            'supervisorName' => $supervisor->user->name ?? '-',
            'supervisorPosition' => $supervisor->position ?? '-',
            'supervisorEmployeeId' => $supervisor->employee_id ?? '-',
            'reportNumber' => $reportNumber,
            'reportMonth' => $reportMonth,
            'submissions' => $submissions->toArray(),
            'signatureDateFormatted' => DateHelper::formatDateIndonesian(now()),
        ];

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $id = (string) Str::uuid();
        $inputPath = $tmpDir . '/grades-' . $id . '-input.json';
        $outputPath = $tmpDir . '/grades-' . $id . '-output.pdf';

        file_put_contents($inputPath, json_encode($data));

        try {
            $scriptPath = base_path('resources/pdf-renderers/render-grades.cjs');

            $env = array_filter([
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'windir' => getenv('windir') ?: 'C:\\Windows',
                'PATH' => getenv('PATH'),
            ]);

            $result = Process::timeout(30)->env($env)->run(['node', $scriptPath, $inputPath, $outputPath]);

            if (!$result->successful()) {
                throw new \RuntimeException('React-PDF grades render failed: ' . $result->errorOutput());
            }

            $pdfContent = file_get_contents($outputPath);
        } finally {
            if (file_exists($inputPath)) {
                unlink($inputPath);
            }
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
        }

        $fileName = 'rekap-nilai-' . Str::slug($student->user->name) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
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

        return Inertia::render('Supervisor/Students/Documents', [
            'student' => $student->load('user'),
            'documents' => $documents,
        ]);
    }
}
