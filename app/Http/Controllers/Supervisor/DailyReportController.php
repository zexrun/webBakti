<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index()
    {
        $studentIds = Auth::user()->supervisor->students()->pluck('id');

        $reports = DailyReport::whereIn('student_id', $studentIds)
                                ->with('student.user')
                                ->latest('activity_date')
                                ->paginate(15);

        return view('supervisor.daily-reports.index', compact('reports'));
    }

    public function show(DailyReport $report)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $report->student_id)->exists();

        if(!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        if (!$report->is_verified) {
            $report->update(['is_verified' => true]);
        }

        return view('supervisor.daily-reports.show', compact('report'));
    }

    public function verify(DailyReport $report)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $report->student_id)->exists();

        if(!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $report->update(['is_verified' => true]);

        return back()->with('success', 'Laporan berhasil ditandai sebagai telah dilihat');
    }
}
