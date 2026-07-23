# Custom Notifications (Toast + Confirm Dialog) — Design

## Goal

Replace every remaining browser-native `alert()` and `confirm()` call across the app with custom, app-styled equivalents: a toast notification for one-off success/error messages, and a modal confirm dialog for yes/no confirmations before destructive or important actions.

## Non-goals

- No change to `ApprovalModal.jsx` — it already has its own notes/textarea-based confirmation flow for attendance/exception approvals and stays as-is.
- No change to `FlashBanner` or Inertia's server-side flash-message flow — this feature only replaces client-side, JS-triggered `alert()`/`confirm()` calls.
- No redesign of any page's layout beyond adding the two new global providers.

## Scope: every call site being replaced

**`alert()` → toast (9 call sites, 4 files):**
- `Components/ApprovalModal.jsx` — "Pilih keputusan terlebih dahulu"
- `Components/ProfilePhotoCard.jsx` — crop/face-detection errors (3 messages)
- `Pages/Student/Attendance/AttendanceModal.jsx` — location/photo/face validation (3 messages)
- `Pages/Student/Attendance/Index.jsx` — check-in/out submit errors (2 messages)

**`confirm()` → ConfirmDialog (10 call sites, 10 files):**
- `Pages/Messages/Show.jsx` — delete message
- `Pages/Notifications/Index.jsx` — delete all notifications
- `Pages/Supervisor/Tasks/Show.jsx` and `Tasks/Index.jsx` — delete task
- `Pages/Admin/Attendance/Approvals.jsx` and `Index.jsx` — approve/reject attendance
- `Pages/Supervisor/Attendance/Approvals.jsx` and `Index.jsx` — approve/reject attendance
- `Pages/Admin/Announcements/Index.jsx` — delete announcement
- `Pages/Supervisor/Students/AssessmentEdit.jsx` — save assessment changes
- `Pages/Student/Documents/Index.jsx` — delete document

## 1. Toast notifications

**New dependency:** `sonner`.

**Mounting:** A single `<Toaster position="top-right" richColors />` is added once to `resources/js/Layouts/AppShell.jsx` — the shared shell every role layout (Admin/Supervisor/Student) renders through, so it's available everywhere without per-page wiring.

**Helper:** `resources/js/lib/toast.js` re-exports sonner's `toast` object directly (no wrapping needed beyond a single import point, so call sites do `import { toast } from '@/lib/toast'` rather than `from 'sonner'` — keeps the dependency name swappable later without touching every call site).

**Call-site changes:** every `alert(message)` becomes `toast.error(message)` for error paths, or `toast.success(message)` for the (rare) success-style alert. Each replacement keeps the exact same Indonesian message text already in place — this is a mechanical transport change, not a copy rewrite.

## 2. Confirm dialog

**New component:** `resources/js/Components/ConfirmDialog.jsx` — a centered modal with a dark overlay, a title, a description, and two buttons ("Batal" and a confirm button whose label and variant are configurable — `destructive` red for deletions, default for approvals/saves). Visually consistent with the existing `AttendanceModal`'s overlay/centering pattern already used elsewhere in the app.

**New hook + provider:** `resources/js/hooks/useConfirm.jsx` exports:
- `ConfirmDialogProvider` — mounted once in `AppShell.jsx` (alongside `<Toaster />`), holds the dialog's open/closed state and the pending Promise's resolve function.
- `useConfirm()` — returns a `confirm(options)` function with signature `confirm({ title, description, confirmLabel?, variant? }): Promise<boolean>`. Calling it opens the dialog and returns a promise that resolves `true` if the user clicks confirm, `false` if they click "Batal" or dismiss the overlay/Escape.

**Call-site changes:** every `if (confirm('message text')) { ...action... }` becomes:
```js
const confirmed = await confirm({ title: 'message text', variant: 'destructive' })
if (confirmed) { ...action... }
```
using the exact same message text as the title (or split into title/description where a call site's message is long enough to warrant it, e.g. the "tidak dapat dibatalkan" task-deletion warnings). The enclosing function becomes `async` where it isn't already (all 10 call sites are already inside event handlers, none are render-time code, so this is safe).

## Error handling

- Toast and dialog are pure client-side UI — neither has a failure mode of its own beyond what the action they wrap already handles (e.g., a rejected Inertia request still surfaces via the same `err.response?.data?.message` pattern already in place, just routed through `toast.error` instead of `alert`).
- Dismissing the confirm dialog (Escape, overlay click, "Batal") always resolves `false` — callers already gate their action behind `if (confirmed)`, so no explicit dismiss-handling change is needed at call sites.

## Testing approach

No automated test framework exists in this repo (consistent with every prior feature this session) — verification is `php -l`/`npm run build` plus manual browser checks: trigger each of the 9 toast call sites and confirm a toast (not a native alert) appears with the correct message and styling (error vs success); trigger each of the 10 confirm call sites and confirm the modal appears, "Batal" cancels the action, and confirming proceeds exactly as the old `confirm()`-gated code did.
