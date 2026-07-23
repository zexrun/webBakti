<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Base state: a normal, on-time present day. AttendanceSeeder
     * overrides date/user_id/status/times per row - this definition
     * exists mainly so ->state([...]) calls have sensible defaults for
     * fields the seeder doesn't explicitly set on a given row.
     */
    public function definition(): array
    {
        return [
            'status' => 'present',
            'supervisor_approval' => 'approved',
            'location_verification_status' => 'verified',
            'location_spoofing_score' => 0,
            'requires_manual_review' => false,
            'photo_exif_status' => 'valid',
            'face_verification_status' => 'verified',
            'face_match_distance' => fake()->randomFloat(4, 0.1, 0.5),
        ];
    }
}
