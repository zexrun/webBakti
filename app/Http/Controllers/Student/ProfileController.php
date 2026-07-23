<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
}
