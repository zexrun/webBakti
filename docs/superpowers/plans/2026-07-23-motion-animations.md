# Motion Animations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Framer Motion (`motion/react`) fade+scale animations to every modal/dialog/lightbox, a global fade+shift page transition in `AppShell.jsx`, and a stagger fade-in for every `StatCard` instance across the app (55 call sites in 20 files).

**Architecture:** `motion` is a new dependency. Each of the 4 modal components wraps its own return in `<AnimatePresence>` + `motion.div` (self-contained — no shared modal-animation component needed since each already has divergent JSX inside its panel). `AppShell.jsx` wraps `{children}` in `<AnimatePresence mode="wait"><motion.div key={url}>` using Inertia's `usePage().url` as the transition key. `StatCard.jsx` becomes a `motion.div`-wrapped card with a new optional `index` prop driving a stagger `transition.delay`; all 20 call-site files get `index={0..N}` added to each `<StatCard>` in their respective grids.

**Tech Stack:** React 19, `motion` (Framer Motion's React package, new dependency, ^12.42.2), Tailwind CSS (unchanged).

---

### Task 1: Add motion dependency

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Install**

```bash
npm install motion@^12.42.2
```

- [ ] **Step 2: Verify**

```bash
npm run build
```
Expected: build succeeds (motion isn't imported anywhere yet).

- [ ] **Step 3: Commit**

```bash
git add package.json package-lock.json
git commit -m "chore: Add motion (Framer Motion) dependency for UI animations"
```

---

### Task 2: Animate ConfirmDialog and PhotoLightbox

**Files:**
- Modify: `resources/js/Components/ConfirmDialog.jsx`
- Modify: `resources/js/Components/PhotoLightbox.jsx`

- [ ] **Step 1: ConfirmDialog.jsx**

Replace the full file:

```jsx
import { AnimatePresence, motion } from 'motion/react'
import { Button } from '@/Components/ui/button'

/**
 * Presentational confirm modal. Controlled entirely by useConfirm()'s
 * provider - this component has no state of its own, it just renders
 * whatever the provider currently holds.
 */
export default function ConfirmDialog({ open, title, description, confirmLabel, cancelLabel, variant, onConfirm, onCancel }) {
  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="fixed inset-0 z-[100] bg-black/50"
          onClick={(e) => e.target === e.currentTarget && onCancel()}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.15 }}
        >
          <div className="flex min-h-screen items-center justify-center p-4">
            <motion.div
              className="w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-lg"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.15 }}
            >
              <h3 className="text-lg font-semibold text-foreground">{title}</h3>
              {description && <p className="mt-2 text-sm text-muted-foreground">{description}</p>}
              <div className="mt-6 flex gap-2">
                <Button type="button" variant="outline" onClick={onCancel} className="flex-1">
                  {cancelLabel || 'Batal'}
                </Button>
                <Button
                  type="button"
                  variant={variant === 'destructive' ? 'destructive' : 'default'}
                  onClick={onConfirm}
                  className="flex-1"
                >
                  {confirmLabel || 'Konfirmasi'}
                </Button>
              </div>
            </motion.div>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  )
}
```

- [ ] **Step 2: PhotoLightbox.jsx**

Replace the full file:

```jsx
import { AnimatePresence, motion } from 'motion/react'
import { X } from 'lucide-react'

/**
 * Full-size photo overlay. Structurally mirrors ApprovalModal (fixed
 * inset overlay, click-outside-to-close, close button) for visual
 * consistency with the rest of the attendance review UI.
 */
export default function PhotoLightbox({ open, onClose, url, caption }) {
  return (
    <AnimatePresence>
      {open && url && (
        <motion.div
          className="fixed inset-0 z-[60] bg-black/70"
          onClick={(e) => e.target === e.currentTarget && onClose()}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.15 }}
        >
          <div className="flex min-h-screen items-center justify-center p-4">
            <motion.div
              className="relative max-w-2xl"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.15 }}
            >
              <button
                type="button"
                onClick={onClose}
                aria-label="Tutup"
                className="absolute -top-10 right-0 rounded-md p-1.5 text-white/80 transition-colors duration-150 hover:bg-white/10 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
              <img src={url} alt={caption} className="max-h-[80vh] w-full rounded-lg object-contain" />
              {caption && (
                <p className="mt-2 text-center text-sm text-white/80">{caption}</p>
              )}
            </motion.div>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  )
}
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/ConfirmDialog.jsx resources/js/Components/PhotoLightbox.jsx
git commit -m "feat: Add fade+scale animation to ConfirmDialog and PhotoLightbox"
```

---

### Task 3: Animate ApprovalModal and AttendanceModal

**Files:**
- Modify: `resources/js/Components/ApprovalModal.jsx`
- Modify: `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`

Both files currently start their return with `if (!open || !item) return null` (ApprovalModal) or `if (!open) return null` (AttendanceModal), then a fixed-inset overlay div. Both need the same transformation: remove the early-return guard, wrap the whole returned tree in `<AnimatePresence>{open && (<motion.div>...)}</AnimatePresence>`, and make the inner panel a `motion.div` too.

- [ ] **Step 1: ApprovalModal.jsx**

Add the import:
```jsx
import { AnimatePresence, motion } from 'motion/react'
```

Change:
```jsx
  if (!open || !item) return null

  function handleSubmit(e) {
```
to:
```jsx
  function handleSubmit(e) {
```
(remove the early return; the guard moves into the JSX condition below).

Change the return statement's outer structure from:
```jsx
  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg">
```
to:
```jsx
  return (
    <AnimatePresence>
      {open && item && (
        <motion.div
          className="fixed inset-0 z-50 bg-black/50"
          onClick={(e) => e.target === e.currentTarget && onClose()}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.15 }}
        >
          <div className="flex min-h-screen items-center justify-center p-4">
            <motion.div
              className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.15 }}
            >
```

And the closing tags at the end of the component, from:
```jsx
        </div>
      </div>

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </div>
  )
}
```
to:
```jsx
            </motion.div>
          </div>

          <PhotoLightbox
            open={Boolean(lightbox)}
            onClose={() => setLightbox(null)}
            url={lightbox?.url}
            caption={lightbox?.caption}
          />
        </motion.div>
      )}
    </AnimatePresence>
  )
}
```

(Every line of JSX content between these two edited regions — the header, the form, the decision options, the textarea, the buttons — stays exactly as-is; only the outer wrapper changes and indentation shifts one level deeper. `PhotoLightbox` moves inside the outer `motion.div` so it's still only rendered while the parent modal is open, matching current behavior.)

- [ ] **Step 2: AttendanceModal.jsx**

Add the import:
```jsx
import { AnimatePresence, motion } from 'motion/react'
```

Change:
```jsx
  if (!open) return null

  async function handleSubmit(e) {
```
to:
```jsx
  async function handleSubmit(e) {
```

Change the return statement's outer structure from:
```jsx
  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-xl border border-border bg-card shadow-lg">
```
to:
```jsx
  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="fixed inset-0 z-50 bg-black/50"
          onClick={(e) => e.target === e.currentTarget && onClose()}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.15 }}
        >
          <div className="flex min-h-screen items-center justify-center p-4">
            <motion.div
              className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-xl border border-border bg-card shadow-lg"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.15 }}
            >
```

And the closing tags, from:
```jsx
          </div>
        </div>
      </div>
    </div>
  )
}
```
to:
```jsx
              </div>
            </motion.div>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  )
}
```

(Everything inside — the location banner, the video/canvas/photo capture UI, the notes textarea, the submit button — is untouched; only the outer wrapper changes, with one extra level of indentation for the whole block.)

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/ApprovalModal.jsx resources/js/Pages/Student/Attendance/AttendanceModal.jsx
git commit -m "feat: Add fade+scale animation to ApprovalModal and AttendanceModal"
```

---

### Task 4: Global page transition in AppShell

**Files:**
- Modify: `resources/js/Layouts/AppShell.jsx`

- [ ] **Step 1: Add imports**

```jsx
import { AnimatePresence, motion } from 'motion/react'
```

- [ ] **Step 2: Read the current page URL**

Change:
```jsx
export default function AppShell({ nav, homeRoute, children }) {
  const { auth } = usePage().props
```
to:
```jsx
export default function AppShell({ nav, homeRoute, children }) {
  const { auth, url } = usePage()
```

(`usePage()` itself carries `url` as a top-level property alongside `props` — this reads the current Inertia page URL, which changes every navigation and is what `AnimatePresence` needs as a changing `key`.)

- [ ] **Step 3: Wrap children in the transition**

Change:
```jsx
        <main className="flex-1 p-4 sm:p-6 lg:p-8">{children}</main>
```
to:
```jsx
        <main className="flex-1 p-4 sm:p-6 lg:p-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={url}
              initial={{ opacity: 0, y: 8 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -8 }}
              transition={{ duration: 0.15 }}
            >
              {children}
            </motion.div>
          </AnimatePresence>
        </main>
```

- [ ] **Step 4: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Layouts/AppShell.jsx
git commit -m "feat: Add global fade+shift page transition to AppShell"
```

---

### Task 5: Animate StatCard component

**Files:**
- Modify: `resources/js/Components/StatCard.jsx`

- [ ] **Step 1: Replace the full file**

```jsx
import { motion } from 'motion/react'
import { Card, CardContent } from '@/Components/ui/card'
import { cn } from '@/lib/utils'

/**
 * Semantic tone → icon-tile colors, light + dark. Tones follow the
 * project's category color convention (blue=user, green=student/success,
 * purple=supervisor, orange=directorate/attention, ...). Opacity
 * modifiers here are safe: they apply to Tailwind's built-in palette,
 * not our custom CSS-var colors.
 */
const tones = {
  neutral: 'bg-slate-100 text-slate-700 dark:bg-slate-500/15 dark:text-slate-300',
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  emerald: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
  purple: 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-300',
  orange: 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
  amber: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
  cyan: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-300',
  indigo: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300',
}

/**
 * `index` (default 0) drives a small per-card stagger delay so a row of
 * StatCards fades in one after another instead of all at once - pass
 * each card's position within its own grid (each grid restarts at 0).
 */
export default function StatCard({ icon: Icon, label, value, hint, tone = 'neutral', className, index = 0 }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.2, delay: index * 0.05 }}
    >
      <Card className={className}>
        <CardContent className="flex items-center gap-4 p-5">
          {Icon && (
            <div className={cn('flex h-11 w-11 shrink-0 items-center justify-center rounded-lg', tones[tone] ?? tones.neutral)}>
              <Icon className="h-5 w-5" />
            </div>
          )}
          <div className="min-w-0">
            <p className="truncate text-sm text-muted-foreground">{label}</p>
            <p className="text-2xl font-semibold tabular-nums text-foreground">{value}</p>
            {hint && <p className="mt-0.5 truncate text-xs text-muted-foreground">{hint}</p>}
          </div>
        </CardContent>
      </Card>
    </motion.div>
  )
}
```

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds (no call site passes `index` yet, so every card defaults to `index={0}` — same animation timing for all, no visible stagger until Tasks 6-9 add the prop).

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/StatCard.jsx
git commit -m "feat: Add fade-in animation and stagger index prop to StatCard"
```

---

### Task 6: Add stagger index to StatCard call sites — Dashboards

**Files:**
- Modify: `resources/js/Pages/Admin/Dashboard.jsx`
- Modify: `resources/js/Pages/Supervisor/Dashboard.jsx`
- Modify: `resources/js/Pages/Student/Dashboard.jsx`
- Modify: `resources/js/Pages/Supervisor/Analytics/Dashboard.jsx`

- [ ] **Step 1: Admin/Dashboard.jsx**

This file has two separate stat-card grids (each restarts its index at 0). Change:
```jsx
          <StatCard icon={Users} label="Total Users" value={userCount} tone="blue" />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={studentCount} tone="green" />
          <StatCard icon={UserCog} label="Pembimbing" value={supervisorCount} tone="purple" />
          <StatCard icon={Building2} label="Direktorat" value={directorateCount} tone="orange" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Users" value={userCount} tone="blue" index={0} />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={studentCount} tone="green" index={1} />
          <StatCard icon={UserCog} label="Pembimbing" value={supervisorCount} tone="purple" index={2} />
          <StatCard icon={Building2} label="Direktorat" value={directorateCount} tone="orange" index={3} />
```

And change:
```jsx
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskCount} tone="indigo" />
          <StatCard icon={CheckCircle2} label="Submission Dinilai" value={submissionsWithGrades} tone="emerald" />
          <StatCard icon={CalendarCheck} label="Total Presensi" value={totalAttendance} tone="cyan" />
          <StatCard icon={UserCog} label="Admin" value={adminCount} tone="neutral" />
```
to:
```jsx
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskCount} tone="indigo" index={0} />
          <StatCard icon={CheckCircle2} label="Submission Dinilai" value={submissionsWithGrades} tone="emerald" index={1} />
          <StatCard icon={CalendarCheck} label="Total Presensi" value={totalAttendance} tone="cyan" index={2} />
          <StatCard icon={UserCog} label="Admin" value={adminCount} tone="neutral" index={3} />
```

- [ ] **Step 2: Supervisor/Dashboard.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={stats.totalStudents} tone="blue" />
          <StatCard icon={ClipboardList} label="Total Tugas" value={stats.totalTasks} tone="green" />
          <StatCard icon={Clock} label="Menunggu Penilaian Akhir" value={stats.pendingAssessments} tone="orange" />
          <StatCard icon={GraduationCap} label="Magang Selesai" value={stats.completedInternships} tone="purple" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={stats.totalStudents} tone="blue" index={0} />
          <StatCard icon={ClipboardList} label="Total Tugas" value={stats.totalTasks} tone="green" index={1} />
          <StatCard icon={Clock} label="Menunggu Penilaian Akhir" value={stats.pendingAssessments} tone="orange" index={2} />
          <StatCard icon={GraduationCap} label="Magang Selesai" value={stats.completedInternships} tone="purple" index={3} />
```

- [ ] **Step 3: Student/Dashboard.jsx**

Change:
```jsx
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskStats?.total ?? 0} tone="blue" />
          <StatCard icon={CheckCircle2} label="Tugas Selesai" value={taskStats?.completed ?? 0} tone="green" />
          <StatCard icon={Clock} label="Tugas Pending" value={taskStats?.pending ?? 0} tone="amber" />
          <StatCard icon={GraduationCap} label="Sudah Dinilai" value={taskStats?.graded ?? 0} tone="purple" />
```
to:
```jsx
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskStats?.total ?? 0} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Tugas Selesai" value={taskStats?.completed ?? 0} tone="green" index={1} />
          <StatCard icon={Clock} label="Tugas Pending" value={taskStats?.pending ?? 0} tone="amber" index={2} />
          <StatCard icon={GraduationCap} label="Sudah Dinilai" value={taskStats?.graded ?? 0} tone="purple" index={3} />
```

- [ ] **Step 4: Supervisor/Analytics/Dashboard.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={totalStudents} tone="blue" />
          <StatCard icon={ClipboardList} label="Total Tugas" value={totalTasks} tone="purple" />
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="green" />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedSubmissions} hint={`${completePercent}% selesai`} tone="orange" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={totalStudents} tone="blue" index={0} />
          <StatCard icon={ClipboardList} label="Total Tugas" value={totalTasks} tone="purple" index={1} />
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="green" index={2} />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedSubmissions} hint={`${completePercent}% selesai`} tone="orange" index={3} />
```

- [ ] **Step 5: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Admin/Dashboard.jsx resources/js/Pages/Supervisor/Dashboard.jsx resources/js/Pages/Student/Dashboard.jsx resources/js/Pages/Supervisor/Analytics/Dashboard.jsx
git commit -m "feat: Add stagger index to StatCard on dashboard pages"
```

---

### Task 7: Add stagger index to StatCard call sites — Attendance pages

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Index.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Approvals.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Approvals.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Reports.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Reports.jsx`
- Modify: `resources/js/Pages/Student/Attendance/Index.jsx`
- Modify: `resources/js/Pages/Student/Attendance/History.jsx`

- [ ] **Step 1: Admin/Attendance/Index.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" index={0} />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" index={1} />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" index={2} />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" index={3} />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" index={4} />
```

- [ ] **Step 2: Supervisor/Attendance/Index.jsx**

Identical change to Step 1, in the supervisor version (same 5 cards, same labels/props, only the surrounding file differs):
```jsx
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" index={0} />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" index={1} />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" index={2} />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" index={3} />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" index={4} />
```

- [ ] **Step 3: Admin/Attendance/Approvals.jsx**

Change:
```jsx
          <StatCard icon={Clock} label="Pending Absensi" value={pendingAttendances.total} tone="amber" />
          <StatCard icon={FileText} label="Pending Izin" value={pendingExceptions.total} tone="blue" />
```
to:
```jsx
          <StatCard icon={Clock} label="Pending Absensi" value={pendingAttendances.total} tone="amber" index={0} />
          <StatCard icon={FileText} label="Pending Izin" value={pendingExceptions.total} tone="blue" index={1} />
```

- [ ] **Step 4: Supervisor/Attendance/Approvals.jsx**

Identical change to Step 3:
```jsx
          <StatCard icon={Clock} label="Pending Absensi" value={pendingAttendances.total} tone="amber" index={0} />
          <StatCard icon={FileText} label="Pending Izin" value={pendingExceptions.total} tone="blue" index={1} />
```

- [ ] **Step 5: Admin/Attendance/Reports.jsx**

Change:
```jsx
              <StatCard label="Total Mahasiswa" value={summary.length} tone="neutral" />
              <StatCard label="Rata-rata Hadir" value={average(summary, 'present')} tone="green" />
              <StatCard label="Rata-rata Terlambat" value={average(summary, 'late')} tone="amber" />
              <StatCard label="Rata-rata Tidak Hadir" value={average(summary, 'absent')} tone="red" />
```
to:
```jsx
              <StatCard label="Total Mahasiswa" value={summary.length} tone="neutral" index={0} />
              <StatCard label="Rata-rata Hadir" value={average(summary, 'present')} tone="green" index={1} />
              <StatCard label="Rata-rata Terlambat" value={average(summary, 'late')} tone="amber" index={2} />
              <StatCard label="Rata-rata Tidak Hadir" value={average(summary, 'absent')} tone="red" index={3} />
```

- [ ] **Step 6: Supervisor/Attendance/Reports.jsx**

Identical change to Step 5:
```jsx
              <StatCard label="Total Mahasiswa" value={summary.length} tone="neutral" index={0} />
              <StatCard label="Rata-rata Hadir" value={average(summary, 'present')} tone="green" index={1} />
              <StatCard label="Rata-rata Terlambat" value={average(summary, 'late')} tone="amber" index={2} />
              <StatCard label="Rata-rata Tidak Hadir" value={average(summary, 'absent')} tone="red" index={3} />
```

- [ ] **Step 7: Student/Attendance/Index.jsx**

Change:
```jsx
          <StatCard icon={CheckCircle2} label="Hadir (7 hari)" value={presentCount} tone="green" />
          <StatCard icon={AlertTriangle} label="Terlambat (7 hari)" value={lateCount} tone="orange" />
          <StatCard icon={FileText} label="Pengajuan Pending" value={pendingExceptions ?? 0} tone="blue" />
```
to:
```jsx
          <StatCard icon={CheckCircle2} label="Hadir (7 hari)" value={presentCount} tone="green" index={0} />
          <StatCard icon={AlertTriangle} label="Terlambat (7 hari)" value={lateCount} tone="orange" index={1} />
          <StatCard icon={FileText} label="Pengajuan Pending" value={pendingExceptions ?? 0} tone="blue" index={2} />
```

- [ ] **Step 8: Student/Attendance/History.jsx**

Change:
```jsx
            <StatCard icon={CheckCircle2} label="Total Hadir" value={presentCount} tone="green" />
            <StatCard icon={AlertTriangle} label="Terlambat" value={lateCount} tone="orange" />
            <StatCard icon={XCircle} label="Tidak Hadir" value={absentCount} tone="red" />
            <StatCard icon={FileText} label="Pengajuan Izin" value={exceptions.length} tone="blue" />
```
to:
```jsx
            <StatCard icon={CheckCircle2} label="Total Hadir" value={presentCount} tone="green" index={0} />
            <StatCard icon={AlertTriangle} label="Terlambat" value={lateCount} tone="orange" index={1} />
            <StatCard icon={XCircle} label="Tidak Hadir" value={absentCount} tone="red" index={2} />
            <StatCard icon={FileText} label="Pengajuan Izin" value={exceptions.length} tone="blue" index={3} />
```

- [ ] **Step 9: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 10: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Index.jsx resources/js/Pages/Supervisor/Attendance/Index.jsx resources/js/Pages/Admin/Attendance/Approvals.jsx resources/js/Pages/Supervisor/Attendance/Approvals.jsx resources/js/Pages/Admin/Attendance/Reports.jsx resources/js/Pages/Supervisor/Attendance/Reports.jsx resources/js/Pages/Student/Attendance/Index.jsx resources/js/Pages/Student/Attendance/History.jsx
git commit -m "feat: Add stagger index to StatCard on attendance pages"
```

---

### Task 8: Add stagger index to StatCard call sites — Tasks, Submissions, Students, Logbooks, Monitoring, Plotting

**Files:**
- Modify: `resources/js/Pages/Supervisor/Tasks/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Submissions/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Students/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Logbooks/Index.jsx`
- Modify: `resources/js/Pages/Admin/Monitoring/Index.jsx`
- Modify: `resources/js/Pages/Admin/Plotting.jsx`

- [ ] **Step 1: Supervisor/Tasks/Index.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" />
          <StatCard icon={ClipboardList} label="Total Tugas (halaman ini)" value={totalTasks} tone="green" />
          <StatCard icon={Clock} label="Tugas Aktif (halaman ini)" value={activeTasks} tone="amber" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" index={0} />
          <StatCard icon={ClipboardList} label="Total Tugas (halaman ini)" value={totalTasks} tone="green" index={1} />
          <StatCard icon={Clock} label="Tugas Aktif (halaman ini)" value={activeTasks} tone="amber" index={2} />
```

- [ ] **Step 2: Supervisor/Submissions/Index.jsx**

Change:
```jsx
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="blue" />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedCount} tone="green" />
          <StatCard icon={Clock} label="Menunggu Nilai" value={pendingCount} tone="amber" />
```
to:
```jsx
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedCount} tone="green" index={1} />
          <StatCard icon={Clock} label="Menunggu Nilai" value={pendingCount} tone="amber" index={2} />
```

- [ ] **Step 3: Supervisor/Students/Index.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" />
          <StatCard icon={FileCheck} label="Dokumen Lengkap" value={completedDocs} tone="green" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" index={0} />
          <StatCard icon={FileCheck} label="Dokumen Lengkap" value={completedDocs} tone="green" index={1} />
```

- [ ] **Step 4: Supervisor/Logbooks/Index.jsx**

Change:
```jsx
          <StatCard icon={NotebookPen} label="Total Logbook" value={logbooks.total} tone="blue" />
          <StatCard icon={CheckCircle2} label="Telah Dilihat" value={verifiedCount} hint="halaman ini" tone="green" />
          <StatCard icon={Clock} label="Belum Dilihat" value={unverifiedCount} hint="halaman ini" tone="amber" />
          <StatCard icon={CalendarDays} label="Hari Ini" value={todayCount} hint="halaman ini" tone="purple" />
```
to:
```jsx
          <StatCard icon={NotebookPen} label="Total Logbook" value={logbooks.total} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Telah Dilihat" value={verifiedCount} hint="halaman ini" tone="green" index={1} />
          <StatCard icon={Clock} label="Belum Dilihat" value={unverifiedCount} hint="halaman ini" tone="amber" index={2} />
          <StatCard icon={CalendarDays} label="Hari Ini" value={todayCount} hint="halaman ini" tone="purple" index={3} />
```

- [ ] **Step 5: Admin/Monitoring/Index.jsx**

Change:
```jsx
          <StatCard icon={UserCog} label="Supervisor" value={stats.total_supervisors} tone="blue" />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={stats.total_students} tone="green" />
          <StatCard icon={Users} label="Rata-rata/Supervisor" value={stats.avg_students_per_supervisor} tone="orange" />
          <StatCard icon={ClipboardList} label="Submission Pending" value={stats.active_submissions} tone="purple" />
```
to:
```jsx
          <StatCard icon={UserCog} label="Supervisor" value={stats.total_supervisors} tone="blue" index={0} />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={stats.total_students} tone="green" index={1} />
          <StatCard icon={Users} label="Rata-rata/Supervisor" value={stats.avg_students_per_supervisor} tone="orange" index={2} />
          <StatCard icon={ClipboardList} label="Submission Pending" value={stats.active_submissions} tone="purple" index={3} />
```

- [ ] **Step 6: Admin/Plotting.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.length} tone="blue" />
          <StatCard icon={CheckCircle2} label="Sudah Ditugaskan" value={assignedCount} tone="green" />
          <StatCard icon={Clock} label="Belum Ditugaskan" value={unassignedCount} tone="orange" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Mahasiswa" value={students.length} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Sudah Ditugaskan" value={assignedCount} tone="green" index={1} />
          <StatCard icon={Clock} label="Belum Ditugaskan" value={unassignedCount} tone="orange" index={2} />
```

- [ ] **Step 7: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 8: Commit**

```bash
git add resources/js/Pages/Supervisor/Tasks/Index.jsx resources/js/Pages/Supervisor/Submissions/Index.jsx resources/js/Pages/Supervisor/Students/Index.jsx resources/js/Pages/Supervisor/Logbooks/Index.jsx resources/js/Pages/Admin/Monitoring/Index.jsx resources/js/Pages/Admin/Plotting.jsx
git commit -m "feat: Add stagger index to StatCard on tasks/submissions/students/logbooks/monitoring/plotting pages"
```

---

### Task 9: Add stagger index to StatCard call sites — Analytics pages

**Files:**
- Modify: `resources/js/Pages/Supervisor/Analytics/TaskAnalytics.jsx`
- Modify: `resources/js/Pages/Supervisor/Analytics/StudentReport.jsx`

- [ ] **Step 1: Supervisor/Analytics/TaskAnalytics.jsx**

Change:
```jsx
          <StatCard icon={Users} label="Total Assigned" value={submissionStats.total_assigned} tone="blue" />
          <StatCard icon={FileCheck} label="Submitted" value={submissionStats.submitted} tone="green" />
          <StatCard icon={UserX} label="Not Submitted" value={submissionStats.not_submitted} tone="red" />
          <StatCard icon={TrendingUp} label="Submission Rate" value={`${submissionStats.submission_rate}%`} tone="orange" />
```
to:
```jsx
          <StatCard icon={Users} label="Total Assigned" value={submissionStats.total_assigned} tone="blue" index={0} />
          <StatCard icon={FileCheck} label="Submitted" value={submissionStats.submitted} tone="green" index={1} />
          <StatCard icon={UserX} label="Not Submitted" value={submissionStats.not_submitted} tone="red" index={2} />
          <StatCard icon={TrendingUp} label="Submission Rate" value={`${submissionStats.submission_rate}%`} tone="orange" index={3} />
```

- [ ] **Step 2: Supervisor/Analytics/StudentReport.jsx**

Change:
```jsx
          <StatCard icon={ClipboardList} label="Tugas Diberikan" value={taskStats.assigned} tone="blue" />
          <StatCard icon={FileCheck} label="Dikumpulkan" value={taskStats.submitted} tone="green" />
          <StatCard icon={Award} label="Dinilai" value={taskStats.graded} tone="purple" />
          <StatCard icon={TrendingUp} label="Completion Rate" value={`${taskStats.completion_rate}%`} tone="orange" />
```
to:
```jsx
          <StatCard icon={ClipboardList} label="Tugas Diberikan" value={taskStats.assigned} tone="blue" index={0} />
          <StatCard icon={FileCheck} label="Dikumpulkan" value={taskStats.submitted} tone="green" index={1} />
          <StatCard icon={Award} label="Dinilai" value={taskStats.graded} tone="purple" index={2} />
          <StatCard icon={TrendingUp} label="Completion Rate" value={`${taskStats.completion_rate}%`} tone="orange" index={3} />
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Analytics/TaskAnalytics.jsx resources/js/Pages/Supervisor/Analytics/StudentReport.jsx
git commit -m "feat: Add stagger index to StatCard on analytics pages"
```

---

### Task 10: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Build check**

```bash
npm run build
```
Expected: build succeeds, `motion` appears bundled.

- [ ] **Step 2: Confirm every StatCard call site got an index**

```bash
grep -rn "<StatCard" resources/js/Pages --include="*.jsx" | grep -v "index="
```
Expected: no output (every one of the 55 call sites across 20 files now has an `index` prop).

- [ ] **Step 3: Manual browser check — modals**

Open and close each of the 4 animated components (a `ConfirmDialog` via any delete/approve action, `PhotoLightbox` via an attendance photo thumbnail, `ApprovalModal` via an attendance/exception review, `AttendanceModal` via student check-in). Confirm each fades+scales in and out smoothly instead of popping instantly.

- [ ] **Step 4: Manual browser check — page transitions**

Navigate between at least 3 different pages (e.g. Dashboard → Attendance → Tasks) as any role. Confirm a brief fade+shift transition on each navigation, without a jarring flash or double-render.

- [ ] **Step 5: Manual browser check — stat card stagger**

Load a page with multiple stat cards (e.g. Admin Attendance Index, which now has 5). Confirm they animate in with a visible left-to-right (or top-to-bottom on mobile) staggered sequence rather than all appearing simultaneously.

- [ ] **Step 6: Commit any fixes found during manual verification**

Only if Steps 3-5 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
