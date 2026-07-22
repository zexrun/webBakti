# Attendance working_hours/is_late Serialization Fix — Design Spec

## Background

`app/Models/Attendance.php` defines `getWorkingHoursAttribute()` and `getIsLateAttribute()` (both correct and side-effect-free — verified via `php artisan tinker` against real rows: `working_hours` returns a numeric hour value or `null`, `is_late` returns a boolean, neither throws). Neither is listed in `protected $appends`, so Laravel's `toArray()`/JSON serialization — what Inertia actually sends to the frontend on every page load — omits both fields entirely.

Four React pages across all three roles already read these fields, written correctly from day one, but have been silently receiving `undefined` since the features were built:
- `resources/js/Pages/Admin/Attendance/Index.jsx` (lines 149, 155)
- `resources/js/Pages/Supervisor/Attendance/Index.jsx` (lines 144, 150)
- `resources/js/Pages/Student/Attendance/Index.jsx` (lines 137, 151, 223, 234)
- `resources/js/Pages/Student/Attendance/History.jsx` (lines 129, 133)

Effect: the "Durasi" (working hours) column always shows its `-` fallback, and the "Terlambat" (late) badge never renders, on all four pages, for every user, regardless of actual attendance data.

A third accessor, `getStatusBadgeAttribute()`, exists on the same model but is not read anywhere in the frontend (confirmed via a full-tree search — the only string match for "statusBadge" is an unrelated local variable in `resources/js/Pages/Student/Tasks/Show.jsx`, for the `Task` model, not `Attendance`). It's dead code, not a bug.

This bug was discovered as a side effect of reviewing an unrelated commit during the "Attendance Photo Viewing" feature (which correctly appended `check_in_photo_url`/`check_out_photo_url`), and is being fixed now as its own scoped piece of work.

## Goal

`working_hours` and `is_late` are included in every `Attendance` model's JSON/Inertia serialization, so the four already-correct frontend pages start displaying real data with zero frontend changes.

## Non-goals

- No change to the accessors' calculation logic — both are already correct.
- No change to any frontend file — the bug is purely a backend serialization gap; the pages already expect and handle these fields correctly.
- No fix to the separate, unrelated `working_hours` precision quirk observed during verification (a `diffInHours`-based accessor returned a sub-1 fractional value on one test row, e.g. `0.0038...`, on data where check-in/check-out were seconds apart — likely because `diffInHours(..., false)` with float precision or similar; this is a pre-existing edge case in the calculation itself, not the serialization gap this fix addresses, and needs its own investigation if it turns out to matter with real-world attendance durations measured in hours rather than seconds).
- No fix to the separately-tracked `Admin/Attendance/Suspicious.jsx` field-name bug (`check_in_time`/`latitude`/`longitude`) — unrelated file, unrelated fields, already logged as its own follow-up item.

## Architecture

Single-file change, `app/Models/Attendance.php`:

1. Add `'working_hours'` and `'is_late'` to the existing `protected $appends` array (currently `['check_in_photo_url', 'check_out_photo_url']`), so it becomes `['check_in_photo_url', 'check_out_photo_url', 'working_hours', 'is_late']`. This mirrors the exact pattern already used for the photo URL accessors — no controller changes needed anywhere, since `$appends` applies automatically to every serialization of every `Attendance` instance across the whole app (Admin, Supervisor, Student controllers alike).
2. Remove `getStatusBadgeAttribute()` and its local `$badges` array entirely — confirmed unused anywhere in the frontend.

## Error handling

Both accessors already handle their edge cases defensively (`getWorkingHoursAttribute()` returns `null` when either `check_in` or `check_out` is missing; `getIsLateAttribute()` returns `false` when `check_in` is missing). No new error handling is needed — appending them to `$appends` doesn't change when or how they're computed, only whether the already-computed value is included in serialized output.

## Testing

No automated test suite exists in this project (established convention). Verification is manual:
- `php -l app/Models/Attendance.php`.
- `php artisan tinker` check that `Attendance::first()->toArray()` now includes `working_hours` and `is_late` keys (and no longer includes `status_badge`).
- Manual: visit each of the four affected pages as the relevant role, confirm the "Durasi" column shows a real value (not `-`) for rows with both check-in and check-out recorded, and confirm the "Terlambat" badge appears for rows where the accessor's logic says they should (cross-check against `AttendanceSetting`'s configured work-start-time/late-tolerance).
