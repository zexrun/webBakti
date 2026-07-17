<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Student;
use App\Notifications\TaskDeadlineReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaskDeadlineReminders extends Command
{
    protected $signature = 'tasks:send-deadline-reminders';
    protected $description = 'Send deadline reminder notifications for tasks ending in 7 days, 1 day, or today';

    public function handle()
    {
        $this->info('Starting task deadline reminders...');

        $now = Carbon::now();
        $in7Days = $now->copy()->addDays(7)->endOfDay();
        $tomorrow = $now->copy()->addDay()->endOfDay();
        $today = $now->copy()->endOfDay();

        // Tasks ending today
        $tasksEndingToday = Task::whereBetween('due_date', [$now->copy()->startOfDay(), $today])->get();
        foreach ($tasksEndingToday as $task) {
            $this->sendReminderToStudents($task, 0);
        }

        // Tasks ending tomorrow
        $tasksEndingTomorrow = Task::whereBetween('due_date', [$tomorrow->copy()->subDay()->endOfDay(), $tomorrow])->get();
        foreach ($tasksEndingTomorrow as $task) {
            $this->sendReminderToStudents($task, 1);
        }

        // Tasks ending in 7 days
        $tasksEnding7Days = Task::whereBetween('due_date', [$in7Days->copy()->subDay()->endOfDay(), $in7Days])->get();
        foreach ($tasksEnding7Days as $task) {
            $this->sendReminderToStudents($task, 7);
        }

        $this->info('Task deadline reminders sent successfully!');
    }

    private function sendReminderToStudents(Task $task, int $daysUntilDeadline)
    {
        $students = $task->students()->get();

        foreach ($students as $student) {
            $submission = $student->submissions()->where('task_id', $task->id)->first();

            if (!$submission || !$submission->grade) {
                $student->user->notify(new TaskDeadlineReminder($task, $daysUntilDeadline));
                $this->line("Reminder sent to {$student->user->name} for task: {$task->title}");
            }
        }
    }
}
