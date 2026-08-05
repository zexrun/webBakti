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
 *
 * type and status are forced through a fixed rotation rather than left
 * to AttendanceExceptionFactory's random pick - with only 2-4 rows per
 * student, pure randomness risks an entire seed run never producing one
 * of the 4 types or the 'rejected'/'pending' statuses at all. The
 * rotation guarantees every student's first exception is a pending
 * 'sick', so the approvals page always has at least one fresh request
 * to demo per student.
 */
class AttendanceExceptionSeeder extends Seeder
{
    private const ROTATION = [
        ['type' => 'sick', 'status' => 'pending', 'reason' => 'Sakit demam, disertai surat keterangan dokter.'],
        ['type' => 'leave', 'status' => 'approved', 'reason' => 'Cuti keperluan keluarga.'],
        ['type' => 'permit', 'status' => 'rejected', 'reason' => 'Izin mengurus keperluan administrasi kampus.'],
        ['type' => 'official', 'status' => 'approved', 'reason' => 'Mengikuti kegiatan resmi kampus.'],
    ];

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
            $rotation = self::ROTATION[$i % count(self::ROTATION)];

            AttendanceException::factory()->create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
                'type' => $rotation['type'],
                'status' => $rotation['status'],
                'reason' => $rotation['reason'],
            ]);
        }
    }
}
