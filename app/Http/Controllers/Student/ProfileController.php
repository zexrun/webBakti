<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $student = Auth::user()->student;
        $student->load('supervisor.user', 'finalAssessment');

        $universities = University::orderBy('name')
                                    ->pluck('name')
                                    ->toArray();

        $hasProposal = $student->documents()->where('type', 'proposal')->exists();
        $hasLaporanAkhir = $student->documents()->where('type', 'laporan_akhir')->exists();
        $hasAssessment = $student->finalAssessment && $student->finalAssessment->final_grade !== null;
        $hasCertificate = $student->finalAssessment && $student->finalAssessment->certificate_generated_at;

        if ($hasCertificate) {
            $progress = 100;
            $progressText = 'Selesai - Sertifikat tersedia';
        } elseif ($hasAssessment) {
            $progress = 75;
            $progressText = 'Sudah dinilai - Menunggu sertifikat';
        } elseif ($hasLaporanAkhir) {
            $progress = 50;
            $progressText = 'Laporan akhir sudah dikumpulkan';
        } elseif ($hasProposal) {
            $progress = 25;
            $progressText = 'Proposal sudah dikumpulkan';
        } else {
            $progress = 0;
            $progressText = 'Belum mengumpulkan proposal';
        }

        return Inertia::render('Student/Info/Edit', [
            'student' => $student,
            'universities' => $universities,
            'certificateProgress' => [
                'percent' => $progress,
                'text' => $progressText,
                'hasProposal' => $hasProposal,
                'hasLaporanAkhir' => $hasLaporanAkhir,
                'hasAssessment' => $hasAssessment,
                'hasCertificate' => (bool) $hasCertificate,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:25',
            'universitas' => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'semester' => 'required|integer|min:1|max:14',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
        ]);

        $student = Auth::user()->student;

        $student->update([
            'nim' => $request->nim,
            'universitas' => $request->universitas,
            'program_studi' => $request->program_studi,
            'semester' => $request->semester,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
        ]);

        return redirect()->route('student.info.edit')->with('success', "Data status mahasiswa berhasil diperbarui");
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
            'face_descriptor' => 'required|string',
        ]);

        $descriptor = json_decode($request->input('face_descriptor'), true);

        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return back()->with('error', 'Wajah tidak terdeteksi pada foto. Silakan coba lagi.');
        }

        $student = Auth::user()->student;

        if ($student->profile_photo && Storage::disk('public')->exists($student->profile_photo)) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        $photoPath = $request->file('photo')->store('students/profile', 'public');

        $student->update([
            'profile_photo' => $photoPath,
            'face_descriptor' => $descriptor,
        ]);

        return redirect()->route('student.info.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function generateCertificate()
    {
        // Ambil data student dari user yang login
        $student = Auth::user()->student;

        if (!$student) {
        // Tangani error, misalnya kembali dengan pesan
        return back()->with('error', 'Detail mahasiswa tidak ditemukan.');
        }
        
        // Muat relasi-relasi yang dibutuhkan
        $student->load('user', 'finalAssessment.supervisor.user');

        // Otorisasi
        if (!$student->finalAssessment || !$student->finalAssessment->certificate_generated_at) {
            abort(403, 'Sertifikat belum tersedia.');
        }

        // Siapkan data untuk PDF
        $data = [
            'student' => $student,
            'assessment' => $student->finalAssessment,
            'supervisorName' => $student->finalAssessment->supervisor->user->name,
        ];

        // Render dan download PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('supervisor.pdf.certificate-pdf', $data);
        return $pdf->download('sertifikat-magang-' . $student->user->name . '.pdf');
    }
}
