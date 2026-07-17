<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinalAssessmentController extends Controller
{
    /**
     * Menampilkan form untuk memberi penilaian akhir.
     */
    public function create(Student $student)
    {
        // Otorisasi sederhana untuk memastikan supervisor hanya menilai mahasiswa bimbingannya
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, "AKSES DITOLAK");
        }

        return view('supervisor.students.assessment.create', compact('student'));
    }

    public function edit(Student $student)
    {
        // Otorisasi: Supervisor hanya bisa edit penilaian student bimbingannya
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, "AKSES DITOLAK");
        }

        $assessment = $student->finalAssessment;

        // Jika belum ada penilaian, arahkan ke halaman create
        if (!$assessment) {
            return redirect()->route('supervisor.students.assessment.create', $student->id);
        }

        return view('supervisor.students.assessment.edit', compact('student', 'assessment'));
    }

    public function update(Request $request, Student $student)
    {
        // Otorisasi: Supervisor hanya bisa update penilaian student bimbingannya
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, "AKSES DITOLAK");
        }

        $request->validate([
            'final_grade' => 'required|string|max:10',
            'overall_comments' => 'required|string',
        ]);

        $student->finalAssessment->update([
            'final_grade' => $request->final_grade,
            'overall_comments' => $request->overall_comments,
        ]);

        return redirect()->route('supervisor.students.list.index')->with('success', 'Penilaian akhir berhasil diperbarui!');
    }

public function generateCertificate(Student $student)
{
    $student->load('user');
    $supervisor = Auth::user();

    // Validasi supervisor
    $isAuthorized = $supervisor->supervisor->students()->where('id', $student->id)->exists();
    if (!$isAuthorized) {
        abort(403, 'Akses ditolak');
    }

    $assessment = $student->finalAssessment;
    if (!$assessment) {
        return back()->with('error', 'Penilaian akhir belum diisi.');
    }

    // Cek kelengkapan dokumen (single query)
    $requiredDocuments = ['proposal', 'laporan_akhir'];
    $documentTypes = $student->documents()
        ->whereIn('type', $requiredDocuments)
        ->pluck('type')
        ->toArray();

    $missingDocuments = array_diff($requiredDocuments, $documentTypes);
    if (!empty($missingDocuments)) {
        return back()->with('error', 'Dokumen belum lengkap.');
    }

    // Cek apakah nilai diubah setelah sertifikat digenerate
    $isAssessmentUpdated = !$assessment->certificate_generated_at || 
                           $assessment->updated_at > $assessment->certificate_generated_at;

    // Jika nilai berubah atau belum pernah generate, generate ulang
    if ($isAssessmentUpdated) {
        $assessment->certificate_generated_at = now();
        $assessment->save();
    }

    // Generate PDF dan kirim ke browser (langsung tanpa simpan ke storage)
    $generatedDate = now()->format('d F Y H:i:s');;
    $supervisorName = $supervisor->name ?? 'Pembimbing';

    $pdf = Pdf::loadView('supervisor.pdf.certificate-pdf', [
        'student' => $student,
        'assessment' => $assessment,
        'supervisorName' => $supervisorName,
        'generatedDate' => $generatedDate
    ]);

    $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

    return $pdf->stream($fileName);
}

public function studentDownload()
{
    $user = Auth::user();
    $student = $user->student; // relasi 'student' di model User

    if (!$student || !$student->finalAssessment) {
        return back()->with('error', 'Data tidak ditemukan atau penilaian akhir belum tersedia.');
    }

    $assessment = $student->finalAssessment;

    // Cek apakah pembimbing sudah generate
    if (!$assessment->certificate_generated_at) {
        return back()->with('error', 'Sertifikat belum tersedia. Silakan tunggu pembimbing mengenerate.');
    }

    // Generate ulang PDF untuk dikirim ke browser (bukan dari storage)
    $generatedDate = $assessment->certificate_generated_at->format('d F Y H:i:s');
    $supervisorName = $student->supervisor->user->name ?? 'Pembimbing';

    $pdf = Pdf::loadView('supervisor.pdf.certificate-pdf', [
        'student' => $student,
        'assessment' => $assessment,
        'supervisorName' => $supervisorName,
        'generatedDate' => $generatedDate
    ]);

    $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

    return $pdf->stream($fileName);
}




/*     public function downloadCertificate()
    {
        $student = Auth::user()->student;
        $assessment = FinalAssessment::where('student_id', $student->id)->first();

        if (!$assessment || !$assessment->certificate_generated_at) {
            return abort(403, 'Sertifikat belum tersedia. Silakan hubungi pembimbing Anda.');
        }

        $fileName = 'certificates/certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

        if (!Storage::exists($fileName)) {
            return abort(404, 'Sertifikat tidak ditemukan.');
        }

        return Storage::download($fileName);
    } */


    public function store(Request $request, Student $student)
    {

        $isAuthorized = Auth::user()->supervisor->students()->where('id', $student->id)->exists();
        if (!$isAuthorized) {
            abort(403, "AKSES DITOLAK");
        }

        $request->validate([
            'final_grade' => 'required|string|max:10',
            'overall_comments' => 'required|string',
        ]);

        FinalAssessment::updateOrCreate(
            ['student_id' => $student->id],
            [
                'supervisor_id' => Auth::user()->supervisor->id,
                'final_grade' => $request->final_grade,
                'overall_comments' => $request->overall_comments,
            ]
        );

        return redirect()->route('supervisor.students.list.index')->with('success', 'Penilaian akhir berhasil disimpan!');
    }
}
