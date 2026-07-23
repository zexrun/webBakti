# Profile Photo Relocation & Multi-Role Upload — Design Spec

## Background

Face verification at check-in (built earlier this session) uses a reference photo/descriptor stored on the `Student` model (`profile_photo`, `face_descriptor` columns), captured exclusively via live webcam through a `ProfilePhotoCard` on `Student/Info/Edit.jsx`, and only ever managed there.

This change relocates profile photo management so:
- Every role (admin, supervisor, student) can have a profile photo, not just students.
- Photos can come from either the live camera flow (already built) or a plain file upload — both go through the same face-detection validation before being accepted.
- Management happens in exactly one place — the shared `Profile/Edit.jsx` page — rather than being split between a student-only "first upload" location and a separate "change later" location. The student-specific `ProfilePhotoCard` on `Student/Info/Edit.jsx` is removed entirely.

## Goal

1. `profile_photo`/`face_descriptor` move from `students` to `users`, so any authenticated user can have a reference photo.
2. `resources/js/Pages/Profile/Edit.jsx` (the existing shared profile page, used by all three roles) gains a photo card supporting both camera capture and file upload, both validated by face-api.js before saving.
3. `Student/Info/Edit.jsx` no longer has any profile-photo UI at all.
4. Check-in face verification (`AttendanceController::checkIn()`, `FaceVerificationService`) reads the reference descriptor from `$user->face_descriptor` instead of `$user->student->face_descriptor` — no other change to the verification logic, threshold, or status handling.

## Non-goals

- No change to `FaceVerificationService`'s comparison logic, threshold, or the `verified`/`mismatch`/`no_reference`/`unavailable` status model.
- No UI for viewing/managing other users' profile photos (e.g. an admin browsing everyone's photos) — each user manages only their own.
- No change to `Profile/Show.jsx` (the read-only profile view) — the photo card lives on `Profile/Edit.jsx` only for this change.
- Does not touch the in-progress "face verification can't tell people apart" investigation (the temporary diagnostic logging in `FaceVerificationService` stays as-is, untouched by this change).
- Does not touch the still-uncommitted CSRF fix in `Student/Attendance/Index.jsx` from the same investigation.

## Architecture

### Database

New migration: adds `profile_photo` (nullable string) and `face_descriptor` (nullable json) to `users`; copies any existing non-null values from `students.profile_photo`/`students.face_descriptor` to the matching `users` row (matched by `students.user_id`) before dropping those two columns from `students`. This preserves any reference photo a student already saved under the old scheme rather than silently losing it.

### Backend

