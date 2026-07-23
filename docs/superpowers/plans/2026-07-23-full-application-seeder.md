# Full Application Seeder Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Populate every currently-empty model (Attendance, AttendanceException, Task+Submission, FinalAssessment, Document, Logbook, Message, Announcement, AttendanceSetting) with realistic demo data on top of the existing 3 students / 2 supervisors / 1 admin, and remove two dead-code relationships found during schema exploration.

**Architecture:** New Eloquent factories in `database/factories/` for each currently-factory-less model, paired with new seeders in `database/seeders/` that use them with explicit variety (mixed statuses, some deliberately "incomplete" rows) rather than uniform fake data. `StudentSupervisorAssignmentSeeder` gains a round-robin backfill so every student has a supervisor before any task/assessment seeding runs. All new seeders are registered in `DatabaseSeeder.php` in dependency order.

**Tech Stack:** Laravel 12 Eloquent factories/seeders, Faker (via `fake()` helper, already used elsewhere in this codebase).

---

### Task 1: Remove dead-code relationships

**Files:**
- Modify: `app/Models/Logbook.php`
- Modify: `app/Models/Document.php`

- [ ] **Step 1: Remove Logbook::user()**

In `app/Models/Logbook.php`, remove this method (it references a `user_id` column that doesn't exist on the `logbooks` table, and isn't called anywhere in the codebase):

```php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
```

