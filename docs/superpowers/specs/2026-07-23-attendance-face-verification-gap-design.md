# Attendance Face Verification Gap — Design

## Goal

Close two gaps in the attendance face-verification feature so it fully satisfies these rules:

1. Student uploads a profile photo (already implemented).
2. Both check-in **and** check-out run face-match verification against that reference (currently: check-in only).
3. A mismatch never blocks the attendance action — it's recorded and flagged for admin/supervisor manual review (already implemented for check-in; extend to check-out).
4. The system requires a face to be present in the captured photo, rejecting empty/non-face photos client-side before submit (already implemented for check-in; extend to check-out).
5. **New requirement:** a student cannot attempt check-in or check-out at all until they have uploaded a profile photo (a face-descriptor reference) — enforced both in the UI and on the server.

## Non-goals

- No change to the face-match algorithm, threshold (0.6), or `FaceVerificationService`'s comparison logic itself.
- No change to location verification, exception requests, or any other attendance concern.
- No change to how the profile photo is uploaded/cropped (already shipped).

## 1. Require a profile photo before any attendance action

**Backend enforcement** (defense in depth, the authoritative check):

In `AttendanceController::checkIn()` and `checkOut()`, add a guard at the very start of the try block, before any other validation:

```php
$user = Auth::user();

if (!$user->face_descriptor) {
    return response()->json([
        'success' => false,
        'message' => 'Anda harus mengupload foto profil terlebih dahulu sebelum melakukan presensi.'
    ], 422);
}
```

This makes `FaceVerificationService`'s `no_reference` status unreachable from the attendance flow going forward (a student without a reference is stopped before verification even runs). The service itself is left unchanged and keeps handling `no_reference` defensively — it's a general-purpose comparator, not attendance-specific, and shouldn't assume its only caller enforces this.

**Frontend enforcement** (UX — stop the student before they even open the camera):

In `Student/Attendance/Index.jsx`, read `auth.user.profile_photo_url` from Inertia's shared props (already flows through `HandleInertiaRequests` and is already used the same way in `AppShell.jsx`). If it's falsy:
- Render a warning banner above the "Status Hari Ini" card: "Anda belum mengupload foto profil. Upload foto profil terlebih dahulu untuk dapat melakukan presensi." with a link to `route('profile.edit')`.
- Hide/disable both the check-in and check-out CTA buttons (the ones that open `AttendanceModal`), so the student cannot even reach the camera step.

## 2. Add face verification to check-out

**Files:** `database/migrations/`, `app/Http/Controllers/AttendanceController.php`, `resources/js/Pages/Student/Attendance/Index.jsx`, `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`

**New columns** (migration): `attendances.check_out_face_verification_status` (string, nullable), `attendances.check_out_face_match_distance` (float, nullable). These are separate from the existing `face_verification_status`/`face_match_distance` columns (which remain check-in-only, unrenamed for backward compatibility with existing data and the Suspicious pages) — a single shared pair of columns would let check-out silently overwrite check-in's result on the same row, losing information about which action actually failed the check.

**Controller logic** (`checkOut()`):
- Accept `face_descriptor` as a nullable string in the request validation, identical to `checkIn()`.
- Decode it the same way `checkIn()` does (`json_decode` into an array, validate it's an array).
- Call `FaceVerificationService::verify($checkOutDescriptor, $user->face_descriptor, $user->id)`.
- Store the result in the new `check_out_face_verification_status` / `check_out_face_match_distance` columns.
- If the result is `mismatch`, set `requires_manual_review = true` — but only ever set it to `true`, never overwrite an existing `true` back to `false` if check-in already flagged it. Since `checkOut()` uses `$attendance->update()` on the existing row (not `updateOrCreate` creating fresh), this means reading the current `requires_manual_review` value first: `$requiresManualReview = $attendance->requires_manual_review || $faceResult['status'] === 'mismatch'`.

**Frontend:**
- `Student/Attendance/Index.jsx`: pass `requireFaceCheck` to the check-out `<AttendanceModal>` too (currently only the check-in modal has this prop). This is the same prop that already drives the "reject if no face detected" behavior in `AttendanceModal.jsx` — no changes needed inside `AttendanceModal.jsx` itself, since that component is already generic and reads `requireFaceCheck` as a plain prop.
- `submitAttendance()` in `Index.jsx` already forwards `faceDescriptor` generically to whichever endpoint it's called with — no change needed there.

## 3. Surface check-out mismatches in admin/supervisor review

**Files:** `resources/js/Pages/Admin/Attendance/Suspicious.jsx`, `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`

Both pages already render, inside the same table cell as the location-mismatch note:

```jsx
{attendance.face_verification_status === 'mismatch' && (
  <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
    <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
    Wajah tidak cocok (jarak: {Number(attendance.face_match_distance).toFixed(2)})
  </p>
)}
```

Add an equivalent block right after it for the check-out result, using the new columns and a distinguishing label:

```jsx
{attendance.check_out_face_verification_status === 'mismatch' && (
  <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
    <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
    Wajah check-out tidak cocok (jarak: {Number(attendance.check_out_face_match_distance).toFixed(2)})
  </p>
)}
```

No backend prop changes needed for these pages — both already pass through the full `Attendance` model's attributes to the frontend (confirmed by the fact that `face_verification_status`/`face_match_distance` already reach the page without any explicit `select()` narrowing in their controllers), so the new columns arrive automatically once added to the table.

## Error handling

- Missing profile photo: `422` JSON response with a clear Indonesian message, caught by the existing `AttendanceModal`/`Index.jsx` error-alert path (same `err.response?.data?.message` pattern already in place from the earlier CSRF fix).
- Check-out face detection unavailable/no-face: identical alert-and-abort behavior as check-in, inherited for free once `requireFaceCheck` is passed to the check-out modal.
- Check-out mismatch: never blocks the check-out — `requires_manual_review` is set, the response is still `success: true`, matching check-in's existing non-blocking behavior.

## Testing approach

- No automated test framework exists in this repo currently (verified by prior features in this session, which relied on `php -l`, `npm run build`, and manual browser verification) — this feature follows the same approach.
- Manual verification: (1) a student with no profile photo sees the banner and cannot open either modal, and a direct API call without one is rejected with 422; (2) a student with a profile photo can check in and check out normally; (3) presenting a different face (or no face) at check-out is rejected client-side the same way check-in already is; (4) a genuine face mismatch at check-out still completes the check-out but flags `requires_manual_review`, visible on the admin/supervisor Suspicious pages with the new check-out-specific message.
