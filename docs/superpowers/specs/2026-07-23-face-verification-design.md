# Face Verification for Check-in — Design Spec

## Background

Students already capture a live photo via webcam (`resources/js/hooks/useCamera.js`, `getUserMedia` with `facingMode: 'user'`, canvas-based JPEG capture) during check-in/check-out, submitted through `resources/js/Pages/Student/Attendance/AttendanceModal.jsx` to `AttendanceController::checkIn()`/`checkOut()`. The photo is stored (`check_in_photo`/`check_out_photo` columns, now viewable via the recently-added photo-viewing feature) but never analyzed — it's evidence, not verification.

A parallel anti-spoofing system already exists for location: `LocationVerificationService` computes a `location_verification_status` (`verified`/`flagged`/`suspicious`/`unverified`) and a `spoofing_score`, and sets `requires_manual_review = true` on high-risk attendance rows. Those rows surface automatically in the existing "Kehadiran Mencurigakan" (Suspicious) pages for Admin and Supervisor, where a human makes the final approve/reject call. This feature extends that same anomaly-detection pattern to faces: is the person checking in actually who they claim to be?

The `User`/`Student` models currently have no profile photo field at all — this is a prerequisite gap that must be filled before face matching can work.

## Goal

1. Students can capture a reference profile photo (via the same live-camera flow used for attendance) from `Student/Info/Edit.jsx`.
2. During check-in, the captured photo is compared against the student's reference photo. A mismatch flags the attendance for manual review through the existing suspicious-review workflow — it never blocks check-in outright.

## Non-goals

- No face verification on check-out — only check-in (the anti-proxy-attendance concern is about arriving, not leaving; can be added later as its own scoped change if needed).
- No changes to `Student/Attendance/History.jsx`, Reports pages, or CSV export.
- No automatic threshold calibration or admin-facing settings UI for the match threshold — a fixed default is used, adjustable only in code if it proves miscalibrated later.
- No admin-initiated reset/removal of a student's reference photo — students overwrite their own reference by re-uploading; no re-approval step.
- No server-side ML infrastructure (no Python/OpenCV/cloud API) — all face detection and descriptor computation happens client-side in the browser.
- Does not touch the already-fixed `Admin/Attendance/Suspicious.jsx` field-name issue from earlier this session.

## Architecture

### Technology choice

**face-api.js** (client-side, browser-based), self-hosted via npm + static model weight files served from `public/`, consistent with this project's existing pattern of self-hosting assets rather than depending on CDNs (e.g. `@fontsource-variable/inter`). Two models are needed: `TinyFaceDetector` (~190KB, locates a face in an image) and `FaceRecognitionNet` (~6.2MB, produces a 128-number "face descriptor" — a numeric fingerprint of facial features). Both load once when a relevant modal opens, not eagerly on every page, to avoid bloating the initial bundle.

This avoids any server-side ML dependency: the browser computes descriptors, and the server only ever stores/compares arrays of 128 floats using a plain Euclidean distance calculation in PHP — no ML library needed server-side.

### Part 1: Reference profile photo

**Database** (new migration): add `profile_photo` (nullable string, storage path) and `face_descriptor` (nullable JSON, array of 128 floats) to the `students` table.

**Frontend**: a new "Foto Profil" card in `resources/js/Pages/Student/Info/Edit.jsx` (right column, alongside the existing "Ubah Password" card from the earlier whitespace fix), reusing `useCamera.js` for live capture — not a plain file upload, so the reference is guaranteed to be a real-time photo of the person at the camera, not an old or unrelated image. After capture, face-api.js runs in-browser: if no face is detected in the captured frame, the upload is rejected client-side with a clear message ("Wajah tidak terdeteksi, coba lagi") and the student must retake — a hard validation at the reference point, since a reference photo without a detectable face makes the entire matching feature useless.

**Backend**: a new `updateProfilePhoto()` method on the controller behind `Student/Info/Edit.jsx` (`StudentProfileController`), accepting the photo file plus the browser-computed descriptor (JSON array), storing both on the `Student` row.

### Part 2: Face matching at check-in