The file should end up as:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{

    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'activity_date',
        'start_time',
        'end_time',
        'description',
        'feeling',
        'file_path',
        'is_verified',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
```

- [ ] **Step 2: Remove Document::documents()**

In `app/Models/Document.php`, remove this method (self-referential relation with no supporting column, unused anywhere):

```php
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
```

The file should end up as:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Document extends Model
{

    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_name',
        'file_path',
        'type',
        'mime_type',
        'file_size',
        'original_filename',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l app/Models/Logbook.php
php -l app/Models/Document.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Verify no other code references the removed methods**

```bash
grep -rn "\->user()" app/Models/Logbook.php resources/js app/Http/Controllers 2>/dev/null | grep -i logbook
grep -rn "\->documents()" app/Http/Controllers resources/js 2>/dev/null | grep -v "student->documents\|\\\$student->documents"
```
Expected: no output referencing `Logbook->user()` or a bare `Document->documents()` call (only `$student->documents()` calls are expected to exist elsewhere, which is a different, valid relationship on the `Student` model, not this one).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Logbook.php app/Models/Document.php
git commit -m "refactor: Remove dead-code Logbook::user() and Document::documents() relations"
```

---

### Task 2: Backfill supervisor assignment for unassigned students

**Files:**
- Modify: `database/seeders/StudentSupervisorAssignmentSeeder.php`

- [ ] **Step 1: Add the round-robin backfill**

Replace the file's `run()` method. Current file:

```php
<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Assigns specific students to specific supervisors, on top of what
 * SupervisorSeeder/StudentSeeder already create. UserFactory's
 * afterCreating() hook auto-creates the Student/Supervisor rows but
 * never sets supervisor_id (StudentFactory leaves it null), so this
 * seeder exists to make the assignment explicit and reproducible
 * rather than relying on incidental factory/seeder ordering.
 */
class StudentSupervisorAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assign('mrifqy821@gmail.com', 'dede@baktitest.com');
    }

    private function assign(string $studentEmail, string $supervisorEmail): void
    {
        $studentUser = User::where('email', $studentEmail)->first();
        $supervisorUser = User::where('email', $supervisorEmail)->first();

        if (!$studentUser || !$studentUser->student) {
            $this->command->warn("Skipped: no student user/record found for {$studentEmail}");
            return;
        }

        if (!$supervisorUser || !$supervisorUser->supervisor) {
            $this->command->warn("Skipped: no supervisor user/record found for {$supervisorEmail}");
            return;
        }

        $studentUser->student->update([
            'supervisor_id' => $supervisorUser->supervisor->id,
        ]);
    }
}
```

Change to:

```php
<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Assigns specific students to specific supervisors, on top of what
 * SupervisorSeeder/StudentSeeder already create. UserFactory's
 * afterCreating() hook auto-creates the Student/Supervisor rows but
 * never sets supervisor_id (StudentFactory leaves it null), so this
 * seeder exists to make the assignment explicit and reproducible
 * rather than relying on incidental factory/seeder ordering.
 */
class StudentSupervisorAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assign('mrifqy821@gmail.com', 'dede@baktitest.com');
        $this->backfillUnassigned();
    }

    private function assign(string $studentEmail, string $supervisorEmail): void
    {
        $studentUser = User::where('email', $studentEmail)->first();
        $supervisorUser = User::where('email', $supervisorEmail)->first();

        if (!$studentUser || !$studentUser->student) {
            $this->command->warn("Skipped: no student user/record found for {$studentEmail}");
            return;
        }

        if (!$supervisorUser || !$supervisorUser->supervisor) {
            $this->command->warn("Skipped: no supervisor user/record found for {$supervisorEmail}");
            return;
        }

        $studentUser->student->update([
            'supervisor_id' => $supervisorUser->supervisor->id,
        ]);
    }

    /**
     * Round-robin any remaining unassigned students across existing
     * supervisors, so every downstream seeder (Task/Submission/
     * FinalAssessment/Message) can rely on every student having a
     * supervisor - without hardcoding which supervisor gets which
     * leftover student.
     */
    private function backfillUnassigned(): void
    {
        $supervisorIds = Supervisor::pluck('id');

        if ($supervisorIds->isEmpty()) {
            $this->command->warn('Skipped backfill: no supervisors exist yet.');
            return;
        }

        $unassigned = Student::whereNull('supervisor_id')->get();

        foreach ($unassigned as $index => $student) {
            $supervisorId = $supervisorIds[$index % $supervisorIds->count()];
            $student->update(['supervisor_id' => $supervisorId]);
        }
    }
}
```

- [ ] **Step 2: Verify syntax**

```bash
php -l database/seeders/StudentSupervisorAssignmentSeeder.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```bash
git add database/seeders/StudentSupervisorAssignmentSeeder.php
git commit -m "feat: Backfill supervisor assignment for any unassigned students"
```

---

### Task 3: AttendanceSetting seeder

**Files:**
- Create: `database/seeders/AttendanceSettingSeeder.php`

- [ ] **Step 1: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use Illuminate\Database\Seeder;

/**
 * AttendanceSetting::getSettings() already lazily creates a default row
 * if none exists - this seeder just guarantees that happens during a
 * fresh seed run, so the row exists deterministically rather than on
 * whichever request happens to touch it first.
 */
class AttendanceSettingSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceSetting::getSettings();
    }
}
```

- [ ] **Step 2: Verify syntax**

```bash
php -l database/seeders/AttendanceSettingSeeder.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```bash
git add database/seeders/AttendanceSettingSeeder.php
git commit -m "feat: Add AttendanceSettingSeeder to guarantee a default settings row"
```

---

### Task 4: Attendance factory and seeder (90 working days, with suspicious cases)

**Files:**
- Create: `database/factories/AttendanceFactory.php`
- Create: `database/seeders/AttendanceSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Base state: a normal, on-time present day. AttendanceSeeder
     * overrides date/user_id/status/times per row - this definition
     * exists mainly so ->state([...]) calls have sensible defaults for
     * fields the seeder doesn't explicitly set on a given row.
     */
    public function definition(): array
    {
        return [
            'status' => 'present',
            'supervisor_approval' => 'approved',
            'location_verification_status' => 'verified',
            'location_spoofing_score' => 0,
            'requires_manual_review' => false,
            'photo_exif_status' => 'valid',
            'face_verification_status' => 'verified',
            'face_match_distance' => fake()->randomFloat(4, 0.1, 0.5),
        ];
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Generates ~90 calendar days (weekdays only) of attendance history per
 * student, with a realistic status mix and a deliberate slice of
 * "suspicious" rows (face mismatch / flagged location) so the
 * Suspicious review pages and the Monitoring page's "Mencurigakan"
 * counter have real data to display.
 */
class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $settings = AttendanceSetting::getSettings();
        $students = Student::with('user')->get();

        foreach ($students as $student) {
            if (!$student->user) {
                continue;
            }

            $this->seedForUser($student->user->id, $settings);
        }
    }

    private function seedForUser(int $userId, AttendanceSetting $settings): void
    {
        $day = Carbon::now()->subDays(90);
        $today = Carbon::today();

        while ($day->lte($today)) {
            if ($day->isWeekend()) {
                $day->addDay();
                continue;
            }

            $roll = fake()->numberBetween(1, 100);

            // ~8% absent, ~15% late, rest present.
            if ($roll <= 8) {
                $this->createAbsent($userId, $day->copy());
            } elseif ($roll <= 23) {
                $this->createWorkedDay($userId, $day->copy(), $settings, late: true);
            } else {
                $this->createWorkedDay($userId, $day->copy(), $settings, late: false);
            }

            $day->addDay();
        }
    }

    private function createAbsent(int $userId, Carbon $date): void
    {
        Attendance::factory()->create([
            'user_id' => $userId,
            'date' => $date->toDateString(),
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'location_verification_status' => 'unverified',
            'face_verification_status' => null,
            'face_match_distance' => null,
        ]);
    }

    private function createWorkedDay(int $userId, Carbon $date, AttendanceSetting $settings, bool $late): void
    {
        [$startHour, $startMinute] = explode(':', $settings->work_start_time);
        $checkIn = $date->copy()->setTime((int) $startHour, (int) $startMinute)
            ->addMinutes($late ? fake()->numberBetween($settings->late_tolerance_minutes + 5, 90) : fake()->numberBetween(-10, 5));
        $checkOut = $date->copy()->setTime(17, fake()->numberBetween(0, 30));

        // ~7% of worked days are deliberately flagged as suspicious.
        $suspicious = fake()->numberBetween(1, 100) <= 7;
        $suspiciousReason = $suspicious ? fake()->randomElement(['face', 'location']) : null;

        Attendance::factory()->create(array_merge([
            'user_id' => $userId,
            'date' => $date->toDateString(),
            'status' => $late ? 'late' : 'present',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'requires_manual_review' => $suspicious,
        ], $this->suspiciousAttributes($suspiciousReason)));
    }

    private function suspiciousAttributes(?string $reason): array
    {
        if ($reason === 'face') {
            return [
                'face_verification_status' => 'mismatch',
                'face_match_distance' => fake()->randomFloat(4, 0.65, 1.2),
            ];
        }

        if ($reason === 'location') {
            return [
                'location_verification_status' => 'flagged',
                'location_spoofing_score' => fake()->numberBetween(60, 95),
                'location_notes' => 'Lokasi terindikasi di luar radius kantor saat presensi.',
            ];
        }

        return [];
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/AttendanceFactory.php
php -l database/seeders/AttendanceSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/AttendanceFactory.php database/seeders/AttendanceSeeder.php
git commit -m "feat: Add Attendance factory and seeder with 90 days of varied history"
```

---

### Task 5: AttendanceException factory and seeder

**Files:**
- Create: `database/factories/AttendanceExceptionFactory.php`
- Create: `database/seeders/AttendanceExceptionSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceException>
 */
class AttendanceExceptionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['sick', 'leave', 'permit', 'official']);

        $reasons = [
            'sick' => 'Sakit demam, disertai surat keterangan dokter.',
            'leave' => 'Cuti keperluan keluarga.',
            'permit' => 'Izin mengurus keperluan administrasi kampus.',
            'official' => 'Mengikuti kegiatan resmi kampus.',
        ];

        return [
            'type' => $type,
            'reason' => $reasons[$type],
            'status' => fake()->randomElement(['pending', 'approved', 'approved', 'rejected']),
        ];
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\AttendanceException;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * 2-4 exceptions per student over the same 90-day window Attendance
 * uses, on distinct dates so an exception day never collides with a
 * worked/absent Attendance row for the same student+date.
 */
class AttendanceExceptionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('user')->get();

        foreach ($students as $student) {
            if (!$student->user) {
                continue;
            }

            $this->seedForStudent($student->user->id);
        }
    }

    private function seedForStudent(int $userId): void
    {
        $count = fake()->numberBetween(2, 4);
        $usedDates = [];

        for ($i = 0; $i < $count; $i++) {
            $date = Carbon::now()->subDays(fake()->numberBetween(1, 89));

            while ($date->isWeekend() || in_array($date->toDateString(), $usedDates, true)) {
                $date = Carbon::now()->subDays(fake()->numberBetween(1, 89));
            }

            $usedDates[] = $date->toDateString();

            AttendanceException::factory()->create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
            ]);
        }
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/AttendanceExceptionFactory.php
php -l database/seeders/AttendanceExceptionSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/AttendanceExceptionFactory.php database/seeders/AttendanceExceptionSeeder.php
git commit -m "feat: Add AttendanceException factory and seeder"
```

---

### Task 6: Extend TaskFactory/TaskSeeder to attach students, register it

**Files:**
- Modify: `database/factories/TaskFactory.php`
- Modify: `database/seeders/TaskSeeder.php`

- [ ] **Step 1: Update TaskFactory to accept an explicit supervisor and richer titles**

Change `database/factories/TaskFactory.php` from:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supervisor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    use HasFactory;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supervisor_id' => Supervisor::inRandomOrder()->first()->id,
            'title' => 'Tugas Magang',
            'description' => fake()->paragraph(2),
            'file_path' => fake()->boolean() ? 'path/to/some/file.pdf' : null, // Contoh penambahan data file_path
            'type' => fake()->randomElement(['harian']),
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
```

to:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supervisor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    use HasFactory;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supervisor_id' => Supervisor::inRandomOrder()->first()->id,
            'title' => fake()->randomElement([
                'Laporan Mingguan Aktivitas',
                'Dokumentasi Modul Sistem',
                'Presentasi Progress Magang',
                'Analisis Kebutuhan Sistem',
                'Implementasi Fitur',
                'Laporan Akhir Magang',
            ]),
            'description' => fake()->paragraph(2),
            'file_path' => fake()->boolean() ? 'path/to/some/file.pdf' : null, // Contoh penambahan data file_path
            'type' => fake()->randomElement(['harian', 'akhir']),
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
```

- [ ] **Step 2: Update TaskSeeder to create tasks per supervisor and attach that supervisor's students**

Change `database/seeders/TaskSeeder.php` from:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Sequence;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 10 data tugas palsu menggunakan TaskFactory
        Task::factory()
            ->count(10)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['title' => 'Tugas Magang ke-' . $sequence->index + 1],
            ))
            ->create();
    }
}
```

to:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supervisor;
use App\Models\Task;

/**
 * Creates 3-4 tasks per supervisor (mixed harian/akhir) and attaches
 * every one of that supervisor's students to each task via the
 * task_student pivot - TaskFactory alone never does this, so without
 * this step every Task would have zero assigned students and
 * SubmissionSeeder would have nothing to iterate.
 */
