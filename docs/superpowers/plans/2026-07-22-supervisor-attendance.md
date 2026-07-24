# Supervisor Attendance Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give supervisors a scoped view of their own students' attendance (monitoring, approvals, suspicious-flag review, monthly reports + CSV export), while closing an existing authorization leak that lets any supervisor read/write global attendance data through unscoped routes.

**Architecture:** Extract the reusable, non-Inertia parts of `Admin\AttendanceController` (approve/reject logic, suspicious-review logic, CSV row builders) into a shared trait `Concerns\HandlesAttendanceActions`. Build a new `Supervisor\AttendanceController` that uses the trait and scopes every query to `Auth::user()->supervisor->students`. Port the four Admin Attendance React pages into `Pages/Supervisor/Attendance/`, extract the shared `ApprovalModal` component so both roles use one implementation, and add a new sidebar section. Delete the six leaked routes.

**Tech Stack:** Laravel 12 (PHP), Eloquent, Inertia.js v3, React 19, Tailwind v3, shadcn/ui (existing components only — no new ones needed).

---

## Task 0: Baseline check — confirm the leak and current behavior

**Files:** none (read-only verification task)

- [ ] **Step 1: Confirm the leaked routes exist today**

Run: `php artisan route:list --name=supervisor.admin.attendance`

Expected output: 5 rows, including `GET|HEAD supervisor/attendance ... supervisor.admin.attendance.index` and `GET|HEAD supervisor/attendance/settings ... supervisor.admin.attendance.settings`. This confirms the bug described in the spec is real before you touch anything.

- [ ] **Step 2: Confirm no frontend page currently links to these leaked routes**

Run (from repo root, Bash):
```bash
grep -rn "admin.attendance" resources/js/Layouts/SupervisorLayout.jsx
```
Expected output: no matches (empty). This confirms removing the leaked routes breaks no currently-reachable UI.

- [ ] **Step 3: Note the test accounts you'll use**

From `docs/UI_CONTEXT_FOR_AI.md` §8:
- Supervisor: `dede@baktitest.com` / password `1`
- Admin: `admin@bakti.com` / password `1`

Run `php artisan tinker --execute="echo App\Models\Supervisor::whereHas('user', fn(\$q) => \$q->where('email','dede@baktitest.com'))->first()->students()->count();"` to confirm this supervisor has at least one supervised student (needed for later manual verification). If it prints `0`, note it — Task 12's manual steps will need a different account or a seeded student.

No commit for this task — it's pure verification.

---

## Task 1: Extract shared attendance-action trait

**Files:**
- Create: `app/Http/Controllers/Concerns/HandlesAttendanceActions.php`
- Modify: `app/Http/Controllers/Admin/AttendanceController.php`

This trait holds the four methods that are identical in behavior for admin and supervisor: `approve()`, `reviewSuspicious()`, and the two private CSV row-builders. Extracting first (before writing the new controller) means the new controller can `use` the trait from day one instead of duplicating ~150 lines.

- [ ] **Step 1: Create the trait with the extracted methods**

Create `app/Http/Controllers/Concerns/HandlesAttendanceActions.php`:

```php
<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Attendance;
use App\Models\AttendanceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Shared attendance approve/review/export logic used by both
 * Admin\AttendanceController (unscoped) and Supervisor\AttendanceController
 * (scoped to the supervisor's own students). Each caller is responsible
 * for authorization/scoping *before* calling these methods — the trait
 * itself only performs the ownership check already present in
 * approveAttendanceOrException() (supervisor-role guard) and adds the
 * same guard to reviewSuspiciousAttendance(), which previously had none.
 */
trait HandlesAttendanceActions
{
    protected function approveAttendanceOrException(Request $request, string $type, int $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if ($type === 'attendance') {
            $item = Attendance::with('user.student')->findOrFail($id);

            if ($user->role === 'supervisor' && $item->user->student?->supervisor_id !== $user->id) {
                abort(403, 'Anda tidak berhak mengapprove attendance student ini.');
            }

            $item->update([
                'supervisor_approval' => $request->action === 'approve' ? 'approved' : 'rejected',
                'supervisor_notes' => $request->notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        } else {
            $item = AttendanceException::with('user.student')->findOrFail($id);

            if ($user->role === 'supervisor' && $item->user->student?->supervisor_id !== $user->id) {
                abort(403, 'Anda tidak berhak mengapprove exception student ini.');
            }

            $item->update([
                'status' => $request->action === 'approve' ? 'approved' : 'rejected',
                'supervisor_notes' => $request->notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        }

        $message = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Item berhasil {$message}!");
    }

    protected function reviewSuspiciousAttendance(Request $request, Attendance $attendance)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $attendance->loadMissing('user.student');

        if ($user->role === 'supervisor' && $attendance->user->student?->supervisor_id !== $user->id) {
            abort(403, 'Anda tidak berhak me-review attendance student ini.');
        }

        $attendance->update([
            'requires_manual_review' => false,
            'location_verification_status' => $request->action === 'approve' ? 'verified' : 'flagged',
            'location_notes' => $request->notes,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $message = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Kehadiran berhasil di-review dan {$message}!");
    }

    protected function exportDetailedCsv($handle, $attendances): void
    {
        $headers = [
            'Tanggal', 'Nama Mahasiswa', 'Nim', 'Pembimbing', 'Direktorat',
            'Waktu Masuk', 'Waktu Keluar', 'Jam Kerja', 'Status', 'Lokasi', 'Catatan',
        ];

        fputcsv($handle, $headers);

        foreach ($attendances as $attendance) {
            $checkInTime = $attendance->check_in_time
                ? Carbon::parse($attendance->check_in_time)->format('H:i:s')
                : '-';
            $checkOutTime = $attendance->check_out_time
                ? Carbon::parse($attendance->check_out_time)->format('H:i:s')
                : '-';

            fputcsv($handle, [
                Carbon::parse($attendance->date)->format('d-m-Y'),
                $attendance->user->name,
                $attendance->user->student->nim ?? '-',
                $attendance->user->student?->supervisor?->user->name ?? '-',
                $attendance->user->student?->supervisor?->direktorat ?? '-',
                $checkInTime,
                $checkOutTime,
                $attendance->working_hours ?? '-',
                ucfirst($attendance->status),
                $attendance->location_name ?? '-',
                $attendance->location_notes ?? '-',
            ]);
        }
    }

    protected function exportSummaryCsv($handle, $attendances): void
    {
        $headers = [
            'Nama Mahasiswa', 'NIM', 'Pembimbing', 'Total Hari Kerja',
            'Hadir', 'Terlambat', 'Tidak Hadir', 'Rata-rata Jam Kerja',
        ];

        fputcsv($handle, $headers);

        $summary = $attendances->groupBy('user_id')->map(function ($userAttendances) {
            $totalHours = 0;
            $countWithHours = 0;

            foreach ($userAttendances->where('check_out_time', '!=', null) as $att) {
                $totalHours += $att->working_hours ?? 0;
                $countWithHours++;
            }

            $avgHours = $countWithHours > 0 ? round($totalHours / $countWithHours, 2) : 0;

            return [
                'user' => $userAttendances->first()->user,
                'total_days' => $userAttendances->count(),
                'present' => $userAttendances->where('status', 'present')->count(),
                'late' => $userAttendances->where('status', 'late')->count(),
                'absent' => $userAttendances->where('status', 'absent')->count(),
                'avg_hours' => $avgHours,
            ];
        });

        foreach ($summary as $item) {
            fputcsv($handle, [
                $item['user']->name,
                $item['user']->student->nim ?? '-',
                $item['user']->student?->supervisor?->user->name ?? '-',
                $item['total_days'],
                $item['present'],
                $item['late'],
                $item['absent'],
                $item['avg_hours'] . ' jam',
            ]);
        }
    }
}
```

