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
 */
class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = Supervisor::with('students')->get();

        foreach ($supervisors as $supervisor) {
            $studentIds = $supervisor->students->pluck('id');

            if ($studentIds->isEmpty()) {
                continue;
            }

            $taskCount = fake()->numberBetween(3, 4);

            Task::factory()
                ->count($taskCount)
                ->create(['supervisor_id' => $supervisor->id])
                ->each(function (Task $task) use ($studentIds) {
                    $task->students()->attach($studentIds);
                });
        }
    }
}
