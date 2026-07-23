# Custom Notifications (Toast + Confirm Dialog) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace every remaining browser-native `alert()` (9 call sites, 4 files) and `confirm()` (10 call sites, 10 files) with a custom toast (sonner) and a custom `ConfirmDialog` + `useConfirm()` hook, both mounted once globally in `AppShell.jsx`.

**Architecture:** `sonner` provides the toast primitive; a thin re-export at `resources/js/lib/toast.js` is the only import path call sites use. A new `ConfirmDialog.jsx` component + `useConfirm.jsx` hook/provider replace `confirm()` with a Promise-based `confirm({ title, description, confirmLabel, variant })` API that mirrors the old callback style closely enough that each call site only needs to add `await` and swap the message into the new shape. Both providers mount once in `AppShell.jsx`, so every role layout gets them for free.

**Tech Stack:** React 19, Inertia.js, `sonner` (new dependency), Tailwind CSS.

---

### Task 1: Add sonner dependency and toast helper

**Files:**
- Modify: `package.json`
- Create: `resources/js/lib/toast.js`

- [ ] **Step 1: Install sonner**

```bash
npm install sonner@^2.0.7
```

- [ ] **Step 2: Create the toast helper**

```js
export { toast } from 'sonner'
```

Save as `resources/js/lib/toast.js`. This single re-export point means every call site imports from `@/lib/toast` rather than `sonner` directly — if the toast library is ever swapped later, only this file changes.

- [ ] **Step 3: Verify**

