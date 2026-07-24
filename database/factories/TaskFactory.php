<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supervisor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    use HasFactory;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supervisor_id' => Supervisor::inRandomOrder()->first()->id,
            'title' => fake()->randomElement([
                'Laporan Mingguan Aktivitas',
                'Dokumentasi Modul Sistem',
                'Presentasi Progress Magang',
                'Analisis Kebutuhan Sistem',
                'Implementasi Fitur',
                'Laporan Akhir Magang',
            ]),
            'description' => fake()->paragraph(2),
            'file_path' => fake()->boolean() ? 'path/to/some/file.pdf' : null, // Contoh penambahan data file_path
            'type' => fake()->randomElement(['daily', 'final']),
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}