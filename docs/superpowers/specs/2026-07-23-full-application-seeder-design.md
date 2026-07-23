# Full Application Seeder — Design

## Goal

Populate every currently-empty model in the application (Attendance, AttendanceException, Announcement, Document, FinalAssessment, Logbook, Message, Submission, Task+Submission relations, AttendanceSetting) with realistic, varied demo/test data, on top of the existing Admin/Supervisor/Student/Directorate/Position/University seeders — so every page in the app has something to show instead of empty states, without needing to click through the UI manually to create test data.

## Non-goals

- No change to volume of Users/Students/Supervisors — reuse the 3 students (Rifqy, Devin, Nibras) and 2 supervisors (Dede, Karma) already created by `StudentSeeder`/`SupervisorSeeder`.
- No change to `reseed.ps1`/`reseed.sh` (local-only, not committed) or to the existing `StudentSupervisorAssignmentSeeder`'s single hardcoded assignment — it's extended, not replaced.
- No UI/controller changes — this is purely `database/factories/` + `database/seeders/` + two small dead-code removals in models (see below).

## 0. Dead-code cleanup (small, bundled with this work)

Two relationships found during schema exploration don't correspond to any real column and aren't used anywhere in the codebase — removed as part of this change since they'd otherwise confuse anyone writing a `LogbookFactory`/`DocumentFactory` who might reasonably try to use them:
- `Logbook::user()` — `belongsTo(User::class)`, but `logbooks` has no `user_id` column.
- `Document::documents()` — self-referential `hasMany(Document::class)`, no supporting column.

## 1. Supervisor backfill

**File:** extend `database/seeders/StudentSupervisorAssignmentSeeder.php`

Currently only assigns Rifqy→Dede explicitly. Add a backfill step that runs after the explicit assignment: find every `Student` row with a null `supervisor_id`, and round-robin assign them across all existing `Supervisor` rows (so Devin and Nibras each get a supervisor, deterministically, without hardcoding which one — resilient to future supervisor count changes).

## 2. AttendanceSetting

**File:** new `database/seeders/AttendanceSettingSeeder.php`

Just calls `AttendanceSetting::getSettings()` — the model's existing static helper already creates a default row if none exists, so this seeder's only job is to guarantee that call happens during a fresh seed (rather than lazily on first page load), keeping seed output deterministic.

## 3. Attendance (90 working days, with suspicious cases)

**Files:** new `database/factories/AttendanceFactory.php`, new `database/seeders/AttendanceSeeder.php`

For each Student's `user_id`, generate one `Attendance` row per weekday (Mon-Fri) over the last 90 calendar days (~64 working days), respecting the `unique(['user_id', 'date'])` constraint (one row per date, never duplicated).

Status distribution per row: mostly `present`, some `late`, occasional `absent` (absent rows have null check_in/check_out). For non-absent rows, `check_in`/`check_out` timestamps are set consistent with `AttendanceSetting`'s work hours (with some rows deliberately late to justify `status = late`).

**Suspicious cases (deliberate):** ~5-8% of non-absent rows across all students are flagged: `requires_manual_review = true`, with either:
- `face_verification_status = 'mismatch'` + a `face_match_distance` above the 0.6 threshold, or
- `location_verification_status = 'flagged'` + a non-empty `location_notes` explaining why (e.g., "Lokasi di luar radius kantor").

This ensures the Suspicious pages and the Monitoring page's "Mencurigakan" counter (built earlier this session) have real rows to display, not an empty state.

## 4. AttendanceException

**Files:** new `database/factories/AttendanceExceptionFactory.php`, new `database/seeders/AttendanceExceptionSeeder.php`

2-4 exceptions per student, spread across the same 90-day window, on dates that don't already have an `Attendance` row (an exception day is a day off, not a worked day). Mix of `type` (sick/leave/permit/official) and `status` (pending/approved/rejected) so the Admin/Supervisor Approvals pages have a mix of decided and pending items.

## 5. Task + Submission

**Files:** modify `database/seeders/TaskSeeder.php` (register it, and extend to attach students), new `database/factories/SubmissionFactory.php`, new `database/seeders/SubmissionSeeder.php`

`TaskSeeder` currently exists but isn't registered in `DatabaseSeeder`, and its factory never attaches students to the pivot table. Extend it: for each Supervisor, create 3-4 tasks (mix of `type` harian/akhir), then attach ALL of that supervisor's students to each task via the `task_student` pivot (`$task->students()->attach($studentIds)`).

