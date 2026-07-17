<?php

namespace App\Http\Controllers\Student;

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
        $logbook = Auth::user()->student->logbooks()->latest('activity_date')->paginate(10);

        return Inertia::render('Student/Logbooks/Index', compact('logbook'));
    }

    public function create(): Response
    {
        return Inertia::render('Student/Logbooks/Create');
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
        'file' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('logbook_files', 'public');
        }

        Logbook::create([
            'student_id' => Auth::user()->student->id,
            'title' => $request->title,
            'activity_date' => $request->activity_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'description' => $request->description,
            'feeling' => $request->feeling,
            'file_path' => $filePath,
        ]);

        return redirect()->route('student.logbooks.index')->with('success', 'Laporan harian berhasil disimpan!');
    }

    public function show(Logbook $logbook): Response
    {
        if ($logbook->student_id !== Auth::user()->student->id) {
            abort(403, 'AKSES DITOLAK');
        }

        return Inertia::render('Student/Logbooks/Show', compact('logbook'));
    }

    public function edit(Logbook $logbook): Response
    {
        if ($logbook->student_id !== Auth::user()->student->id) {
            abort(403, 'AKSES DITOLAK');
        }

        return Inertia::render('Student/Logbooks/Edit', compact('logbook'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        if ($logbook->student_id !== Auth::user()->student->id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'feeling' => 'required|string',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only(['title', 'activity_date', 'start_time', 'end_time', 'feeling', 'description']);

        if ($request->hasFile('file')) {
            if ($logbook->file_path && \Storage::disk('public')->exists($logbook->file_path)) {
                \Storage::disk('public')->delete($logbook->file_path);
            }
            $data['file_path'] = $request->file('file')->store('logbook_files', 'public');
        }

        $logbook->update($data);

        return redirect()->route('student.logbooks.index')->with('success', 'Laporan harian berhasil diperbarui!');
    }

    public function destroy(Logbook $logbook)
    {
        if ($logbook->student_id !== Auth::user()->student->id) {
            abort(403, 'AKSES DITOLAK');
        }

        if ($logbook->file_path && \Storage::disk('public')->exists($logbook->file_path)) {
            \Storage::disk('public')->delete($logbook->file_path);
        }

        $logbook->delete();

        return redirect()->route('student.logbooks.index')->with('success', 'Laporan harian berhasil dihapus!');
    }
}
