# Supervisor Attendance — Design Spec

## Background

Supervisors currently have no way to see attendance data for the students
they supervise. There are three attendance surfaces today:

- **Admin** (`app/Http/Controllers/Admin/AttendanceController.php`,
  routes under `admin.attendance.*` at `routes/web.php:112-120`):
  monitoring (all students), approvals, suspicious review, reports +
  CSV export, and global settings. Unscoped — sees everyone.
- **Student** (`app/Http/Controllers/AttendanceController.php`, routes
  `attendance.*` at `routes/web.php:215-219`): check-in/out, own
  history, exception requests. Scoped to `Auth::id()`.
- **Supervisor**: nothing dedicated. `SupervisorLayout.jsx` has no
  Attendance nav section.

### Bug found during investigation

`routes/web.php:188-193` sit **inside** the `supervisor` route group
(`Route::middleware(['auth','role:supervisor'])->prefix('supervisor')
->name('supervisor.')`, opened at line 133, closed at line 197). This
means six routes are live today under paths like
`GET /supervisor/attendance` (route name `supervisor.admin.attendance.index`),
all pointing at `Admin\AttendanceController`, which queries `Attendance::`
with **no scoping to the logged-in supervisor's students** — a
supervisor hitting this URL sees every student's attendance. One of the
six is `admin.attendance.settings` / `.settings.update`, which lets a
supervisor read and overwrite **global** attendance configuration
(work hours, location radius, photo requirement) — a privilege
escalation.

No frontend link points at these routes, so they're not reachable via
normal navigation, but they are live, unscoped, and guessable.

**Decision (confirmed with user): delete these 6 lines.** They are
replaced entirely by the new, properly-scoped `supervisor.attendance.*`
routes below. This is a bug fix bundled with the feature build, not
scope creep — the routes being deleted were never wired to any UI and
were never part of a finished feature.

`Admin\AttendanceController@approve` and `@reviewSuspicious` already
carry a supervisor-role authorization check
(`if ($user->role === 'supervisor' && $item->user->student?->supervisor_id !== $user->id) abort(403)`,
lines 73 and 87). This logic is correct and will be reused by having
the new supervisor endpoints delegate to the same controller methods
rather than duplicating the approve/reject logic (see Architecture).

## Goal

Supervisors can, for students they supervise only:

1. View today's (or any date's) attendance status — who checked in,
   who's late, who's absent (**Index**).
2. View and act on pending attendance + exception-request approvals
   (**Approvals** — approve/reject, reusing existing controller logic).
3. View attendance flagged as suspicious (anomalous location/photo)
   and resolve the flag (**Suspicious** — approve/reject, reusing
   existing controller logic).
4. View a monthly summary report per student, with CSV export scoped
   to their own students (**Reports**).

Global attendance **Settings** stays admin-only — not built for
supervisors, and the leaked routes granting it are removed.

## Non-goals

- Changing anything in `Admin\AttendanceController` or the Admin
  Attendance pages' behavior, routes, or page components — those keep
  their exact current routes/names. Admin's `ApprovalModal.jsx` moves
  location (see Architecture) but its behavior and API are unchanged;
  admin pages update their import path only.
- Changing the Student attendance check-in/out flow.
- Adding new database columns, migrations, or changing `Attendance`,
  `AttendanceException`, or `AttendanceSetting` models.
- Real-time updates/notifications when a student checks in.
- Supervisor ability to edit global settings (explicitly excluded —
  this is the privilege escalation being closed).

## Architecture

### Backend

**New controller**: `app/Http/Controllers/Supervisor/AttendanceController.php`

All methods scope queries through the student relationship:
`whereHas('user.student', fn ($q) => $q->where('supervisor_id', Auth::user()->supervisor->id))`.

