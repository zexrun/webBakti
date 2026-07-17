<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskDeadlineReminder extends Notification
{
    use Queueable;

    public $task;
    public $daysUntilDeadline;

    public function __construct(Task $task, int $daysUntilDeadline)
    {
        $this->task = $task;
        $this->daysUntilDeadline = $daysUntilDeadline;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->daysUntilDeadline) {
            0 => '🚨 URGENT: Tugas berakhir HARI INI - ' . $this->task->title,
            default => '⏰ Reminder: Tugas berakhir dalam ' . $this->daysUntilDeadline . ' hari',
        };

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.task-deadline-reminder', [
                'task' => $this->task,
                'daysUntilDeadline' => $this->daysUntilDeadline,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date->toDateTimeString(),
            'days_until_deadline' => $this->daysUntilDeadline,
            'message' => $this->getNotificationMessage(),
            'action_url' => url('/student/tasks/' . $this->task->id),
            'type' => 'task_deadline_reminder',
            'priority' => $this->getRemindingPriority(),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function getNotificationMessage(): string
    {
        return match($this->daysUntilDeadline) {
            0 => 'Tugas "' . $this->task->title . '" BERAKHIR HARI INI!',
            1 => 'Tugas "' . $this->task->title . '" berakhir dalam 1 hari.',
            default => 'Tugas "' . $this->task->title . '" berakhir dalam ' . $this->daysUntilDeadline . ' hari.',
        };
    }

    private function getTimeRemaining(): string
    {
        return match($this->daysUntilDeadline) {
            0 => 'HARI INI (urgent!)',
            1 => '1 hari',
            default => $this->daysUntilDeadline . ' hari',
        };
    }

    private function getRemindingPriority(): string
    {
        return match($this->daysUntilDeadline) {
            0 => 'urgent',
            1 => 'high',
            default => 'medium',
        };
    }
}
