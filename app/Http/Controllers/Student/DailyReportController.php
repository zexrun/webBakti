<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\alert;

class DailyReportController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return view('student.daily-reports.create');
    }

    public function store(Request $request)
    {

        $request->validate([
        'title' => 'required|string|max:255',
        'activity_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'feeling' => 'required|string',
        'description' => 'required|string',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('daily_reports_photo', 'public');
        }

        DailyReport::create([
            'student_id' => auth()->user()->student()->first()->id,
            'title' => $request->title,
            'activity_date' => $request->activity_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'description' => $request->description,
            'feeling' => $request->feeling,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('student.daily-reports.index')->with('success', 'Laporan harian berhasil disimpan!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