```bash
npm run build
```
Expected: build succeeds (sonner isn't mounted anywhere yet, so this just confirms the install and re-export file are valid).

- [ ] **Step 4: Commit**

```bash
git add package.json package-lock.json resources/js/lib/toast.js
git commit -m "chore: Add sonner dependency and a toast re-export helper"
```

---

### Task 2: Build the ConfirmDialog component and useConfirm hook

**Files:**
- Create: `resources/js/Components/ConfirmDialog.jsx`
- Create: `resources/js/hooks/useConfirm.jsx`

- [ ] **Step 1: Write the ConfirmDialog component**

```jsx
import { Button } from '@/Components/ui/button'

/**
 * Presentational confirm modal. Controlled entirely by useConfirm()'s
 * provider - this component has no state of its own, it just renders
 * whatever the provider currently holds.
 */
export default function ConfirmDialog({ open, title, description, confirmLabel, cancelLabel, variant, onConfirm, onCancel }) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-[100] bg-black/50" onClick={(e) => e.target === e.currentTarget && onCancel()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-lg">
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
        </div>
      </div>
    </div>
  )
}
```

- [ ] **Step 2: Write the useConfirm hook + provider**

```jsx
import { createContext, useCallback, useContext, useRef, useState } from 'react'
import ConfirmDialog from '@/Components/ConfirmDialog'

const ConfirmContext = createContext(null)

/**
 * Mounted once in AppShell.jsx. Holds the dialog's current options and
 * the pending promise's resolve function, so any descendant component
 * can call useConfirm()'s confirm() and await a boolean result - the
 * same call-site shape as the old window.confirm(), just async.
 */
export function ConfirmDialogProvider({ children }) {
  const [state, setState] = useState(null) // null when closed, else the options object
  const resolveRef = useRef(null)

  const confirm = useCallback((options) => {
    setState(options)
    return new Promise((resolve) => {
      resolveRef.current = resolve
    })
  }, [])

  function handleConfirm() {
    resolveRef.current?.(true)
    setState(null)
  }

  function handleCancel() {
    resolveRef.current?.(false)
    setState(null)
  }

  return (
    <ConfirmContext.Provider value={confirm}>
      {children}
      <ConfirmDialog
        open={Boolean(state)}
        title={state?.title}
        description={state?.description}
        confirmLabel={state?.confirmLabel}
        cancelLabel={state?.cancelLabel}
        variant={state?.variant}
        onConfirm={handleConfirm}
        onCancel={handleCancel}
      />
    </ConfirmContext.Provider>
  )
}

/** Returns confirm({ title, description?, confirmLabel?, cancelLabel?, variant? }) => Promise<boolean> */
export function useConfirm() {
  const confirm = useContext(ConfirmContext)
  if (!confirm) {
    throw new Error('useConfirm() must be used within a ConfirmDialogProvider')
  }
  return confirm
}
```

- [ ] **Step 3: Verify syntax**

```bash
npm run build
```
Expected: build succeeds (neither file is imported/mounted anywhere yet, so this just confirms both are valid JSX/JS).

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/ConfirmDialog.jsx resources/js/hooks/useConfirm.jsx
git commit -m "feat: Add ConfirmDialog component and useConfirm hook"
```

---

### Task 3: Mount Toaster and ConfirmDialogProvider in AppShell

**Files:**
- Modify: `resources/js/Layouts/AppShell.jsx`

- [ ] **Step 1: Add the imports**

At the top of `AppShell.jsx`, add two imports alongside the existing ones:

```jsx
import { Toaster } from 'sonner'
import { ConfirmDialogProvider } from '@/hooks/useConfirm'
```

- [ ] **Step 2: Wrap the existing render output**

The component currently returns a single top-level `<div className="min-h-screen bg-background">...</div>`. Wrap that whole returned tree in `<ConfirmDialogProvider>`, and add `<Toaster />` as a sibling right after the opening tag. Change:

```jsx
  return (
    <div className="min-h-screen bg-background">
      <aside
```

to:

```jsx
  return (
    <ConfirmDialogProvider>
      <Toaster position="top-right" richColors />
      <div className="min-h-screen bg-background">
        <aside
```

And change the closing of the function (the final lines of the file):

```jsx
        <main className="flex-1 p-4 sm:p-6 lg:p-8">{children}</main>
      </div>
    </div>
  )
}
```

to:

```jsx
        <main className="flex-1 p-4 sm:p-6 lg:p-8">{children}</main>
      </div>
    </div>
    </ConfirmDialogProvider>
  )
}
```

(Every other line in the JSX tree between these two edited regions stays exactly as-is — only the outermost wrapper changes, and one level of indentation shifts for the pre-existing `<div className="min-h-screen bg-background">` block. Re-indent that block's children if your editor auto-formats; functionally nothing else changes.)

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Layouts/AppShell.jsx
git commit -m "feat: Mount Toaster and ConfirmDialogProvider globally in AppShell"
```

---

### Task 4: Replace alert() in ApprovalModal.jsx and ProfilePhotoCard.jsx

**Files:**
- Modify: `resources/js/Components/ApprovalModal.jsx`
- Modify: `resources/js/Components/ProfilePhotoCard.jsx`

- [ ] **Step 1: ApprovalModal.jsx**

Add the import:
```jsx
import { toast } from '@/lib/toast'
```

Change:
```jsx
    if (!decision) {
      alert('Pilih keputusan terlebih dahulu')
      return
    }
```
to:
```jsx
    if (!decision) {
      toast.error('Pilih keputusan terlebih dahulu')
      return
    }
```

- [ ] **Step 2: ProfilePhotoCard.jsx**

Add the import:
```jsx
import { toast } from '@/lib/toast'
```

Change all three:
```jsx
      alert(err.message)
```
```jsx
        alert('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba lagi atau gunakan perangkat/browser lain.')
```
```jsx
        alert('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
```
to:
```jsx
      toast.error(err.message)
```
```jsx
        toast.error('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba lagi atau gunakan perangkat/browser lain.')
```
```jsx
        toast.error('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/ApprovalModal.jsx resources/js/Components/ProfilePhotoCard.jsx
git commit -m "feat: Replace alert() with toast in ApprovalModal and ProfilePhotoCard"
```

---

### Task 5: Replace alert() in Student attendance files

