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
        $subject = $this->status === 'approved'
            ? '✓ Kehadiran Disetujui'
            : '✗ Kehadiran Ditolak';

        return (new MailMessage)
            ->subject($subject . ' - ' . ($this->attendance->check_in ? $this->attendance->check_in->format('d M Y') : 'N/A'))
            ->view('emails.attendance-approval', [
                'attendance' => $this->attendance,
                'status' => $this->status,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'attendance_id' => $this->attendance->id,
            'status' => $this->status,
            'check_in' => $this->attendance->check_in,
            'message' => 'Kehadiran Anda pada ' . ($this->attendance->check_in ? $this->attendance->check_in->format('d M Y H:i') : 'N/A') . ' telah ' . ($this->status === 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'action_url' => url('/student/attendance/' . $this->attendance->id),
            'type' => 'attendance_' . $this->status,
            'priority' => $this->status === 'rejected' ? 'high' : 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
