<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalAssessment>
 */
class FinalAssessmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'final_grade' => fake()->randomElement(['A', 'A-', 'B+', 'B', 'B-', 'C+']),
            'overall_comments' => fake()->paragraph(2),
            'certificate_generated_at' => null,
        ];
    }

    /**
     * A final assessment whose certificate has already been generated -
     * exercises the "already generated, only regenerate if grade
     * changed since" branch in FinalAssessmentController::generateCertificate().
     */
    public function withCertificate(): static
    {
        return $this->state(fn () => [
            'certificate_generated_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
