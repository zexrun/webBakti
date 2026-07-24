<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Notifications\LogbookFeedbackGiven;
use App\Helpers\DateHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LogbookController extends Controller
{
    public function index(Request $request): Response
    {
        $logbooks = $this->filteredQuery($request)
            ->latest('activity_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Supervisor/Logbooks/Index', [
            'logbooks' => $logbooks,
            'students' => Auth::user()->supervisor->students()->with('user')->get(),
            'filters' => [
                'student_id' => $request->get('student_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ],
        ]);
    }

    public function show(Logbook $logbook): Response
    {
        $logbook->load('student.user');

        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        if (!$logbook->is_verified) {
            $logbook->update(['is_verified' => true]);
        }

        return Inertia::render('Supervisor/Logbooks/Show', compact('logbook'));
    }

    public function verify(Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $logbook->update(['is_verified' => true]);

        return back()->with('success', 'Laporan berhasil ditandai sebagai telah dilihat');
    }

    public function sendFeedback(Request $request, Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'feedback' => 'required|string|max:2000',
        ]);

        $logbook->update([
            'feedback' => $request->feedback,
            'feedback_at' => now(),
        ]);

        $logbook->load('student.user', 'student.supervisor.user');
        $logbook->student->user->notify(new LogbookFeedbackGiven($logbook));

        return back()->with('success', 'Feedback berhasil dikirim');
    }

    public function exportPdf(Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $logbook->load('student.user', 'student.supervisor.user');
        $student = $logbook->student;
        $supervisor = $student->supervisor;

        $data = [
            'studentName' => $student->user->name,
            'studentNim' => $student->nim ?? '-',
            'studentUniversitas' => $student->university ?? '-',
            'supervisorName' => $supervisor->user->name ?? '-',
            'logNumber' => str_pad(($student->id * 37 + $logbook->activity_date->day) % 900 + 100, 3, '0', STR_PAD_LEFT),
            'reportMonth' => $logbook->activity_date->format('m/Y'),
            'activityDate' => DateHelper::formatDateIndonesian($logbook->activity_date),
            'activityTime' => $logbook->start_time . ' - ' . $logbook->end_time,
            'feeling' => $logbook->feeling ?? '-',
            'description' => $logbook->description ?? '-',
            'feedback' => $logbook->feedback ?? null,
        ];

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $id = (string) Str::uuid();
        $inputPath = $tmpDir . '/logbook-' . $id . '-input.json';
        $outputPath = $tmpDir . '/logbook-' . $id . '-output.pdf';

        file_put_contents($inputPath, json_encode($data));

        try {
            $scriptPath = base_path('resources/pdf-renderers/render-logbook.cjs');

            $env = array_filter([
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'windir' => getenv('windir') ?: 'C:\\Windows',
                'PATH' => getenv('PATH'),
            ]);

            $result = Process::timeout(30)->env($env)->run(['node', $scriptPath, $inputPath, $outputPath]);

            if (!$result->successful()) {
                throw new \RuntimeException('React-PDF logbook render failed: ' . $result->errorOutput());
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

        $fileName = 'logbook-' . Str::slug($student->user->name) . '-' . $logbook->activity_date->format('Y-m-d') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function exportRecapPdf(Request $request)
    {
        $logbooks = $this->filteredQuery($request)
            ->with('student.user', 'student.supervisor.user')
            ->orderBy('activity_date')
            ->get();

        $supervisor = Auth::user()->supervisor;
        $logNumber = str_pad(($supervisor->id * 37 + now()->day) % 900 + 100, 3, '0', STR_PAD_LEFT);

        $logs = $logbooks->map(fn($log) => [
            'date' => DateHelper::formatDateIndonesian($log->activity_date),
            'time' => $log->start_time . '-' . $log->end_time,
            'title' => $log->title ?? '-',
            'feeling' => $log->feeling ?? '-',
            'status' => $log->is_verified ? 'Telah Dilihat' : 'Belum Dilihat',
            'studentName' => $log->student->user->name ?? '-',
        ])->toArray();

        $student = $logbooks->first()?->student;

        $data = [
            'studentName' => $student?->user->name ?? 'Semua Mahasiswa',
            'studentNim' => $student?->nim ?? '-',
            'studentUniversitas' => $student?->university ?? '-',
            'studentProgramStudi' => $student?->study_program ?? '-',
            'supervisorName' => $supervisor->user->name ?? '-',
            'supervisorPosition' => $supervisor->position ?? '-',
            'logNumber' => $logNumber,
            'reportMonth' => now()->format('m/Y'),
            'logs' => $logs,
            'totalLogs' => count($logs),
            'multipleStudents' => $logbooks->groupBy('student_id')->count() > 1,
            'period' => ($request->get('date_from') && $request->get('date_to'))
                ? DateHelper::formatDateIndonesian($request->get('date_from')) . ' s.d. ' . DateHelper::formatDateIndonesian($request->get('date_to'))
                : 'Semua periode',
            'signatureDateFormatted' => DateHelper::formatDateIndonesian(now()),
        ];

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $id = (string) Str::uuid();
        $inputPath = $tmpDir . '/logbook-recap-' . $id . '-input.json';
        $outputPath = $tmpDir . '/logbook-recap-' . $id . '-output.pdf';

        file_put_contents($inputPath, json_encode($data));

        try {
            $scriptPath = base_path('resources/pdf-renderers/render-logbook-recap.cjs');

            $env = array_filter([
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
                'windir' => getenv('windir') ?: 'C:\\Windows',
                'PATH' => getenv('PATH'),
            ]);

            $result = Process::timeout(30)->env($env)->run(['node', $scriptPath, $inputPath, $outputPath]);

            if (!$result->successful()) {
                throw new \RuntimeException('React-PDF logbook recap render failed: ' . $result->errorOutput());
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

        $fileName = 'rekap-logbook-' . now()->format('Y-m-d') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $studentIds = Auth::user()->supervisor->students()->pluck('id');

        $query = Logbook::whereIn('student_id', $studentIds)->with('student.user');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->get('student_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->get('date_to'));
        }

        return $query;
    }
}
