<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendAccountActivationEmail extends Notification
{
    use Queueable;
    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $activationUrl = route('activation.form', ['token' => $this->token]);

        return (new MailMessage)
                    ->subject('Aktivasi Akun Anda')
                    ->line('Anda telah didaftarkan ke Sistem Monitoring Magang. Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda.')
                    ->action('Aktivasi Akun', $activationUrl);
    }
}