<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceException>
 */
class AttendanceExceptionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['sick', 'leave', 'permit', 'official']);

        $reasons = [
            'sick' => 'Sakit demam, disertai surat keterangan dokter.',
            'leave' => 'Cuti keperluan keluarga.',
            'permit' => 'Izin mengurus keperluan administrasi kampus.',
            'official' => 'Mengikuti kegiatan resmi kampus.',
        ];

        return [
            'type' => $type,
            'reason' => $reasons[$type],
            'status' => fake()->randomElement(['pending', 'approved', 'approved', 'rejected']),
        ];
    }
}
