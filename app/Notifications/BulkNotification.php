<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BulkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $title;
    private $message;
    private $priority;

    public function __construct($title, $message, $priority = 'normal')
    {
        $this->title = $title;
        $this->message = $message;
        $this->priority = $priority;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'bulk_notification',
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.bulk-notification', [
                'user' => $notifiable,
                'title' => $this->title,
                'message' => $this->message,
                'priority' => $this->priority,
            ])
            ->subject($this->title);
    }
}
