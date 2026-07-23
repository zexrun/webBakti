<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Logbook>
 */
class LogbookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Rapat Koordinasi Tim',
                'Pengembangan Fitur',
                'Testing dan Debugging',
                'Dokumentasi Progress',
                'Diskusi dengan Pembimbing',
            ]),
            'start_time' => '08:30:00',
            'end_time' => '16:30:00',
            'description' => fake()->paragraph(2),
            'feeling' => fake()->randomElement(['Semangat', 'Produktif', 'Biasa saja', 'Sedikit lelah']),
            'file_path' => null,
            'is_verified' => fake()->boolean(50),
        ];
    }
}
