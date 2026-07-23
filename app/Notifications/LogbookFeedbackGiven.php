<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Logbook;

class LogbookFeedbackGiven extends Notification
{
    use Queueable;

    public $logbook;

    public function __construct(Logbook $logbook)
    {
        $this->logbook = $logbook;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Feedback Baru untuk Logbook: ' . $this->logbook->title)
            ->view('emails.logbook-feedback', [
                'logbook' => $this->logbook,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'logbook_id' => $this->logbook->id,
            'logbook_title' => $this->logbook->title,
            'activity_date' => $this->logbook->activity_date,
            'feedback' => $this->logbook->feedback,
            'supervisor_name' => $this->logbook->student->supervisor->user->name,
            'message' => 'Pembimbing memberikan feedback pada logbook "' . $this->logbook->title . '"',
            'action_url' => url('/student/logbooks/' . $this->logbook->id),
            'type' => 'logbook_feedback',
            'priority' => 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
