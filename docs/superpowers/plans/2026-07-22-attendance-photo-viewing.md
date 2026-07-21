# Attendance Photo Viewing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let Admin and Supervisor see the check-in/check-out photos already captured by students, as thumbnails in the attendance monitoring/approval/suspicious tables and inside the shared approval modal, with a click-to-enlarge lightbox.

**Architecture:** Add two URL accessors to the `Attendance` model (auto-appended to every serialization, so no controller changes needed). Add two new shared React components (`AttendancePhotoThumb`, `PhotoLightbox`). Wire a "Foto" column into the 6 existing attendance tables (Admin + Supervisor × Index/Approvals/Suspicious) and a photo section into the shared `ApprovalModal`.

**Tech Stack:** Laravel 12 Eloquent accessors, React 19 + Inertia.js, Tailwind v3, lucide-react icons (`Camera`, `CameraOff`, `X`).

---

## Task 1: Add photo URL accessors to the Attendance model

**Files:**
- Modify: `app/Models/Attendance.php`

- [ ] **Step 1: Add `$appends` and the two accessor methods**

In `app/Models/Attendance.php`, add a new `protected $appends` property right after the existing `protected $casts` array (after line 58, before the `user()` method):

```php
    protected $appends = [
        'check_in_photo_url',
        'check_out_photo_url',
    ];
```

Then add the two accessor methods anywhere in the class body — place them right after `getWorkingHoursAttribute()` (after line 77, before `getIsLateAttribute()`):

```php
    public function getCheckInPhotoUrlAttribute()
    {
        return $this->check_in_photo ? Storage::disk('public')->url($this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute()
    {
        return $this->check_out_photo ? Storage::disk('public')->url($this->check_out_photo) : null;
    }
```

`Storage` is already imported at the top of this file (`use Illuminate\Support\Facades\Storage;`, line 8), so no new import is needed.

- [ ] **Step 2: Verify the file has no syntax errors**

Run: `php -l app/Models/Attendance.php`
Expected output: `No syntax errors detected in app/Models/Attendance.php`

- [ ] **Step 3: Verify the accessors actually produce URLs**

Run:
```bash
php artisan tinker --execute="
\$a = App\Models\Attendance::whereNotNull('check_in_photo')->first();
if (\$a) {
    echo 'check_in_photo path: ' . \$a->check_in_photo . PHP_EOL;
    echo 'check_in_photo_url: ' . \$a->check_in_photo_url . PHP_EOL;
} else {
    echo 'no attendance row with a check_in_photo exists to test against — accessor logic still verified by Step 2 syntax check' . PHP_EOL;
}
"
```
Expected: if a row with a photo exists, `check_in_photo_url` prints a URL string starting with your `APP_URL` value followed by `/storage/attendance/check-in/...`. If no such row exists yet, the fallback message prints — that's fine, it just means this environment has no test photo data yet; the Task 12 manual verification step will cover this instead if real data becomes available.

- [ ] **Step 4: Commit**

```bash
git add app/Models/Attendance.php
git commit -m "feat: Add check_in_photo_url/check_out_photo_url accessors

Auto-appended to every Attendance serialization so Inertia pages get
ready-to-use photo URLs without any controller changes. Returns null
when no photo was captured (upload has always been optional)."
```

---

## Task 2: Build the `AttendancePhotoThumb` component

**Files:**
- Create: `resources/js/Components/AttendancePhotoThumb.jsx`

- [ ] **Step 1: Create the component**

Create `resources/js/Components/AttendancePhotoThumb.jsx`:

```jsx
import { useState } from 'react'
import { Camera, CameraOff } from 'lucide-react'
import { cn } from '@/lib/utils'

/**
 * Small clickable photo thumbnail for attendance check-in/check-out
 * photos. Renders a neutral placeholder tile (crossed-out camera icon)
 * when there's no photo, or when the stored file fails to load — photo
 * upload has always been optional, so a missing photo is a normal,
 * expected state, not an error.
 */
export default function AttendancePhotoThumb({ url, label, onClick }) {
  const [failed, setFailed] = useState(false)
  const showPlaceholder = !url || failed

  if (showPlaceholder) {
    return (
      <div
        className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-muted"
        title={`${label}: tidak ada foto`}
        aria-label={`${label}: tidak ada foto`}
      >
        <CameraOff className="h-4 w-4 text-muted-foreground" />
      </div>
    )
  }

  return (
    <button
      type="button"
      onClick={onClick}
      className={cn(
        'group relative h-10 w-10 shrink-0 overflow-hidden rounded-md border border-border',
        'transition-opacity duration-150 hover:opacity-80',
      )}
      title={`Lihat foto ${label}`}
      aria-label={`Lihat foto ${label}`}
    >
      <img
        src={url}
        alt={label}
        onError={() => setFailed(true)}
        className="h-full w-full object-cover"
      />
      <span className="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-opacity duration-150 group-hover:bg-black/20 group-hover:opacity-100">
        <Camera className="h-4 w-4 text-white" />
      </span>
    </button>
  )
}
```

