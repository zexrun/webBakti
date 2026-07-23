<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Jadwal Libur Nasional',
                'Pembaruan Kebijakan Presensi',
                'Batas Waktu Pengumpulan Laporan Akhir',
                'Pemeliharaan Sistem Terjadwal',
                'Sosialisasi Program Magang Batch Baru',
            ]),
            'content' => fake()->paragraph(4),
            'priority' => fake()->randomElement(['low', 'normal', 'normal', 'high', 'urgent']),
            'target_roles' => ['student', 'supervisor'],
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }
}
