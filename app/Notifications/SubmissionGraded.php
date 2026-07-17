<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Submission;

class SubmissionGraded extends Notification
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
        $supervisorName = $this->submission->task->supervisor->user->name;
        $taskUrl = url('/student/tasks/' . $this->submission->task->id);

        return (new MailMessage)
            ->subject('Tugas Dinilai: ' . $this->submission->task->title)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pembimbing **' . $supervisorName . '** telah memberikan nilai untuk tugas Anda.')
            ->line('')
            ->line('**Tugas:** ' . $this->submission->task->title)
            ->line('**Nilai:** ' . strtoupper($this->submission->grade))
            ->line('')
            ->if($this->submission->comments, function ($mail) {
                $mail->line('**Komentar Pembimbing:**')
                    ->line($this->submission->comments);
            })
            ->action('Lihat Detail', $taskUrl)
            ->line('Terus tingkatkan kualitas pekerjaan Anda!')
            ->salutation('Tim Sistem Monitoring Magang');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task->id,
            'task_title' => $this->submission->task->title,
            'grade' => $this->submission->grade,
            'comments' => $this->submission->comments,
            'supervisor_name' => $this->submission->task->supervisor->user->name,
            'message' => 'Tugas "' . $this->submission->task->title . '" telah dinilai dengan nilai ' . $this->submission->grade,
            'action_url' => url('/student/tasks/' . $this->submission->task->id),
            'type' => 'submission_graded',
            'priority' => 'high',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
