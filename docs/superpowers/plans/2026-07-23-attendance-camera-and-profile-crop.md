# Attendance Camera Overlay/Ratio + Profile Photo Cropper Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a face-position guide overlay and a 16:9 aspect-ratio crop to the student attendance camera flow, and add an interactive pan/zoom crop step (1:1 output) to the profile photo feature for both its camera and upload paths.

**Architecture:** `useCamera.js` gains a 16:9 center-crop in `capture()` and a 16:9 constraint hint in `start()`; `AttendanceModal.jsx` gets a purely-visual oval overlay `<div>` shown only while streaming. `ProfilePhotoCard.jsx` gains a new "cropping" UI stage (via the new `react-easy-crop` dependency) that sits between "photo obtained" (camera capture or file select) and "save" — the crop is confirmed by the user, rendered to a square blob via a new `cropImage.js` util, and that cropped blob is what gets face-detected and uploaded.

**Tech Stack:** React 19, `react-easy-crop` (new dependency, ^6.2.2), face-api.js (existing), Tailwind CSS (existing `aspect-video` utility), HTML5 Canvas.

---

### Task 1: Add 16:9 constraint and center-crop to useCamera

**Files:**
- Modify: `resources/js/hooks/useCamera.js`

- [ ] **Step 1: Add aspectRatio hint to getUserMedia constraints**

In `resources/js/hooks/useCamera.js`, change the `start()` function's `getUserMedia` call:

```js
  async function start() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 }, aspectRatio: { ideal: 16 / 9 } },
      })
```

- [ ] **Step 2: Change capture() to crop the source frame to 16:9 before drawing**

Replace the `capture()` function body:

```js
  function capture() {
    return new Promise((resolve) => {
      const video = videoRef.current
      const canvas = canvasRef.current
      if (!video || !canvas) return resolve(null)

      const sourceWidth = video.videoWidth
      const sourceHeight = video.videoHeight
      const targetRatio = 16 / 9

      let cropWidth = sourceWidth
      let cropHeight = sourceWidth / targetRatio
      if (cropHeight > sourceHeight) {
        cropHeight = sourceHeight
        cropWidth = sourceHeight * targetRatio
      }
      const cropX = (sourceWidth - cropWidth) / 2
      const cropY = (sourceHeight - cropHeight) / 2

      canvas.width = cropWidth
      canvas.height = cropHeight
      canvas.getContext('2d').drawImage(video, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight)

      canvas.toBlob(
        (blob) => {
          setPreviewUrl(URL.createObjectURL(blob))
          setPhase('captured')
          stopStream()
          resolve(blob)
        },
        'image/jpeg',
        0.8,
      )
    })
  }
```

