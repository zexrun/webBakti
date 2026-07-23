<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Notifications\LogbookFeedbackGiven;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $pdf = Pdf::loadView('supervisor.pdf.logbook-pdf', compact('logbook'));

        return $pdf->stream('logbook-' . $logbook->student->user->name . '-' . $logbook->activity_date . '.pdf');
    }

    public function exportRecapPdf(Request $request)
    {
        $logbooks = $this->filteredQuery($request)
            ->with('student.user')
            ->orderBy('student_id')
            ->orderBy('activity_date')
            ->get();

        $logbooksByStudent = (function () use ($logbooks) {
            foreach ($logbooks->groupBy('student_id') as $studentGroup) {
                yield $studentGroup->first()->student => $studentGroup;
            }
        })();

        $pdf = Pdf::loadView('supervisor.pdf.logbook-recap-pdf', [
            'logbooksByStudent' => $logbooksByStudent,
            'dateFrom' => $request->get('date_from'),
            'dateTo' => $request->get('date_to'),
            'generatedAt' => now(),
        ]);

        return $pdf->stream('rekap-logbook-' . now()->format('Y-m-d') . '.pdf');
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
