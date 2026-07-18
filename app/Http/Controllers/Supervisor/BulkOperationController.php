<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BulkTaskAssignedNotification;
use Inertia\Inertia;

class BulkOperationController extends Controller
{
    public function createBulkTaskForm()
    {
        $supervisor = Auth::user()->supervisor;
        $students = $supervisor->students()->with('user')->get();

        return Inertia::render('Supervisor/Bulk/CreateTask', [
            'students' => $students,
        ]);
    }

    public function storeBulkTask(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.due_date' => 'required|date|after:today',
            'tasks.*.student_ids' => 'required|array|min:1',
            'tasks.*.student_ids.*' => 'exists:students,id',
        ], [
            'tasks.required' => 'Minimal 1 tugas harus ditambahkan',
            'tasks.*.title.required' => 'Judul tugas harus diisi',
            'tasks.*.due_date.required' => 'Deadline harus diisi',
            'tasks.*.due_date.after' => 'Deadline harus di masa depan',
            'tasks.*.student_ids.required' => 'Pilih minimal 1 mahasiswa',
        ]);

        $createdTasks = [];
        $totalAssignments = 0;

        foreach ($request->tasks as $taskData) {
            $task = Task::create([
                'supervisor_id' => $supervisor->id,
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'due_date' => $taskData['due_date'],
            ]);

            // Attach students to task
            $studentIds = $taskData['student_ids'];
            $task->students()->sync($studentIds);
            $totalAssignments += count($studentIds);

            $createdTasks[] = [
                'id' => $task->id,
                'title' => $task->title,
                'students_count' => count($studentIds),
            ];

            // Send notifications to students
            $students = Student::whereIn('id', $studentIds)->with('user')->get();
            foreach ($students as $student) {
                $student->user->notify(new BulkTaskAssignedNotification($task));
            }
        }

        return redirect()->route('supervisor.tasks.index')
            ->with('success', 'Berhasil membuat ' . count($createdTasks) . ' tugas dengan total ' . $totalAssignments . ' assignment');
    }

    public function bulkNotificationForm()
    {
        $supervisor = Auth::user()->supervisor;
        $students = $supervisor->students()->with('user')->get();

        return Inertia::render('Supervisor/Bulk/SendNotification', [
            'students' => $students,
        ]);
    }

    public function sendBulkNotification(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $request->validate([
            'type' => 'required|in:all,selected',
            'student_ids' => 'required_if:type,selected|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        if ($request->type === 'all') {
            $students = $supervisor->students()->with('user')->get();
        } else {
            $students = Student::whereIn('id', $request->student_ids)->with('user')->get();
        }

        $recipientCount = $students->count();

        foreach ($students as $student) {
            $student->user->notify(new \App\Notifications\BulkNotification(
                $request->title,
                $request->message,
                $request->priority
            ));
        }

        return redirect()->back()
            ->with('success', 'Notifikasi berhasil dikirim ke ' . $recipientCount . ' mahasiswa');
    }

    public function gradeImportForm()
    {
        return Inertia::render('Supervisor/Bulk/ImportGrades');
    }

    public function importGrades(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file.required' => 'File tidak boleh kosong',
            'file.mimes' => 'File harus berformat CSV atau TXT',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        $imported = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            try {
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    continue;
                }

                $submissionId = intval($row[0]);
                $grade = floatval($row[1]);
                $feedback = $row[2] ?? '';

                // Verify submission belongs to this supervisor's task
                $submission = \App\Models\Submission::with('task')
                    ->where('id', $submissionId)
                    ->whereHas('task', fn($q) => $q->where('supervisor_id', $supervisor->id))
                    ->first();

                if (!$submission) {
                    $errors++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": Submission tidak ditemukan atau tidak authorized";
                    continue;
                }

                // Validate grade
                if ($grade < 0 || $grade > 100) {
                    $errors++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": Nilai harus antara 0-100";
                    continue;
                }

                $submission->update([
                    'grade' => $grade,
                    'feedback' => $feedback,
                ]);

                // Send grade notification
                $submission->student->user->notify(
                    new \App\Notifications\SubmissionGraded($submission)
                );

                $imported++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport $imported nilai";
        if ($errors > 0) {
            $message .= " ($errors error)";
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('import_errors', $errorMessages);
    }

    public function automationSettingsForm()
    {
        $supervisor = Auth::user()->supervisor;

        return Inertia::render('Supervisor/Bulk/AutomationSettings', [
            'settings' => [
                'auto_deadline_reminder' => (bool) $supervisor->auto_deadline_reminder,
                'reminder_days_before' => $supervisor->reminder_days_before
                    ? json_decode($supervisor->reminder_days_before, true)
                    : [],
                'auto_submission_reminder' => (bool) $supervisor->auto_submission_reminder,
                'submission_reminder_days' => $supervisor->submission_reminder_days,
            ],
        ]);
    }

    public function saveAutomationSettings(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        $request->validate([
            'auto_deadline_reminder' => 'nullable|boolean',
            'reminder_days_before' => 'required_if:auto_deadline_reminder,true|array',
            'reminder_days_before.*' => 'integer|min:0|max:30',
            'auto_submission_reminder' => 'nullable|boolean',
            'submission_reminder_days' => 'required_if:auto_submission_reminder,true|integer|min:1|max:30',
        ]);

        $supervisor->update([
            'auto_deadline_reminder' => $request->has('auto_deadline_reminder'),
            'reminder_days_before' => $request->has('auto_deadline_reminder')
                ? json_encode($request->reminder_days_before)
                : null,
            'auto_submission_reminder' => $request->has('auto_submission_reminder'),
            'submission_reminder_days' => $request->submission_reminder_days ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Pengaturan automasi berhasil disimpan');
    }
}