- [ ] **Step 2: Build to verify no import errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors (this component isn't imported anywhere yet, so the build just needs to not choke on the new file's own syntax — Vite only compiles files that are actually imported, so this step mainly guards against a typo breaking a later import; if the build succeeds trivially because the file isn't referenced yet, that's fine, Task 4 onward will exercise it for real).

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/AttendancePhotoThumb.jsx
git commit -m "feat: Add AttendancePhotoThumb component

Small clickable thumbnail with a placeholder state for missing/failed
photos. Shared across all attendance tables and the approval modal."
```

---

## Task 3: Build the `PhotoLightbox` component

**Files:**
- Create: `resources/js/Components/PhotoLightbox.jsx`

- [ ] **Step 1: Create the component**

Create `resources/js/Components/PhotoLightbox.jsx`:

```jsx
import { X } from 'lucide-react'

/**
 * Full-size photo overlay. Structurally mirrors ApprovalModal (fixed
 * inset overlay, click-outside-to-close, close button) for visual
 * consistency with the rest of the attendance review UI.
 */
export default function PhotoLightbox({ open, onClose, url, caption }) {
  if (!open || !url) return null

  return (
    <div className="fixed inset-0 z-[60] bg-black/70" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="relative max-w-2xl">
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
        </div>
      </div>
    </div>
  )
}
```

Note the `z-[60]` — one level above `ApprovalModal`'s `z-50`, since the lightbox needs to render on top of the modal when opened from inside it (Task 7).

- [ ] **Step 2: Build to verify no import errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/PhotoLightbox.jsx
git commit -m "feat: Add PhotoLightbox component

Full-size photo overlay, one z-index layer above ApprovalModal so it
can be opened from within the modal as well as from table thumbnails."
```

---

## Task 4: Add photo column to Admin/Attendance/Index.jsx

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Index.jsx`

- [ ] **Step 1: Add imports**

In `resources/js/Pages/Admin/Attendance/Index.jsx`, add two new imports right after the existing `import ApprovalModal from '@/Components/ApprovalModal'` (line 16):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

In the `Index` function body, right after the existing `const [modalItem, setModalItem] = useState(null)` (line 41), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" table header**

Change the `<TableHead>Mahasiswa</TableHead>` header row (lines 128-134) from:
```jsx
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead>Aksi</TableHead>
```
to:
```jsx
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead className="hidden lg:table-cell">Foto</TableHead>
                    <TableHead>Aksi</TableHead>
```

- [ ] **Step 4: Add the photo cell to each row**

In the `attendances.data.map((attendance) => ...)` block, add a new `<TableCell>` right after the "Approval" cell (after line 162's closing `</TableCell>`, before the "Aksi" `<TableCell>` on line 163). Change:
```jsx
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
```
to:
```jsx
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell className="hidden lg:table-cell">
                        <div className="flex items-center gap-1.5">
                          <AttendancePhotoThumb
                            url={attendance.check_in_photo_url}
                            label="Check-in"
                            onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                          />
                          <AttendancePhotoThumb
                            url={attendance.check_out_photo_url}
                            label="Check-out"
                            onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                          />
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
```

- [ ] **Step 5: Add photo URLs to the modal item, and render the lightbox**

Change the `setModalItem({...})` call (lines 169-174) from:
```jsx
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                            })}
```
to:
```jsx
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                              checkInPhotoUrl: attendance.check_in_photo_url,
                              checkOutPhotoUrl: attendance.check_out_photo_url,
                            })}
```

Then add the `<PhotoLightbox>` render right after the existing `<ApprovalModal>` block (after line 217's closing `/>`, before the closing `</AdminLayout>` on line 218). Change:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="admin"
      />
    </AdminLayout>
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

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </AdminLayout>
```

- [ ] **Step 6: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Index.jsx
git commit -m "feat: Show check-in/check-out photo thumbnails in Admin Attendance Index

