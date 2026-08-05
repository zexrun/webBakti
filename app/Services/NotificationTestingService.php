<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\Logbook;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AttendanceApprovalNotification;
use App\Notifications\ExceptionApprovalNotification;
use App\Notifications\LogbookFeedbackGiven;
use App\Notifications\NewTaskAssigned;
use App\Notifications\SubmissionGraded;
use App\Notifications\TaskDeadlineReminder;
use App\Notifications\TaskSubmitted;
use Illuminate\Notifications\Notification;

/**
 * Lets an admin manually fire any notification class against a chosen
 * user, using real seeded data as the notification's subject, without
 * completing the actual workflow (grading a submission, approving an
 * exception, etc). Used by NotificationTestController for the
 * "Uji Notifikasi" debug panel - see PreviewEmailTemplates for the
 * CLI equivalent this mirrors.
 */
class NotificationTestingService
{
    /**
     * @return array<string, array{label: string, build: callable(): ?Notification}>
     */
    public function catalog(): array
    {
        return [
            'submission-graded' => [
                'label' => 'Nilai Diberikan (SubmissionGraded)',
                'build' => fn () => $this->firstOrNull(
                    Submission::whereHas('task.supervisor.user')->whereNotNull('grade'),
                    fn ($submission) => new SubmissionGraded($submission),
                ),
            ],
            'task-submitted' => [
                'label' => 'Tugas Dikumpulkan (TaskSubmitted)',
                'build' => fn () => $this->firstOrNull(
                    Submission::whereHas('task.supervisor.user'),
                    fn ($submission) => new TaskSubmitted($submission),
                ),
            ],
            'new-task-assigned' => [
                'label' => 'Tugas Baru Diberikan (NewTaskAssigned)',
                'build' => fn () => $this->firstOrNull(
                    Task::whereHas('supervisor.user'),
                    fn ($task) => new NewTaskAssigned($task),
                ),
            ],
            'task-deadline-reminder' => [
                'label' => 'Pengingat Deadline Tugas (TaskDeadlineReminder)',
                'build' => fn () => $this->firstOrNull(
                    Task::whereHas('supervisor.user'),
                    fn ($task) => new TaskDeadlineReminder($task, 1),
                ),
            ],
            'attendance-approved' => [
                'label' => 'Presensi Disetujui (AttendanceApprovalNotification)',
                'build' => fn () => $this->firstOrNull(
                    Attendance::whereNotNull('check_in'),
                    fn ($attendance) => new AttendanceApprovalNotification($attendance, 'approved'),
                ),
            ],
            'attendance-rejected' => [
                'label' => 'Presensi Ditolak (AttendanceApprovalNotification)',
                'build' => fn () => $this->firstOrNull(
                    Attendance::whereNotNull('check_in'),
                    fn ($attendance) => new AttendanceApprovalNotification($attendance, 'rejected'),
                ),
            ],
            'exception-approved' => [
                'label' => 'Pengajuan Izin Disetujui (ExceptionApprovalNotification)',
                'build' => fn () => $this->firstOrNull(
                    AttendanceException::query(),
                    fn ($exception) => new ExceptionApprovalNotification($exception, 'approved'),
                ),
            ],
            'exception-rejected' => [
                'label' => 'Pengajuan Izin Ditolak (ExceptionApprovalNotification)',
                'build' => fn () => $this->firstOrNull(
                    AttendanceException::query(),
                    fn ($exception) => new ExceptionApprovalNotification($exception, 'rejected'),
                ),
            ],
            'logbook-feedback' => [
                'label' => 'Feedback Laporan Kegiatan Harian (LogbookFeedbackGiven)',
                'build' => fn () => $this->firstOrNull(
                    Logbook::whereHas('student.supervisor.user')->whereNotNull('feedback'),
                    fn ($logbook) => new LogbookFeedbackGiven($logbook),
                ),
            ],
        ];
    }

    /**
     * Sends the named notification (from catalog()) to the given user.
     * Returns null on success, or an error string if seed data for it
     * doesn't exist yet.
     */
    public function send(string $key, User $recipient): ?string
    {
        $entry = $this->catalog()[$key] ?? null;

        if (! $entry) {
            return "Jenis notifikasi '{$key}' tidak dikenal.";
        }

        $notification = $entry['build']();

        if (! $notification) {
            return 'Tidak ada data contoh yang cocok di database untuk notifikasi ini.';
        }

        $recipient->notify($notification);

        return null;
    }

    private function firstOrNull($query, callable $build): ?Notification
    {
        $model = $query->first();

        return $model ? $build($model) : null;
    }
}
