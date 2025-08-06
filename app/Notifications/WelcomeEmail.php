<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

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
            ->subject(' Selamat Datang di Sistem Monitoring Magang!')
            ->greeting('Halo, **' . $this->user->name . '**! 👋')
            ->line('---')
            ->line('##  Akun Anda Telah Berhasil Diaktifkan!')
            ->line('')
            ->line('Selamat bergabung dengan **Sistem Monitoring Magang**! Kami sangat senang Anda menjadi bagian dari platform kami.')
            ->line('')
            ->line('###  Informasi Akun Anda:')
            ->line('** Email:** ' . $this->user->email)
            ->line('** Nama:** ' . $this->user->name)
            ->line('** Role:** ' . $this->getUserRole())
            ->line('')
            ->line('###  Apa yang Bisa Anda Lakukan:')
            ->line($this->getFeaturesByRole())
            ->line('')
            ->action(' Mulai Jelajahi Sekarang', url('/dashboard'))
            ->line('')
            ->line('### 💡 Tips untuk Memulai:')
            ->line('1. **Login** ke akun Anda')
            ->line('2. **Lengkapi profil** Anda di dashboard')
            ->line('3. **Jelajahi fitur-fitur** yang tersedia')
            ->line('4. **Hubungi support** jika butuh bantuan')
            ->line('')
            ->line('---')
            ->line('** Butuh Bantuan?**')
            ->line('Jika Anda mengalami kesulitan, jangan ragu menghubungi tim support kami di **baktimagang@gmail.com**')
            ->line('')
            ->line('Terima kasih telah bergabung dengan kami! 🙏')
            ->salutation('**Magang BAKTI**');
    }

    /**
     * Dapatkan role user dalam format yang readable
     */
    private function getUserRole(): string
    {
        // Sesuaikan dengan sistem role Anda
        if (method_exists($this->user, 'hasRole')) {
            if ($this->user->hasRole('student')) {
                return 'Mahasiswa 🎓';
            } elseif ($this->user->hasRole('supervisor')) {
                return 'Supervisor 👨‍🏫';
            } elseif ($this->user->hasRole('admin')) {
                return 'Administrator ⚙️';
            }
        }
        
        // Fallback jika tidak ada role system
        return 'Pengguna 👤';
    }

    /**
     * Dapatkan daftar fitur berdasarkan role user
     */
    private function getFeaturesByRole(): string
    {
        if (method_exists($this->user, 'hasRole')) {
            if ($this->user->hasRole('student')) {
                return "**📚 Sebagai Mahasiswa, Anda dapat:**\n" .
                       "• ✅ Mengakses dan mengerjakan tugas magang\n" .
                       "• 📝 Membuat dan mengelola logbook harian\n" .
                       "• 📊 Memantau progress magang Anda\n" .
                       "• 📄 Upload dokumen dan laporan";
            } elseif ($this->user->hasRole('supervisor')) {
                return "**👨‍🏫 Sebagai Supervisor, Anda dapat:**\n" .
                       "• 👥 Mengelola mahasiswa bimbingan\n" .
                       "• ✅ Review dan memberikan feedback tugas\n" .
                       "• 📊 Memantau progress mahasiswa\n" .
                       "• 📝 Membuat penilaian akhir\n";
            } elseif ($this->user->hasRole('admin')) {
                return "**⚙️ Sebagai Administrator, Anda dapat:**\n" .
                       "• 👥 Mengelola seluruh pengguna sistem\n" .
                       "• 📊 Mengakses dashboard analytics lengkap\n" .
                       "• ⚙️ Melakukan konfigurasi sistem\n" .
                       "• 📄 Mengelola dokumen\n";
            }
        }
        
        // Fallback untuk user tanpa role spesifik
        return "**🎯 Anda dapat:**\n" .
               "• 🔍 Menjelajahi fitur-fitur platform\n" .
               "• 📝 Mengelola profil Anda\n" .
               "• 💬 Berkomunikasi melalui sistem\n" .
               "• 📊 Memantau aktivitas Anda";
    }
}