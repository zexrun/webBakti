<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AttendanceException;

class ExceptionApprovalNotification extends Notification
{
    use Queueable;

    public $exception;
    public $status;

    public function __construct(AttendanceException $exception, string $status)
    {
        $this->exception = $exception;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        $exceptionDate = $this->exception->date ? \Carbon\Carbon::parse($this->exception->date)->format('d M Y') : 'N/A';

        $mail = (new MailMessage)
            ->subject('Pengajuan Exception ' . $statusLabel . ' - ' . $exceptionDate)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pengajuan exception Anda telah dikaji oleh admin.')
            ->line('');

        if ($this->status === 'approved') {
            $mail->line('✓ **Pengajuan Anda DISETUJUI**')
                ->line('')
                ->line('Alasan sakit/izin Anda pada ' . $exceptionDate . ' telah disetujui.')
                ->line('');
        } else {
            $mail->line('✗ **Pengajuan Anda DITOLAK**')
                ->line('')
                ->line('Alasan penolakan: ' . ($this->exception->admin_notes ?? 'Tidak ada keterangan yang diberikan'))
                ->line('');
        }

        $mail->line('**Tanggal:** ' . $exceptionDate)
            ->line('**Tipe:** ' . ($this->exception->type ?? 'N/A'))
            ->line('**Alasan:** ' . ($this->exception->reason ?? 'N/A'));

        if ($this->status === 'rejected') {
            $mail->line('')
                ->line('Jika Anda ingin mengajukan banding atau memiliki pertanyaan, silakan hubungi admin.')
                ->line('Email: magang.baktikomdigi@gmail.com');
        }

        return $mail->salutation('Tim Sistem Monitoring Magang');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'exception_id' => $this->exception->id,
            'status' => $this->status,
            'date' => $this->exception->date,
            'type' => $this->exception->type,
            'reason' => $this->exception->reason,
            'message' => 'Pengajuan exception Anda pada ' . ($this->exception->date ? \Carbon\Carbon::parse($this->exception->date)->format('d M Y') : 'N/A') . ' telah ' . ($this->status === 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'action_url' => url('/student/exceptions/' . $this->exception->id),
            'type' => 'exception_' . $this->status,
            'priority' => $this->status === 'rejected' ? 'high' : 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
