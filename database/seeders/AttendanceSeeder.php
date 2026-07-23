<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Generates ~90 calendar days (weekdays only) of attendance history per
 * student, with a realistic status mix and a deliberate slice of
 * "suspicious" rows (face mismatch / flagged location) so the
 * Suspicious review pages and the Monitoring page's "Mencurigakan"
 * counter have real data to display.
 */
class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $settings = AttendanceSetting::getSettings();
        $students = Student::with('user')->get();

        foreach ($students as $student) {
            if (!$student->user) {
                continue;
            }

            $this->seedForUser($student->user->id, $settings);
        }
    }

    private function seedForUser(int $userId, AttendanceSetting $settings): void
    {
        $day = Carbon::now()->subDays(90);
        $today = Carbon::today();

        while ($day->lte($today)) {
            if ($day->isWeekend()) {
                $day->addDay();
                continue;
            }

            $roll = fake()->numberBetween(1, 100);

            // ~8% absent, ~15% late, rest present.
            if ($roll <= 8) {
                $this->createAbsent($userId, $day->copy());
            } elseif ($roll <= 23) {
                $this->createWorkedDay($userId, $day->copy(), $settings, late: true);
            } else {
                $this->createWorkedDay($userId, $day->copy(), $settings, late: false);
            }

            $day->addDay();
        }
    }

    private function createAbsent(int $userId, Carbon $date): void
    {
        Attendance::factory()->create([
            'user_id' => $userId,
            'date' => $date->toDateString(),
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'location_verification_status' => 'unverified',
            'face_verification_status' => null,
            'face_match_distance' => null,
        ]);
    }

    private function createWorkedDay(int $userId, Carbon $date, AttendanceSetting $settings, bool $late): void
    {
        [$startHour, $startMinute] = explode(':', $settings->work_start_time);
        $checkIn = $date->copy()->setTime((int) $startHour, (int) $startMinute)
            ->addMinutes($late ? fake()->numberBetween($settings->late_tolerance_minutes + 5, 90) : fake()->numberBetween(-10, 5));
        $checkOut = $date->copy()->setTime(17, fake()->numberBetween(0, 30));

        // ~7% of worked days are deliberately flagged as suspicious.
        $suspicious = fake()->numberBetween(1, 100) <= 7;
        $suspiciousReason = $suspicious ? fake()->randomElement(['face', 'location']) : null;

        Attendance::factory()->create(array_merge([
            'user_id' => $userId,
            'date' => $date->toDateString(),
            'status' => $late ? 'late' : 'present',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'requires_manual_review' => $suspicious,
        ], $this->suspiciousAttributes($suspiciousReason)));
    }

    private function suspiciousAttributes(?string $reason): array
    {
        if ($reason === 'face') {
            return [
                'face_verification_status' => 'mismatch',
                'face_match_distance' => fake()->randomFloat(4, 0.65, 1.2),
            ];
        }

        if ($reason === 'location') {
            return [
                'location_verification_status' => 'flagged',
                'location_spoofing_score' => fake()->numberBetween(60, 95),
                'location_notes' => 'Lokasi terindikasi di luar radius kantor saat presensi.',
            ];
        }

        return [];
    }
}
