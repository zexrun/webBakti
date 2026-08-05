<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supervisor;
use App\Models\Task;

/**
 * Creates 3-4 tasks per supervisor (mixed harian/akhir) and attaches
 * every one of that supervisor's students to each task via the
 * task_student pivot - TaskFactory alone never does this, so without
 * this step every Task would have zero assigned students and
 * SubmissionSeeder would have nothing to iterate.
 *
 * due_date is forced through a fixed rotation (overdue / due soon /
 * due later) instead of TaskFactory's random future-only range, so
 * every supervisor has at least one overdue task to demo "Terlambat"
 * status on - a plain random range could otherwise land every task in
 * the future across an entire run.
 */
class TaskSeeder extends Seeder
{
    private const DUE_DATE_ROTATION = [
        '-1 week',   // overdue
        '+2 days',   // due soon
        '+3 weeks',  // due later
        '+6 weeks',  // due later still
    ];

    public function run(): void
    {
        $supervisors = Supervisor::with('students')->get();

        foreach ($supervisors as $supervisor) {
            $studentIds = $supervisor->students->pluck('id');

            if ($studentIds->isEmpty()) {
                continue;
            }

            $taskCount = fake()->numberBetween(3, 4);

            for ($i = 0; $i < $taskCount; $i++) {
                $dueDate = now()->modify(self::DUE_DATE_ROTATION[$i % count(self::DUE_DATE_ROTATION)]);

                $task = Task::factory()->create([
                    'supervisor_id' => $supervisor->id,
                    'due_date' => $dueDate,
                ]);

                $task->students()->attach($studentIds);
            }
        }
    }
}
