<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class NewTaskAssigned extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $supervisorName = $this->task->supervisor->user->name;
        $taskUrl = url('/student/tasks/' . $this->task->id);
        $dueDate = $this->task->due_date ? $this->task->due_date->format('d M Y, H:i') : 'Belum ditentukan';
        
        return (new MailMessage)
            ->subject('Tugas Baru dari ' . $supervisorName . ' - ' . $this->task->title)
            ->greeting('Halo, **' . $notifiable->name . '**!')
            ->line('---')
            ->line('## Anda Mendapat Tugas Baru!')
            ->line('')
            ->line('Pembimbing **' . $supervisorName . '** telah memberikan Anda tugas baru yang perlu dikerjakan.')
            ->line('')
            ->line('### Detail Tugas:')
            ->line('**Judul:** ' . $this->task->title)
            ->line('**Pembimbing:** ' . $supervisorName)
            ->line('**Batas Waktu:** ' . $dueDate)
            ->line('')
            ->line('### Deskripsi Tugas:')
            ->line($this->task->description ?: 'Tidak ada deskripsi tambahan.')
            ->line('')
            ->line('### Langkah Selanjutnya:')
            ->line('1. **Baca** detail tugas dengan seksama')
            ->line('2. **Rencanakan** strategi pengerjaan')
            ->line('3. **Mulai kerjakan** tugas sesuai batas waktu')
            ->line('4. **Upload** hasil pekerjaan di sistem')
            ->line('5. **Komunikasikan** dengan pembimbing jika ada kendala')
            ->line('')
            ->action('Lihat Detail Tugas', $taskUrl)
            ->line('')
            ->line('### Tips Mengerjakan Tugas:')
            ->line('• **Baca instruksi** dengan teliti sebelum memulai')
            ->line('• **Manfaatkan waktu** dengan efektif')
            ->line('• **Jangan ragu** bertanya jika ada yang kurang jelas')
            ->line('• **Dokumentasikan** progress Anda di logbook')
            ->line('• **Submit tepat waktu** sebelum batas waktu berakhir')
            ->line('')
            ->line('---')
            ->line('**Penting:** Pastikan Anda menyelesaikan tugas sebelum batas waktu. Jika mengalami kesulitan, segera hubungi pembimbing Anda.')
            ->line('')
            ->line('**Butuh Bantuan?**')
            ->line('Hubungi pembimbing Anda: **' . ($this->task->supervisor->user->email ?? 'Email tidak tersedia') . '**')
            ->line('Atau hubungi admin sistem di **magang.baktikomdigi@gmail.com**')
            ->line('')
            ->line('Semangat mengerjakan tugas! Semoga sukses!')
            ->salutation('**Tim Sistem Monitoring Magang**');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'supervisor_name' => $this->task->supervisor->user->name ?? 'Unknown',
            'due_date' => $this->task->due_date?->toDateTimeString(),
            'status' => $this->task->status ?? 'pending',
            'message' => 'Anda mendapatkan tugas baru "' . $this->task->title . '" dari pembimbing ' . ($this->task->supervisor->user->name ?? 'Unknown'),
            'action_url' => url('/student/tasks/' . $this->task->id),
            'type' => 'new_task',
            'priority' => $this->getTaskPriority(),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Tentukan prioritas tugas berdasarkan due_date
     */
    private function getTaskPriority(): string
    {
        if (!$this->task->due_date) {
            return 'normal';
        }

        $daysUntilDueDate = now()->diffInDays($this->task->due_date, false);
        
        if ($daysUntilDueDate <= 1) {
            return 'urgent';
        } elseif ($daysUntilDueDate <= 3) {
            return 'high';
        } elseif ($daysUntilDueDate <= 7) {
            return 'medium';
        }
        
        return 'normal';
    }
}