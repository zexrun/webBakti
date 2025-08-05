<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\alert;

class LogbookController extends Controller
{
    public function index()
    {
        $logbook = Auth::user()->student->logbooks()->paginate(10);

        return view('student.logbooks.index', compact('logbook'));
    }

    public function create()
    {
        return view('student.logbooks.create');
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
        'file' => 'nullable|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240',
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

    public function show(Logbook $logbook)
    {
        if ($logbook->student_id !== Auth::user()->student->id) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('student.logbooks.show', compact('logbook'));
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