- [ ] **Step 2: Verify the trait file has no syntax errors**

Run: `php -l app/Http/Controllers/Concerns/HandlesAttendanceActions.php`
Expected output: `No syntax errors detected in app/Http/Controllers/Concerns/HandlesAttendanceActions.php`

- [ ] **Step 3: Update `Admin\AttendanceController` to use the trait instead of its own copies**

In `app/Http/Controllers/Admin/AttendanceController.php`, add the `use` import and trait declaration, then delete the four method bodies that now live in the trait, replacing each call site with a call to the trait method.

Add after the existing `use` statements (near the top, alongside `use App\Models\Attendance;` etc.):
```php
use App\Http\Controllers\Concerns\HandlesAttendanceActions;
```

Add the trait to the class body, immediately after `class AttendanceController extends Controller` `{`:
```php
class AttendanceController extends Controller
{
    use HandlesAttendanceActions;

```

Replace the entire `approve()` method body:
```php
    public function approve(Request $request, $type, $id)
    {
        return $this->approveAttendanceOrException($request, $type, (int) $id);
    }
```

Replace the entire `reviewSuspicious()` method body:
```php
    public function reviewSuspicious(Request $request, Attendance $attendance)
    {
        return $this->reviewSuspiciousAttendance($request, $attendance);
    }
```

Delete the private `exportDetailedCsv()` and `exportSummaryCsv()` method bodies entirely from this file — they now come from the trait and are called via `$this->exportDetailedCsv(...)` / `$this->exportSummaryCsv(...)`, which is exactly how `exportCsv()` already calls them, so **no change is needed to `exportCsv()` itself**.

- [ ] **Step 4: Verify the modified controller has no syntax errors**

Run: `php -l app/Http/Controllers/Admin/AttendanceController.php`
Expected output: `No syntax errors detected in app/Http/Controllers/Admin/AttendanceController.php`

- [ ] **Step 5: Manually verify admin attendance approve still works (no automated test suite exists in this project)**

Run: `php artisan serve --port=8000` (in background/separate terminal), then:
1. Log in as `admin@bakti.com` / `1`.
2. Visit `/admin/attendance/approvals`.
3. If there is at least one pending attendance or exception, click "Detail", choose "Setujui", submit.
4. Confirm the item disappears from the pending list and a success flash message appears.

If there are no pending items to test with, skip to Step 6 and note this needs re-verification once test data exists (Task 12 covers full manual verification anyway).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Concerns/HandlesAttendanceActions.php app/Http/Controllers/Admin/AttendanceController.php
git commit -m "refactor: Extract attendance approve/review/CSV logic into shared trait

Prepares for Supervisor\AttendanceController reusing the same
approve/reject and suspicious-review logic without duplicating it.
No behavior change to Admin attendance endpoints."
```

---

## Task 2: Remove the leaked supervisor routes

**Files:**
- Modify: `routes/web.php:188-193`

- [ ] **Step 1: Delete the six leaked route lines**

In `routes/web.php`, inside the `Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () { ... })` block, delete these lines (currently at 188-193, immediately after the `pdf.certificate.download` route and before `students.documents`):

```php
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/attendance/approvals', [AdminAttendanceController::class, 'approvals'])->name('admin.attendance.approvals');
    Route::post('/attendance/approve/{type}/{id}', [AdminAttendanceController::class, 'approve'])->name('admin.attendance.approve');
    Route::get('/attendance/reports', [AdminAttendanceController::class, 'reports'])->name('admin.attendance.reports');
    Route::get('/attendance/settings', [AdminAttendanceController::class, 'settings'])->name('admin.attendance.settings');
    Route::post('/attendance/settings', [AdminAttendanceController::class, 'updateSettings'])->name('admin.attendance.settings.update');
```

Leave the surrounding lines (`pdf.certificate.generate`/`download` above, `students.documents`/`documents.download` below) untouched — only these six lines are removed. Do not add the replacement routes yet — that's Task 4, after the controller exists.

- [ ] **Step 2: Verify the leaked routes are gone**

Run: `php artisan route:list --name=supervisor.admin.attendance`
Expected output: empty (no rows) — Laravel prints nothing when no route matches the `--name` filter.

- [ ] **Step 3: Verify the file still parses**

Run: `php -l routes/web.php`
Expected output: `No syntax errors detected in routes/web.php`

- [ ] **Step 4: Commit**

```bash
git add routes/web.php
git commit -m "fix: Remove leaked unscoped attendance routes from supervisor group