Adds a Foto column with click-to-enlarge thumbnails, and threads photo
URLs into the ApprovalModal item so photos are visible there too."
```

---

## Task 5: Add photo column to Admin/Attendance/Approvals.jsx

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Approvals.jsx`

This page has TWO tables (attendance tab, exceptions tab). Only the attendance table gets a photo column — `AttendanceException` rows have no photo fields (per spec's non-goals).

- [ ] **Step 1: Add imports**

Add after `import ApprovalModal from '@/Components/ApprovalModal'` (line 15):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

After `const [modalType, setModalType] = useState('attendance')` (line 66), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" header to the attendance table only**

Change the attendance table's header (lines 136-143) from:
```jsx
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
```
to:
```jsx
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead className="hidden lg:table-cell">Foto</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
```

Do NOT modify the exceptions table header (lines 196-204) — it stays as-is, no Foto column.

- [ ] **Step 4: Add the photo cell to each attendance row**

In the attendance `pendingAttendances.data.map((attendance) => ...)` block, change (lines 166-169):
```jsx
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
```
to:
```jsx
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell className="hidden lg:table-cell">
                          <div className="flex items-center gap-1.5">
                            <AttendancePhotoThumb
                              url={attendance.check_in_photo_url}
                              label="Check-in"
                              onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                            />
                            <AttendancePhotoThumb
                              url={attendance.check_out_photo_url}
                              label="Check-out"
                              onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                            />
                          </div>
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
```

Do NOT add a photo cell to the exceptions table's rows.

- [ ] **Step 5: Thread photo URLs into the modal item, guarding for exceptions**

The `openModal(approvalType, item)` function (lines 77-85) is called for BOTH `'attendance'` and `'exception'` items. `AttendanceException` rows have no `check_in_photo_url`/`check_out_photo_url` fields, so accessing them is safe (`undefined`, not a crash) but we should only set them when the type is `'attendance'` to keep the modal item's shape intentional rather than accidentally-undefined. Change:
```jsx
  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
    })
  }
```
to:
```jsx
  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
      checkInPhotoUrl: approvalType === 'attendance' ? item.check_in_photo_url : null,
      checkOutPhotoUrl: approvalType === 'attendance' ? item.check_out_photo_url : null,
    })
  }
```

- [ ] **Step 6: Render the lightbox**

Change the end of the component (lines 254-261) from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="admin"
      />
    </AdminLayout>
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

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </AdminLayout>
```

- [ ] **Step 7: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 8: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Approvals.jsx
git commit -m "feat: Show check-in/check-out photos in Admin Attendance Approvals

Photo column added to the attendance-pending table only (exceptions
have no photo fields). ApprovalModal now receives photo URLs when the
item is an attendance record."
```

---

## Task 6: Add photo column to Admin/Attendance/Suspicious.jsx

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Suspicious.jsx`

This page has a pre-existing, out-of-scope bug: it reads `attendance.check_in_time` and `attendance.latitude`/`attendance.longitude`, which don't exist on the `Attendance` model (real columns are `check_in`, `check_in_latitude`, `check_in_longitude`). Per the design spec's non-goals, this bug is NOT being fixed here — the photo feature is additive and independent of it, using the correct `check_in_photo_url`/`check_out_photo_url` accessor names (which DO exist correctly, since Task 1 added them correctly regardless of this page's unrelated pre-existing bug).

- [ ] **Step 1: Add imports**

Add after `import ApprovalModal from '@/Components/ApprovalModal'` (line 13):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

After `const [modalItem, setModalItem] = useState(null)` (line 28), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" header**

Change the header row (lines 63-70) from:
```jsx
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
```
to:
```jsx
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead className="hidden lg:table-cell">Foto</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
```

- [ ] **Step 4: Add the photo cell to each row**

Change (lines 103-117):
```jsx
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
```
to:
```jsx
                    <TableCell className="hidden lg:table-cell">
                      <div className="flex items-center gap-1.5">
                        <AttendancePhotoThumb
                          url={attendance.check_in_photo_url}
                          label="Check-in"
                          onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                        />
                        <AttendancePhotoThumb
                          url={attendance.check_out_photo_url}
                          label="Check-out"
                          onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                        />
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
                          checkInPhotoUrl: attendance.check_in_photo_url,
                          checkOutPhotoUrl: attendance.check_out_photo_url,
                        })}
                      >
                        Review
                      </Button>
                    </TableCell>
```

- [ ] **Step 5: Render the lightbox**

Change the end of the component (lines 131-138) from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
        routePrefix="admin"
      />
    </AdminLayout>
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

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </AdminLayout>
```