- **`app/Models/User.php`**: add `profile_photo`, `face_descriptor` to `$fillable`; `face_descriptor` cast to `array`; `profile_photo` excluded from serialization via `$hidden` alongside `face_descriptor` (mirroring the exact pattern already used on `Student.php`, since `face_descriptor` is a 128-float array no frontend page reads directly, and `profile_photo` is superseded by a computed `profile_photo_url` accessor the same way `Attendance::check_in_photo_url` already works); add `getProfilePhotoUrlAttribute()`, appended via `$appends`.
- **`app/Models/Student.php`**: remove `profile_photo`, `face_descriptor` from `$fillable`/`$casts`/`$hidden`/`$appends`, and remove `getProfilePhotoUrlAttribute()` — fully relocated, not duplicated.
- **`app/Http/Controllers/ProfileController.php`** (the generic, all-roles controller behind `Profile/Edit.jsx`): add `updateProfilePhoto(Request $request)`. Accepts `photo` (image file, from either camera-captured blob or a plain file input — indistinguishable to the backend) and `face_descriptor` (required JSON string, 128 numbers). Validates the descriptor server-side exactly like the current `Student\ProfileController::updateProfilePhoto()` does (reject with a flash error if not a 128-element array), deletes any previous photo from the `public` disk, stores the new one, updates `$user->profile_photo`/`face_descriptor` in one `update()` call.
- **`app/Http/Controllers/Student/ProfileController.php`**: remove `updateProfilePhoto()` entirely — no longer reachable from anywhere.
- **`app/Http/Controllers/AttendanceController.php`**: in `checkIn()`, change `$student?->face_descriptor` to `$user->face_descriptor` (the `$student = $user->student;` line and the `FaceVerificationService::verify()` call site stay otherwise unchanged — only the second argument's source changes).
- **Routes** (`routes/web.php`): add `POST /profile/photo` → `ProfileController::updateProfilePhoto`, named `profile.photo.update`, placed in the same `auth`-only middleware group as the existing `profile.show`/`profile.edit`/`profile.update` routes (not role-restricted, since every role can use it). Remove the old `POST /student/info/profile-photo` route entirely. Regenerate `resources/js/ziggy.js` afterward — a prior feature this session broke silently by missing this step.

### Frontend

- **`resources/js/Components/ProfilePhotoCard.jsx`** (new, shared location — not under `Pages/Student/`): reuses the existing `useCamera` and `useFaceDetection` hooks unchanged. Two input modes:
  - **Camera** (unchanged from the current student-only version): live video → capture → canvas → `detectDescriptor(canvas)`.
  - **Upload**: a plain `<input type="file" accept="image/*">`. On file selection, the file is loaded into a hidden `<img>` element via `URL.createObjectURL()`; once the image has loaded (`onLoad`), `detectDescriptor(imgElement)` runs against it — `detectDescriptor()` already accepts any image-like element (video/canvas/img), so no hook change is needed.
  - Both modes funnel into one shared "attempt to save" step that runs the identical validation sequence: if `available` is `false` (models failed to load) → reject with an alert telling the user to retry or use a different browser, do not submit; if `available` is `true` but `descriptor` is `null` (no face found) → reject with an alert asking for a clearer photo, do not submit; otherwise submit `photo` + `face_descriptor` (JSON-stringified) to `profile.photo.update` via `router.post(...)` with `FormData`.
- **`resources/js/Pages/Student/Info/ProfilePhotoCard.jsx`**: deleted.
- **`resources/js/Pages/Student/Info/Edit.jsx`**: remove the `ProfilePhotoCard` import and its usage; the right-column wrapping `<div className="space-y-6 self-start">` introduced to hold it collapses back to just the certificate `<Card>` (matching the page's pre-photo-feature structure).
- **`resources/js/Pages/Profile/Edit.jsx`**: add the new shared `ProfilePhotoCard`, positioned in the left column's existing avatar `<Card>` (which currently just shows an initial-letter circle) — replacing that static initial with the real photo (or the initial as a fallback when no photo is set yet) and adding the upload/camera controls beneath it.

## Error handling

- Server-side descriptor validation (`updateProfilePhoto()`) is unconditional regardless of which client-side mode produced the photo — the backend cannot distinguish camera-sourced from upload-sourced images, and doesn't need to; both are validated identically.
- If the face-detection models fail to load (`available: false`), both camera and upload flows reject client-side with the same "try again / different browser" message — consistent per this session's clarified decision, not silently downgraded to a photo-without-descriptor save the way check-in itself tolerates infrastructure failure. Profile photos are the referenced data other verifications depend on, so a bad reference is worse than no reference — no partial-quality save is accepted.
- Old photo cleanup (`Storage::disk('public')->delete(...)`) only fires when a previous photo file actually exists on disk, matching the existing pattern already used before this change.

## Testing

No automated test suite exists in this project (established convention — `php -l`, `npm run build`, manual browser QA).
- `php -l` on all modified/new PHP files.
- `php artisan migrate` runs clean; verify via `php artisan tinker` that a student with a previously-saved `face_descriptor` has it correctly copied to their `users` row (if any exists at migration time) and removed from `students`.
- `npm run build`.
- Manual QA per role (admin, supervisor, student): visit `/profile/edit`, confirm the photo card renders; test the camera path (existing, already-verified behavior) and the upload path (new) each independently for: successful save with a real face, rejected save with no face in the image, rejected save when models fail to load.
- Confirm `Student/Info/Edit.jsx` no longer shows any photo-related UI.
- Confirm a student check-in still correctly reads their reference from `users.face_descriptor` (re-run the same manual verification steps used when the original face-verification feature shipped: verified/mismatch/no_reference cases).
