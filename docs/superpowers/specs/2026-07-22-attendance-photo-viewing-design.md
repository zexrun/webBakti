# Attendance Photo Viewing — Design Spec

## Background

The `Attendance` model already stores optional check-in/check-out photos (`check_in_photo`, `check_out_photo` — nullable string paths, populated by `AttendanceController::checkIn()`/`checkOut()` via `$request->file('photo')->store('attendance/check-in', 'public')` / `'attendance/check-out'`). These are stored on Laravel's `public` disk, and the `public/storage` symlink already exists and is populated with real files.

No page anywhere in the app — Admin, Supervisor, or Student — currently displays these photos. They are captured and stored but never surfaced in the UI. This spec adds the ability to *view* them; it does not change how they're captured or uploaded.

## Goal

Supervisors and Admins reviewing attendance (in Index/monitoring, Approvals, and Suspicious pages) can see the check-in and check-out photos for any attendance record, both as a small in-table thumbnail and enlarged in a lightbox. The existing shared `ApprovalModal` also shows the photos for the record being approved/rejected.

## Non-goals

- No changes to the upload flow (`AttendanceController::checkIn()`/`checkOut()` stay as-is — photo upload was, and remains, optional).
- No new database columns/migrations — `check_in_photo`/`check_out_photo` already exist.
- No changes to the Student-facing attendance pages (Student never needed to view their own photos back — out of scope unless requested separately).
- No changes to Admin's Reports/CSV export pages — this is about visual review pages (Index, Approvals, Suspicious), not the tabular reports.

## Architecture

**Model layer** (`app/Models/Attendance.php`): add two accessors, `getCheckInPhotoUrlAttribute()` and `getCheckOutPhotoUrlAttribute()`, each returning `Storage::disk('public')->url($this->check_in_photo)` (or `check_out_photo`) when the underlying path is non-null, else `null`. Add `protected $appends = ['check_in_photo_url', 'check_out_photo_url'];` so these automatically serialize into every Inertia response containing an `Attendance` model — no controller query changes needed in `Admin\AttendanceController` or `Supervisor\AttendanceController`.

**Frontend components** (new, shared between Admin and Supervisor pages):
- `resources/js/Components/AttendancePhotoThumb.jsx` — a small (40×40px, rounded) clickable thumbnail. Props: `url` (string|null), `label` (e.g. "Check-in" / "Check-out", used for alt text and lightbox caption), `onClick`. If `url` is null, renders a neutral gray placeholder tile with a crossed-out camera icon (`CameraOff` from lucide-react) instead of a clickable image — visually distinct from real photos, not interactive.
- `resources/js/Components/PhotoLightbox.jsx` — a modal overlay (structurally similar to the existing `ApprovalModal`: fixed inset overlay, click-outside-to-close, close button) showing one photo at full size with its caption. Props: `open`, `onClose`, `url`, `caption`.

**Pages modified** (6 files — Index/Approvals/Suspicious × Admin/Supervisor):
`resources/js/Pages/Admin/Attendance/{Index,Approvals,Suspicious}.jsx` and `resources/js/Pages/Supervisor/Attendance/{Index,Approvals,Suspicious}.jsx`. Each gets a new "Foto" table column containing two `AttendancePhotoThumb`s side by side (check-in, check-out). Each page owns one `lightbox` state object (`{ url, caption } | null`) and renders one `PhotoLightbox` at the bottom of the page, exactly like they already do for `ApprovalModal`.

**Shared component modified**: `resources/js/Components/ApprovalModal.jsx` gains a photo section (two `AttendancePhotoThumb`s, check-in/check-out) shown between the header and the decision radio buttons, when photo URLs are present on `item`. Clicking a thumbnail inside the modal opens the same `PhotoLightbox` (the modal manages its own lightbox state internally, layered above itself).

**Data flow**: every page that currently builds a `modalItem` object (e.g. `{ id, approvalType, userName, date }`) when opening `ApprovalModal` adds two more fields: `checkInPhotoUrl: attendance.check_in_photo_url` and `checkOutPhotoUrl: attendance.check_out_photo_url`. These ride along on the existing object, no new prop threading needed beyond that.

## Error handling / edge cases

- **No photo captured** (common — photo upload is optional): thumbnail shows the gray placeholder tile, not a broken image icon. No click handler attached.
- **Photo path exists but file missing from storage** (deleted manually, migrated environment, etc.): the `<img>` tag's `onError` handler swaps it to the same placeholder tile rather than showing a broken-image icon.
- **AttendanceException rows** (used in Approvals' second tab): these don't have photo fields at all (different model). No photo column/thumbnails apply there — only `Attendance` rows show photos. The Approvals page's exception table stays as-is.

## Testing

No automated test suite exists in this project (established convention — verification is via `php -l`, `php artisan route:list`, `npm run build`, and manual browser QA with the existing test accounts). This feature will be verified the same way:
- `php -l` on the modified model.
- `npm run build` after each frontend file.
- Manual check: log in as `dede@baktitest.com` (supervisor) and `admin@bakti.com` (admin), confirm thumbnails render (real photo or placeholder) on all 6 pages, confirm lightbox opens on click, confirm `ApprovalModal` shows photos and its own lightbox still works, confirm dark mode and a placeholder tile look correct.

## Open items resolved during brainstorming

1. Scope: all 6 pages (Admin + Supervisor Index/Approvals/Suspicious) — not just Supervisor's newer pages.
2. Table presentation: thumbnail + click-to-enlarge (not icon-only, not modal-only).
3. Missing-photo case: gray placeholder tile with crossed-out camera icon, not a blank dash — keeps the column visually consistent since missing photos will be common (upload is optional).
4. Enlarge mechanism: in-app lightbox modal (new `PhotoLightbox` component), not opening the raw file in a new tab.
5. `ApprovalModal`: also shows photos, so Admin/Supervisor can see them while deciding approve/reject without leaving the modal.