`SubmissionSeeder` then iterates every (task, student) pair from the pivot and, for variety, splits into three buckets:
- ~30%: no Submission row at all (student hasn't submitted yet)
- ~30%: Submission exists with `content`/`file_path` but `grade` is null (submitted, awaiting grading)
- ~40%: Submission graded, with a `grade` (mix of numeric 60-100 and letter A/A-/B+/B/B-/C values matching the grade-classification logic already in the PDF templates) and a `comments` string

## 6. FinalAssessment

**Files:** new `database/factories/FinalAssessmentFactory.php`, new `database/seeders/FinalAssessmentSeeder.php`

For each Student, one of three states:
- No `FinalAssessment` row at all (~30% — assessment not started)
- `FinalAssessment` with `final_grade`/`overall_comments` but `certificate_generated_at` null (~40% — graded, certificate not yet generated)
- Full `FinalAssessment` with `certificate_generated_at` set (~30% — certificate already generated, exercises the "already generated" branch in `FinalAssessmentController::generateCertificate()`)

## 7. Document

**Files:** new `database/factories/DocumentFactory.php`, new `database/seeders/DocumentSeeder.php`

Per Student: always a `proposal` document; `laporan_akhir` only for students who also have a graded/certificate-stage `FinalAssessment` from step 6 (keeps the two features' data mutually consistent, matching `Student/Info/Edit.jsx`'s progress tracker logic which checks for both). Occasional extra `lainnya` document. `file_path` values point at placeholder paths (no real files are written to storage — the seeder only creates DB rows, consistent with how `check_in_photo`/`check_out_photo` etc. work elsewhere when demo-seeded).

## 8. Logbook

**Files:** new `database/factories/LogbookFactory.php`, new `database/seeders/LogbookSeeder.php`

Per Student: one Logbook entry per weekday over the last 2-3 weeks (a smaller, more recent window than Attendance's 90 days, since logbooks are typically filled more sporadically than daily attendance). Mix of `is_verified` true/false so the Supervisor Logbooks page's "Telah Dilihat"/"Belum Dilihat" stat cards both have non-zero counts.

## 9. Message

**Files:** new `database/factories/MessageFactory.php`, new `database/seeders/MessageSeeder.php`

A handful of messages per student↔their-supervisor pair, in both directions (student→supervisor and supervisor→student), mixed `is_read` state (unread messages have `read_at = null`).

## 10. Announcement

**Files:** new `database/factories/AnnouncementFactory.php`, new `database/seeders/AnnouncementSeeder.php`

5-6 announcements from the Admin user, mixed `priority`, mostly `published_at` set (a couple recent, a couple older) plus one or two with `published_at = null` (drafts) so the Admin Announcements page shows both published and draft states.

## Ordering in DatabaseSeeder.php

```
AdminSeeder
DirectorateSeeder
PositionSeeder
SupervisorSeeder
StudentSeeder
StudentSupervisorAssignmentSeeder   (now includes the round-robin backfill)
UniversitySeeder
AttendanceSettingSeeder
AttendanceSeeder
AttendanceExceptionSeeder
TaskSeeder                          (now attaches students + is registered)
SubmissionSeeder
FinalAssessmentSeeder
DocumentSeeder
LogbookSeeder
MessageSeeder
AnnouncementSeeder
```

Each new seeder assumes all Students/Supervisors already have their `supervisor_id` assignments resolved (i.e., runs after `StudentSupervisorAssignmentSeeder`), since Task/Submission/FinalAssessment/Message generation all need a valid student→supervisor link.

## Error handling

- Every new seeder is idempotent-safe for a fresh `migrate:fresh --seed` (the project's standard reseed flow) — none of them need to handle "row already exists" conflicts, since they always run against an empty table in that flow. No seeder assumes partial prior state.
- `AttendanceSeeder` respects the `unique(['user_id','date'])` constraint by construction (one row generated per date per student, no overlap) rather than catching a constraint-violation exception.

## Testing approach

No automated test framework in this repo (consistent with every other feature this session). Verification: run `php artisan migrate:fresh --seed`, confirm it completes without errors, then spot-check row counts per table via `php artisan tinker` and visually check a sample of previously-empty pages (Attendance history, Suspicious, Tasks, Submissions, Final Assessment, Documents, Logbooks, Messages, Announcements) to confirm they now show data instead of empty states.
