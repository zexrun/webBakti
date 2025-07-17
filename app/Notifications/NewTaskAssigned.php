<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class NewTaskAssigned extends Notification
{
    use Queueable;

    public $task;
    
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembimbing ' . $this->task->supervisor->user->name . ' memberikan anda tugas baru')
            ->line('Anda memiliki tugas baru: ' . $this->task->title)
            ->line($this->task->description)
            ->action('Lihat Tugas', url('/student/tasks' . $this->task->id))
            ->line('Selamat mengerjakan tugas anda!');
    }
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message'=> 'Anda mendapatkan tugas baru dari pembimbing ' . $this->task->supervisor->user->name,
        ];
    }
}