This computes a center-cropped 16:9 region of whatever the camera actually delivers (handles both a camera that's already close to 16:9 via the `start()` hint, and one that ignores the hint and delivers something else like 4:3), so the canvas — and therefore the saved photo — is always exactly 16:9.

- [ ] **Step 3: Manual verification**

Run `npm run build` to confirm no syntax errors:
```bash
npm run build
```
Expected: build succeeds with no new errors (the pre-existing `useFaceDetection` chunk-size warning is unrelated and expected).

- [ ] **Step 4: Commit**

```bash
git add resources/js/hooks/useCamera.js
git commit -m "feat: Crop attendance camera capture to 16:9"
```

---

### Task 2: Box the attendance video preview to 16:9

**Files:**
- Modify: `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`

- [ ] **Step 1: Wrap the video element in an aspect-video container**

In `AttendanceModal.jsx`, find this block (around line 109-118):

```jsx
                <video
                  ref={camera.videoRef}
                  className={cn('h-48 w-full rounded-lg border border-border bg-muted object-cover', camera.phase !== 'streaming' && 'hidden')}
                  muted
                  playsInline
                />
                <canvas ref={camera.canvasRef} className="hidden" />
                {camera.previewUrl && (
                  <img src={camera.previewUrl} alt="Preview" className="h-48 w-full rounded-lg border border-border object-cover" />
                )}
```

Replace it with (video now sits in an `aspect-video` container so it's boxed to 16:9; the overlay from Task 3 will be added inside this same container; the captured-photo preview also switches to `aspect-video` so it visually matches the now-16:9 saved photo):

```jsx
                <div className={cn('relative w-full overflow-hidden rounded-lg border border-border bg-muted', camera.phase !== 'streaming' && 'hidden')}>
                  <div className="aspect-video">
                    <video ref={camera.videoRef} className="h-full w-full object-cover" muted playsInline />
                  </div>
                </div>
                <canvas ref={camera.canvasRef} className="hidden" />
                {camera.previewUrl && (
                  <img src={camera.previewUrl} alt="Preview" className="aspect-video w-full rounded-lg border border-border object-cover" />
                )}
```

- [ ] **Step 2: Manual verification**

Run `npm run build`:
```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Student/Attendance/AttendanceModal.jsx
git commit -m "feat: Box attendance camera preview to a 16:9 frame"
```

---

### Task 3: Add face-position oval overlay to AttendanceModal

**Files:**
- Modify: `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`

- [ ] **Step 1: Add the overlay markup**

Inside the same container introduced in Task 2, add the overlay as a sibling to the inner `aspect-video` div, so the final block looks like:

```jsx
                <div className={cn('relative w-full overflow-hidden rounded-lg border border-border bg-muted', camera.phase !== 'streaming' && 'hidden')}>
                  <div className="aspect-video">
                    <video ref={camera.videoRef} className="h-full w-full object-cover" muted playsInline />
                  </div>
                  <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" className="absolute inset-0 h-full w-full">
                      <defs>
                        <mask id="face-guide-mask">
                          <rect x="0" y="0" width="100" height="100" fill="white" />
                          <ellipse cx="50" cy="50" rx="22" ry="32" fill="black" />
                        </mask>
                      </defs>
                      <rect x="0" y="0" width="100" height="100" fill="rgba(0,0,0,0.45)" mask="url(#face-guide-mask)" />
                      <ellipse cx="50" cy="50" rx="22" ry="32" fill="none" stroke="white" strokeWidth="0.6" strokeOpacity="0.85" />
                    </svg>
                    <p className="absolute bottom-3 rounded-full bg-black/50 px-3 py-1 text-xs text-white">
                      Posisikan wajah Anda di dalam oval
                    </p>
                  </div>
                </div>
```

Notes on this implementation:
- The `viewBox="0 0 100 100"` with `preserveAspectRatio="none"` makes the ellipse's `rx`/`ry` percentages of the container's actual width/height, so the oval keeps the same proportional position/size regardless of the container's rendered pixel dimensions (which vary by screen width since the container is `w-full aspect-video`).
- The mask makes everything outside the ellipse dark (`rgba(0,0,0,0.45)`) while the ellipse interior stays fully transparent (shows the live video unobstructed).
- `pointer-events-none` ensures the overlay never blocks clicks/taps meant for elements behind it (there are none in this container today, but it's defensive since this sits above the video).
- This is rendered only while `camera.phase === 'streaming'` (inherited from the parent container's conditional `hidden` class from Task 2) — it does not appear over the post-capture preview image, matching the spec's "visible only while streaming" requirement.

- [ ] **Step 2: Manual verification**

Run `npm run build`:
```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Student/Attendance/AttendanceModal.jsx
git commit -m "feat: Add face-position oval guide overlay to attendance camera"
```

---

### Task 4: Add react-easy-crop dependency

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Install the dependency**

```bash
npm install react-easy-crop@^6.2.2
```

Expected: `package.json`'s `dependencies` gains `"react-easy-crop": "^6.2.2"` (or whatever exact resolved version npm picks within that range), and `package-lock.json` updates accordingly.

- [ ] **Step 2: Verify it installed cleanly**

```bash
npm run build
```
Expected: build still succeeds (the new dependency isn't imported anywhere yet, so this just confirms the install didn't break anything).

- [ ] **Step 3: Commit**

```bash
git add package.json package-lock.json
git commit -m "chore: Add react-easy-crop dependency for profile photo cropping"
```

---

### Task 5: Create the cropImage util

**Files:**
- Create: `resources/js/lib/cropImage.js`

- [ ] **Step 1: Write the util**

```js
/**
 * Renders the given pixel crop region of `imageSrc` onto an offscreen
 * canvas and resolves a JPEG Blob of just that region. Standard pattern
 * adapted from react-easy-crop's own docs, returning a Blob (for
 * FormData upload) instead of a data URL.
 */
export function getCroppedImg(imageSrc, croppedAreaPixels) {
  return new Promise((resolve, reject) => {
    const image = new Image()
    image.crossOrigin = 'anonymous'
    image.onload = () => {
      const canvas = document.createElement('canvas')
      canvas.width = croppedAreaPixels.width
      canvas.height = croppedAreaPixels.height
      const ctx = canvas.getContext('2d')

      ctx.drawImage(
        image,
        croppedAreaPixels.x,
        croppedAreaPixels.y,
        croppedAreaPixels.width,
        croppedAreaPixels.height,
        0,
        0,
        croppedAreaPixels.width,
        croppedAreaPixels.height,
      )

      canvas.toBlob(
        (blob) => (blob ? resolve(blob) : reject(new Error('Gagal memproses gambar.'))),
        'image/jpeg',
        0.8,
      )
    }
    image.onerror = () => reject(new Error('Gagal memuat gambar.'))
    image.src = imageSrc
  })
}
```

- [ ] **Step 2: Verify syntax**

```bash
node --check resources/js/lib/cropImage.js
```
Expected: no output (exits 0).

- [ ] **Step 3: Commit**

```bash
git add resources/js/lib/cropImage.js
git commit -m "feat: Add getCroppedImg util for interactive profile photo cropping"
```

---

### Task 6: Add cropping stage to ProfilePhotoCard

**Files:**
- Modify: `resources/js/Components/ProfilePhotoCard.jsx`

This task replaces the current "capture/select → immediately show Simpan/Ulangi" flow with "capture/select → crop → Simpan/Ulangi", for both camera and upload paths, sharing one `Cropper` instance.

- [ ] **Step 1: Replace the full file contents**

```jsx
import { useCallback, useEffect, useRef, useState } from 'react'
import { router } from '@inertiajs/react'
import Cropper from 'react-easy-crop'
import { Camera, RefreshCw, UserRound, Upload, Check } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { getCroppedImg } from '@/lib/cropImage'
import { cn } from '@/lib/utils'

/**
 * Profile photo card for any role. Supports two capture modes, both
 * validated identically by face-api.js before saving:
 *   - Camera: the existing live getUserMedia flow, capture to canvas.
 *   - Upload: a plain file input, loaded into a hidden <img> so
 *     detectDescriptor() (which accepts any image-like element) can
 *     run against it the same way it runs against the camera canvas.
 *
 * Both modes route their raw photo through an interactive crop step
 * (pan/zoom, 1:1 output) before face detection runs - the cropped
 * result is what gets face-detected and uploaded, so the stored photo
 * and the descriptor used for verification are always the same pixels.
 *
 * Neither mode saves a photo without a valid descriptor - a reference
 * photo the matching feature can't use is worse than no reference at
 * all, so unlike check-in (which tolerates detection being
 * unavailable), this always rejects on `!available` too.
 */
export default function ProfilePhotoCard({ profilePhotoUrl }) {
  const camera = useCamera()
  const face = useFaceDetection()
  const fileInputRef = useRef(null)
  const [uploadPreviewUrl, setUploadPreviewUrl] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  const [cropSource, setCropSource] = useState(null) // data/object URL being cropped, or null
  const [crop, setCrop] = useState({ x: 0, y: 0 })
  const [zoom, setZoom] = useState(1)
  const [croppedAreaPixels, setCroppedAreaPixels] = useState(null)

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    if (camera.phase === 'captured' && camera.previewUrl) {
      setCrop({ x: 0, y: 0 })
      setZoom(1)
      setCropSource(camera.previewUrl)
    }
  }, [camera.phase, camera.previewUrl])

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (!file) return

    camera.reset()
    const url = URL.createObjectURL(file)
    setUploadPreviewUrl(url)
    setCrop({ x: 0, y: 0 })
    setZoom(1)
    setCropSource(url)
  }

  function clearUpload() {
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(null)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  function cancelCrop() {
    setCropSource(null)
    setCroppedAreaPixels(null)
    camera.reset()
    clearUpload()
  }

  const onCropComplete = useCallback((_croppedArea, croppedAreaPixelsValue) => {
    setCroppedAreaPixels(croppedAreaPixelsValue)
  }, [])

  async function confirmCrop() {
    if (!croppedAreaPixels) return

    setStatusMessage('Memproses gambar...')

    let blob
    try {
      blob = await getCroppedImg(cropSource, croppedAreaPixels)
    } catch (err) {
      setStatusMessage('')
      alert(err.message)
      return
    }

    const objectUrl = URL.createObjectURL(blob)
    const img = new Image()
    img.onload = async () => {
      setStatusMessage('Memeriksa wajah...')
      const { available, descriptor } = await face.detectDescriptor(img)
      URL.revokeObjectURL(objectUrl)

      if (!available) {
        setStatusMessage('')
        alert('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba lagi atau gunakan perangkat/browser lain.')
        return
      }

      if (!descriptor) {
        setStatusMessage('')
        alert('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
        return
      }

      setStatusMessage('')
      setSubmitting(true)
      setCropSource(null)

      const formData = new FormData()
      formData.append('photo', blob, 'profile.jpg')
      formData.append('face_descriptor', JSON.stringify(descriptor))

      router.post(route('profile.photo.update'), formData, {
        preserveScroll: true,
        onFinish: () => {
          setSubmitting(false)
          camera.reset()
          clearUpload()
        },
      })
    }
    img.src = objectUrl
  }

  const showIdlePreview = camera.phase === 'idle' && !uploadPreviewUrl && !cropSource && profilePhotoUrl

  return (
    <Card>
      <CardHeader className="border-b">
        <CardTitle className="flex items-center gap-2">
          <UserRound className="h-4 w-4 text-muted-foreground" /> Foto Profil
        </CardTitle>
        <CardDescription>Digunakan sebagai referensi verifikasi wajah saat check-in</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4 pt-6">
        {showIdlePreview && (
          <img src={profilePhotoUrl} alt="Foto profil" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}

        {!cropSource && (
          <video
            ref={camera.videoRef}
            className={cn('h-48 w-full rounded-lg border border-border bg-muted object-cover', camera.phase !== 'streaming' && 'hidden')}
            muted
            playsInline
          />
        )}
        <canvas ref={camera.canvasRef} className="hidden" />

        {cropSource && (
          <div className="space-y-3">
            <div className="relative h-64 w-full overflow-hidden rounded-lg border border-border bg-muted">
              <Cropper
                image={cropSource}
                crop={crop}
                zoom={zoom}
                aspect={1}
                cropShape="round"
                onCropChange={setCrop}
                onZoomChange={setZoom}
                onCropComplete={onCropComplete}
              />
            </div>
            <input
              type="range"
              min={1}
              max={3}
              step={0.05}
              value={zoom}
              onChange={(e) => setZoom(Number(e.target.value))}
              className="w-full"
              aria-label="Perbesar"
            />
          </div>
        )}

        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex flex-wrap gap-2">
          {camera.phase === 'idle' && !uploadPreviewUrl && !cropSource && (
            <>
              <Button type="button" onClick={camera.start} className="flex-1">
                <Camera /> Ambil Foto
              </Button>
              <Button type="button" variant="outline" onClick={() => fileInputRef.current?.click()} className="flex-1">
                <Upload /> Upload Foto
              </Button>
              <input
                ref={fileInputRef}
                type="file"
                accept="image/*"
                onChange={handleFileChange}
                className="hidden"
              />
            </>
          )}

          {camera.phase === 'streaming' && !cropSource && (
            <Button type="button" onClick={camera.capture} className="flex-1">
              <Camera /> Ambil Foto
            </Button>
          )}

          {cropSource && (
            <>
              <Button type="button" onClick={cancelCrop} variant="outline" className="flex-1">
                <RefreshCw /> Batal
              </Button>
              <Button type="button" onClick={confirmCrop} disabled={submitting || !croppedAreaPixels} className="flex-1">
                {submitting ? 'Menyimpan...' : <><Check /> Pakai Foto Ini</>}
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
```

Key design notes for whoever picks this up:
- `cropSource` is the single source of truth for "are we in the cropping stage" — set from either the camera path (`useEffect` watching `camera.phase === 'captured'`) or the upload path (`handleFileChange`), and cleared by `cancelCrop()` or after a successful `confirmCrop()`.
- The old `uploadReady`/`uploadImgRef` state from the pre-crop version is removed — cropping now owns the "is this image actually loaded and ready" concern via `react-easy-crop` itself, and detection runs against a freshly-loaded `Image()` built from the cropped blob inside `confirmCrop()`, not against the upload's raw preview.
- `cropShape="round"` is a purely visual hint from `react-easy-crop` (draws a circular cropping guide) even though the underlying crop math is still a square (`aspect={1}`) — this matches common profile-photo UX (circular avatar preview) while keeping the stored image a square JPEG.

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds, `react-easy-crop` appears bundled into the relevant chunk.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/ProfilePhotoCard.jsx
git commit -m "feat: Add interactive pan/zoom crop step to profile photo upload and camera capture"
```

---

### Task 7: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Lint/syntax check every touched file**

```bash
node --check resources/js/lib/cropImage.js
npm run build
```
Expected: both succeed with no errors.

- [ ] **Step 2: Confirm react-easy-crop is present in the lockfile and bundle**

```bash
grep -c "react-easy-crop" package.json package-lock.json
```
Expected: at least 1 match in each file.

- [ ] **Step 3: Manual browser check (attendance)**

Log in as a student, open check-in modal:
- Confirm the video preview is boxed to a 16:9 rectangle (noticeably wider than tall, not the old `h-48` near-square-ish box).
- Confirm the dimmed overlay with a bright oval outline appears centered over the live video, with the instruction text below it.
- Capture a photo, confirm the overlay disappears once `phase` moves to `captured` (only the preview image shows, no overlay).
- Submit check-in successfully as today.

- [ ] **Step 4: Manual browser check (profile photo, camera path)**

Go to `/profile/edit` (any role), click "Ambil Foto", capture a photo:
- Confirm a square crop UI appears (pan/zoom slider) instead of immediately showing Simpan/Ulangi.
- Drag to reposition, use the zoom slider, click "Pakai Foto Ini".
- Confirm "Memeriksa wajah..." status appears, then either a rejection alert (no face) or a successful save redirecting back to the profile page with the new square photo displayed.

- [ ] **Step 5: Manual browser check (profile photo, upload path)**

On the same page, click "Upload Foto", pick an image file:
- Confirm the same crop UI appears for the uploaded image.
- Confirm "Batal" during cropping fully resets the card back to the initial Ambil Foto/Upload Foto buttons.
- Confirm a completed crop + valid face saves successfully, same as the camera path.

- [ ] **Step 6: Commit any fixes found during manual verification**

Only if Steps 3-5 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
