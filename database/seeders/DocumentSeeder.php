<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Every student gets a proposal document. Only students who reached
 * the graded/certificate stage in FinalAssessmentSeeder also get a
 * laporan_akhir document, keeping the two features' data consistent
 * with Student/Info/Edit.jsx's progress tracker (which checks for both
 * a laporan_akhir document AND a graded assessment before showing
 * "selesai").
 */
class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('finalAssessment')->get();

        foreach ($students as $student) {
            Document::factory()->proposal()->create(['student_id' => $student->id]);

            if ($student->finalAssessment && $student->finalAssessment->final_grade) {
                Document::factory()->laporanAkhir()->create(['student_id' => $student->id]);
            }

            if (fake()->boolean(30)) {
                Document::factory()->create(['student_id' => $student->id]);
            }
        }
    }
}
