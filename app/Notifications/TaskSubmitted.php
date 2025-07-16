<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Submission;

class TaskSubmitted extends Notification
{
    use Queueable;
    public $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->submission->student->user->name;
        $taskTitle = $this->submission->task->title;

        return(new MailMessage)
            ->subject("Submission baru untuk Tugas: {$taskTitle}")
            ->line("Mahasiswa {$studentName} telah mengumpulkan tugas.")
            ->action('Lihat Submission', route('supervisor.tasks.show', $this->submission->task_id));
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->submission->student->user->name;
        return [
            'message' => "{$studentName} telah mengumpulkan tugas.",
            'url' => route('supervisor.tasks.show', $this->submission->task_id),
        ];
    }
}
