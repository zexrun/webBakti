<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\Logbook;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AttendanceApprovalNotification;
use App\Notifications\ExceptionApprovalNotification;
use App\Notifications\LogbookFeedbackGiven;
use App\Notifications\SubmissionGraded;
use App\Notifications\TaskDeadlineReminder;
use Illuminate\Console\Command;

class PreviewEmailTemplates extends Command
{
    protected $signature = 'email:preview {--to=admin@webbakti.local : Email recipient}';

    protected $description = 'Send every email notification template to the given address (e.g. Mailtrap inbox) using seeded data';

    public function handle(): int
    {
        $to = $this->option('to');

        $notifiable = User::where('role', 'student')->first();

        if (! $notifiable) {
            $this->error('No student user found to use as notifiable.');

            return Command::FAILURE;
        }

        $notifiable->email = $to;

        $jobs = [
            'submission-graded' => fn () => Submission::whereHas('task.supervisor.user')->whereNotNull('grade')->first()
                ? new SubmissionGraded(Submission::whereHas('task.supervisor.user')->whereNotNull('grade')->first())
                : null,
            'task-deadline-reminder' => fn () => Task::whereHas('supervisor.user')->first()
                ? new TaskDeadlineReminder(Task::whereHas('supervisor.user')->first(), 1)
                : null,
            'attendance-approval' => fn () => Attendance::whereNotNull('check_in')->first()
                ? new AttendanceApprovalNotification(Attendance::whereNotNull('check_in')->first(), 'approved')
                : null,
            'exception-approval' => fn () => AttendanceException::first()
                ? new ExceptionApprovalNotification(AttendanceException::first(), 'rejected')
                : null,
            'logbook-feedback' => fn () => Logbook::whereHas('student.supervisor.user')->first()
                ? new LogbookFeedbackGiven(Logbook::whereHas('student.supervisor.user')->first())
                : null,
        ];

        foreach ($jobs as $name => $factory) {
            $notification = $factory();

            if (! $notification) {
                $this->warn("Skipped {$name}: no seed data available");
                continue;
            }

            $notifiable->notify($notification);
            $this->info("Sent {$name} to {$to}");
            sleep(3);
        }

        return Command::SUCCESS;
    }
}
