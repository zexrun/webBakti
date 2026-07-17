<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LogbookController extends Controller
{
    public function index(): Response
    {
        $studentIds = Auth::user()->supervisor->students()->pluck('id');

        $logbooks = Logbook::whereIn('student_id', $studentIds)
            ->with('student.user')
            ->latest('activity_date')
            ->paginate(15);

        return Inertia::render('Supervisor/Logbooks/Index', compact('logbooks'));
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
}
