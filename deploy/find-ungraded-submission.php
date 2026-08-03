<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$submission = App\Models\Submission::whereNull('grade')->with('student.user', 'task')->first();

if (!$submission) {
    echo 'No ungraded submission found.' . PHP_EOL;
    exit;
}

echo 'Submission ID: ' . $submission->id . PHP_EOL;
echo 'Task: ' . $submission->task->title . PHP_EOL;
echo 'Student: ' . $submission->student->user->name . PHP_EOL;
echo 'Current grade: ' . ($submission->grade ?? 'NULL (belum dinilai)') . PHP_EOL;
echo 'Edit URL: /supervisor/submissions/' . $submission->id . '/edit' . PHP_EOL;
