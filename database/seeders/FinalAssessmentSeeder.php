<?php

namespace Database\Seeders;

use App\Models\FinalAssessment;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Splits students into three assessment states: not started (~30%),
 * graded but certificate not yet generated (~40%), and fully graded
 * with certificate already generated (~30%) - so both the Supervisor
 * assessment pages and the certificate-generation flow have real
 * examples of every stage.
 */
class FinalAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::whereNotNull('supervisor_id')->get();

        foreach ($students as $index => $student) {
            $roll = fake()->numberBetween(1, 100);

            if ($roll <= 30) {
                // Not started yet - no FinalAssessment row.
                continue;
            }

            $factory = FinalAssessment::factory();

            if ($roll > 70) {
                $factory = $factory->withCertificate();
            }

            $factory->create([
                'student_id' => $student->id,
                'supervisor_id' => $student->supervisor_id,
            ]);
        }
    }
}
