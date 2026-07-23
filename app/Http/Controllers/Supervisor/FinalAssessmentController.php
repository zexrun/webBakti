<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

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

        return Inertia::render('Supervisor/Students/AssessmentCreate', [
            'student' => $student->load('user', 'documents'),
        ]);
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

        return Inertia::render('Supervisor/Students/AssessmentEdit', [
            'student' => $student->load('user'),
            'assessment' => $assessment,
        ]);
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
    $supervisorName = $supervisor->name ?? 'Pembimbing';

    return $this->streamCertificatePdf($student, $assessment, $supervisorName, now());
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
    $supervisorName = $student->supervisor->user->name ?? 'Pembimbing';

    return $this->streamCertificatePdf($student, $assessment, $supervisorName, $assessment->certificate_generated_at);
}

/**
 * Shared PDF-building logic for both generateCertificate() (supervisor)
 * and studentDownload() (student) - the certificate is always
 * regenerated from the view rather than read from storage, so both
 * callers need the same $data shape and filename convention.
 */
private function streamCertificatePdf(Student $student, FinalAssessment $assessment, string $supervisorName, $generatedAt)
{
    $pdf = Pdf::loadView('supervisor.pdf.certificate-pdf', [
        'student' => $student,
        'assessment' => $assessment,
        'supervisorName' => $supervisorName,
        'generatedAt' => $generatedAt,
        'generatedDate' => $generatedAt->format('d F Y H:i:s'),
    ]);

    $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

    return $pdf->stream($fileName);
}




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
