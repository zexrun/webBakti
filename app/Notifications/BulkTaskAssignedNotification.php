<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BulkTaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'task_assigned',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'supervisor_name' => $this->task->supervisor->user->name,
            'due_date' => $this->task->due_date->format('d M Y'),
            'message' => "Tugas baru: {$this->task->title} dari {$this->task->supervisor->user->name}",
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.bulk-task-assigned', [
                'user' => $notifiable,
                'task' => $this->task,
                'supervisor' => $this->task->supervisor->user,
            ])
            ->subject("Tugas Baru: {$this->task->title}");
    }
}