- [ ] **Step 6: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Suspicious.jsx
git commit -m "feat: Show check-in/check-out photos in Admin Attendance Suspicious

Uses the correct check_in_photo_url/check_out_photo_url accessor names
(added in an earlier commit) — independent of this page's pre-existing,
out-of-scope check_in_time/latitude field-name bug, which is left
untouched per the design spec's non-goals."
```

---

## Task 7: Add photo section to the shared ApprovalModal

**Files:**
- Modify: `resources/js/Components/ApprovalModal.jsx`

- [ ] **Step 1: Add imports**

In `resources/js/Components/ApprovalModal.jsx`, change the top imports from:
```jsx
import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react'
import { CheckCircle2, XCircle, X } from 'lucide-react'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'
```
to:
```jsx
import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react'
import { CheckCircle2, XCircle, X } from 'lucide-react'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state inside the component**

Change:
```jsx
export default function ApprovalModal({ open, onClose, type, item, notesRequired = false, routePrefix }) {
  const [decision, setDecision] = useState('')
  const { data, setData, post, processing, errors, reset } = useForm({ action: '', notes: '' })
```
to:
```jsx
export default function ApprovalModal({ open, onClose, type, item, notesRequired = false, routePrefix }) {
  const [decision, setDecision] = useState('')
  const [lightbox, setLightbox] = useState(null)
  const { data, setData, post, processing, errors, reset } = useForm({ action: '', notes: '' })
```

- [ ] **Step 3: Reset lightbox state when the modal closes**

Change the existing reset effect:
```jsx
  useEffect(() => {
    if (!open) {
      setDecision('')
      reset()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])
```
to:
```jsx
  useEffect(() => {
    if (!open) {
      setDecision('')
      setLightbox(null)
      reset()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])
```

- [ ] **Step 4: Add the photo section, and render the lightbox above the modal**

Change the header block and the start of the form from:
```jsx
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
```
to:
```jsx
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
            {(item.checkInPhotoUrl || item.checkOutPhotoUrl) && (
              <div className="space-y-2">
                <label className="block text-sm font-medium text-foreground">Foto Presensi</label>
                <div className="flex items-center gap-3">
                  <AttendancePhotoThumb
                    url={item.checkInPhotoUrl}
                    label="Check-in"
                    onClick={() => setLightbox({ url: item.checkInPhotoUrl, caption: `Check-in — ${item.userName}` })}
                  />
                  <AttendancePhotoThumb
                    url={item.checkOutPhotoUrl}
                    label="Check-out"
                    onClick={() => setLightbox({ url: item.checkOutPhotoUrl, caption: `Check-out — ${item.userName}` })}
                  />
                </div>
              </div>
            )}

            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Keputusan</label>
```

Then change the very end of the component from:
```jsx
          </form>
        </div>
      </div>
    </div>
  )
}
```
to:
```jsx
          </form>
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

Note: the `<PhotoLightbox>` is placed as a sibling of the modal's inner content `<div>`, still inside the outermost `fixed inset-0` overlay `<div>` — this works because `PhotoLightbox` itself renders its own separate `fixed inset-0 z-[60]` overlay (from Task 3), which visually stacks above the modal's `z-50` overlay regardless of DOM nesting.

- [ ] **Step 5: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Components/ApprovalModal.jsx
git commit -m "feat: Show check-in/check-out photos inside ApprovalModal

Photo section appears only when the item carries photo URLs (i.e. not
for AttendanceException items). Clicking a thumbnail opens a
PhotoLightbox layered above the modal itself."
```

---

## Task 8: Add photo column to Supervisor/Attendance/Index.jsx

**Files:**
- Modify: `resources/js/Pages/Supervisor/Attendance/Index.jsx`

This page is structurally identical to `Admin/Attendance/Index.jsx` (verified: only route names, layout component, and description copy differ). Apply the same change as Task 4.

- [ ] **Step 1: Add imports**

Add after `import ApprovalModal from '@/Components/ApprovalModal'` (line 16):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

After `const [modalItem, setModalItem] = useState(null)` (line 41), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" table header**

Change (lines 123-129):
```jsx
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead>Aksi</TableHead>
```
to:
```jsx
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead className="hidden lg:table-cell">Foto</TableHead>
                    <TableHead>Aksi</TableHead>
```

- [ ] **Step 4: Add the photo cell to each row**

