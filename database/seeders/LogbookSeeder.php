<?php

namespace Database\Seeders;

use App\Models\Logbook;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * One logbook entry per weekday over the last ~3 weeks per student - a
 * smaller, more recent window than Attendance's 90 days, since logbooks
 * are filled in more sporadically in practice than daily attendance.
 */
class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        foreach ($students as $student) {
            $day = Carbon::now()->subWeeks(3);
            $today = Carbon::today();

            while ($day->lte($today)) {
                if (!$day->isWeekend()) {
                    Logbook::factory()->create([
                        'student_id' => $student->id,
                        'activity_date' => $day->toDateString(),
                    ]);
                }

                $day->addDay();
            }
        }
    }
}
