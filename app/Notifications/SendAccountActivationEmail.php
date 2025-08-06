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
            ->subject('Aktivasi Akun - Sistem Monitoring Magang')
            ->greeting('Halo, **' . $notifiable->name . '**!')
            ->line('---')
            ->line('## Selamat! Akun Anda Telah Terdaftar')
            ->line('')
            ->line('Anda telah berhasil didaftarkan ke **Sistem Monitoring Magang**. Untuk melanjutkan dan mengakses semua fitur platform, Anda perlu mengaktifkan akun terlebih dahulu.')
            ->line('')
            ->line('### Informasi Akun Anda:')
            ->line('**Nama:** ' . $notifiable->name)
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Status:** Menunggu Aktivasi')
            ->line('')
            ->line('### Langkah Aktivasi:')
            ->line('1. **Klik tombol aktivasi** di bawah ini')
            ->line('2. **Isi data diri** untuk akun Anda')
            ->line('3. **Buat password** untuk keamanan akun Anda')
            ->line('4. **Login** dan mulai menggunakan platform')
            ->line('')
            ->action('Aktivasi Akun Sekarang', $activationUrl)
            ->line('')
            ->line('### Mengapa Perlu Aktivasi?')
            ->line('Proses aktivasi memastikan:')
            ->line('• **Keamanan akun** Anda terjaga')
            ->line('• **Email valid** dan dapat menerima notifikasi')
            ->line('• **Akses terkontrol** ke sistem monitoring')
            ->line('')
            ->line('### Setelah Aktivasi, Anda Dapat:')
            ->line($this->getFeaturesByRole($notifiable))
            ->line('')
            ->line('---')
            ->line('**Penting:** Link aktivasi hanya **sekali pakai** untuk keamanan. Jika link sudah tidak berlaku, silakan hubungi administrator untuk mendapatkan link baru.')
            ->line('')
            ->line('**Butuh Bantuan?**')
            ->line('Jika Anda mengalami kesulitan dalam proses aktivasi, jangan ragu untuk menghubungi tim support kami di **baktimagang@gmail.com** atau hubungi administrator sistem.')
            ->line('')
            ->line('Terima kasih telah bergabung dengan Sistem Monitoring Magang!')
            ->salutation('**Tim Sistem Monitoring Magang**');
    }

    private function getFeaturesByRole($user): string
    {
        $role = $user->role ?? 'user';

        switch ($role) {
            case 'student':
                return "**Sebagai Mahasiswa:**\n" .
                       "• Mengakses dan mengerjakan tugas magang\n" .
                       "• Membuat dan mengelola logbook harian\n" .
                       "• Memantau progress dan penilaian\n" .
                       "• Upload dokumen dan laporan magang";
                       
            case 'supervisor':
                return "**Sebagai Supervisor:**\n" .
                       "• Mengelola mahasiswa bimbingan\n" .
                       "• Memberikan tugas dan feedback\n" .
                       "• Memantau progress mahasiswa\n" .
                       "• Melakukan penilaian akhir\n" ;
                       
            case 'admin':
                return "**Sebagai Administrator:**\n" .
                       "• Mengelola seluruh pengguna sistem\n" .
                       "• Mengakses dashboard analytics\n" .
                       "• Konfigurasi sistem dan pengaturan\n" .
                       "• Mengelola dokumen dan template\n" .
                       "• Monitoring aktivitas sistem";
                       
            default:
                return "**Fitur Platform:**\n" .
                       "• Mengelola profil dan pengaturan akun\n" .
                       "• Mengakses dashboard personal\n" .
                       "• Berkomunikasi melalui sistem\n" .
                       "• Memantau aktivitas dan notifikasi";
        }
    }
}