Change (lines 153-159):
```jsx
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
```
to:
```jsx
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell className="hidden lg:table-cell">
                        <div className="flex items-center gap-1.5">
                          <AttendancePhotoThumb
                            url={attendance.check_in_photo_url}
                            label="Check-in"
                            onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                          />
                          <AttendancePhotoThumb
                            url={attendance.check_out_photo_url}
                            label="Check-out"
                            onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                          />
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
```

- [ ] **Step 5: Thread photo URLs into the modal item, and render the lightbox**

Change (lines 164-169):
```jsx
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                            })}
```
to:
```jsx
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                              checkInPhotoUrl: attendance.check_in_photo_url,
                              checkOutPhotoUrl: attendance.check_out_photo_url,
                            })}
```

Change the end of the component (lines 206-213) from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="supervisor"
      />
    </SupervisorLayout>
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="supervisor"
      />

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </SupervisorLayout>
```

- [ ] **Step 6: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Index.jsx
git commit -m "feat: Show check-in/check-out photo thumbnails in Supervisor Attendance Index"
```

---

## Task 9: Add photo column to Supervisor/Attendance/Approvals.jsx

**Files:**
- Modify: `resources/js/Pages/Supervisor/Attendance/Approvals.jsx`

This page is structurally identical to `Admin/Attendance/Approvals.jsx` (verified: only route names, layout, and description copy differ). Apply the same change as Task 5.

- [ ] **Step 1: Add imports**

Add after `import ApprovalModal from '@/Components/ApprovalModal'` (line 15):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

After `const [modalType, setModalType] = useState('attendance')` (line 66), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" header to the attendance table only**

Change (lines 136-143):
```jsx
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
```
to:
```jsx
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead className="hidden lg:table-cell">Foto</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
```

Do NOT modify the exceptions table header — it stays as-is.

- [ ] **Step 4: Add the photo cell to each attendance row**

Change (lines 166-169):
```jsx
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
```
to:
```jsx
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell className="hidden lg:table-cell">
                          <div className="flex items-center gap-1.5">
                            <AttendancePhotoThumb
                              url={attendance.check_in_photo_url}
                              label="Check-in"
                              onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                            />
                            <AttendancePhotoThumb
                              url={attendance.check_out_photo_url}
                              label="Check-out"
                              onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                            />
                          </div>
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
```

Do NOT add a photo cell to the exceptions table's rows.

- [ ] **Step 5: Thread photo URLs into the modal item, guarding for exceptions**

Change:
```jsx
  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
    })
  }
```
to:
```jsx
  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
      checkInPhotoUrl: approvalType === 'attendance' ? item.check_in_photo_url : null,
      checkOutPhotoUrl: approvalType === 'attendance' ? item.check_out_photo_url : null,
    })
  }
```

- [ ] **Step 6: Render the lightbox**

Change the end of the component from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="supervisor"
      />
    </SupervisorLayout>
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="supervisor"
      />

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </SupervisorLayout>
```

- [ ] **Step 7: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 8: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Approvals.jsx
git commit -m "feat: Show check-in/check-out photos in Supervisor Attendance Approvals"
```

---

## Task 10: Add photo column to Supervisor/Attendance/Suspicious.jsx

**Files:**
- Modify: `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`

Unlike Admin's Suspicious page, the Supervisor version already correctly uses `attendance.check_in` (not the buggy `check_in_time`) — see the previous feature's implementation. This page needs only the photo addition, no other field-name concerns.

- [ ] **Step 1: Add imports**

Add after `import ApprovalModal from '@/Components/ApprovalModal'` (line 13):

```jsx
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
```

- [ ] **Step 2: Add lightbox state**

After `const [modalItem, setModalItem] = useState(null)` (line 28), add:

```jsx
  const [lightbox, setLightbox] = useState(null)
```

- [ ] **Step 3: Add a "Foto" header**

Change the header row from:
```jsx
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
```
to:
```jsx
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead className="hidden lg:table-cell">Foto</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
```

- [ ] **Step 4: Add the photo cell to each row**

Change:
```jsx
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
```
to:
```jsx
                    <TableCell className="hidden lg:table-cell">
                      <div className="flex items-center gap-1.5">
                        <AttendancePhotoThumb
                          url={attendance.check_in_photo_url}
                          label="Check-in"
                          onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                        />
                        <AttendancePhotoThumb
                          url={attendance.check_out_photo_url}
                          label="Check-out"
                          onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                        />
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
                          checkInPhotoUrl: attendance.check_in_photo_url,
                          checkOutPhotoUrl: attendance.check_out_photo_url,
                        })}
                      >
                        Review
                      </Button>
                    </TableCell>
```