class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = Supervisor::with('students')->get();

        foreach ($supervisors as $supervisor) {
            $studentIds = $supervisor->students->pluck('id');

            if ($studentIds->isEmpty()) {
                continue;
            }

            $taskCount = fake()->numberBetween(3, 4);

            Task::factory()
                ->count($taskCount)
                ->create(['supervisor_id' => $supervisor->id])
                ->each(function (Task $task) use ($studentIds) {
                    $task->students()->attach($studentIds);
                });
        }
    }
}
```

- [ ] **Step 3: Register TaskSeeder in DatabaseSeeder.php**

This is done together with all the other new seeders in Task 12 (Full registration) — no separate registration step here to avoid a half-registered `DatabaseSeeder.php` mid-plan.

- [ ] **Step 4: Verify syntax**

```bash
php -l database/factories/TaskFactory.php
php -l database/seeders/TaskSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 5: Commit**

```bash
git add database/factories/TaskFactory.php database/seeders/TaskSeeder.php
git commit -m "feat: Attach students to tasks in TaskSeeder, broaden task variety"
```

---

### Task 7: Submission factory and seeder

**Files:**
- Create: `database/factories/SubmissionFactory.php`
- Create: `database/seeders/SubmissionSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(3),
            'file_path' => fake()->boolean(70) ? 'path/to/submission-file.pdf' : null,
            'grade' => null,
            'comments' => null,
        ];
    }

    /**
     * A submission that has been graded, with a mix of numeric scores
     * and letter grades matching the classification already used by
     * the grades PDF template (A/B/C/D bands).
     */
    public function graded(): static
    {
        return $this->state(function () {
            $grade = fake()->randomElement([
                fake()->numberBetween(85, 100),
                fake()->numberBetween(75, 84),
                fake()->numberBetween(65, 74),
                fake()->numberBetween(40, 64),
                'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C',
            ]);

            return [
                'grade' => (string) $grade,
                'comments' => fake()->randomElement([
                    'Hasil kerja sangat baik dan sesuai target.',
                    'Perlu perbaikan pada beberapa bagian, namun cukup baik.',
                    'Dikerjakan dengan baik, pertahankan konsistensi.',
                    'Analisis kurang mendalam, harap ditingkatkan.',
                ]),
            ];
        });
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * For every (task, student) pair created by TaskSeeder's pivot
 * attachment, splits into three buckets: no submission yet (~30%),
 * submitted but ungraded (~30%), and graded (~40%) - so the
 * Submissions/Tasks pages exercise every state instead of only
 * "everything graded".
 */
class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::with('students')->get();

        foreach ($tasks as $task) {
            foreach ($task->students as $student) {
                $roll = fake()->numberBetween(1, 100);

                if ($roll <= 30) {
                    // No submission at all - student hasn't submitted yet.
                    continue;
                }

                if ($roll <= 60) {
                    Submission::factory()->create([
                        'task_id' => $task->id,
                        'student_id' => $student->id,
                    ]);
                    continue;
                }

                Submission::factory()->graded()->create([
                    'task_id' => $task->id,
                    'student_id' => $student->id,
                ]);
            }
        }
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/SubmissionFactory.php
php -l database/seeders/SubmissionSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/SubmissionFactory.php database/seeders/SubmissionSeeder.php
git commit -m "feat: Add Submission factory and seeder with unsubmitted/ungraded/graded mix"
```

