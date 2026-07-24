<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('##########'),
            'university' => 'Telkom University',
            'study_program' => 'S1 Informatika',
            'semester' => '6',
        ];
    }
}
