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
        return (new MailMessage)
            ->subject('Submission Dinilai: ' . $this->submission->task->title)
            ->view('emails.submission-graded', [
                'submission' => $this->submission,
                'notifiable' => $notifiable,
            ]);
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