---

### Task 8: FinalAssessment factory and seeder

**Files:**
- Create: `database/factories/FinalAssessmentFactory.php`
- Create: `database/seeders/FinalAssessmentSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalAssessment>
 */
class FinalAssessmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'final_grade' => fake()->randomElement(['A', 'A-', 'B+', 'B', 'B-', 'C+']),
            'overall_comments' => fake()->paragraph(2),
            'certificate_generated_at' => null,
        ];
    }

    /**
     * A final assessment whose certificate has already been generated -
     * exercises the "already generated, only regenerate if grade
     * changed since" branch in FinalAssessmentController::generateCertificate().
     */
    public function withCertificate(): static
    {
        return $this->state(fn () => [
            'certificate_generated_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\FinalAssessment;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Splits students into three assessment states: not started (~30%),
 * graded but certificate not yet generated (~40%), and fully graded
 * with certificate already generated (~30%) - so both the Supervisor
 * assessment pages and the certificate-generation flow have real
 * examples of every stage.
 */
class FinalAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::whereNotNull('supervisor_id')->get();

        foreach ($students as $index => $student) {
            $roll = fake()->numberBetween(1, 100);

            if ($roll <= 30) {
                // Not started yet - no FinalAssessment row.
                continue;
            }

            $factory = FinalAssessment::factory();

            if ($roll > 70) {
                $factory = $factory->withCertificate();
            }

            $factory->create([
                'student_id' => $student->id,
                'supervisor_id' => $student->supervisor_id,
            ]);
        }
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/FinalAssessmentFactory.php
php -l database/seeders/FinalAssessmentSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/FinalAssessmentFactory.php database/seeders/FinalAssessmentSeeder.php
git commit -m "feat: Add FinalAssessment factory and seeder with not-started/graded/certificated mix"
```

---

### Task 9: Document factory and seeder

