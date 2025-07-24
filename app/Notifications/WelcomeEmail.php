<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User; // Import model User

class WelcomeEmail extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Buat instance notifikasi baru.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Hanya kirim via email
    }

    /**
     * Buat representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Selamat Datang di Sistem Monitoring Magang!')
                    ->greeting('Halo, ' . $this->user->name . '!')
                    ->line('Akun Anda telah berhasil diaktifkan. Selamat bergabung dengan platform kami.')
                    ->action('Mulai Jelajahi', url('/home'))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }
}