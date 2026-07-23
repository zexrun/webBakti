<?php

namespace Database\Seeders;

use App\Models\AttendanceException;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * 2-4 exceptions per student over the same 90-day window Attendance
 * uses, on distinct dates so an exception day never collides with a
 * worked/absent Attendance row for the same student+date.
 */
class AttendanceExceptionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('user')->get();

        foreach ($students as $student) {
            if (!$student->user) {
                continue;
            }

            $this->seedForStudent($student->user->id);
        }
    }

    private function seedForStudent(int $userId): void
    {
        $count = fake()->numberBetween(2, 4);
        $usedDates = [];

        for ($i = 0; $i < $count; $i++) {
            $date = Carbon::now()->subDays(fake()->numberBetween(1, 89));

            while ($date->isWeekend() || in_array($date->toDateString(), $usedDates, true)) {
                $date = Carbon::now()->subDays(fake()->numberBetween(1, 89));
            }

            $usedDates[] = $date->toDateString();

            AttendanceException::factory()->create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
            ]);
        }
    }
}
