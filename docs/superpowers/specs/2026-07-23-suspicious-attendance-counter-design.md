# Suspicious Attendance Counter — Design

## Goal

Add a "Mencurigakan" (suspicious) counter to the Monitoring Absensi pages (both Admin and Supervisor), so admins/supervisors can see at a glance how many attendances on the currently-viewed date need manual review — without having to navigate to the separate Suspicious page. The counter must automatically decrease once a suspicious attendance is approved/rejected.

## Non-goals

- No new value added to the `attendances.status` enum (`present`/`late`/`absent`/`pending`) — "suspicious" stays represented purely by the existing `requires_manual_review` boolean flag, not a status value. Changing the enum's meaning would ripple into Reports, CSV export, and existing status filters, none of which this feature needs to touch.
- No change to the existing Suspicious.jsx pages (Admin/Supervisor) — they remain the place to actually review/approve suspicious attendances. This feature only adds visibility on the Monitoring page.
- No change to `reviewSuspiciousAttendance()`'s approval logic — it already sets `requires_manual_review = false` on approve/reject, which is sufficient for the counter to self-correct.

## 1. Backend — add a `suspicious` count to both controllers' stats

**Files:** `app/Http/Controllers/Admin/AttendanceController.php`, `app/Http/Controllers/Supervisor/AttendanceController.php`

Both controllers already build a `$stats` array scoped to the currently-filtered `$date` (Admin: unscoped by student; Supervisor: scoped to the supervisor's own students via `scopeToSupervisedStudents()`). Add one more key, `suspicious`, counting rows where `requires_manual_review = true` for that same date — using the same base query each controller already uses for its other three counts, so the new count inherits the same date-scoping (and, for Supervisor, the same student-scoping) for free.

## 2. Frontend — a 5th stat card and inline badge

**Files:** `resources/js/Pages/Admin/Attendance/Index.jsx`, `resources/js/Pages/Supervisor/Attendance/Index.jsx`

- Add a fifth `StatCard` ("Mencurigakan", `stats.suspicious`, an `AlertTriangle` icon, red/orange tone) next to the existing four. The stat grid's `lg:grid-cols-4` becomes `lg:grid-cols-5` so all five sit in one row on large screens (unchanged single/double-column behavior on smaller screens).
- In the attendance table, add a small "Mencurigakan" badge next to the existing present/late/absent status badge whenever `attendance.requires_manual_review` is true, so a suspicious row is visible directly in the table, not just via the aggregate count.

## 3. Counter auto-decreases on approval

No new logic needed. `HandlesAttendanceActions::reviewSuspiciousAttendance()` (used by both Admin and Supervisor Suspicious pages) already does `$attendance->update(['requires_manual_review' => false, ...])` on approve/reject. Since the new `suspicious` stat is computed live from `where('requires_manual_review', true)` on every page load, the count reflects the change immediately after the review action's redirect completes — no caching, no separate decrement step.

## Error handling

None beyond what already exists — this is a read-only count added to an existing read-only stats block, with no new user input or failure mode.

## Testing approach

No automated test framework exists in this repo (consistent with every other feature built in this session) — verification is: `php -l` on both controllers, `npm run build`, and a manual check that (a) the counter matches the number of rows flagged `requires_manual_review = true` for the selected date, (b) approving/rejecting one from the Suspicious page drops the count by one on next visit to Monitoring, and (c) the Supervisor count only reflects that supervisor's own students.