**Files:**
- Modify: `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`
- Modify: `resources/js/Pages/Student/Attendance/Index.jsx`

- [ ] **Step 1: AttendanceModal.jsx**

Add the import:
```jsx
import { toast } from '@/lib/toast'
```

Change all three:
```jsx
      alert('Lokasi belum didapatkan, silakan tunggu...')
```
```jsx
      alert('Silakan ambil foto terlebih dahulu')
```
```jsx
        alert('Wajah tidak terdeteksi pada foto. Silakan pastikan wajah Anda terlihat jelas dan coba lagi.')
```
to:
```jsx
      toast.error('Lokasi belum didapatkan, silakan tunggu...')
```
```jsx
      toast.error('Silakan ambil foto terlebih dahulu')
```
```jsx
        toast.error('Wajah tidak terdeteksi pada foto. Silakan pastikan wajah Anda terlihat jelas dan coba lagi.')
```

- [ ] **Step 2: Index.jsx**

Add the import (alongside the existing ones):
```jsx
import { toast } from '@/lib/toast'
```

Change:
```jsx
      if (data.success) {
        setCheckInOpen(false)
        setCheckOutOpen(false)
        router.reload()
      } else {
        alert('Error: ' + data.message)
      }
    } catch (err) {
      // Non-2xx responses (e.g. 403 suspicious location, 500 server error)
      // land here with axios, unlike the previous fetch()-based version -
      // surface the server's own message when it sent one, so this stays
      // as informative as before rather than regressing to a generic alert.
      alert('Error: ' + (err.response?.data?.message ?? 'Terjadi kesalahan saat memproses absensi.'))
    } finally {
```
to:
```jsx
      if (data.success) {
        setCheckInOpen(false)
        setCheckOutOpen(false)
        router.reload()
      } else {
        toast.error(data.message)
      }
    } catch (err) {
      // Non-2xx responses (e.g. 403 suspicious location, 500 server error)
      // land here with axios, unlike the previous fetch()-based version -
      // surface the server's own message when it sent one, so this stays
      // as informative as before rather than regressing to a generic alert.
      toast.error(err.response?.data?.message ?? 'Terjadi kesalahan saat memproses absensi.')
    } finally {
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Student/Attendance/AttendanceModal.jsx resources/js/Pages/Student/Attendance/Index.jsx
git commit -m "feat: Replace alert() with toast in student attendance flow"
```

---

### Task 6: Replace confirm() in Messages/Show.jsx and Notifications/Index.jsx

**Files:**
- Modify: `resources/js/Pages/Messages/Show.jsx`
- Modify: `resources/js/Pages/Supervisor/Tasks/Show.jsx` — NOT in this task, see Task 7
- Modify: `resources/js/Pages/Notifications/Index.jsx`

- [ ] **Step 1: Messages/Show.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Show` component, add the hook call near the top (alongside the existing `useForm`/`usePage` calls):
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleDelete() {
    if (confirm('Hapus pesan ini?')) {
      router.delete(r('messages.delete', message.id))
    }
  }
```
to:
```jsx
  async function handleDelete() {
    const confirmed = await confirm({ title: 'Hapus pesan ini?', variant: 'destructive' })
    if (confirmed) {
      router.delete(r('messages.delete', message.id))
    }
  }
```

- [ ] **Step 2: Notifications/Index.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Index` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function deleteAll() {
    if (!confirm('Hapus semua notifikasi?')) return
    router.delete(r('notifications.delete-all'), { preserveScroll: true })
  }
```
to:
```jsx
  async function deleteAll() {
    const confirmed = await confirm({ title: 'Hapus semua notifikasi?', variant: 'destructive' })
    if (!confirmed) return
    router.delete(r('notifications.delete-all'), { preserveScroll: true })
  }
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Messages/Show.jsx resources/js/Pages/Notifications/Index.jsx
git commit -m "feat: Replace confirm() with ConfirmDialog in Messages and Notifications"
```

---

