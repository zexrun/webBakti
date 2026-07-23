# Suspicious Attendance Counter Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a date-scoped "Mencurigakan" (suspicious) count to the Admin and Supervisor Monitoring Absensi pages' stats, backed by the existing `requires_manual_review` flag, with a matching stat card and an inline table badge — and no new logic needed for the counter to auto-decrease, since it's computed live on every page load from the same flag that `reviewSuspiciousAttendance()` already clears on approval.

**Architecture:** Both `Admin\AttendanceController::index()` and `Supervisor\AttendanceController::index()` gain one more key in their existing `$stats` array, reusing each controller's existing date-scoped (and, for Supervisor, student-scoped) base query. Both `Index.jsx` pages get a 5th `StatCard` and a small inline badge in the table's Status cell.

**Tech Stack:** Laravel 12, Inertia.js, React 19. No new dependencies.

---

### Task 1: Add the suspicious count to Admin's stats

**Files:**
- Modify: `app/Http/Controllers/Admin/AttendanceController.php`

- [ ] **Step 1: Add the stat**

In `AttendanceController::index()`, change:

```php
        $stats = [
            'total' => Attendance::where('date', $date)->count(),
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
        ];
```

to:

```php
        $stats = [
            'total' => Attendance::where('date', $date)->count(),
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'suspicious' => Attendance::where('date', $date)->where('requires_manual_review', true)->count(),
        ];
```

- [ ] **Step 2: Verify syntax**

```bash
php -l app/Http/Controllers/Admin/AttendanceController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/AttendanceController.php
git commit -m "feat: Add suspicious attendance count to admin monitoring stats"
```

---

### Task 2: Add the suspicious count to Supervisor's stats

**Files:**
- Modify: `app/Http/Controllers/Supervisor/AttendanceController.php`

- [ ] **Step 1: Add the stat**

In `AttendanceController::index()`, change:

```php
        $stats = [
            'total' => $baseStatsQuery()->count(),
            'present' => $baseStatsQuery()->where('status', 'present')->count(),
            'late' => $baseStatsQuery()->where('status', 'late')->count(),
            'absent' => $baseStatsQuery()->where('status', 'absent')->count(),
        ];
```

to:

```php
        $stats = [
            'total' => $baseStatsQuery()->count(),
            'present' => $baseStatsQuery()->where('status', 'present')->count(),
            'late' => $baseStatsQuery()->where('status', 'late')->count(),
            'absent' => $baseStatsQuery()->where('status', 'absent')->count(),
            'suspicious' => $baseStatsQuery()->where('requires_manual_review', true)->count(),
        ];
```

This reuses the existing `$baseStatsQuery` closure, which already applies both the date filter and the `scopeToSupervisedStudents()` scoping — so the new count is automatically limited to the logged-in supervisor's own students, same as the other four.

- [ ] **Step 2: Verify syntax**

```bash
php -l app/Http/Controllers/Supervisor/AttendanceController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Supervisor/AttendanceController.php
git commit -m "feat: Add suspicious attendance count to supervisor monitoring stats"
```

---

### Task 3: Add the stat card and table badge to the Admin Monitoring page

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Index.jsx`

- [ ] **Step 1: Import AlertTriangle and add the 5th stat card**

Change the lucide-react import line:

```jsx
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3, Settings as SettingsIcon } from 'lucide-react'
```

to:

```jsx
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3, Settings as SettingsIcon, AlertTriangle } from 'lucide-react'
```

Then change:

```jsx
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
        </div>
```

to:

```jsx
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" />
        </div>
```

- [ ] **Step 2: Add the inline table badge**

Change:

```jsx
                      <TableCell>
                        <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                          {statusLabel[attendance.status] ?? attendance.status}
                        </Badge>
                      </TableCell>
```

to:

```jsx
                      <TableCell>
                        <div className="flex flex-wrap items-center gap-1">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {statusLabel[attendance.status] ?? attendance.status}
                          </Badge>
                          {attendance.requires_manual_review && (
                            <Badge variant="warning">Mencurigakan</Badge>
                          )}
                        </div>
                      </TableCell>
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Index.jsx
git commit -m "feat: Show suspicious attendance count and badge on admin monitoring page"
```

---

### Task 4: Add the stat card and table badge to the Supervisor Monitoring page

**Files:**
- Modify: `resources/js/Pages/Supervisor/Attendance/Index.jsx`

- [ ] **Step 1: Import AlertTriangle and add the 5th stat card**

Change:

```jsx
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3 } from 'lucide-react'
```

to:

```jsx
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3, AlertTriangle } from 'lucide-react'
```

Then change:

```jsx
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
        </div>
```

to:

```jsx
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" />
        </div>
```

- [ ] **Step 2: Add the inline table badge**

Change:

```jsx
                      <TableCell>
                        <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                          {statusLabel[attendance.status] ?? attendance.status}
                        </Badge>
                      </TableCell>
```

to:

```jsx
                      <TableCell>
                        <div className="flex flex-wrap items-center gap-1">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {statusLabel[attendance.status] ?? attendance.status}
                          </Badge>
                          {attendance.requires_manual_review && (
                            <Badge variant="warning">Mencurigakan</Badge>
                          )}
                        </div>
                      </TableCell>
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Index.jsx
git commit -m "feat: Show suspicious attendance count and badge on supervisor monitoring page"
```

---

### Task 5: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax/build check every touched file**

```bash
php -l app/Http/Controllers/Admin/AttendanceController.php
php -l app/Http/Controllers/Supervisor/AttendanceController.php
npm run build
```
Expected: all succeed with no errors.

- [ ] **Step 2: Manual browser check — counter matches flagged rows**

Visit `/admin/attendance` for a date known to have at least one `requires_manual_review = true` row (e.g. via `php artisan tinker --execute="App\Models\Attendance::where('requires_manual_review', true)->get(['id','date']);"` to find one). Confirm:
- The "Mencurigakan" stat card shows the same count as rows with `requires_manual_review = true` for that date.
- Any such row in the table shows the "Mencurigakan" badge alongside its normal status badge.

- [ ] **Step 3: Manual browser check — counter decreases after approval**

From `/admin/attendance/suspicious`, approve or reject one of the flagged attendances for the date checked in Step 2. Return to `/admin/attendance` for that same date and confirm the "Mencurigakan" count has decreased by exactly one, and that row's badge is gone.

- [ ] **Step 4: Manual browser check — supervisor scoping**

Log in as a supervisor with at least one suspicious attendance among their own students. Confirm `/supervisor/attendance` shows a "Mencurigakan" count matching only their own students' flagged rows (not the site-wide total).

- [ ] **Step 5: Commit any fixes found during manual verification**

Only if Steps 2-4 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
