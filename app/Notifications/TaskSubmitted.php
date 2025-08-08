<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Submission;
use Carbon\Carbon;

class TaskSubmitted extends Notification
{
    use Queueable;

    public $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->submission->student->user->name;
        $taskTitle = $this->submission->task->title;
        $submissionUrl = route('supervisor.tasks.show', $this->submission->task_id);
        
        // Handle due_date yang bisa string atau datetime
        $dueDate = 'Tidak ditentukan';
        $submissionStatus = $this->getSubmissionStatus();
        
        if ($this->submission->task->due_date) {
            try {
                $dueDateObj = is_string($this->submission->task->due_date) 
                    ? Carbon::parse($this->submission->task->due_date) 
                    : $this->submission->task->due_date;
                $dueDate = $dueDateObj->format('d M Y, H:i');
            } catch (\Exception $e) {
                $dueDate = $this->submission->task->due_date;
            }
        }

        $submittedAt = $this->submission->created_at 
            ? $this->submission->created_at->format('d M Y, H:i') 
            : 'Tidak diketahui';

        return (new MailMessage)
            ->subject('Tugas Dikumpulkan: ' . $taskTitle . ' - ' . $studentName)
            ->greeting('Halo, **' . $notifiable->name . '**!')
            ->line('---')
            ->line('## Mahasiswa Mengumpulkan Tugas!')
            ->line('')
            ->line('Mahasiswa **' . $studentName . '** telah mengumpulkan tugas yang Anda berikan. Silakan review dan berikan feedback.')
            ->line('')
            ->line('### Detail Submission:')
            ->line('**Mahasiswa:** ' . $studentName)
            ->line('**Tugas:** ' . $taskTitle)
            ->line('**Waktu Pengumpulan:** ' . $submittedAt)
            ->line('**Batas Waktu:** ' . $dueDate)
            ->line('**Status:** ' . $submissionStatus['text'])
            ->line('')
            ->line('### Informasi Submission:')
            ->line($this->getSubmissionDetails())
            ->line('')
            ->line('### Langkah Selanjutnya:')
            ->line('1. **Review** hasil pekerjaan mahasiswa')
            ->line('2. **Periksa** kelengkapan dan kualitas submission')
            ->line('3. **Berikan feedback** yang konstruktif')
            ->line('4. **Tentukan nilai** atau status approval')
            ->line('5. **Komunikasikan** hasil review kepada mahasiswa')
            ->line('')
            ->action('Review Submission Sekarang', $submissionUrl)
            ->line('')
            ->line('### Tips untuk Review:')
            ->line('• **Periksa kelengkapan** sesuai dengan requirement')
            ->line('• **Berikan feedback** yang spesifik dan membangun')
            ->line('• **Apresiasi usaha** mahasiswa dalam mengerjakan')
            ->line('• **Saran perbaikan** jika diperlukan')
            ->line('• **Respond dalam 2-3 hari** untuk menjaga momentum belajar')
            ->line('')
            ->line($submissionStatus['message'])
            ->line('')
            ->line('---')
            ->line('**Reminder:** Mahasiswa menunggu feedback dari Anda. Review yang cepat dan berkualitas akan membantu proses pembelajaran mereka.')
            ->line('')
            ->line('**Butuh Bantuan?**')
            ->line('Hubungi admin sistem di **magang.baktikomdigi@gmail.com** jika ada kendala teknis.')
            ->line('')
            ->line('Terima kasih atas bimbingan Anda!')
            ->salutation('**Tim Sistem Monitoring Magang**');
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->submission->student->user->name;
        $taskTitle = $this->submission->task->title;
        $submissionStatus = $this->getSubmissionStatus();

        return [
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task_id,
            'student_id' => $this->submission->student_id,
            'student_name' => $studentName,
            'task_title' => $taskTitle,
            'submitted_at' => $this->submission->created_at?->toDateTimeString(),
            'submission_status' => $submissionStatus['status'],
            'is_late' => $submissionStatus['is_late'],
            'message' => $studentName . ' telah mengumpulkan tugas "' . $taskTitle . '"' . 
                        ($submissionStatus['is_late'] ? ' (Terlambat)' : ''),
            'action_url' => route('supervisor.tasks.show', $this->submission->task_id),
            'type' => 'task_submitted',
            'priority' => $submissionStatus['is_late'] ? 'high' : 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Dapatkan status submission (tepat waktu atau terlambat)
     */
    private function getSubmissionStatus(): array
    {
        $isLate = false;
        $status = 'on_time';
        $text = 'Tepat Waktu';
        $message = '';

        if ($this->submission->task->due_date && $this->submission->created_at) {
            try {
                $dueDateObj = is_string($this->submission->task->due_date) 
                    ? Carbon::parse($this->submission->task->due_date) 
                    : $this->submission->task->due_date;
                
                if ($this->submission->created_at->gt($dueDateObj)) {
                    $isLate = true;
                    $status = 'late';
                    $text = 'Terlambat';
                    
                    $lateDays = $this->submission->created_at->diffInDays($dueDateObj);
                    $lateHours = $this->submission->created_at->diffInHours($dueDateObj) % 24;
                    
                    if ($lateDays > 0) {
                        $message = "**Perhatian:** Submission ini terlambat {$lateDays} hari. Pertimbangkan untuk memberikan feedback tentang pentingnya manajemen waktu.";
                    } else {
                        $message = "**Perhatian:** Submission ini terlambat {$lateHours} jam. Ingatkan mahasiswa tentang pentingnya submit tepat waktu.";
                    }
                } else {
                    $message = "**Bagus!** Mahasiswa mengumpulkan tugas tepat waktu. Berikan apresiasi atas kedisiplinan mereka.";
                }
            } catch (\Exception $e) {
                // Jika parsing gagal, anggap tepat waktu
                $message = "Mahasiswa telah mengumpulkan tugas. Silakan review dan berikan feedback.";
            }
        } else {
            $message = "Mahasiswa telah mengumpulkan tugas. Silakan review dan berikan feedback.";
        }

        return [
            'is_late' => $isLate,
            'status' => $status,
            'text' => $text,
            'message' => $message,
        ];
    }

    /**
     * Dapatkan detail submission
     */
    private function getSubmissionDetails(): string
    {
        $details = [];
        
        if ($this->submission->file_path) {
            $details[] = '**File:** Ada file yang diupload';
        }
        
        if ($this->submission->notes) {
            $details[] = '**Catatan Mahasiswa:** ' . substr($this->submission->notes, 0, 100) . 
                        (strlen($this->submission->notes) > 100 ? '...' : '');
        }
        
        if ($this->submission->submission_text) {
            $details[] = '**Jawaban:** ' . substr($this->submission->submission_text, 0, 150) . 
                        (strlen($this->submission->submission_text) > 150 ? '...' : '');
        }
        
        if (empty($details)) {
            $details[] = 'Mahasiswa telah menandai tugas sebagai selesai.';
        }
        
        return implode("\n", $details);
    }
}