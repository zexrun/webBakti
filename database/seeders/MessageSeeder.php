<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * A handful of messages in both directions (student->supervisor and
 * supervisor->student) per student-supervisor pair, with a mixed
 * read/unread state.
 */
class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with(['user', 'supervisor.user'])->get();

        foreach ($students as $student) {
            if (!$student->user || !$student->supervisor || !$student->supervisor->user) {
                continue;
            }

            $studentUserId = $student->user->id;
            $supervisorUserId = $student->supervisor->user->id;

            $count = fake()->numberBetween(2, 4);

            for ($i = 0; $i < $count; $i++) {
                $studentToSupervisor = fake()->boolean();

                Message::factory()->create([
                    'sender_id' => $studentToSupervisor ? $studentUserId : $supervisorUserId,
                    'recipient_id' => $studentToSupervisor ? $supervisorUserId : $studentUserId,
                ]);
            }
        }
    }
}