**Files:**
- Create: `database/factories/DocumentFactory.php`
- Create: `database/seeders/DocumentSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_name' => 'Dokumen Magang',
            'file_path' => 'documents/placeholder.pdf',
            'type' => 'lainnya',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(100_000, 2_000_000),
            'original_filename' => 'dokumen.pdf',
        ];
    }

    public function proposal(): static
    {
        return $this->state(fn () => [
            'document_name' => 'Proposal Magang',
            'file_path' => 'documents/proposal-placeholder.pdf',
            'type' => 'proposal',
            'original_filename' => 'proposal-magang.pdf',
        ]);
    }

    public function laporanAkhir(): static
    {
        return $this->state(fn () => [
            'document_name' => 'Laporan Akhir Magang',
            'file_path' => 'documents/laporan-akhir-placeholder.pdf',
            'type' => 'laporan_akhir',
            'original_filename' => 'laporan-akhir-magang.pdf',
        ]);
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Every student gets a proposal document. Only students who reached
 * the graded/certificate stage in FinalAssessmentSeeder also get a
 * laporan_akhir document, keeping the two features' data consistent
 * with Student/Info/Edit.jsx's progress tracker (which checks for both
 * a laporan_akhir document AND a graded assessment before showing
 * "selesai").
 */
class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('finalAssessment')->get();

        foreach ($students as $student) {
            Document::factory()->proposal()->create(['student_id' => $student->id]);

            if ($student->finalAssessment && $student->finalAssessment->final_grade) {
                Document::factory()->laporanAkhir()->create(['student_id' => $student->id]);
            }

            if (fake()->boolean(30)) {
                Document::factory()->create(['student_id' => $student->id]);
            }
        }
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/DocumentFactory.php
php -l database/seeders/DocumentSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/DocumentFactory.php database/seeders/DocumentSeeder.php
git commit -m "feat: Add Document factory and seeder, consistent with FinalAssessment progress"
```

---

### Task 10: Logbook factory and seeder

**Files:**
- Create: `database/factories/LogbookFactory.php`
- Create: `database/seeders/LogbookSeeder.php`

- [ ] **Step 1: Write the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Logbook>
 */
class LogbookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Rapat Koordinasi Tim',
                'Pengembangan Fitur',
                'Testing dan Debugging',
                'Dokumentasi Progress',
                'Diskusi dengan Pembimbing',
            ]),
            'start_time' => '08:30:00',
            'end_time' => '16:30:00',
            'description' => fake()->paragraph(2),
            'feeling' => fake()->randomElement(['Semangat', 'Produktif', 'Biasa saja', 'Sedikit lelah']),
            'file_path' => null,
            'is_verified' => fake()->boolean(50),
        ];
    }
}
```

- [ ] **Step 2: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Logbook;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * One logbook entry per weekday over the last ~3 weeks per student - a
 * smaller, more recent window than Attendance's 90 days, since logbooks
 * are filled in more sporadically in practice than daily attendance.
 */
class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        foreach ($students as $student) {
            $day = Carbon::now()->subWeeks(3);
            $today = Carbon::today();

            while ($day->lte($today)) {
                if (!$day->isWeekend()) {
                    Logbook::factory()->create([
                        'student_id' => $student->id,
                        'activity_date' => $day->toDateString(),
                    ]);
                }

                $day->addDay();
            }
        }
    }
}
```

- [ ] **Step 3: Verify syntax**

```bash
php -l database/factories/LogbookFactory.php
php -l database/seeders/LogbookSeeder.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 4: Commit**

```bash
git add database/factories/LogbookFactory.php database/seeders/LogbookSeeder.php
git commit -m "feat: Add Logbook factory and seeder covering the last 3 weeks"
```

---

### Task 11: Message and Announcement factories and seeders

**Files:**
- Create: `database/factories/MessageFactory.php`
- Create: `database/seeders/MessageSeeder.php`
- Create: `database/factories/AnnouncementFactory.php`
- Create: `database/seeders/AnnouncementSeeder.php`

- [ ] **Step 1: Write the Message factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        $isRead = fake()->boolean(60);

        return [
            'subject' => fake()->randomElement([
                'Progress Minggu Ini',
                'Pertanyaan Terkait Tugas',
                'Konfirmasi Jadwal Bimbingan',
                'Update Laporan Magang',
            ]),
            'body' => fake()->paragraph(3),
            'is_read' => $isRead,
            'read_at' => $isRead ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
```