### Task 7: Replace confirm() in Supervisor Tasks pages

**Files:**
- Modify: `resources/js/Pages/Supervisor/Tasks/Show.jsx`
- Modify: `resources/js/Pages/Supervisor/Tasks/Index.jsx`

- [ ] **Step 1: Tasks/Show.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Show` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }
```
to:
```jsx
  async function handleDelete() {
    const confirmed = await confirm({
      title: 'Hapus tugas ini?',
      description: 'Tindakan ini tidak dapat dibatalkan.',
      variant: 'destructive',
    })
    if (confirmed) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }
```

- [ ] **Step 2: Tasks/Index.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Index` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleDelete(task) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }
```
to:
```jsx
  async function handleDelete(task) {
    const confirmed = await confirm({
      title: 'Hapus tugas ini?',
      description: 'Tindakan ini tidak dapat dibatalkan.',
      variant: 'destructive',
    })
    if (confirmed) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }
```

- [ ] **Step 3: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Supervisor/Tasks/Show.jsx resources/js/Pages/Supervisor/Tasks/Index.jsx
git commit -m "feat: Replace confirm() with ConfirmDialog in supervisor tasks pages"
```

---

### Task 8: Replace confirm() in Admin and Supervisor Attendance Approvals/Index pages

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Approvals.jsx`
- Modify: `resources/js/Pages/Admin/Attendance/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Approvals.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Index.jsx`

- [ ] **Step 1: Admin/Attendance/Approvals.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Approvals` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    if (confirm(`Yakin ingin ${label} item ini?`)) {
      router.post(r('admin.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }
```
to:
```jsx
  async function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    const confirmed = await confirm({
      title: `Yakin ingin ${label} item ini?`,
      variant: action === 'approve' ? 'default' : 'destructive',
    })
    if (confirmed) {
      router.post(r('admin.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }
```

- [ ] **Step 2: Admin/Attendance/Index.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Index` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function quickApprove(attendance) {
    if (confirm('Apakah Anda yakin ingin menyetujui absensi ini?')) {
      router.post(r('admin.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }
```
to:
```jsx
  async function quickApprove(attendance) {
    const confirmed = await confirm({ title: 'Setujui absensi ini?' })
    if (confirmed) {
      router.post(r('admin.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }
```

- [ ] **Step 3: Supervisor/Attendance/Approvals.jsx**

Identical change to Step 1, in the supervisor version — add the import, add `const confirm = useConfirm()`, and change:
```jsx
  function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    if (confirm(`Yakin ingin ${label} item ini?`)) {
      router.post(r('supervisor.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }
```
to:
```jsx
  async function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    const confirmed = await confirm({
      title: `Yakin ingin ${label} item ini?`,
      variant: action === 'approve' ? 'default' : 'destructive',
    })
    if (confirmed) {
      router.post(r('supervisor.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }
```

- [ ] **Step 4: Supervisor/Attendance/Index.jsx**

Identical change to Step 2, in the supervisor version — add the import, add `const confirm = useConfirm()`, and change:
```jsx
  function quickApprove(attendance) {
    if (confirm('Apakah Anda yakin ingin menyetujui absensi ini?')) {
      router.post(r('supervisor.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }
```
to:
```jsx
  async function quickApprove(attendance) {
    const confirmed = await confirm({ title: 'Setujui absensi ini?' })
    if (confirmed) {
      router.post(r('supervisor.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }
```

- [ ] **Step 5: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Approvals.jsx resources/js/Pages/Admin/Attendance/Index.jsx resources/js/Pages/Supervisor/Attendance/Approvals.jsx resources/js/Pages/Supervisor/Attendance/Index.jsx
git commit -m "feat: Replace confirm() with ConfirmDialog in attendance approval flows"
```

---

### Task 9: Replace confirm() in Announcements, AssessmentEdit, and Documents pages

**Files:**
- Modify: `resources/js/Pages/Admin/Announcements/Index.jsx`
- Modify: `resources/js/Pages/Supervisor/Students/AssessmentEdit.jsx`
- Modify: `resources/js/Pages/Student/Documents/Index.jsx`

- [ ] **Step 1: Admin/Announcements/Index.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Index` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleDelete(announcement) {
    if (confirm('Hapus pengumuman ini?')) {
      router.delete(r('admin.announcements.destroy', announcement.id))
    }
  }
