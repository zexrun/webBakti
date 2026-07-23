<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * For every (task, student) pair created by TaskSeeder's pivot
 * attachment, splits into three buckets: no submission yet (~30%),
 * submitted but ungraded (~30%), and graded (~40%) - so the
 * Submissions/Tasks pages exercise every state instead of only
 * "everything graded".
 */
class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::with('students')->get();

        foreach ($tasks as $task) {
            foreach ($task->students as $student) {
                $roll = fake()->numberBetween(1, 100);

                if ($roll <= 30) {
                    // No submission at all - student hasn't submitted yet.
                    continue;
                }

                if ($roll <= 60) {
                    Submission::factory()->create([
                        'task_id' => $task->id,
                        'student_id' => $student->id,
                    ]);
                    continue;
                }

                Submission::factory()->graded()->create([
                    'task_id' => $task->id,
                    'student_id' => $student->id,
                ]);
            }
        }
    }
}