- [ ] **Step 5: Render the lightbox**

Change the end of the component from:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
        routePrefix="supervisor"
      />
    </SupervisorLayout>
```
to:
```jsx
      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
        routePrefix="supervisor"
      />

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </SupervisorLayout>
```

- [ ] **Step 6: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Suspicious.jsx
git commit -m "feat: Show check-in/check-out photos in Supervisor Attendance Suspicious"
```

---

## Task 11: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: PHP lint the modified model**

Run: `php -l app/Models/Attendance.php`
Expected: `No syntax errors detected in app/Models/Attendance.php`

- [ ] **Step 2: Full frontend rebuild**

Run: `npm run build`
Expected: `✓ built in ...`, no errors or warnings about unresolved imports.

- [ ] **Step 3: Manual verification — thumbnails and placeholders render**

Start `php artisan serve --port=8000` (if not already running) and ensure `public/hot` doesn't exist (`rm -f public/hot`) so the built assets in `public/build` are served. Log in as `admin@bakti.com` / `1` and separately as `dede@baktitest.com` / `1` (supervisor). For each of the 6 pages — `/admin/attendance`, `/admin/attendance/approvals`, `/admin/attendance/suspicious`, `/supervisor/attendance`, `/supervisor/attendance/approvals`, `/supervisor/attendance/suspicious` — confirm:
- A "Foto" column appears (may be hidden below the `lg` breakpoint — widen the browser window past 1024px if needed, since the column uses `hidden lg:table-cell`).
- Rows without a captured photo show the gray placeholder tile with a crossed-out camera icon, not a broken image.
- If any real photo data exists, its thumbnail renders as an actual image; clicking it opens the `PhotoLightbox` with the full-size photo and a caption.
- Clicking "Detail"/"Review" opens `ApprovalModal`, which also shows the photo section (or omits it entirely if there's no photo — confirm the section doesn't render as an empty box when both URLs are null, since the condition is `(item.checkInPhotoUrl || item.checkOutPhotoUrl)`).
- In Approvals pages, switch to the "Pengajuan Izin" (exceptions) tab and confirm it still renders normally with no Foto column and no crash (exceptions have no photo fields).

- [ ] **Step 4: Manual verification — dark mode and responsive**

On `/supervisor/attendance`, toggle dark mode and confirm the placeholder tile and thumbnails remain legible (no invisible borders/icons). Resize to 375px width and confirm no horizontal overflow (the Foto column is hidden below `lg` anyway, so this should be unaffected, but verify the rest of the row layout is undisturbed).

- [ ] **Step 5: Commit (only if any fixes were needed)**

If Steps 1-4 all pass cleanly with no code changes needed, there is nothing to commit here.

---

## Self-review notes (completed during plan authoring, not a task to run)

- **Spec coverage**: Model accessors (Task 1), `AttendancePhotoThumb` (Task 2), `PhotoLightbox` (Task 3), all 6 pages (Tasks 4-6, 8-10), `ApprovalModal` (Task 7), placeholder behavior for missing/failed photos (built into `AttendancePhotoThumb` itself, Task 2), `AttendanceException` rows correctly excluded from photo columns (Tasks 5 and 9 explicitly skip the exceptions table and guard the modal item), dark mode / responsive manual checks (Task 11) — all spec sections have a corresponding task.
- **Fixed during authoring**: initially considered adding photo fields to the `AttendanceException` branch of `openModal()` unconditionally, then caught that `AttendanceException` has no photo columns at all — added the `approvalType === 'attendance' ? ... : null` guard in Tasks 5 and 9 so the modal item's shape stays intentional rather than silently reading `undefined` off the wrong model type.
- **Type/name consistency**: `checkInPhotoUrl`/`checkOutPhotoUrl` (camelCase, JS-side modal item fields) are used identically across every page and inside `ApprovalModal` itself — no mismatched casing or naming between Tasks 4-10 and Task 7. `check_in_photo_url`/`check_out_photo_url` (snake_case, PHP accessor names / Inertia prop names straight off the `Attendance` model) are used identically in every page's `attendance.check_in_photo_url` reads — matches exactly what Task 1 defines via Laravel's automatic `getXAttribute()` → `x_attribute` snake_case conversion.
- **No placeholders**: every step has complete, exact code — no "similar to Task N" shortcuts (each of Tasks 8-10 repeats the full diff inline even though structurally parallel to Tasks 4-6, since a worker may execute tasks out of order).
