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
 *
 * Entries from more than a week ago get supervisor feedback filled in;
 * the most recent week is left without feedback, so the demo always has
 * both "sudah diberi feedback" and "belum diberi feedback" entries -
 * LogbookFactory never sets feedback/feedback_at at all, so without this
 * every seeded logbook would be missing feedback entirely.
 */
class LogbookSeeder extends Seeder
{
    private const FEEDBACK_EXAMPLES = [
        'Progress bagus, lanjutkan dengan dokumentasi yang lebih detail.',
        'Sudah sesuai target minggu ini, pertahankan konsistensinya.',
        'Coba tambahkan lebih banyak detail teknis pada laporan berikutnya.',
        'Kerja bagus, koordinasi dengan tim sudah baik.',
    ];

    public function run(): void
    {
        $students = Student::all();

        foreach ($students as $student) {
            $day = Carbon::now()->subWeeks(3);
            $today = Carbon::today();
            $feedbackCutoff = Carbon::now()->subWeek();

            while ($day->lte($today)) {
                if (!$day->isWeekend()) {
                    $hasFeedback = $day->lte($feedbackCutoff);

                    Logbook::factory()->create([
                        'student_id' => $student->id,
                        'activity_date' => $day->toDateString(),
                        'feedback' => $hasFeedback ? fake()->randomElement(self::FEEDBACK_EXAMPLES) : null,
                        'feedback_at' => $hasFeedback ? $day->copy()->addDay()->setTime(9, 0) : null,
                    ]);
                }

                $day->addDay();
            }
        }
    }
}
