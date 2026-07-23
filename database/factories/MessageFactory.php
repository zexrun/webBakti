<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        $isRead = fake()->boolean(60);

        return [
            'subject' => fake()->randomElement([
                'Progress Minggu Ini',
                'Pertanyaan Terkait Tugas',
                'Konfirmasi Jadwal Bimbingan',
                'Update Laporan Magang',
            ]),
            'body' => fake()->paragraph(3),
            'is_read' => $isRead,
            'read_at' => $isRead ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
