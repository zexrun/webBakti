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
        $taskUrl = url('/student/tasks/' . $this->task->id);
        $dueDate = $this->task->due_date->format('d M Y, H:i');

        $subject = match($this->daysUntilDeadline) {
            0 => 'URGENT: Tugas berakhir HARI INI - ' . $this->task->title,
            default => 'Reminder: Tugas berakhir dalam ' . $this->daysUntilDeadline . ' hari - ' . $this->task->title,
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ini adalah pengingat bahwa tugas Anda akan segera berakhir.')
            ->line('')
            ->line('**Tugas:** ' . $this->task->title)
            ->line('**Batas Waktu:** ' . $dueDate)
            ->line('**Waktu Tersisa:** ' . $this->getTimeRemaining())
            ->line('')
            ->line('Pastikan Anda menyelesaikan dan mengumpulkan tugas sebelum batas waktu!')
            ->action('Lihat Tugas Sekarang', $taskUrl)
            ->line('Jangan sampai ketinggalan!')
            ->salutation('Tim Sistem Monitoring Magang');
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