- [ ] **Step 2: Write the Message seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * A handful of messages in both directions (student->supervisor and
 * supervisor->student) per student-supervisor pair, with a mixed
 * read/unread state.
 */
class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with(['user', 'supervisor.user'])->get();

        foreach ($students as $student) {
            if (!$student->user || !$student->supervisor || !$student->supervisor->user) {
                continue;
            }

            $studentUserId = $student->user->id;
            $supervisorUserId = $student->supervisor->user->id;

            $count = fake()->numberBetween(2, 4);

            for ($i = 0; $i < $count; $i++) {
                $studentToSupervisor = fake()->boolean();

                Message::factory()->create([
                    'sender_id' => $studentToSupervisor ? $studentUserId : $supervisorUserId,
                    'recipient_id' => $studentToSupervisor ? $supervisorUserId : $studentUserId,
                ]);
            }
        }
    }
}
```

- [ ] **Step 3: Write the Announcement factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Jadwal Libur Nasional',
                'Pembaruan Kebijakan Presensi',
                'Batas Waktu Pengumpulan Laporan Akhir',
                'Pemeliharaan Sistem Terjadwal',
                'Sosialisasi Program Magang Batch Baru',
            ]),
            'content' => fake()->paragraph(4),
            'priority' => fake()->randomElement(['low', 'normal', 'normal', 'high', 'urgent']),
            'target_roles' => ['student', 'supervisor'],
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }
}
```

- [ ] **Step 4: Write the Announcement seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 5-6 announcements from the admin user, mostly published (mixed
 * priority) with one or two left as drafts (published_at null) so the
 * Admin Announcements page shows both states.
 */
class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('Skipped: no admin user found.');
            return;
        }

        Announcement::factory()->count(4)->create(['admin_id' => $admin->id]);
        Announcement::factory()->draft()->count(2)->create(['admin_id' => $admin->id]);
    }
}
```

- [ ] **Step 5: Verify syntax**

```bash
php -l database/factories/MessageFactory.php
php -l database/seeders/MessageSeeder.php
php -l database/factories/AnnouncementFactory.php
php -l database/seeders/AnnouncementSeeder.php
```
Expected: `No syntax errors detected` for all four.

- [ ] **Step 6: Commit**

```bash
git add database/factories/MessageFactory.php database/seeders/MessageSeeder.php database/factories/AnnouncementFactory.php database/seeders/AnnouncementSeeder.php
git commit -m "feat: Add Message and Announcement factories and seeders"
```

---

### Task 12: Register every new seeder in DatabaseSeeder.php

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Replace the full file**

```php
<?php

namespace Database\Seeders;

use Database\Seeders\AdminSeeder;
use Database\Seeders\DirectorateSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\SupervisorSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\StudentSupervisorAssignmentSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\UniversitySeeder;
use Database\Seeders\AttendanceSettingSeeder;
use Database\Seeders\AttendanceSeeder;
use Database\Seeders\AttendanceExceptionSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\FinalAssessmentSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\LogbookSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\AnnouncementSeeder;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            DirectorateSeeder::class,
            PositionSeeder::class,
            SupervisorSeeder::class,
            StudentSeeder::class,
            StudentSupervisorAssignmentSeeder::class,
            UniversitySeeder::class,
            AttendanceSettingSeeder::class,
            AttendanceSeeder::class,
            AttendanceExceptionSeeder::class,
            TaskSeeder::class,
            SubmissionSeeder::class,
            FinalAssessmentSeeder::class,
            DocumentSeeder::class,
            LogbookSeeder::class,
            MessageSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