```
to:
```jsx
  async function handleDelete(announcement) {
    const confirmed = await confirm({ title: 'Hapus pengumuman ini?', variant: 'destructive' })
    if (confirmed) {
      router.delete(r('admin.announcements.destroy', announcement.id))
    }
  }
```

- [ ] **Step 2: Supervisor/Students/AssessmentEdit.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `AssessmentEdit` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleSubmit(e) {
    e.preventDefault()
    if (!confirm('Apakah Anda yakin ingin menyimpan perubahan penilaian ini?')) return
    patch(r('supervisor.students.assessment.update', student.id))
  }
```
to:
```jsx
  async function handleSubmit(e) {
    e.preventDefault()
    const confirmed = await confirm({ title: 'Simpan perubahan penilaian ini?' })
    if (!confirmed) return
    patch(r('supervisor.students.assessment.update', student.id))
  }
```

Note: this `handleSubmit` is a form's `onSubmit` handler — making it `async` is safe here since `e.preventDefault()` already runs synchronously before the `await`, so the browser's native form submission is still blocked regardless of how long the confirm dialog stays open.

- [ ] **Step 3: Student/Documents/Index.jsx**

Add the import:
```jsx
import { useConfirm } from '@/hooks/useConfirm'
```

Inside the `Index` component, add:
```jsx
  const confirm = useConfirm()
```

Change:
```jsx
  function handleDelete(document) {
    if (confirm(`Yakin ingin menghapus dokumen ${document.document_name}?`)) {
      router.delete(r('student.documents.destroy', document.id))
    }
  }
```
to:
```jsx
  async function handleDelete(document) {
    const confirmed = await confirm({
      title: `Hapus dokumen ${document.document_name}?`,
      variant: 'destructive',
    })
    if (confirmed) {
      router.delete(r('student.documents.destroy', document.id))
    }
  }
```

- [ ] **Step 4: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Pages/Admin/Announcements/Index.jsx resources/js/Pages/Supervisor/Students/AssessmentEdit.jsx resources/js/Pages/Student/Documents/Index.jsx
git commit -m "feat: Replace confirm() with ConfirmDialog in announcements, assessment, and documents pages"
```

---

### Task 10: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Confirm no native alert()/confirm() calls remain**

```bash
grep -rn "alert(\|confirm(" resources/js --include="*.jsx" | grep -v "useConfirm\|const confirm ="
```
Expected: no output (every prior call site has been converted; the only remaining occurrences of the string "confirm" are the `useConfirm` import/hook-call lines, which the grep filters out).

- [ ] **Step 2: Full build**

```bash
npm run build
```
Expected: build succeeds, `sonner` appears bundled into the shared chunk that includes `AppShell`.

- [ ] **Step 3: Manual browser check — toasts**

For at least 2 of the 4 files touched in Tasks 4-5 (e.g. try saving a profile photo with no face detected, and try submitting check-in without a captured photo), confirm a styled toast appears top-right and auto-dismisses, instead of a native browser alert.

- [ ] **Step 4: Manual browser check — confirm dialogs**

For at least 3 of the 10 confirm() call sites across different roles (e.g. delete a message as any role, delete a task as supervisor, quick-approve an attendance as admin), confirm:
- The custom modal appears (not a native browser confirm popup).
- Clicking "Batal" or the overlay closes it without performing the action.
- Clicking the confirm button performs the exact same action the old `confirm()`-gated code did.

- [ ] **Step 5: Commit any fixes found during manual verification**

Only if Steps 3-4 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