| Method | Behavior |
|---|---|
| `index(Request $request)` | Same shape as `Admin\AttendanceController@index` (date + status filter, paginated, stats), scoped to supervisor's students. Renders `Supervisor/Attendance/Index`. |
| `approvals()` | Same shape as `Admin\AttendanceController@approvals`, scoped. Renders `Supervisor/Attendance/Approvals`. |
| `approve(Request $request, $type, $id)` | **Delegates to** `Admin\AttendanceController::approve()` by calling it directly (inject/instantiate `Admin\AttendanceController` and call the method, or extract the shared body into a trait — see Implementation note below). The existing method already authorizes correctly for supervisors and updates the same models; no new logic needed, only reachable at a new route name. |
| `suspicious()` | Same shape as `Admin\AttendanceController@suspicious`, scoped via the same `whereHas`. Renders `Supervisor/Attendance/Suspicious`. |
| `reviewSuspicious(Request $request, Attendance $attendance)` | Delegates to `Admin\AttendanceController::reviewSuspicious()` the same way as `approve()` — the target `Attendance` row's ownership must additionally be checked against the supervisor's students before delegating (that method itself has no supervisor-ownership guard, unlike `approve()` — this endpoint adds one). |
| `reports(Request $request)` | Same shape as `Admin\AttendanceController@reports`, scoped: student list (`$users`) becomes `Auth::user()->supervisor->students` instead of `User::where('role','student')`. |
| `exportCsv(Request $request)` | Same shape as `Admin\AttendanceController@exportCsv`, scoped query. Reuses the same private `exportDetailedCsv`/`exportSummaryCsv` helpers — call them via the parent controller instance, or extract to a shared trait (see Implementation note). |

**Implementation note on code reuse**: rather than copy-pasting the
~250 lines of CSV export and approve/review logic, extract the
**non-Inertia-response** parts of `Admin\AttendanceController`
(`approve`, `reviewSuspicious`'s update logic, `exportDetailedCsv`,
`exportSummaryCsv`) into a trait,
`app/Http/Controllers/Concerns/HandlesAttendanceActions.php`, used by
both `Admin\AttendanceController` and the new
`Supervisor\AttendanceController`. The Inertia-rendering methods
(`index`, `approvals`, `suspicious`, `reports`) stay separate per
controller because their query scoping and view paths differ, but they
share the trait's helpers where the row-shaping logic is identical
(e.g. the `$attendances->groupBy('user_id')->map(...)` summary-building
closure in `reports()`).

**Routes** — new block in `routes/web.php`, inside the existing
`supervisor` group (replacing the deleted leaked lines):

```php
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [SupervisorAttendanceController::class, 'index'])->name('index');
    Route::get('/approvals', [SupervisorAttendanceController::class, 'approvals'])->name('approvals');
    Route::post('/approve/{type}/{id}', [SupervisorAttendanceController::class, 'approve'])->name('approve');
    Route::get('/suspicious', [SupervisorAttendanceController::class, 'suspicious'])->name('suspicious');
    Route::post('/suspicious/{attendance}/review', [SupervisorAttendanceController::class, 'reviewSuspicious'])->name('suspicious.review');
    Route::get('/reports', [SupervisorAttendanceController::class, 'reports'])->name('reports');
    Route::get('/export-csv', [SupervisorAttendanceController::class, 'exportCsv'])->name('export-csv');
});
```

Resulting route names: `supervisor.attendance.index`,
`supervisor.attendance.approvals`, `supervisor.attendance.approve`,
`supervisor.attendance.suspicious`, `supervisor.attendance.suspicious.review`,
`supervisor.attendance.reports`, `supervisor.attendance.export-csv`.

### Frontend

**New pages** in `resources/js/Pages/Supervisor/Attendance/`:
`Index.jsx`, `Approvals.jsx`, `Suspicious.jsx`, `Reports.jsx` — each a
straight port of the matching `Admin/Attendance/*` page:

- Same layout patterns (`PageHeader`, `StatCard`, `ui/table`,
  `EmptyState`, `UserCell`, `Pagination`) already established in
  `docs/DESIGN_SYSTEM.md`.
- Same status/priority badge conventions.
- Route helper calls (`r('...')`) updated to the new
  `supervisor.attendance.*` names.
