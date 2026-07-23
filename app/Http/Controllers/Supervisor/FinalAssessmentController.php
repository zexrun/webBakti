<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

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
 * regenerated fresh rather than read from storage, so both callers
 * need the same $data shape and filename convention. Rendering runs
 * through a Node/React-PDF script rather than DomPDF, since DomPDF
 * was found to truncate long Indonesian sentences mid-line
 * regardless of container width - a bug React-PDF's Yoga-based
 * layout engine does not exhibit.
 */
private function streamCertificatePdf(Student $student, FinalAssessment $assessment, string $supervisorName, $generatedAt)
{
    $certDate = $generatedAt ?? now();
    $certNumber = str_pad(($student->id * 37 + $certDate->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
    $documentNumber = $certNumber . '/KOMDIG/BAKTI/SDA/' . $certDate->format('m/Y') . '/PKL.01.' . $certDate->format('d/m/Y');

    $hasPeriode = isset($student->periode_mulai) && isset($student->periode_selesai);

    $data = [
        'documentNumber' => $documentNumber,
        'supervisorName' => $supervisorName,
        'signerName' => 'SUDARMANTO',
        'signerNip' => '196907071959031002',
        'signerPosition' => 'Kepala Divisi SDM dan Humas',
        'studentName' => $student->user->name,
        'studentNim' => $student->nim ?? '-',
        'studentProgramStudi' => $student->program_studi ?? '-',
        'studentUniversitas' => $student->universitas ?? '-',
        'periodeMulaiFormatted' => $hasPeriode ? date('d F Y', strtotime($student->periode_mulai)) : null,
        'periodeSelesaiFormatted' => $hasPeriode ? date('d F Y', strtotime($student->periode_selesai)) : null,
        'hasPeriode' => $hasPeriode,
        'signatureDateFormatted' => date('d F Y'),
    ];

    $tmpDir = storage_path('app/tmp');
    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0755, true);
    }

    $id = (string) Str::uuid();
    $inputPath = $tmpDir . '/cert-' . $id . '-input.json';
    $outputPath = $tmpDir . '/cert-' . $id . '-output.pdf';

    file_put_contents($inputPath, json_encode($data));

    try {
        $scriptPath = base_path('resources/pdf-renderers/render-certificate.cjs');
        $result = Process::timeout(30)->run(['node', $scriptPath, $inputPath, $outputPath]);

        if (!$result->successful()) {
            throw new \RuntimeException('React-PDF certificate render failed: ' . $result->errorOutput());
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

    $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

    return response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $fileName . '"',
    ]);
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
