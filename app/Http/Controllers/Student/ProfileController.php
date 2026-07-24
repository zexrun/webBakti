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
            'university' => 'required|string|max:100',
            'study_program' => 'required|string|max:100',
            'semester' => 'required|integer|min:1|max:14',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $student = Auth::user()->student;

        $student->update([
            'nim' => $request->nim,
            'university' => $request->university,
            'study_program' => $request->study_program,
            'semester' => $request->semester,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
        ]);

        return redirect()->route('student.info.edit')->with('success', "Data status mahasiswa berhasil diperbarui");
    }
}
