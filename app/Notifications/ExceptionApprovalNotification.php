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
        $subject = $this->status === 'approved'
            ? '✓ Exception Disetujui'
            : '✗ Exception Ditolak';

        return (new MailMessage)
            ->subject($subject . ' - ' . ($this->exception->date ? \Carbon\Carbon::parse($this->exception->date)->format('d M Y') : 'N/A'))
            ->view('emails.exception-approval', [
                'exception' => $this->exception,
                'status' => $this->status,
                'notifiable' => $notifiable,
            ]);
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