- No admin-only affordances (no Settings link, no "manage all
  students" framing) — copy adjusted to reflect "mahasiswa bimbingan
  Anda" scope where the admin copy says "semua mahasiswa" or similar.
- Approvals/Suspicious pages read the new shared modal (see below)
  instead of a locally co-located one.

**Shared component**: `resources/js/Components/ApprovalModal.jsx`,
moved from `resources/js/Pages/Admin/Attendance/ApprovalModal.jsx`
verbatim (no behavior change — same props: `open`, `onClose`, `type`,
`item`, `notesRequired`). Both `Admin/Attendance/Approvals.jsx`,
`Admin/Attendance/Suspicious.jsx`, `Admin/Attendance/Index.jsx`, and
the four new Supervisor pages import from
`@/Components/ApprovalModal`. The modal's internal `r(...)` route
lookups for submission
(`type === 'suspicious' ? r('admin.attendance.suspicious.review', ...) : r('admin.attendance.approve', ...)`)
must become **role-aware**: add a required `routePrefix` prop
(e.g. `'admin'` or `'supervisor'`) so the modal builds
`r('${routePrefix}.attendance.suspicious.review', item.id)` etc. Every
call site passes its own prefix explicitly.

**Sidebar**: `resources/js/Layouts/SupervisorLayout.jsx` gains a new
nav section `ATTENDANCE` (matching the section-header pattern already
used for `BIMBINGAN`, `PENILAIAN`, etc.), with four items: Kehadiran
(`supervisor.attendance.index`), Approval
(`supervisor.attendance.approvals`), Laporan
(`supervisor.attendance.reports`), Mencurigakan
(`supervisor.attendance.suspicious`). Positioned after the existing
`BIMBINGAN` section, before `PENILAIAN`, matching where Attendance
sits in `AdminLayout.jsx`'s ordering relative to other sections.

## Data flow

1. Supervisor visits `/supervisor/attendance` →
   `Supervisor\AttendanceController::index()` → query
   `Attendance::whereHas('user.student', fn($q) => $q->where('supervisor_id', $supervisorId))`
   filtered by date/status → paginate → Inertia render
   `Supervisor/Attendance/Index` with `attendances`, `stats`, `date`, `status`.
2. Clicking "Detail" on a row opens `ApprovalModal` with
   `routePrefix="supervisor"`; submitting posts to
   `supervisor.attendance.approve` → `Supervisor\AttendanceController::approve()`
   → delegates to the shared trait method → same `Attendance`/`AttendanceException`
   update as admin, `approved_by` = the supervisor's user id.
3. Reports page: supervisor picks month/year/student (student dropdown
   populated from `Auth::user()->supervisor->students` only) → same
   grouping/summary logic as admin, scoped query → CSV export endpoint
   re-applies the same scope so a supervisor cannot export another
   supervisor's students by manipulating the `user_id` query param (the
   scope is applied via the `whereHas` regardless of `user_id` filter
   value — an id belonging to another supervisor's student, if passed,
   returns no rows).

## Error handling

- Any `$id`/`Attendance $attendance` route-model-bound to a student not
  supervised by the current user → `403` (existing `abort(403, ...)`
  pattern in `approve()`; the same guard is added explicitly to
  `reviewSuspicious()` since the trait extraction must not lose it).
- A supervisor with no assigned students sees the existing
  `EmptyState` pattern on Index/Approvals/Suspicious ("Belum ada data
  absensi" / "Tidak ada absensi pending" / "Tidak ada kehadiran
  mencurigakan"), not an error — an empty scoped query is a valid
  state.
- CSV export with zero matching rows still streams a valid CSV with
  just headers (matches existing admin behavior, unchanged).

## Testing

Manual verification only (per project convention observed in this
redesign — no PHPUnit test suite has been touched or added elsewhere
in the redesign work):

1. `php artisan route:list --name=supervisor.attendance` shows the 7
   new routes; `php artisan route:list --name=supervisor.admin`
   returns nothing (leaked routes gone).
2. Log in as the supervisor test account (`dede@baktitest.com` / `1`),
   confirm the sidebar shows the new ATTENDANCE section.
3. Visit each of the 4 pages, confirm only that supervisor's own
   students' attendance appears (cross-check against
   `php artisan tinker` query of `Supervisor::find(...)->students`).
4. Approve/reject one pending attendance and one pending exception via
   the modal; confirm the update lands and the row leaves the pending
   list.
5. Attempt (via a spoofed request or `tinker`-created record) to
   approve/review an item belonging to a student **not** supervised by
   the logged-in account → confirm `403`.
6. Export CSV, confirm only the logged-in supervisor's students appear
   in the file.
7. Confirm `/admin/attendance/*` pages and behavior are pixel-identical
   to before (no regression from the `ApprovalModal` relocation).
8. Light + dark mode, 375/768/1440 viewports on all 4 new pages, per
   `docs/DESIGN_SYSTEM.md` Definition of Done.

## Open items resolved during brainstorming

- Scope: view + approve/reject (not read-only).
- Sub-features included: Index, Approvals, Reports, Suspicious.
  Settings excluded (stays admin-only; leaked access removed).
- CSV export: included, scoped to supervisor's students.
- `ApprovalModal`: extracted to `Components/ApprovalModal.jsx`, shared
  by Admin and Supervisor (admin's import path changes as a side
  effect; behavior does not).
- Sidebar: new top-level `ATTENDANCE` section, matching Admin's pattern.
