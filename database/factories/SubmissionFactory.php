<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(3),
            'file_path' => fake()->boolean(70) ? 'path/to/submission-file.pdf' : null,
            'grade' => null,
            'comments' => null,
        ];
    }

    /**
     * A submission that has been graded, with a mix of numeric scores
     * and letter grades matching the classification already used by
     * the grades PDF template (A/B/C/D bands).
     */
    public function graded(): static
    {
        return $this->state(function () {
            $grade = fake()->randomElement([
                fake()->numberBetween(85, 100),
                fake()->numberBetween(75, 84),
                fake()->numberBetween(65, 74),
                fake()->numberBetween(40, 64),
                'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C',
            ]);

            return [
                'grade' => (string) $grade,
                'comments' => fake()->randomElement([
                    'Hasil kerja sangat baik dan sesuai target.',
                    'Perlu perbaikan pada beberapa bagian, namun cukup baik.',
                    'Dikerjakan dengan baik, pertahankan konsistensi.',
                    'Analisis kurang mendalam, harap ditingkatkan.',
                ]),
            ];
        });
    }
}
