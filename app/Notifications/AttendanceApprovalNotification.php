<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Attendance;

class AttendanceApprovalNotification extends Notification
{
    use Queueable;

    public $attendance;
    public $status;

    public function __construct(Attendance $attendance, string $status)
    {
        $this->attendance = $attendance;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        $checkInTime = $this->attendance->check_in_time ? \Carbon\Carbon::parse($this->attendance->check_in_time)->format('d M Y, H:i') : 'N/A';
        $attendanceUrl = url('/student/attendance/' . $this->attendance->id);

        $mail = (new MailMessage)
            ->subject('Attendance ' . $statusLabel . ' - ' . $checkInTime)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Status kehadiran Anda telah dikaji oleh admin.');

        if ($this->status === 'approved') {
            $mail->line('')
                ->line('✓ **Kehadiran Anda DISETUJUI**')
                ->line('');
        } else {
            $mail->line('')
                ->line('✗ **Kehadiran Anda DITOLAK**')
                ->line('')
                ->line('Alasan: ' . ($this->attendance->rejection_reason ?? 'Tidak ada alasan yang diberikan'));
        }

        $mail->line('**Waktu Check-in:** ' . $checkInTime)
            ->line('**Lokasi:** ' . ($this->attendance->location ?? 'Tidak tercatat'))
            ->line('**Status Verifikasi Lokasi:** ' . ($this->attendance->location_verification_status ?? 'Belum diverifikasi'))
            ->action('Lihat Detail', $attendanceUrl)
            ->line('Terima kasih!');

        if ($this->status === 'rejected') {
            $mail->line('')
                ->line('Jika Anda memiliki pertanyaan atau ingin mengajukan banding, silakan hubungi admin.')
                ->line('Email: magang.baktikomdigi@gmail.com');
        }

        return $mail->salutation('Tim Sistem Monitoring Magang');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'attendance_id' => $this->attendance->id,
            'status' => $this->status,
            'check_in_time' => $this->attendance->check_in_time,
            'location' => $this->attendance->location,
            'message' => 'Kehadiran Anda pada ' . ($this->attendance->check_in_time ? \Carbon\Carbon::parse($this->attendance->check_in_time)->format('d M Y H:i') : 'N/A') . ' telah ' . ($this->status === 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'action_url' => url('/student/attendance/' . $this->attendance->id),
            'type' => 'attendance_' . $this->status,
            'priority' => $this->status === 'rejected' ? 'high' : 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
