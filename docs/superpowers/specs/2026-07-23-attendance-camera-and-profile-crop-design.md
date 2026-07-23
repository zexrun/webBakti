# Attendance Camera Overlay/Ratio + Profile Photo Cropper — Design

## Goal

Three related UX improvements to the camera/photo flows built earlier:

1. Add a face-position guide overlay to the attendance check-in/check-out camera.
2. Constrain the attendance camera (both live preview and captured photo) to a 16:9 aspect ratio.
3. Add an interactive crop step (pan/zoom, 1:1 output) to the profile photo feature, for both the camera-capture and file-upload paths.

## Non-goals

- No change to face-descriptor comparison logic, match threshold, or `FaceVerificationService`.
- No change to the attendance check-in/check-out validation/authorization flow.
- No change to which roles can use which feature (already-shipped profile-photo-relocation feature is unaffected).

## 1. Attendance camera: 16:9 ratio + face-guide overlay

**Files:** `resources/js/hooks/useCamera.js`, `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`

- `useCamera().start()`: add `aspectRatio: { ideal: 16 / 9 }` to the `getUserMedia` video constraints (alongside the existing `width`/`height` ideals), so the browser's video track itself is already close to 16:9 where the camera supports it.
- `useCamera().capture()`: currently sets `canvas.width/height` to the raw `video.videoWidth/videoHeight` and draws the full frame. Change to compute a 16:9 center-crop region of the source video frame (matching whichever dimension is the limiting one) and draw only that region to the canvas, so the **saved photo** is always exactly 16:9 regardless of the actual camera's native ratio.
- `AttendanceModal.jsx`: wrap the `<video>` element in a container styled with Tailwind's `aspect-video` (16:9) class instead of the current fixed `h-48 w-full`, so the **live preview** itself is boxed to 16:9 (`object-cover` already handles the crop visually in the browser).
- New overlay: an absolutely-positioned `<div>` layered on top of the `<video>`, visible only when `camera.phase === 'streaming'`. Renders:
  - A semi-transparent dark mask covering the full frame, with an oval "hole" cut out in the center (implemented via an SVG `<mask>` or a radial-gradient trick) so the area outside the oval is dimmed and the area inside is clear.
  - An oval border/outline (border-radius ellipse via CSS, or SVG `<ellipse>`) marking the guide boundary.
  - A short instruction line below/above the oval: "Posisikan wajah Anda di dalam oval".
  - Purely visual — does not read from or feed into `useFaceDetection` in any way; it's a positioning aid only, not a detection region.
- Both check-in and check-out modals use the same `AttendanceModal` component, so this applies to both automatically.

## 2. Profile photo: interactive crop (camera + upload, 1:1 output)

**Files:** `package.json` (new dependency), `resources/js/Components/ProfilePhotoCard.jsx`, new `resources/js/lib/cropImage.js`

- Add `react-easy-crop` as a new dependency.
- New util `resources/js/lib/cropImage.js` exporting `getCroppedImg(imageSrc, croppedAreaPixels): Promise<Blob>` — loads `imageSrc` into an `Image`, draws the `croppedAreaPixels` region onto an offscreen canvas sized to the crop, and resolves a JPEG blob (`canvas.toBlob(..., 'image/jpeg', 0.8)`). This is the standard pattern from `react-easy-crop`'s own docs, adapted to return a Blob instead of a data URL.
- `ProfilePhotoCard.jsx` gains a new phase in its local flow, inserted between "photo obtained" and "save":
  - **Camera path:** after `camera.capture()` reaches `phase === 'captured'`, instead of immediately showing Simpan/Ulangi, show the cropper against `camera.previewUrl`.
  - **Upload path:** after a file is selected (`uploadPreviewUrl` set), instead of immediately showing Simpan/Batal, show the cropper against `uploadPreviewUrl`.
  - Cropper UI: `<Cropper image={source} crop={crop} zoom={zoom} aspect={1} onCropChange={setCrop} onZoomChange={setZoom} onCropComplete={(_, area) => setCroppedAreaPixels(area)} />` in a fixed-height container, with a zoom slider and two buttons: "Batal" (discard, return to idle) and "Pakai Foto Ini" (confirm crop).
  - On confirm: call `getCroppedImg(source, croppedAreaPixels)` to produce the cropped JPEG blob. Build an `<img>` element from that blob (via `URL.createObjectURL` + a temporary `Image()` load, or reuse the existing hidden-image-element pattern) and run `face.detectDescriptor()` **on the cropped image**, not the pre-crop source — so the stored photo and the descriptor used for verification are always derived from the same pixels.
  - Same validation as today applies to the cropped result: reject save if `!available` (detection unavailable) or `!descriptor` (no face found), for both camera and upload paths, per the existing profile-photo-relocation spec.
  - After a successful crop+detect, the existing save flow (`router.post('profile.photo.update', ...)`) runs unchanged, just with the cropped blob substituted for the previously-uncropped one.
- Output aspect ratio is 1:1 (square), independent of the attendance camera's 16:9 — these are two separate features serving different purposes (avatar vs. attendance evidence photo).

## Error handling

- If `react-easy-crop` fails to render the image (corrupt file, unsupported format) — same as today's existing image-load error handling, no new failure mode introduced beyond what "cropping a broken image" already implies from the browser's `<img>` error event.
- Canceling the crop step returns the component fully to `idle` (same as today's "Ulangi"/"Batal" behavior), discarding the captured/selected photo.

## Testing approach

- No new backend surface — `updateProfilePhoto` and `AttendanceController::checkIn/checkOut` are unchanged; the crop and overlay are entirely client-side transforms applied before the existing upload calls.
- Manual verification (build + browser click-through) for: overlay renders only while streaming, captured attendance photo is 16:9, profile crop UI works via both camera and upload entry points, cropped square photo persists and is used for subsequent face verification.