**Frontend** (`AttendanceModal.jsx` + `useCamera.js`): after `camera.capture()` produces the check-in photo, before the form submits, face-api.js detects a face in that frame and computes its descriptor. This descriptor is appended to the same `FormData` already carrying the photo (`face_descriptor` field, JSON) — no separate request. If no face is detected at all in the check-in frame (not a match/mismatch question — the frame simply has no face, e.g. photographed a ceiling), submission is rejected client-side with the same kind of message as the reference-photo flow, and the student must retake. Models load once via `useEffect` when the modal opens, not per-capture.

**Backend** (`AttendanceController::checkIn()`): accepts `face_descriptor` as a new nullable request field (nullable because a student with no reference photo yet has nothing to compare against — see below). A new `FaceVerificationService` (mirroring `LocationVerificationService`'s role and placement in `app/Services/`) takes the check-in descriptor and the student's stored reference descriptor, computes Euclidean distance, and compares against a fixed threshold (0.6, the commonly-used face-api.js default for "same person").

**Database** (a second migration, separate from Part 1's — different table): add `face_verification_status` (string: `verified`/`mismatch`/`no_reference`/`unavailable`) and `face_match_distance` (nullable float) to `attendances`.

**Decision logic**:
- No reference photo exists for the student (`$student->face_descriptor` is null): `face_verification_status = 'no_reference'`. Does **not** set `requires_manual_review` — this isn't the student's fault, the system simply has nothing to compare against yet.
- Reference exists, distance is within threshold: `face_verification_status = 'verified'`.
- Reference exists, distance exceeds threshold: `face_verification_status = 'mismatch'`. **Sets `requires_manual_review = true`** — reusing the exact same flag that location anomalies already use, so mismatched check-ins automatically appear in the existing "Kehadiran Mencurigakan" pages (Admin and Supervisor) with zero new review UI needed.
- Camera/model unavailable in the browser (permission denied, unsupported browser, model failed to load): check-in proceeds normally without a descriptor at all, `face_verification_status = 'unavailable'`. This feature must never block a normal check-in just because its own client-side infrastructure failed — consistent with how the app already treats other optional-enhancement failures.

**Suspicious pages** (`Admin/Attendance/Suspicious.jsx`, `Supervisor/Attendance/Suspicious.jsx`): the existing "Alasan Anomali" column, which already shows location-based flags, gains a face-mismatch line when applicable ("Wajah tidak cocok (jarak: X.XX)"), shown alongside — not replacing — the existing location info. No new column, no new page.

## Error handling

- Client-side "no face detected" rejection happens at both the profile-photo upload and every check-in attempt — the student retakes before any request reaches the server.
- Server never rejects a check-in for a face mismatch or missing reference — it only flags for review, consistent with the existing location-anomaly pattern's philosophy (never block, always let a human decide on ambiguous signals).
- If `face_descriptor` arrives from the client in an unexpected shape (wrong length array, non-numeric, tampered), the backend treats it the same as `unavailable` (skips comparison) rather than erroring — face verification is a defense-in-depth signal, not a hard gate, so malformed input degrades gracefully instead of breaking check-in.
- No changes to previously-stored attendance rows — `face_verification_status` only applies going forward from when this feature ships; existing rows have no value (nullable column, defaults to null, distinguishable from the four post-feature states).

## Testing

No automated test suite exists in this project (established convention — verification via `php -l`, `npm run build`, and manual browser QA).
- `php -l` on all modified/new PHP files.
- `npm run build`, confirming face-api.js is only pulled into the chunks for the two relevant pages/modals (Info/Edit and the attendance check-in modal) rather than bloating the main app bundle — check the build output's chunk sizes.
- Manual QA: upload a profile photo with a clear face → confirm a descriptor is stored; check in with the same face → confirm `verified`; check in with a visibly different face (have a different person pose) → confirm `mismatch` and that the row appears in both Admin and Supervisor Suspicious pages with the distance shown; check in as a student with no reference photo → confirm `no_reference` and that the row does NOT appear in Suspicious; deny camera permission or simulate a model-load failure → confirm check-in still succeeds with `unavailable`.