```

- [ ] **Step 2: Verify syntax**

```bash
php -l database/seeders/DatabaseSeeder.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DatabaseSeeder.php
git commit -m "feat: Register all new seeders in DatabaseSeeder"
```

---

### Task 13: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax check every touched/created PHP file**

```bash
php -l app/Models/Logbook.php
php -l app/Models/Document.php
php -l database/seeders/StudentSupervisorAssignmentSeeder.php
php -l database/seeders/AttendanceSettingSeeder.php
php -l database/factories/AttendanceFactory.php
php -l database/seeders/AttendanceSeeder.php
php -l database/factories/AttendanceExceptionFactory.php
php -l database/seeders/AttendanceExceptionSeeder.php
php -l database/factories/TaskFactory.php
php -l database/seeders/TaskSeeder.php
php -l database/factories/SubmissionFactory.php
php -l database/seeders/SubmissionSeeder.php
php -l database/factories/FinalAssessmentFactory.php
php -l database/seeders/FinalAssessmentSeeder.php
php -l database/factories/DocumentFactory.php
php -l database/seeders/DocumentSeeder.php
php -l database/factories/LogbookFactory.php
php -l database/seeders/LogbookSeeder.php
php -l database/factories/MessageFactory.php
php -l database/seeders/MessageSeeder.php
php -l database/factories/AnnouncementFactory.php
php -l database/seeders/AnnouncementSeeder.php
php -l database/seeders/DatabaseSeeder.php
```
Expected: `No syntax errors detected` for every file.

- [ ] **Step 2: Run a full fresh migrate+seed**

```bash
php artisan migrate:fresh --seed
```
Expected: completes with no errors, ending in a summary of all seeders having run.

- [ ] **Step 3: Spot-check row counts**

```bash
php artisan tinker --execute="
echo 'Students: ' . App\Models\Student::count() . PHP_EOL;
echo 'Students with supervisor: ' . App\Models\Student::whereNotNull('supervisor_id')->count() . PHP_EOL;
echo 'Attendance rows: ' . App\Models\Attendance::count() . PHP_EOL;
echo 'Suspicious attendance rows: ' . App\Models\Attendance::where('requires_manual_review', true)->count() . PHP_EOL;
echo 'AttendanceException rows: ' . App\Models\AttendanceException::count() . PHP_EOL;
echo 'Tasks: ' . App\Models\Task::count() . PHP_EOL;
echo 'Tasks with students attached: ' . App\Models\Task::has('students')->count() . PHP_EOL;
echo 'Submissions: ' . App\Models\Submission::count() . PHP_EOL;
echo 'Graded submissions: ' . App\Models\Submission::whereNotNull('grade')->count() . PHP_EOL;
echo 'FinalAssessments: ' . App\Models\FinalAssessment::count() . PHP_EOL;
echo 'FinalAssessments with certificate: ' . App\Models\FinalAssessment::whereNotNull('certificate_generated_at')->count() . PHP_EOL;
echo 'Documents: ' . App\Models\Document::count() . PHP_EOL;
echo 'Logbooks: ' . App\Models\Logbook::count() . PHP_EOL;
echo 'Messages: ' . App\Models\Message::count() . PHP_EOL;
echo 'Announcements: ' . App\Models\Announcement::count() . PHP_EOL;
echo 'AttendanceSetting exists: ' . (App\Models\AttendanceSetting::count() > 0 ? 'yes' : 'no') . PHP_EOL;
"
```
Expected: every count is greater than 0 (except counts that are legitimately a subset, like "Suspicious attendance rows" or "FinalAssessments with certificate", which should still be > 0 given the seeded proportions), and "Students with supervisor" equals total Student count (confirming the round-robin backfill worked).

- [ ] **Step 4: Manual browser check**

Log in as each role and visit the previously-empty pages to confirm they now render real data instead of empty states:
- Student: Attendance History, Documents, Logbooks (if a page exists), Messages, Tasks/Submissions
- Supervisor: Attendance Approvals/Suspicious, Tasks/Submissions grading, Students list (Final Assessment), Logbooks review, Messages
- Admin: Attendance Monitoring (including the "Mencurigakan" counter from earlier this session), Announcements

- [ ] **Step 5: Commit any fixes found during manual verification**

Only if Steps 2-4 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