routes/web.php:188-193 were nested inside the supervisor route group,
exposing AdminAttendanceController (all students' data, plus global
attendance settings) to any supervisor without scoping to their own
students. No frontend linked these routes, but they were live and
guessable. Replacement, properly-scoped routes land in a later commit."
```

---

## Task 3: Build `Supervisor\AttendanceController`

**Files:**
- Create: `app/Http/Controllers/Supervisor/AttendanceController.php`

This controller mirrors `Admin\AttendanceController`'s four read methods (`index`, `approvals`, `suspicious`, `reports`) plus `exportCsv`, each scoped via `whereHas('user.student', fn ($q) => $q->where('supervisor_id', ...))`. It reuses the trait from Task 1 for `approve`/`reviewSuspicious`/CSV row-building.

- [ ] **Step 1: Write the controller**

Create `app/Http/Controllers/Supervisor/AttendanceController.php`:

```php
<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesAttendanceActions;
use App\Models\Attendance;
use App\Models\AttendanceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    use HandlesAttendanceActions;

    /**
     * Scope any Attendance/AttendanceException query builder to only the
     * students supervised by the currently logged-in supervisor.
     */
    private function scopeToSupervisedStudents($query)
    {
        $supervisorId = Auth::user()->supervisor->id;

        return $query->whereHas('user.student', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        });
    }

    public function index(Request $request): Response
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $status = $request->get('status');

        $query = $this->scopeToSupervisedStudents(Attendance::with('user')->where('date', $date));

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(20)->withQueryString();

        $baseStatsQuery = fn () => $this->scopeToSupervisedStudents(Attendance::query())->where('date', $date);

        $stats = [
            'total' => $baseStatsQuery()->count(),
            'present' => $baseStatsQuery()->where('status', 'present')->count(),
            'late' => $baseStatsQuery()->where('status', 'late')->count(),
            'absent' => $baseStatsQuery()->where('status', 'absent')->count(),
        ];

        return Inertia::render('Supervisor/Attendance/Index', compact('attendances', 'stats', 'date', 'status'));
    }

    public function approvals(): Response
    {
        $pendingAttendances = $this->scopeToSupervisedStudents(
            Attendance::with('user')->where('supervisor_approval', 'pending')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $pendingExceptions = $this->scopeToSupervisedStudents(
            AttendanceException::with('user')->where('status', 'pending')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Supervisor/Attendance/Approvals', compact('pendingAttendances', 'pendingExceptions'));
    }

    public function approve(Request $request, $type, $id)
    {
        // Ownership is enforced inside approveAttendanceOrException() via the
        // existing supervisor-role guard (it checks $item->user->student->supervisor_id
        // against Auth::id() — note this compares to the User id, matching the
        // guard already shipped in Admin\AttendanceController@approve).
        return $this->approveAttendanceOrException($request, $type, (int) $id);
    }

    public function suspicious(): Response
    {
        $suspiciousAttendances = $this->scopeToSupervisedStudents(
            Attendance::where('requires_manual_review', true)->with('user')
        )->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Supervisor/Attendance/Suspicious', compact('suspiciousAttendances'));
    }

    public function reviewSuspicious(Request $request, Attendance $attendance)
    {
        // reviewSuspiciousAttendance() carries its own ownership guard
        // (added in Task 1 — this method previously had none in the
        // admin-only original, since only admins could reach it before).
        return $this->reviewSuspiciousAttendance($request, $attendance);
    }

    public function reports(Request $request): Response
    {
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = $this->scopeToSupervisedStudents(
            Attendance::with('user')->whereMonth('date', $month)->whereYear('date', $year)
        );

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();

        $users = Auth::user()->supervisor->students()->with('user')->get()->pluck('user');

        $summary = $attendances->groupBy('user_id')->map(function ($userAttendances) {
            return [
                'user' => $userAttendances->first()->user,
                'total_days' => $userAttendances->count(),
                'present' => $userAttendances->where('status', 'present')->count(),
                'late' => $userAttendances->where('status', 'late')->count(),
                'absent' => $userAttendances->where('status', 'absent')->count(),
                'avg_hours' => $userAttendances->where('check_out', '!=', null)->avg(function ($att) {
                    return $att->working_hours;
                }),
            ];
        })->values();

        return Inertia::render('Supervisor/Attendance/Reports', compact('summary', 'users', 'month', 'year', 'userId'));
    }

    public function exportCsv(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');
        $exportType = $request->get('type', 'detail');

        $query = $this->scopeToSupervisedStudents(
            Attendance::with('user.student.supervisor.user')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
        );

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('date', 'asc')->orderBy('user_id')->get();

        $filename = "attendance_export_" . now()->format('Y-m-d_H-i-s') . ".csv";

        $response = new StreamedResponse(function () use ($attendances, $exportType) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($exportType === 'detail') {
                $this->exportDetailedCsv($handle, $attendances);
            } else {
                $this->exportSummaryCsv($handle, $attendances);
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
```

**Note on the `users` field shape in `reports()`**: `Auth::user()->supervisor->students()->with('user')->get()->pluck('user')` returns a collection of `User` models, matching what `Admin\AttendanceController::reports()` passes (`User::where('role', 'student')->get()`) — same shape (`Collection<User>`), just pre-filtered. The React page's `users.map((user) => ...user.student?.nim)` (see Task 8) requires each `User` to have its `student` relation loaded for the NIM to render — **this pluck loses the eager-loaded `student` relation** because `pluck('user')` only pulls the `user` accessor off each `Student`, and `User::student()` is the inverse relation, not loaded on that `User` instance. Fix this by loading it explicitly — see Step 2.

- [ ] **Step 2: Fix the `users` relation loading in `reports()`**

Replace this line in the controller you just wrote:
```php
        $users = Auth::user()->supervisor->students()->with('user')->get()->pluck('user');
```
with:
```php
        $users = Auth::user()->supervisor->students()->with('user')->get()->map(function ($student) {
            $user = $student->user;
            $user->setRelation('student', $student);
            return $user;
        });
```

This attaches each `Student` back onto its own `User` as the `student` relation, so `user.student?.nim` resolves correctly in the React page exactly as it does for the admin version's `User::where('role','student')->get()` (which lazy-loads `student` on access, since the model has that relation defined).

- [ ] **Step 3: Verify the controller has no syntax errors**

Run: `php -l app/Http/Controllers/Supervisor/AttendanceController.php`
Expected output: `No syntax errors detected in app/Http/Controllers/Supervisor/AttendanceController.php`

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Supervisor/AttendanceController.php
git commit -m "feat: Add Supervisor\AttendanceController scoped to own students

Mirrors Admin\AttendanceController's index/approvals/suspicious/reports/
exportCsv, each scoped via whereHas('user.student', ...supervisor_id).
approve()/reviewSuspicious() delegate to the shared trait from the
previous commit — no duplicated authorization logic."
```

---

## Task 4: Add the new supervisor attendance routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Add the import**

In `routes/web.php`, add this line alongside the other `Supervisor\*` imports (near line 22-28, e.g. right after `use App\Http\Controllers\Supervisor\LogbookController as SupervisorLogbookController;`):

```php
use App\Http\Controllers\Supervisor\AttendanceController as SupervisorAttendanceController;
```

This alias is required because `App\Http\Controllers\AttendanceController` (bare, student-facing) and `App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController` are already imported — a third, un-aliased `AttendanceController` import would collide.

- [ ] **Step 2: Add the route group**

In `routes/web.php`, inside the `supervisor` route group, add this block where the leaked routes used to be (between `pdf.certificate.download` and `students.documents` — i.e., where Task 2 removed the six lines):

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

- [ ] **Step 2: Verify the new routes are registered with the correct names**

Run: `php artisan route:list --name=supervisor.attendance`

Expected output: 7 rows —
```
GET|HEAD   supervisor/attendance ................ supervisor.attendance.index
GET|HEAD   supervisor/attendance/approvals ...... supervisor.attendance.approvals
POST       supervisor/attendance/approve/{type}/{id} supervisor.attendance.approve
GET|HEAD   supervisor/attendance/suspicious ..... supervisor.attendance.suspicious
POST       supervisor/attendance/suspicious/{attendance}/review supervisor.attendance.suspicious.review
GET|HEAD   supervisor/attendance/reports ........ supervisor.attendance.reports
GET|HEAD   supervisor/attendance/export-csv ..... supervisor.attendance.export-csv
```

- [ ] **Step 3: Verify the file still parses and Ziggy regenerates cleanly**

Run: `php -l routes/web.php`
Expected: `No syntax errors detected in routes/web.php`

Run: `php artisan ziggy:generate`
Expected: completes with no error (regenerates `resources/js/ziggy.js` so `window.route('supervisor.attendance.index')` works in the frontend — required before Task 8's pages can resolve these route names).

- [ ] **Step 4: Commit**

```bash
git add routes/web.php resources/js/ziggy.js
git commit -m "feat: Register supervisor.attendance.* routes

7 routes backing the new Supervisor\AttendanceController, replacing
the leaked admin routes removed in the previous commit."
```

---

## Task 5: Extract `ApprovalModal` to a shared component

**Files:**
- Create: `resources/js/Components/ApprovalModal.jsx`
- Delete: `resources/js/Pages/Admin/Attendance/ApprovalModal.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Index.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Approvals.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Suspicious.jsx`

The only behavior change: the modal gains a required `routePrefix` prop so it can build either `admin.attendance.*` or `supervisor.attendance.*` route names. All three admin call sites pass `routePrefix="admin"` — no visual or functional change for admin users.

- [ ] **Step 1: Create the shared component**

Create `resources/js/Components/ApprovalModal.jsx` (identical to the current `Pages/Admin/Attendance/ApprovalModal.jsx`, with the `routePrefix` prop added and the two hardcoded `admin.attendance.*` route lookups parameterized):

```jsx
import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react'
import { CheckCircle2, XCircle, X } from 'lucide-react'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

function DecisionOption({ value, current, onSelect, icon: Icon, label, toneClass }) {
  const selected = current === value
  return (
    <label
      className={cn(
        'flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors duration-150',
        selected ? 'border-primary bg-indigo-50 dark:bg-indigo-500/10' : 'border-border hover:bg-muted',
      )}
    >
      <input
        type="radio"
        name="decision"
        value={value}
        checked={selected}
        onChange={() => onSelect(value)}
        className="accent-[var(--primary)]"
      />
      <Icon className={cn('h-5 w-5', toneClass)} />
      <span className={cn('text-sm font-medium', toneClass)}>{label}</span>
    </label>
  )
}

/**
 * Shared approve/reject modal for both Admin and Supervisor attendance
 * pages. `routePrefix` selects which role's route names to build
 * ('admin' or 'supervisor') — both roles' controllers expose the same
 * route shape (attendance.approve, attendance.suspicious.review).
 */
export default function ApprovalModal({ open, onClose, type, item, notesRequired = false, routePrefix }) {
  const [decision, setDecision] = useState('')
  const { data, setData, post, processing, errors, reset } = useForm({ action: '', notes: '' })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  useEffect(() => {
    if (!open) {
      setDecision('')
      reset()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])

  if (!open || !item) return null

  function handleSubmit(e) {
    e.preventDefault()
    if (!decision) {
      alert('Pilih keputusan terlebih dahulu')
      return
    }
    setData('action', decision)

    const endpoint = type === 'suspicious'
      ? r(`${routePrefix}.attendance.suspicious.review`, item.id)
      : r(`${routePrefix}.attendance.approve`, [item.approvalType, item.id])

    post(endpoint, {
      data: { action: decision, notes: data.notes },
      preserveScroll: true,
      onSuccess: () => onClose(),
    })
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg">
          <div className="border-b border-border px-6 py-4">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h3 className="text-lg font-semibold text-foreground">
                  {type === 'suspicious' ? 'Review Kehadiran Mencurigakan' : 'Detail Persetujuan'}
                </h3>
                <p className="mt-1 text-sm text-muted-foreground">
                  {item.userName} &middot; {item.date}
                </p>
              </div>
              <button
                type="button"
                onClick={onClose}
                aria-label="Tutup"
                className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
              >
                <X className="h-5 w-5" />
              </button>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="space-y-5 px-6 py-5">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Keputusan</label>
              <DecisionOption
                value="approve"
                current={decision}
                onSelect={setDecision}
                icon={CheckCircle2}
                label="Setujui"
                toneClass="text-green-700 dark:text-green-400"
              />
              <DecisionOption
                value="reject"
                current={decision}
                onSelect={setDecision}
                icon={XCircle}
                label="Tolak"
                toneClass="text-destructive"
              />
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">
                Catatan {notesRequired ? '' : '(Opsional)'}
              </label>
              <Textarea
                rows={4}
                value={data.notes}
                onChange={(e) => setData('notes', e.target.value)}
                placeholder="Tambahkan catatan..."
                required={notesRequired}
              />
              {errors.notes && <p className="text-sm text-destructive">{errors.notes}</p>}
            </div>

            <div className="flex gap-2">
              <Button type="button" variant="outline" onClick={onClose} className="flex-1">
                Batal
              </Button>
              <Button type="submit" disabled={processing} className="flex-1">
                Simpan Keputusan
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
```

- [ ] **Step 2: Delete the old co-located modal**

```bash
rm "resources/js/Pages/Admin/Attendance/ApprovalModal.jsx"
```

- [ ] **Step 3: Update `Admin/Attendance/Index.jsx` import and usage**

In `resources/js/Pages/Admin/Attendance/Index.jsx`:

Change:
```jsx
import ApprovalModal from './ApprovalModal'
```
to:
```jsx
import ApprovalModal from '@/Components/ApprovalModal'
```

Change the `<ApprovalModal ... />` usage at the bottom of the file from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
      />
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="admin"
      />
```

- [ ] **Step 4: Update `Admin/Attendance/Approvals.jsx` import and usage**

In `resources/js/Pages/Admin/Attendance/Approvals.jsx`:

Change:
```jsx
import ApprovalModal from './ApprovalModal'
```
to:
```jsx
import ApprovalModal from '@/Components/ApprovalModal'
```

Change the `<ApprovalModal ... />` usage at the bottom of the file from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
      />
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="admin"
      />
```

- [ ] **Step 5: Update `Admin/Attendance/Suspicious.jsx` import and usage**

In `resources/js/Pages/Admin/Attendance/Suspicious.jsx`:

Change:
```jsx
import ApprovalModal from './ApprovalModal'
```
to:
```jsx
import ApprovalModal from '@/Components/ApprovalModal'
```

Change the `<ApprovalModal ... />` usage at the bottom of the file from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
      />
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
        routePrefix="admin"
      />
```

- [ ] **Step 6: Build assets and verify no import errors**

Run: `npm run build`
Expected: build completes with `✓ built in ...`, no module-resolution errors mentioning `ApprovalModal`.

- [ ] **Step 7: Manually verify admin pages still work**

With `php artisan serve --port=8000` running and `public/hot` removed if present (`rm -f public/hot`):
1. Log in as `admin@bakti.com` / `1`.
2. Visit `/admin/attendance`, click "Detail" on any row (or `/admin/attendance/approvals`, or `/admin/attendance/suspicious`) — confirm the modal opens with identical appearance to before.
3. Submit a decision, confirm it posts successfully (same as Task 1 Step 5, now via the shared component).

- [ ] **Step 8: Commit**

```bash
git add resources/js/Components/ApprovalModal.jsx resources/js/Pages/Admin/Attendance/
git commit -m "refactor: Extract ApprovalModal to shared Components/

Was co-located under Pages/Admin/Attendance/, only usable by admin
pages. Now takes a routePrefix prop ('admin' | 'supervisor') so the
upcoming Supervisor attendance pages can reuse it verbatim. No visual
or behavioral change for admin users — routePrefix='admin' everywhere
it's already used."
```

---

## Task 6: Add ATTENDANCE section to Supervisor sidebar

**Files:**
- Modify: `resources/js/Layouts/SupervisorLayout.jsx`

- [ ] **Step 1: Add the new icons and nav section**

In `resources/js/Layouts/SupervisorLayout.jsx`, update the `lucide-react` import to add four icons:

Change:
```jsx
import {
  LayoutDashboard,
  ClipboardPlus,
  ClipboardList,
  Users,
  NotebookPen,
  ClipboardCheck,
  BarChart3,
  Layers,
  Send,
  Upload,
  SlidersHorizontal,
  MessageSquare,
  Megaphone,
} from 'lucide-react'
```
to:
```jsx
import {
  LayoutDashboard,
  ClipboardPlus,
  ClipboardList,
  Users,
  NotebookPen,
  ClipboardCheck,
  BarChart3,
  Layers,
  Send,
  Upload,
  SlidersHorizontal,
  MessageSquare,
  Megaphone,
  CalendarCheck,
  AlertTriangle,
  FileText,
} from 'lucide-react'
```

(Note: `ClipboardCheck` is already imported and used for "Dashboard Penilaian" — the new "Approval" nav item below reuses it is not needed since Approval gets its own icon; double check no name collision: the four *new* icons are `CalendarCheck`, `AlertTriangle`, `FileText`, plus `ClipboardCheck` already imported is reused as-is for its existing purpose only.)

Then add a new section into the `nav` array, immediately after the `Bimbingan` section (label: `'Bimbingan'`) and before the `Penilaian` section:

```jsx
  {
    label: 'Bimbingan',
    items: [
      { label: 'Tugas Mahasiswa', icon: ClipboardList, route: 'supervisor.tasks.index', match: 'supervisor.tasks.index' },
      { label: 'Daftar Mahasiswa', icon: Users, route: 'supervisor.students.list.index', match: 'supervisor.students.list.*' },
      { label: 'Logbook Mahasiswa', icon: NotebookPen, route: 'supervisor.logbooks.index', match: 'supervisor.logbooks.*' },
    ],
  },
  {
    label: 'Attendance',
    items: [
      { label: 'Kehadiran', icon: CalendarCheck, route: 'supervisor.attendance.index', match: 'supervisor.attendance.index' },
      { label: 'Approval', icon: ClipboardCheck, route: 'supervisor.attendance.approvals', match: 'supervisor.attendance.approvals' },
      { label: 'Laporan', icon: FileText, route: 'supervisor.attendance.reports', match: 'supervisor.attendance.reports' },
      { label: 'Mencurigakan', icon: AlertTriangle, route: 'supervisor.attendance.suspicious', match: 'supervisor.attendance.suspicious' },
    ],
  },
  {
    label: 'Penilaian',
```

(Only the lines from `label: 'Attendance'` through the closing `},` before `label: 'Penilaian'` are new — the `Bimbingan` block above and `Penilaian` block below are unchanged, shown only to anchor the insertion point.)

This mirrors `AdminLayout.jsx`'s section label `'ATTENDANCE'` in spelling convention (English section label, Indonesian item labels) exactly as that file already does for its own Attendance section.

- [ ] **Step 2: Build and verify no import errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors about unresolved icon imports.

- [ ] **Step 3: Manually verify the sidebar renders**

With the dev server running, log in as `dede@baktitest.com` / `1`, confirm a new "ATTENDANCE" section appears in the sidebar between "BIMBINGAN" and "PENILAIAN", with four items: Kehadiran, Approval, Laporan, Mencurigakan. Clicking them will 404 or error until Task 8 exists — that's expected at this point; just confirm the *sidebar* renders correctly.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Layouts/SupervisorLayout.jsx
git commit -m "feat: Add ATTENDANCE section to supervisor sidebar

Four items: Kehadiran, Approval, Laporan, Mencurigakan. Pages land in
the next commits; this just wires the nav ahead of them so review can
proceed page-by-page."
```

---

## Task 7: Build `Supervisor/Attendance/Index.jsx`

**Files:**
- Create: `resources/js/Pages/Supervisor/Attendance/Index.jsx`

Direct port of `resources/js/Pages/Admin/Attendance/Index.jsx` with: `AdminLayout` → `SupervisorLayout`, route names `admin.attendance.*` → `supervisor.attendance.*`, no "Pengaturan" (Settings) action button (supervisors don't get Settings — see spec Non-goals), copy adjusted to "mahasiswa bimbingan Anda", and the shared `ApprovalModal` with `routePrefix="supervisor"`.

- [ ] **Step 1: Create the page**

Create `resources/js/Pages/Supervisor/Attendance/Index.jsx`:

```jsx
import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import ApprovalModal from '@/Components/ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'warning',
  absent: 'destructive',
}

const statusLabel = {
  present: 'Hadir',
  late: 'Terlambat',
  absent: 'Tidak Hadir',
}

const approvalVariant = {
  pending: 'warning',
  approved: 'success',
  rejected: 'destructive',
}

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

export default function Index({ attendances, stats, date, status }) {
  const [modalItem, setModalItem] = useState(null)
  const [statusFilter, setStatusFilter] = useState(status ?? 'all')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('supervisor.attendance.index'), {
      date: form.date.value,
      ...(statusFilter !== 'all' ? { status: statusFilter } : {}),
    })
  }

  function quickApprove(attendance) {
    if (confirm('Apakah Anda yakin ingin menyetujui absensi ini?')) {
      router.post(r('supervisor.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Monitoring Absensi"
          description="Pantau absensi mahasiswa bimbingan Anda secara real-time"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <Link href={r('supervisor.attendance.approvals')}>
                  <ClipboardCheck /> Persetujuan
                </Link>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link href={r('supervisor.attendance.reports')}>
                  <BarChart3 /> Laporan
                </Link>
              </Button>
            </>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
        </div>

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label htmlFor="date" className="block text-sm font-medium text-foreground">Tanggal</label>
            <Input id="date" name="date" type="date" defaultValue={date} className="w-44" />
          </div>
          <div className="space-y-2">
            <label htmlFor="status" className="block text-sm font-medium text-foreground">Status</label>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger id="status" className="w-44">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Status</SelectItem>
                <SelectItem value="present">Hadir</SelectItem>
                <SelectItem value="late">Terlambat</SelectItem>
                <SelectItem value="absent">Tidak Hadir</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
        </form>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">
              Data Absensi — {new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}
            </h3>
          </div>
          {attendances.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {attendances.data.map((attendance) => (
                    <TableRow key={attendance.id}>
                      <TableCell>
                        <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} />
                      </TableCell>
                      <TableCell>
                        <span className="font-medium tabular-nums text-foreground">{time(attendance.check_in)}</span>
                        {attendance.is_late && <p className="text-xs text-amber-600 dark:text-amber-400">Terlambat</p>}
                      </TableCell>
                      <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                        {time(attendance.check_out)}
                      </TableCell>
                      <TableCell className="hidden text-right tabular-nums text-muted-foreground lg:table-cell">
                        {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                      </TableCell>
                      <TableCell>
                        <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                          {statusLabel[attendance.status] ?? attendance.status}
                        </Badge>
                      </TableCell>
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
                          <Button
                            type="button"
                            size="xs"
                            variant="outline"
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                            })}
                          >
                            Detail
                          </Button>
                          {attendance.supervisor_approval === 'pending' && (
                            <Button
                              type="button"
                              size="xs"
                              variant="ghost"
                              className="text-green-700 hover:text-green-700 dark:text-green-400 dark:hover:text-green-400"
                              onClick={() => quickApprove(attendance)}
                            >
                              Setujui
                            </Button>
                          )}
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {attendances.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={attendances.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={Users}
              title="Tidak ada data absensi"
              description="Belum ada data absensi mahasiswa bimbingan untuk tanggal yang dipilih."
            />
          )}
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="supervisor"
      />
    </SupervisorLayout>
  )
}
```

- [ ] **Step 2: Build and verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no module errors.

- [ ] **Step 3: Manually verify the page**

Log in as `dede@baktitest.com` / `1`, click "Kehadiran" in the sidebar (or visit `/supervisor/attendance` directly). Confirm:
- Page loads without error.
- Stat cards show counts for that supervisor's students only (compare against `php artisan tinker` query from Task 0 Step 3 if you have a specific date with known data, or just confirm no PHP error / the counts are plausible, e.g. `0` if no attendance rows exist yet for this supervisor's students today).
- Filter form works (submit a date, confirm URL updates with `?date=...`).

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Index.jsx
git commit -m "feat: Add Supervisor Attendance Index page

Scoped monitoring view mirroring Admin/Attendance/Index.jsx, minus the
Pengaturan (Settings) shortcut which stays admin-only."
```

---

## Task 8: Build `Supervisor/Attendance/Approvals.jsx`

**Files:**
- Create: `resources/js/Pages/Supervisor/Attendance/Approvals.jsx`

- [ ] **Step 1: Create the page**

Create `resources/js/Pages/Supervisor/Attendance/Approvals.jsx`:

```jsx
import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Clock, FileText, ArrowLeft } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'
import ApprovalModal from '@/Components/ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'warning',
  absent: 'destructive',
}

const statusLabel = {
  present: 'Hadir',
  late: 'Terlambat',
  absent: 'Tidak Hadir',
}

function shortDate(value) {
  return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

function RowActions({ onDetail, onApprove, onReject }) {
  return (
    <div className="flex items-center gap-1.5">
      <Button type="button" size="xs" variant="outline" onClick={onDetail}>Detail</Button>
      <Button
        type="button"
        size="xs"
        variant="ghost"
        className="text-green-700 hover:text-green-700 dark:text-green-400 dark:hover:text-green-400"
        onClick={onApprove}
      >
        Setujui
      </Button>
      <Button
        type="button"
        size="xs"
        variant="ghost"
        className="text-destructive hover:text-destructive"
        onClick={onReject}
      >
        Tolak
      </Button>
    </div>
  )
}

export default function Approvals({ pendingAttendances, pendingExceptions }) {
  const [tab, setTab] = useState('attendance')
  const [modalItem, setModalItem] = useState(null)
  const [modalType, setModalType] = useState('attendance')

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    if (confirm(`Yakin ingin ${label} item ini?`)) {
      router.post(r('supervisor.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }

  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
    })
  }

  const tabs = [
    { id: 'attendance', label: `Absensi Pending (${pendingAttendances.total})` },
    { id: 'exceptions', label: `Pengajuan Izin (${pendingExceptions.total})` },
  ]

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Persetujuan Absensi"
          description="Kelola persetujuan absensi dan pengajuan izin mahasiswa bimbingan Anda"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.attendance.index')}>
                <ArrowLeft /> Kembali ke Monitoring
              </Link>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <StatCard icon={Clock} label="Pending Absensi" value={pendingAttendances.total} tone="amber" />
          <StatCard icon={FileText} label="Pending Izin" value={pendingExceptions.total} tone="blue" />
        </div>

        <Card>
          <nav className="flex gap-6 border-b border-border px-4">
            {tabs.map((t) => (
              <button
                key={t.id}
                type="button"
                onClick={() => setTab(t.id)}
                className={cn(
                  'border-b-2 py-3 text-sm font-medium transition-colors duration-150',
                  tab === t.id
                    ? 'border-primary text-primary'
                    : 'border-transparent text-muted-foreground hover:text-foreground',
                )}
              >
                {t.label}
              </button>
            ))}
          </nav>

          {tab === 'attendance' ? (
            pendingAttendances.data.length ? (
              <>
                <Table>
                  <TableHeader>
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {pendingAttendances.data.map((attendance) => (
                      <TableRow key={attendance.id}>
                        <TableCell>
                          <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} />
                        </TableCell>
                        <TableCell>
                          <p className="font-medium tabular-nums text-foreground">{shortDate(attendance.date)}</p>
                          <p className="text-xs text-muted-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </TableCell>
                        <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                          <div>In: {time(attendance.check_in)}</div>
                          <div>Out: {time(attendance.check_out)}</div>
                        </TableCell>
                        <TableCell>
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {statusLabel[attendance.status] ?? attendance.status}
                          </Badge>
                        </TableCell>
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
                            onApprove={() => quickAction(attendance.id, 'attendance', 'approve')}
                            onReject={() => quickAction(attendance.id, 'attendance', 'reject')}
                          />
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
                {pendingAttendances.links?.length > 3 && (
                  <div className="border-t border-border px-4 py-3">
                    <Pagination links={pendingAttendances.links} />
                  </div>
                )}
              </>
            ) : (
              <EmptyState
                icon={Clock}
                title="Tidak ada absensi pending"
                description="Semua absensi bimbingan Anda sudah diproses atau belum ada pengajuan baru."
              />
            )
          ) : pendingExceptions.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Tanggal</TableHead>
                    <TableHead className="hidden md:table-cell">Jenis</TableHead>
                    <TableHead>Alasan</TableHead>
                    <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {pendingExceptions.data.map((exception) => (
                    <TableRow key={exception.id}>
                      <TableCell>
                        <UserCell name={exception.user?.name} subtitle={exception.user?.email} />
                      </TableCell>
                      <TableCell>
                        <p className="font-medium tabular-nums text-foreground">{shortDate(exception.date)}</p>
                        <p className="text-xs text-muted-foreground">
                          {new Date(exception.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                        </p>
                      </TableCell>
                      <TableCell className="hidden md:table-cell">
                        <Badge variant="secondary">{exception.type_name ?? 'Izin'}</Badge>
                      </TableCell>
                      <TableCell className="max-w-xs truncate whitespace-normal text-foreground" title={exception.reason}>
                        {exception.reason}
                      </TableCell>
                      <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                        {new Date(exception.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </TableCell>
                      <TableCell>
                        <RowActions
                          onDetail={() => openModal('exception', exception)}
                          onApprove={() => quickAction(exception.id, 'exception', 'approve')}
                          onReject={() => quickAction(exception.id, 'exception', 'reject')}
                        />
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {pendingExceptions.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={pendingExceptions.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={FileText}
              title="Tidak ada pengajuan izin pending"
              description="Semua pengajuan izin bimbingan Anda sudah diproses atau belum ada pengajuan baru."
            />
          )}
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="supervisor"
      />
    </SupervisorLayout>
  )
}
```

- [ ] **Step 2: Build and verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 3: Manually verify the page**

Log in as `dede@baktitest.com` / `1`, click "Approval" in the sidebar. Confirm the page loads, tabs switch between Absensi Pending / Pengajuan Izin, and (if pending items exist for this supervisor's students) the modal opens and an approve/reject submits successfully and the row disappears from the pending list.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Approvals.jsx
git commit -m "feat: Add Supervisor Attendance Approvals page

Scoped approval queue mirroring Admin/Attendance/Approvals.jsx."
```

---

## Task 9: Build `Supervisor/Attendance/Suspicious.jsx`

**Files:**
- Create: `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`

- [ ] **Step 1: Create the page**

Create `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`:

```jsx
import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import ApprovalModal from '@/Components/ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'warning',
  absent: 'destructive',
}

const statusLabel = {
  present: 'Hadir',
  late: 'Terlambat',
  absent: 'Tidak Hadir',
}

export default function Suspicious({ suspiciousAttendances }) {
  const [modalItem, setModalItem] = useState(null)
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Kehadiran Mencurigakan"
          description="Kehadiran mahasiswa bimbingan Anda yang memerlukan review manual karena anomali terdeteksi"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.attendance.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        {suspiciousAttendances.data.length === 0 ? (
          <Card>
            <EmptyState
              icon={CheckCircle2}
              title="Tidak ada kehadiran mencurigakan"
              description="Semua data kehadiran bimbingan Anda terlihat normal."
            />
          </Card>
        ) : (
          <Card>
            <div className="flex items-center gap-2.5 border-b border-border px-4 py-3.5">
              <AlertTriangle className="h-4 w-4 text-amber-600 dark:text-amber-400" />
              <h3 className="text-base font-semibold text-foreground">Perlu Review</h3>
              <span className="text-sm tabular-nums text-muted-foreground">({suspiciousAttendances.total})</span>
            </div>
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {suspiciousAttendances.data.map((attendance) => (
                  <TableRow key={attendance.id}>
                    <TableCell>
                      <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} tone="red" />
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">
                      {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">
                      {attendance.check_in_time ? new Date(attendance.check_in_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                    </TableCell>
                    <TableCell>
                      <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                        {statusLabel[attendance.status] ?? attendance.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="whitespace-normal">
                      <div className="space-y-1">
                        {attendance.location_notes && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <MapPin className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            {attendance.location_notes}
                          </p>
                        )}
                        {attendance.latitude && attendance.longitude && (
                          <p className="text-xs tabular-nums text-muted-foreground">
                            Koordinat: {Number(attendance.latitude).toFixed(4)}, {Number(attendance.longitude).toFixed(4)}
                          </p>
                        )}
                      </div>
                    </TableCell>
                    <TableCell>
                      <Button
                        type="button"
                        size="xs"
                        variant="outline"
                        onClick={() => setModalItem({
                          id: attendance.id,
                          userName: attendance.user?.name,
                          date: new Date(attendance.date).toLocaleDateString('id-ID'),
                        })}
                      >
                        Review
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {suspiciousAttendances.links?.length > 3 && (
              <div className="border-t border-border px-4 py-3">
                <Pagination links={suspiciousAttendances.links} />
              </div>
            )}
          </Card>
        )}
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
        routePrefix="supervisor"
      />
    </SupervisorLayout>
  )
}
```

**Note (carried from the design spec, not a new issue to fix here)**: this page references `attendance.latitude`/`attendance.check_in_time`, matching the existing `Admin/Attendance/Suspicious.jsx` verbatim — those field names don't match the `Attendance` model's actual columns (`check_in_latitude`, no plain `check_in_time`/`latitude` attributes exist per `app/Models/Attendance.php`). This is a pre-existing bug in the admin page being ported as-is (per the design spec's Non-goals: "Changing anything in `Admin\AttendanceController`... or the Admin Attendance pages' behavior"). Do not fix it in this task — it would mean the two pages render differently, which isn't part of this plan's scope. If you want it fixed, that's a separate, explicit follow-up plan touching both Admin and Supervisor Suspicious pages together.

- [ ] **Step 2: Build and verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 3: Manually verify the page**

Log in as `dede@baktitest.com` / `1`, click "Mencurigakan" in the sidebar. Confirm the page loads (likely showing the empty state, since `requires_manual_review = true` rows are rare test data) and, if any exist, the Review modal opens and submits.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Suspicious.jsx
git commit -m "feat: Add Supervisor Attendance Suspicious page

Scoped anomaly-review queue mirroring Admin/Attendance/Suspicious.jsx."
```

---

## Task 10: Build `Supervisor/Attendance/Reports.jsx`

**Files:**
- Create: `resources/js/Pages/Supervisor/Attendance/Reports.jsx`

- [ ] **Step 1: Create the page**

Create `resources/js/Pages/Supervisor/Attendance/Reports.jsx`:

```jsx
import { useState } from 'react'
import { router } from '@inertiajs/react'
import { Download, FileSpreadsheet, RotateCcw } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import { Card } from '@/Components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

function average(items, key) {
  if (!items.length) return 0
  return Math.round((items.reduce((sum, item) => sum + item[key], 0) / items.length) * 10) / 10
}

function CountCell({ value, toneWhenPositive }) {
  return (
    <TableCell
      className={cn(
        'text-right tabular-nums',
        value > 0 ? toneWhenPositive : 'text-muted-foreground',
      )}
    >
      {value}
    </TableCell>
  )
}

export default function Reports({ summary, users, month, year, userId }) {
  const [monthFilter, setMonthFilter] = useState(String(month))
  const [yearFilter, setYearFilter] = useState(String(year))
  const [userIdFilter, setUserIdFilter] = useState(userId ? String(userId) : 'all')
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleFilter(e) {
    e.preventDefault()
    router.get(r('supervisor.attendance.reports'), {
      month: monthFilter,
      year: yearFilter,
      ...(userIdFilter !== 'all' ? { user_id: userIdFilter } : {}),
    })
  }

  function exportUrl(type) {
    const params = new URLSearchParams({ month, year, type })
    if (userId) params.set('user_id', userId)
    return `${r('supervisor.attendance.export-csv')}?${params.toString()}`
  }

  const currentYear = new Date().getFullYear()

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Laporan Kehadiran"
          description="Export dan analisis data kehadiran mahasiswa bimbingan Anda"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <a href={exportUrl('detail')}>
                  <Download /> Export Detail
                </a>
              </Button>
              <Button asChild variant="outline" size="sm">
                <a href={exportUrl('summary')}>
                  <FileSpreadsheet /> Export Ringkasan
                </a>
              </Button>
            </>
          }
        />

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Bulan</label>
            <Select value={monthFilter} onValueChange={setMonthFilter}>
              <SelectTrigger className="w-40">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {monthNames.map((name, index) => (
                  <SelectItem key={name} value={String(index + 1)}>{name}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Tahun</label>
            <Select value={yearFilter} onValueChange={setYearFilter}>
              <SelectTrigger className="w-28">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {[0, 1, 2].map((offset) => (
                  <SelectItem key={offset} value={String(currentYear - offset)}>{currentYear - offset}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Mahasiswa</label>
            <Select value={userIdFilter} onValueChange={setUserIdFilter}>
              <SelectTrigger className="w-56">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Mahasiswa Bimbingan</SelectItem>
                {users.map((user) => (
                  <SelectItem key={user.id} value={String(user.id)}>
                    {user.name} ({user.student?.nim ?? '-'})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button type="button" variant="ghost" onClick={() => router.get(r('supervisor.attendance.reports'))}>
            <RotateCcw /> Reset
          </Button>
        </form>

        {summary.length > 0 ? (
          <>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <StatCard label="Total Mahasiswa" value={summary.length} tone="neutral" />
              <StatCard label="Rata-rata Hadir" value={average(summary, 'present')} tone="green" />
              <StatCard label="Rata-rata Terlambat" value={average(summary, 'late')} tone="amber" />
              <StatCard label="Rata-rata Tidak Hadir" value={average(summary, 'absent')} tone="red" />
            </div>

            <Card>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Nama Mahasiswa</TableHead>
                    <TableHead>NIM</TableHead>
                    <TableHead className="text-right">Total Hari</TableHead>
                    <TableHead className="text-right">Hadir</TableHead>
                    <TableHead className="text-right">Terlambat</TableHead>
                    <TableHead className="text-right">Tidak Hadir</TableHead>
                    <TableHead className="text-right">Rata-rata Jam</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {summary.map((item) => (
                    <TableRow key={item.user.id}>
                      <TableCell className="font-medium text-foreground">{item.user.name}</TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">{item.user.student?.nim ?? '-'}</TableCell>
                      <TableCell className="text-right font-medium tabular-nums text-foreground">{item.total_days}</TableCell>
                      <CountCell value={item.present} toneWhenPositive="font-medium text-green-700 dark:text-green-400" />
                      <CountCell value={item.late} toneWhenPositive="font-medium text-amber-700 dark:text-amber-400" />
                      <CountCell value={item.absent} toneWhenPositive="font-medium text-red-700 dark:text-red-400" />
                      <TableCell className="text-right tabular-nums text-foreground">
                        {item.avg_hours ? Number(item.avg_hours).toFixed(1) : 0} jam
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </Card>
          </>
        ) : (
          <Card>
            <EmptyState
              icon={FileSpreadsheet}
              title="Tidak ada data kehadiran"
              description="Tidak ada data kehadiran bimbingan Anda untuk periode yang dipilih."
            />
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
```

**Note:** the "Pembimbing" column from `Admin/Attendance/Reports.jsx`'s table is intentionally dropped here — every row in a supervisor's own report is by definition their own bimbingan, so a column repeating the same name on every row is redundant.

- [ ] **Step 2: Build and verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 3: Manually verify the page**

Log in as `dede@baktitest.com` / `1`, click "Laporan" in the sidebar. Confirm:
- Page loads, month/year/student filters work.
- The "Mahasiswa" dropdown only lists this supervisor's own students (cross-check against the count from Task 0 Step 3).
- Clicking "Export Detail" or "Export Ringkasan" downloads a CSV; open it and confirm every row's student belongs to this supervisor.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Reports.jsx
git commit -m "feat: Add Supervisor Attendance Reports page

Scoped monthly summary + CSV export mirroring Admin/Attendance/Reports.jsx,
minus the redundant Pembimbing column (every row is the viewer's own
supervisee)."
```

---

## Task 11: Full build verification

**Files:** none (verification only)

- [ ] **Step 1: Full asset rebuild**

Run: `npm install && npm run build`
Expected: completes with `✓ built in ...` and no errors or warnings about missing modules.

- [ ] **Step 2: PHP lint on every touched file**

Run:
```bash
php -l app/Http/Controllers/Concerns/HandlesAttendanceActions.php
php -l app/Http/Controllers/Admin/AttendanceController.php
php -l app/Http/Controllers/Supervisor/AttendanceController.php
php -l routes/web.php
```
Expected: `No syntax errors detected in ...` for all four.

- [ ] **Step 3: Full route list sanity check**

Run: `php artisan route:list --name=attendance`
Expected: shows `admin.attendance.*` (unchanged, 8 routes: index, approvals, approve, suspicious, suspicious.review, reports, export-csv, settings + settings.update), `supervisor.attendance.*` (7 new routes), and `attendance.*` (student's own, 5 routes, unchanged) — no `supervisor.admin.attendance.*` entries anywhere.

- [ ] **Step 4: Commit (only if any fixes were needed in this task; otherwise skip — nothing to commit)**

If Steps 1-3 all passed cleanly with no code changes needed, there is nothing to commit here — move to Task 12.

---

## Task 12: Full manual verification pass

**Files:** none (manual QA, per project convention — no automated frontend/backend test suite exists in this repo to extend)

- [ ] **Step 1: Start the server correctly**

```bash
npm run build
php artisan serve --port=8000
rm -f public/hot
```

- [ ] **Step 2: Verify scoping is airtight — cross-supervisor isolation**

Using `php artisan tinker`, find or create a second supervisor with at least one different student than `dede@baktitest.com`'s supervisor account. Note that student's `Attendance` id (or create one via the Student check-in flow logged in as that other student).

Log in as `dede@baktitest.com` / `1`. Attempt:
```
POST /supervisor/attendance/approve/attendance/{id-belonging-to-other-supervisors-student}
```
(e.g. via browser dev tools / a manual fetch, or by editing the modal's target id in a scratch script) with `action=approve`.

Expected: HTTP 403 response — the `approveAttendanceOrException` trait method's guard (`$item->user->student?->supervisor_id !== $user->id`) rejects it.

- [ ] **Step 3: Verify light + dark mode on all 4 new pages**

For each of `/supervisor/attendance`, `/supervisor/attendance/approvals`, `/supervisor/attendance/suspicious`, `/supervisor/attendance/reports`:
- Toggle dark mode (moon/sun icon in the top bar).
- Confirm all text remains legible, StatCard tones render correctly, table borders/hover states are visible in both modes.

- [ ] **Step 4: Verify responsive layout at 375px, 768px, 1440px**

For each of the 4 pages, resize the browser (or use dev tools device toolbar) to 375px, 768px, and 1440px widths. Confirm:
- No horizontal scrollbar on the page body itself (tables scroll within their own container).
- StatCards stack to 1 column at 375px, 2 at ~640px+, up to 4 at 1440px (per the `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4` classes already in each page).
- Sidebar collapses to the mobile hamburger menu at 375px (existing `AppShell` behavior, unaffected by this change).

- [ ] **Step 5: Confirm Admin pages are unaffected**

Log in as `admin@bakti.com` / `1`. Visit `/admin/attendance`, `/admin/attendance/approvals`, `/admin/attendance/suspicious`, `/admin/attendance/reports`, `/admin/attendance/settings`. Confirm every page looks and behaves exactly as it did before this plan (same layout, same data — now showing all students, since admin scoping is intentionally unchanged).

- [ ] **Step 6: Confirm the leaked routes are truly gone end-to-end**

With any authenticated supervisor session, visit `http://localhost:8000/supervisor/attendance/settings` directly (this used to be the leaked `admin.attendance.settings` route under a supervisor path — but note the path has changed: the *old* leaked route was also literally `/supervisor/attendance/settings` since it inherited the `supervisor` prefix). Confirm this now returns a 404 (no such route exists — Settings was never added to the new `supervisor.attendance.*` group, by design).

- [ ] **Step 7: Final commit if any fixes were made during manual verification**

If Steps 2-6 surfaced any bugs and you fixed them, commit those fixes now with a clear message describing what was found and fixed. If everything passed cleanly, there is nothing to commit — the feature is complete as of Task 10's commit.

---

## Self-review notes (completed during plan authoring, not a task to run)

- **Spec coverage**: Index (Task 7), Approvals (Task 8), Suspicious (Task 9), Reports+CSV (Task 10) all covered. Settings explicitly excluded (spec Non-goals) — confirmed no task adds it. Leaked-route removal (Task 2) and trait extraction (Task 1) both covered. Sidebar (Task 6) covered. `ApprovalModal` extraction (Task 5) covered, including updating all three Admin call sites so nothing breaks.
- **Fixed during authoring**: the `reports()` controller method's `$users` collection needed an explicit `setRelation('student', $student)` step (Task 3, Step 2) to match the shape the React page expects — this was caught by tracing the data flow from controller to `user.student?.nim` in the page component, not assumed.
- **Type/name consistency check**: `routePrefix` prop name and usage (`${routePrefix}.attendance.suspicious.review` / `${routePrefix}.attendance.approve`) match exactly between Task 5 (component definition) and Tasks 7-9 (three call sites, all passing `routePrefix="supervisor"`) and Task 5 itself (three admin call sites, all `routePrefix="admin"`). Route names `supervisor.attendance.index/approvals/approve/suspicious/suspicious.review/reports/export-csv` match exactly between Task 4 (route registration) and Tasks 7-10 (frontend `r(...)` calls). Controller class name `Supervisor\AttendanceController` and its import alias `SupervisorAttendanceController` match exactly between Task 3 (definition) and Task 4 (route file usage).
- **No placeholders**: every task has literal, complete code — no "similar to Task N" shortcuts, no "add validation" hand-waves. The one explicit exception is Task 9's Suspicious page, which documents a **pre-existing** field-name bug it inherits by design (matches the spec's Non-goals) rather than silently fixing or silently ignoring it.
