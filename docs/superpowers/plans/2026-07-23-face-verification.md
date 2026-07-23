# Face Verification for Check-in Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Students capture a live reference profile photo; check-in photos are compared against it client-side via face-api.js, and mismatches are flagged into the existing suspicious-review workflow rather than blocking check-in.

**Architecture:** face-api.js (self-hosted npm package + static model weight files in `public/models/face-api/`) runs entirely in the browser to compute 128-number face descriptors — once when a student saves their reference photo, and again on every check-in capture. Only descriptors (small JSON arrays), never raw ML computation, cross the network. A new `FaceVerificationService` (mirroring the existing `LocationVerificationService`) computes Euclidean distance server-side in plain PHP and sets `requires_manual_review` on mismatches, reusing the review workflow already built for location anomalies.

**Tech Stack:** face-api.js (client-side face detection/recognition), existing `useCamera` hook (getUserMedia), Laravel 12 Eloquent, Inertia.js, React 19.

---

## Task 1: Install face-api.js and host model weights

**Files:**
- Modify: `package.json` (via npm install)
- Create: `public/models/face-api/tiny_face_detector_model-weights_manifest.json` and shard files
- Create: `public/models/face-api/face_recognition_model-weights_manifest.json` and shard files

- [ ] **Step 1: Install the package**

Run: `npm install face-api.js`
Expected: adds `face-api.js` to `package.json` dependencies, completes with no errors.

- [ ] **Step 2: Download the two required model weight sets**

face-api.js ships its model weights as static files that must be fetched at runtime by URL (they are not bundled by Vite — they're binary weight shards, not JS). The upstream project publishes these at `https://github.com/justadudewhohacks/face-api.js/tree/master/weights`. Download exactly these files and place them in `public/models/face-api/`:

- `tiny_face_detector_model-weights_manifest.json`
- `tiny_face_detector_model-shard1`
- `face_recognition_model-weights_manifest.json`
- `face_recognition_model-shard1`
- `face_recognition_model-shard2`

Run (from the project root, requires internet access):
```bash
mkdir -p public/models/face-api
cd public/models/face-api
curl -fLO https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/tiny_face_detector_model-weights_manifest.json
curl -fLO https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/tiny_face_detector_model-shard1
curl -fLO https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-weights_manifest.json
curl -fLO https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-shard1
curl -fLO https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-shard2
cd -
```

- [ ] **Step 3: Verify the files downloaded correctly**

Run: `ls -la public/models/face-api/`
Expected: 5 files present, none of them 0 bytes or containing an HTML error page (a failed download from a raw GitHub URL sometimes returns a small HTML/JSON error body instead of the binary shard — spot check with `file public/models/face-api/tiny_face_detector_model-shard1`, expected output should indicate binary data, not ASCII/HTML text).

- [ ] **Step 4: Commit**

```bash
git add package.json package-lock.json public/models/face-api/
git commit -m "feat: Install face-api.js and host its model weights locally

Self-hosted under public/models/face-api/, consistent with this
project's existing pattern of self-hosting assets (fontsource fonts)
rather than depending on a CDN. Not wired into any code yet."
```

---

## Task 2: Add `useFaceDetection` hook

**Files:**
- Create: `resources/js/hooks/useFaceDetection.js`

This hook wraps face-api.js model loading and descriptor computation, mirroring the style of `resources/js/hooks/useCamera.js` and `resources/js/hooks/useGeolocation.js` (both already in the codebase).

- [ ] **Step 1: Create the hook**

Create `resources/js/hooks/useFaceDetection.js`:

```javascript
import { useCallback, useState } from 'react'
import * as faceapi from 'face-api.js'

const MODEL_URL = '/models/face-api'

let modelsLoadingPromise = null

/**
 * Loads face-api.js's TinyFaceDetector + FaceRecognitionNet models once
 * (shared across every hook instance via a module-level promise, so
 * opening the check-in modal twice doesn't re-fetch ~6.4MB of weights),
 * then exposes a function to compute a 128-number face descriptor from
 * an image/canvas/video element.
 *
 * Never throws on failure - callers get `available: false` instead, so
 * a browser that can't run face-api.js (unsupported, blocked, offline)
 * degrades to skipping face verification rather than blocking the
 * student from checking in at all.
 */
export function useFaceDetection() {
  const [status, setStatus] = useState('idle') // idle | loading | ready | unavailable

  const ensureModelsLoaded = useCallback(async () => {
    if (status === 'ready') return true
    if (status === 'unavailable') return false

    setStatus('loading')

    if (!modelsLoadingPromise) {
      modelsLoadingPromise = Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
      ])
    }

    try {
      await modelsLoadingPromise
      setStatus('ready')
      return true
    } catch (err) {
      modelsLoadingPromise = null
      setStatus('unavailable')
      return false
    }
  }, [status])

  /**
   * Detects a single face in the given image-like element and returns
   * its 128-number descriptor as a plain array, or null if no face was
   * detected (or the models failed to load).
   */
  const detectDescriptor = useCallback(async (imageElement) => {
    const ready = await ensureModelsLoaded()
    if (!ready) return null

    try {
      const detection = await faceapi
        .detectSingleFace(imageElement, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor()

      if (!detection) return null

      return Array.from(detection.descriptor)
    } catch (err) {
      return null
    }
  }, [ensureModelsLoaded])

  return { status, ensureModelsLoaded, detectDescriptor }
}
```

- [ ] **Step 2: Build to verify no syntax errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors (this hook isn't imported anywhere yet, so this only guards against a syntax typo).

- [ ] **Step 3: Commit**

```bash
git add resources/js/hooks/useFaceDetection.js
git commit -m "feat: Add useFaceDetection hook

Wraps face-api.js model loading (once, shared across instances) and
descriptor computation. Never throws - failures surface as
status='unavailable' so callers can gracefully skip face verification
instead of blocking the user."
```

---

## Task 3: Database migrations for reference photo and verification result

**Files:**
- Create: `database/migrations/2026_07_23_000001_add_face_reference_to_students_table.php`
- Create: `database/migrations/2026_07_23_000002_add_face_verification_to_attendances_table.php`

- [ ] **Step 1: Create the students migration**

Create `database/migrations/2026_07_23_000001_add_face_reference_to_students_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            // Storage path on the 'public' disk, mirroring attendances.check_in_photo

            $table->json('face_descriptor')->nullable();
            // 128-number face-api.js descriptor computed client-side from profile_photo
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }
};
```

- [ ] **Step 2: Create the attendances migration**

Create `database/migrations/2026_07_23_000002_add_face_verification_to_attendances_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('face_verification_status')->nullable();
            // verified, mismatch, no_reference, unavailable - null on rows from before this feature

            $table->float('face_match_distance')->nullable();
            // Euclidean distance between check-in and reference descriptors, when both exist
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['face_verification_status', 'face_match_distance']);
        });
    }
};
```

- [ ] **Step 3: Run the migrations**

Run: `php artisan migrate`
Expected: both new migrations run and report `DONE`.

- [ ] **Step 4: Verify the columns exist**

Run: `php artisan tinker --execute="
echo Schema::hasColumn('students', 'profile_photo') ? 'students.profile_photo: yes' : 'MISSING' . PHP_EOL;
echo Schema::hasColumn('students', 'face_descriptor') ? 'students.face_descriptor: yes' : 'MISSING' . PHP_EOL;
echo Schema::hasColumn('attendances', 'face_verification_status') ? 'attendances.face_verification_status: yes' : 'MISSING' . PHP_EOL;
echo Schema::hasColumn('attendances', 'face_match_distance') ? 'attendances.face_match_distance: yes' : 'MISSING' . PHP_EOL;
"`
Expected: all four print "yes".

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_07_23_000001_add_face_reference_to_students_table.php database/migrations/2026_07_23_000002_add_face_verification_to_attendances_table.php
git commit -m "feat: Add DB columns for face reference photo and verification result

students gains profile_photo + face_descriptor (the reference).
attendances gains face_verification_status + face_match_distance (the
per-check-in result). Both nullable - existing rows are unaffected."
```

---

## Task 4: Update `Student` and `Attendance` models

**Files:**
- Modify: `app/Models/Student.php`
- Modify: `app/Models/Attendance.php`

- [ ] **Step 1: Add the new fields to `Student::$fillable` and add a photo URL accessor**

In `app/Models/Student.php`, change:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'nim',
        'universitas',
        'program_studi',
        'semester',
        'direktorat',
        'periode_mulai',
        'periode_selesai',
    ];
```
to:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'nim',
        'universitas',
        'program_studi',
        'semester',
        'direktorat',
        'periode_mulai',
        'periode_selesai',
        'profile_photo',
        'face_descriptor',
    ];

    protected $casts = [
        'face_descriptor' => 'array',
    ];

    protected $appends = [
        'profile_photo_url',
    ];
```

Then add the accessor method — insert it right after the `attendances()` method at the end of the class:
```php
    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, User::class, 'id', 'user_id');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? Storage::disk('public')->url($this->profile_photo) : null;
    }
}
```

- [ ] **Step 2: Add the new fields to `Attendance::$fillable` and `$casts`**

In `app/Models/Attendance.php`, change:
```php
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_photo',
        'check_out_photo',
        'notes',
        'status',
        'supervisor_approval',
        'supervisor_notes',
        'approved_by',
        'approved_at',
    ];
```
to:
```php
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_photo',
        'check_out_photo',
        'notes',
        'status',
        'supervisor_approval',
        'supervisor_notes',
        'approved_by',
        'approved_at',
        'face_verification_status',
        'face_match_distance',
    ];
```

(No new accessor needed here — `face_verification_status` and `face_match_distance` are plain columns, already included in serialization without needing `$appends`, unlike the computed `working_hours`/`is_late` accessors fixed earlier this session.)

- [ ] **Step 3: Verify both files have no syntax errors**

Run:
```bash
php -l app/Models/Student.php
php -l app/Models/Attendance.php
```
Expected: `No syntax errors detected in ...` for both.

- [ ] **Step 4: Verify the accessor and casts work**

Run: `php artisan tinker --execute="
\$s = App\Models\Student::first();
\$s->profile_photo = 'students/profile/test.jpg';
echo 'profile_photo_url: ' . \$s->profile_photo_url . PHP_EOL;
\$s->profile_photo = null;
"`
Expected: prints a URL string ending in `/storage/students/profile/test.jpg` (the assignment is not saved, just checking the accessor computes correctly in memory).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Student.php app/Models/Attendance.php
git commit -m "feat: Wire face reference/verification columns into Eloquent models

Student gets profile_photo_url (mirrors Attendance's existing
check_in_photo_url pattern) and face_descriptor cast to array.
Attendance's two new columns are added to \$fillable."
```

---

## Task 5: Build `FaceVerificationService`

**Files:**
- Create: `app/Services/FaceVerificationService.php`

- [ ] **Step 1: Create the service**

Create `app/Services/FaceVerificationService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Compares a check-in face descriptor (computed client-side by
 * face-api.js) against a student's stored reference descriptor.
 * Mirrors LocationVerificationService's role: a plain-PHP anomaly
 * check that never blocks the action it's verifying, only flags it
 * for manual review when the signal is bad.
 */
class FaceVerificationService
{
    /**
     * face-api.js's commonly-used threshold for "same person" - a
     * Euclidean distance below this between two descriptors is
     * considered a match. Fixed for now; not user-configurable.
     */
    private const MATCH_THRESHOLD = 0.6;

    /**
     * Verify a check-in descriptor against a student's reference.
     *
     * Returns ['status' => string, 'distance' => float|null].
     * status is one of: 'verified', 'mismatch', 'no_reference', 'unavailable'.
     */
    public function verify(?array $checkInDescriptor, ?array $referenceDescriptor, int $userId): array
    {
        if (!$referenceDescriptor) {
            return ['status' => 'no_reference', 'distance' => null];
        }

        if (!$this->isValidDescriptor($checkInDescriptor)) {
            return ['status' => 'unavailable', 'distance' => null];
        }

        if (!$this->isValidDescriptor($referenceDescriptor)) {
            // Malformed reference data (shouldn't normally happen since it's
            // validated at save time in Task 8, but degrade gracefully rather
            // than error if it's ever corrupted).
            return ['status' => 'unavailable', 'distance' => null];
        }

        $distance = $this->euclideanDistance($checkInDescriptor, $referenceDescriptor);
        $status = $distance <= self::MATCH_THRESHOLD ? 'verified' : 'mismatch';

        if ($status === 'mismatch') {
            Log::warning('Face verification mismatch at check-in', [
                'user_id' => $userId,
                'distance' => $distance,
                'threshold' => self::MATCH_THRESHOLD,
            ]);
        }

        return ['status' => $status, 'distance' => round($distance, 4)];
    }

    private function isValidDescriptor($descriptor): bool
    {
        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return false;
        }

        foreach ($descriptor as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }

        return true;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sumSquares = 0.0;

        for ($i = 0; $i < 128; $i++) {
            $diff = (float) $a[$i] - (float) $b[$i];
            $sumSquares += $diff * $diff;
        }

        return sqrt($sumSquares);
    }
}
```

- [ ] **Step 2: Verify no syntax errors**

Run: `php -l app/Services/FaceVerificationService.php`
Expected: `No syntax errors detected in app/Services/FaceVerificationService.php`

- [ ] **Step 3: Verify the service's logic with a quick manual check**

Run: `php artisan tinker --execute="
\$service = new App\Services\FaceVerificationService();

// Identical descriptors - distance should be 0, status verified
\$a = array_fill(0, 128, 0.5);
\$result = \$service->verify(\$a, \$a, 1);
echo 'identical: ' . json_encode(\$result) . PHP_EOL;

// Very different descriptors - should mismatch
\$b = array_fill(0, 128, 0.5);
\$c = array_fill(0, 128, -0.5);
\$result2 = \$service->verify(\$b, \$c, 1);
echo 'different: ' . json_encode(\$result2) . PHP_EOL;

// No reference
\$result3 = \$service->verify(\$a, null, 1);
echo 'no reference: ' . json_encode(\$result3) . PHP_EOL;

// Malformed check-in descriptor (wrong length)
\$result4 = \$service->verify([1,2,3], \$a, 1);
echo 'malformed: ' . json_encode(\$result4) . PHP_EOL;
"`
Expected:
- `identical`: `{"status":"verified","distance":0}`
- `different`: `{"status":"mismatch","distance":...}` with a large distance value (well above 0.6)
- `no reference`: `{"status":"no_reference","distance":null}`
- `malformed`: `{"status":"unavailable","distance":null}`

- [ ] **Step 4: Commit**

```bash
git add app/Services/FaceVerificationService.php
git commit -m "feat: Add FaceVerificationService

Plain-PHP Euclidean distance comparison between two face-api.js
descriptors, mirroring LocationVerificationService's role and
placement. Degrades to 'unavailable' on malformed input rather than
throwing - this is a defense-in-depth signal, not a hard gate."
```

---

## Task 6: Wire face verification into `AttendanceController::checkIn()`

**Files:**
- Modify: `app/Http/Controllers/AttendanceController.php`

- [ ] **Step 1: Add the import and validation rule**

In `app/Http/Controllers/AttendanceController.php`, change the top imports from:
```php
use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\AttendanceSetting;
use App\Services\LocationVerificationService;
use Illuminate\Http\Request;
```
to:
```php
use App\Models\Attendance;
use App\Models\AttendanceException;
use App\Models\AttendanceSetting;
use App\Services\LocationVerificationService;
use App\Services\FaceVerificationService;
use Illuminate\Http\Request;
```

- [ ] **Step 2: Accept and decode the descriptor in `checkIn()`**

In the `checkIn()` method, change the validation block from:
```php
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|image|max:2048',
                'notes' => 'nullable|string|max:500',
            ]);
```
to:
```php
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|image|max:2048',
                'notes' => 'nullable|string|max:500',
                'face_descriptor' => 'nullable|string',
            ]);
```

(`face_descriptor` arrives as a JSON-encoded string in the multipart form body alongside the photo file, since `FormData` can't carry a raw array — it's decoded below.)

- [ ] **Step 3: Run face verification and store the result**

Change the `$attendance = Attendance::updateOrCreate(...)` block from:
```php
            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'check_in' => $now,
                    'check_in_latitude' => $request->latitude,
                    'check_in_longitude' => $request->longitude,
                    'check_in_photo' => $photoPath,
                    'notes' => $request->notes,
                    'status' => $status,
                    'location_verification_status' => $locationVerificationStatus,
                    'location_spoofing_score' => $spoofingScore,
                    'location_verification_details' => $verificationResult,
                    'requires_manual_review' => $requiresManualReview,
                ]
            );
```
to:
```php
            $checkInDescriptor = null;
            if ($request->filled('face_descriptor')) {
                $decoded = json_decode($request->input('face_descriptor'), true);
                if (is_array($decoded)) {
                    $checkInDescriptor = $decoded;
                }
            }

            $faceService = new FaceVerificationService();
            $student = $user->student;
            $faceResult = $faceService->verify($checkInDescriptor, $student?->face_descriptor, $user->id);

            if ($faceResult['status'] === 'mismatch') {
                $requiresManualReview = true;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'check_in' => $now,
                    'check_in_latitude' => $request->latitude,
                    'check_in_longitude' => $request->longitude,
                    'check_in_photo' => $photoPath,
                    'notes' => $request->notes,
                    'status' => $status,
                    'location_verification_status' => $locationVerificationStatus,
                    'location_spoofing_score' => $spoofingScore,
                    'location_verification_details' => $verificationResult,
                    'requires_manual_review' => $requiresManualReview,
                    'face_verification_status' => $faceResult['status'],
                    'face_match_distance' => $faceResult['distance'],
                ]
            );
```

Note: `$requiresManualReview` is already declared and defaulted earlier in the method (`$requiresManualReview = false;` near the location-verification block) — this change reuses that same variable, only ever upgrading it to `true`, never downgrading a `true` set by the location check back to `false`.

- [ ] **Step 4: Verify no syntax errors**

Run: `php -l app/Http/Controllers/AttendanceController.php`
Expected: `No syntax errors detected in app/Http/Controllers/AttendanceController.php`

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AttendanceController.php
git commit -m "feat: Run face verification during check-in

Decodes the client-computed face_descriptor, compares it against the
student's stored reference via FaceVerificationService, and stores the
result. A mismatch sets requires_manual_review=true, reusing the exact
flag the location-anomaly system already uses - no new review UI
needed, mismatched check-ins surface in the existing Suspicious pages."
```

---

## Task 7: Capture and send the face descriptor during check-in

**Files:**
- Modify: `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`
- Modify: `resources/js/Pages/Student/Attendance/Index.jsx`

Face verification only applies to check-in, not check-out (per the design spec's non-goals). `AttendanceModal` is shared between both, so this is gated by a new `requireFaceCheck` prop.

- [ ] **Step 1: Add the face detection hook and descriptor capture to `AttendanceModal.jsx`**

Change the imports at the top of `resources/js/Pages/Student/Attendance/AttendanceModal.jsx` from:
```jsx
import { useEffect, useState } from 'react'
import { Camera, RefreshCw, X, MapPin } from 'lucide-react'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { useCamera } from '@/hooks/useCamera'
import { useGeolocation } from '@/hooks/useGeolocation'
import { cn } from '@/lib/utils'
```
to:
```jsx
import { useEffect, useState } from 'react'
import { Camera, RefreshCw, X, MapPin } from 'lucide-react'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { useCamera } from '@/hooks/useCamera'
import { useGeolocation } from '@/hooks/useGeolocation'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'
```

Change the component signature and initial state from:
```jsx
export default function AttendanceModal({ open, onClose, title, subtitle, destructive, notesPlaceholder, onSubmit, submitting }) {
  const camera = useCamera()
  const geo = useGeolocation()
  const [notes, setNotes] = useState('')
```
to:
```jsx
export default function AttendanceModal({ open, onClose, title, subtitle, destructive, notesPlaceholder, onSubmit, submitting, requireFaceCheck = false }) {
  const camera = useCamera()
  const geo = useGeolocation()
  const face = useFaceDetection()
  const [notes, setNotes] = useState('')
  const [faceCheckMessage, setFaceCheckMessage] = useState('')
```

Change the `useEffect` that loads on open from:
```jsx
  useEffect(() => {
    if (open) {
      geo.request()
    } else {
      camera.reset()
      setNotes('')
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])
```
to:
```jsx
  useEffect(() => {
    if (open) {
      geo.request()
      if (requireFaceCheck) face.ensureModelsLoaded()
    } else {
      camera.reset()
      setNotes('')
      setFaceCheckMessage('')
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])
```

- [ ] **Step 2: Detect the face and block submission when none is found, only for check-in**

Change `handleSubmit` from:
```jsx
  async function handleSubmit(e) {
    e.preventDefault()
    if (!geo.position) {
      alert('Lokasi belum didapatkan, silakan tunggu...')
      geo.request()
      return
    }
    if (camera.phase !== 'captured' || !camera.canvasRef.current) {
      alert('Silakan ambil foto terlebih dahulu')
      return
    }

    camera.canvasRef.current.toBlob(
      (blob) => onSubmit({ latitude: geo.position.latitude, longitude: geo.position.longitude, photo: blob, notes }),
      'image/jpeg',
      0.8,
    )
  }
```
to:
```jsx
  async function handleSubmit(e) {
    e.preventDefault()
    if (!geo.position) {
      alert('Lokasi belum didapatkan, silakan tunggu...')
      geo.request()
      return
    }
    if (camera.phase !== 'captured' || !camera.canvasRef.current) {
      alert('Silakan ambil foto terlebih dahulu')
      return
    }

    let faceDescriptor = null

    if (requireFaceCheck) {
      setFaceCheckMessage('Memeriksa wajah...')
      const descriptor = await face.detectDescriptor(camera.canvasRef.current)
      setFaceCheckMessage('')

      if (face.status === 'ready' && !descriptor) {
        alert('Wajah tidak terdeteksi pada foto. Silakan pastikan wajah Anda terlihat jelas dan coba lagi.')
        return
      }

      faceDescriptor = descriptor
    }

    camera.canvasRef.current.toBlob(
      (blob) => onSubmit({
        latitude: geo.position.latitude,
        longitude: geo.position.longitude,
        photo: blob,
        notes,
        faceDescriptor,
      }),
      'image/jpeg',
      0.8,
    )
  }
```

Note the `face.status === 'ready'` guard on the rejection: if the models never finished loading (`status` stayed `'loading'` or became `'unavailable'`), `detectDescriptor` also returns `null`, but that must NOT be treated as "no face in frame" — it means face verification itself is unavailable, which per the spec must never block check-in. Only reject when the models are confirmed `'ready'` and still found no face.

- [ ] **Step 3: Show a brief status message during the check**

Change the submit button block from:
```jsx
              <Button
                type="submit"
                variant={destructive ? 'destructive' : 'default'}
                disabled={submitting || camera.phase !== 'captured'}
                className="w-full"
              >
                {submitting ? 'Memproses...' : title}
              </Button>
```
to:
```jsx
              {faceCheckMessage && <p className="text-sm text-muted-foreground">{faceCheckMessage}</p>}

              <Button
                type="submit"
                variant={destructive ? 'destructive' : 'default'}
                disabled={submitting || camera.phase !== 'captured'}
                className="w-full"
              >
                {submitting ? 'Memproses...' : title}
              </Button>
```

- [ ] **Step 4: Pass `requireFaceCheck` and the descriptor through in `Index.jsx`**

In `resources/js/Pages/Student/Attendance/Index.jsx`, change `submitAttendance` from:
```jsx
  async function submitAttendance(endpoint, { latitude, longitude, photo, notes }) {
    setSubmitting(true)
    const formData = new FormData()
    formData.append('latitude', latitude)
    formData.append('longitude', longitude)
    formData.append('photo', photo, 'photo.jpg')
    formData.append('notes', notes)
```
to:
```jsx
  async function submitAttendance(endpoint, { latitude, longitude, photo, notes, faceDescriptor }) {
    setSubmitting(true)
    const formData = new FormData()
    formData.append('latitude', latitude)
    formData.append('longitude', longitude)
    formData.append('photo', photo, 'photo.jpg')
    formData.append('notes', notes)
    if (faceDescriptor) {
      formData.append('face_descriptor', JSON.stringify(faceDescriptor))
    }
```

Change the check-in `<AttendanceModal>` (the first one, without `destructive`) from:
```jsx
      <AttendanceModal
        open={checkInOpen}
        onClose={() => setCheckInOpen(false)}
        title="Check In"
        subtitle="Lakukan absensi masuk"
        notesPlaceholder={{ optional: true, text: 'Tulis aktivitas atau catatan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-in'), payload)}
      />
```
to:
```jsx
      <AttendanceModal
        open={checkInOpen}
        onClose={() => setCheckInOpen(false)}
        title="Check In"
        subtitle="Lakukan absensi masuk"
        notesPlaceholder={{ optional: true, text: 'Tulis aktivitas atau catatan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-in'), payload)}
        requireFaceCheck
      />
```

Leave the check-out `<AttendanceModal>` (the `destructive` one) unchanged — it does not get `requireFaceCheck`, so it defaults to `false` and behaves exactly as before this feature.

- [ ] **Step 5: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors. Check the build output for the chunk containing `Student/Attendance/Index` or `AttendanceModal` — face-api.js (a sizeable dependency) should appear in that page's own chunk or a chunk shared only with pages that import `useFaceDetection`, not bloat the main `app-*.js` entry chunk. If it does appear inlined into the main chunk, note this as a concern for Task 9's build-verification step rather than blocking here.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Student/Attendance/AttendanceModal.jsx resources/js/Pages/Student/Attendance/Index.jsx
git commit -m "feat: Detect and send a face descriptor on check-in only

AttendanceModal gains a requireFaceCheck prop, true only on the
check-in instance in Index.jsx - check-out is unaffected, matching the
design spec's non-goal of leaving check-out alone. When face-api.js's
models are confirmed loaded and no face is found in the captured
frame, submission is blocked client-side with a clear message. If the
models never loaded (unsupported browser, blocked, offline), check-in
proceeds normally without a descriptor - this feature never blocks
check-in on its own infrastructure failing."
```

---

## Task 8: Add the reference profile photo capture to `Student/Info/Edit.jsx`

**Files:**
- Modify: `app/Http/Controllers/Student/ProfileController.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Student/Info/ProfilePhotoCard.jsx`
- Modify: `resources/js/Pages/Student/Info/Edit.jsx`

- [ ] **Step 1: Add the backend endpoint**

In `app/Http/Controllers/Student/ProfileController.php`, add a `use` import and a new method. Change the imports from:
```php
use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
```
to:
```php
use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
```

Then add a new method right after `update()` (before `generateCertificate()`):
```php
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
            'face_descriptor' => 'required|string',
        ]);

        $descriptor = json_decode($request->input('face_descriptor'), true);

        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return back()->with('error', 'Wajah tidak terdeteksi pada foto. Silakan coba lagi.');
        }

        $student = Auth::user()->student;

        if ($student->profile_photo && Storage::disk('public')->exists($student->profile_photo)) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        $photoPath = $request->file('photo')->store('students/profile', 'public');

        $student->update([
            'profile_photo' => $photoPath,
            'face_descriptor' => $descriptor,
        ]);

        return redirect()->route('student.info.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }
```

Also load the new columns in `edit()` — no code change needed here since `$student` is already passed whole to Inertia via `'student' => $student` and `profile_photo_url`/`face_descriptor` will now automatically be present on it thanks to Task 4's `$appends` addition.

- [ ] **Step 2: Register the route**

In `routes/web.php`, find the existing `student.info.*` routes:
```php
    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');
```
and change to:
```php
    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');
    Route::post('/info/profile-photo', [StudentProfileController::class, 'updateProfilePhoto'])->name('info.profile-photo.update');
```

- [ ] **Step 3: Verify syntax and route registration**

Run:
```bash
php -l app/Http/Controllers/Student/ProfileController.php
php -l routes/web.php
php artisan route:list --name=student.info.profile-photo.update
```
Expected: no syntax errors on either file; the route list shows one row, `POST student/info/profile-photo ... student.info.profile-photo.update`.

- [ ] **Step 4: Regenerate ziggy.js**

This project's frontend calls routes via a checked-in static `resources/js/ziggy.js` file that must be regenerated whenever a new named route is added — missing this step previously caused a silent, hard-to-diagnose bug in the notification-bell feature this session.

Run: `php artisan ziggy:generate`
Expected: completes with no error.

Verify: `grep -c "student.info.profile-photo.update" resources/js/ziggy.js` should print `1`.

- [ ] **Step 5: Build the `ProfilePhotoCard` component**

Create `resources/js/Pages/Student/Info/ProfilePhotoCard.jsx`:

```jsx
import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { Camera, RefreshCw, UserRound } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'

/**
 * Captures a live reference photo (not a file upload - the same
 * getUserMedia flow used for attendance) used as the face-matching
 * baseline for check-in verification. Rejects the capture client-side
 * if face-api.js can't find a face in it, since a reference without a
 * detectable face makes the matching feature useless.
 */
export default function ProfilePhotoCard({ profilePhotoUrl }) {
  const camera = useCamera()
  const face = useFaceDetection()
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  async function handleSave() {
    if (camera.phase !== 'captured' || !camera.canvasRef.current) return

    setStatusMessage('Memeriksa wajah...')

    const descriptor = await face.detectDescriptor(camera.canvasRef.current)

    if (face.status !== 'ready') {
      setStatusMessage('')
      alert('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba di perangkat/browser lain.')
      return
    }

    if (!descriptor) {
      setStatusMessage('')
      alert('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
      return
    }

    setStatusMessage('')
    setSubmitting(true)

    camera.canvasRef.current.toBlob(
      (blob) => {
        const formData = new FormData()
        formData.append('photo', blob, 'profile.jpg')
        formData.append('face_descriptor', JSON.stringify(descriptor))

        router.post(route('student.info.profile-photo.update'), formData, {
          preserveScroll: true,
          onFinish: () => {
            setSubmitting(false)
            camera.reset()
          },
        })
      },
      'image/jpeg',
      0.8,
    )
  }

  return (
    <Card className="self-start">
      <CardHeader className="border-b">
        <CardTitle className="flex items-center gap-2">
          <UserRound className="h-4 w-4 text-muted-foreground" /> Foto Profil
        </CardTitle>
        <CardDescription>Digunakan sebagai referensi verifikasi wajah saat check-in</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4 pt-6">
        {camera.phase === 'idle' && profilePhotoUrl && (
          <img src={profilePhotoUrl} alt="Foto profil" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}

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
        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex gap-2">
          {camera.phase === 'idle' && (
            <Button type="button" onClick={camera.start} className="flex-1">
              <Camera /> {profilePhotoUrl ? 'Ganti Foto' : 'Ambil Foto'}
            </Button>
          )}
          {camera.phase === 'streaming' && (
            <Button type="button" onClick={camera.capture} className="flex-1">
              <Camera /> Ambil Foto
            </Button>
          )}
          {camera.phase === 'captured' && (
            <>
              <Button type="button" onClick={camera.retake} variant="outline" className="flex-1">
                <RefreshCw /> Ulangi
              </Button>
              <Button type="button" onClick={handleSave} disabled={submitting} className="flex-1">
                {submitting ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
```

- [ ] **Step 6: Wire the card into `Edit.jsx`**

In `resources/js/Pages/Student/Info/Edit.jsx`, add the import. Change:
```jsx
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
```
to:
```jsx
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import ProfilePhotoCard from './ProfilePhotoCard'
```

Then add the card to the right column, right before the existing certificate `<Card>`. Change:
```jsx
          <Card className="self-start">
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Award className="h-4 w-4 text-muted-foreground" /> Sertifikat Magang
              </CardTitle>
```
to:
```jsx
          <div className="space-y-6 self-start">
            <ProfilePhotoCard profilePhotoUrl={student.profile_photo_url} />

            <Card>
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Award className="h-4 w-4 text-muted-foreground" /> Sertifikat Magang
              </CardTitle>
```

Then find the closing tags at the end of the certificate card (currently `</Card>` followed by the closing `</div>` of the 3-column grid) and change:
```jsx
            </CardContent>
          </Card>
        </div>
      </div>
    </StudentLayout>
  )
}
```
to:
```jsx
            </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </StudentLayout>
  )
}
```

This wraps the certificate `Card` and the new `ProfilePhotoCard` together inside one `<div className="space-y-6 self-start">`, matching the exact pattern already used in `Supervisor/Students/AssessmentCreate.jsx` and `AssessmentEdit.jsx` for stacking multiple cards in a grid's right column (verified in an earlier feature this session).

- [ ] **Step 7: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Student/ProfileController.php routes/web.php resources/js/ziggy.js resources/js/Pages/Student/Info/ProfilePhotoCard.jsx resources/js/Pages/Student/Info/Edit.jsx
git commit -m "feat: Add reference profile photo capture to Student Info page

New ProfilePhotoCard uses the same live-camera flow as attendance
check-in (not a file upload), and rejects the capture client-side if
face-api.js finds no face in it. Backend endpoint stores both the
photo and the computed descriptor on the Student row."
```

---

## Task 9: Surface face mismatches on the Suspicious pages

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Suspicious.jsx`
- Modify: `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`

Both pages already have an "Alasan Anomali" column showing location-based flags (`location_notes`, coordinates). This adds a face-mismatch line alongside it, in both files identically.

- [ ] **Step 1: Add the face-mismatch line to `Admin/Attendance/Suspicious.jsx`**

Find the "Alasan Anomali" cell (already reads `attendance.check_in`/`attendance.check_in_latitude`/`attendance.check_in_longitude` correctly, per this session's earlier fix):

```jsx
                    <TableCell className="whitespace-normal">
                      <div className="space-y-1">
                        {attendance.location_notes && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <MapPin className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            {attendance.location_notes}
                          </p>
                        )}
                        {attendance.check_in_latitude && attendance.check_in_longitude && (
                          <p className="text-xs tabular-nums text-muted-foreground">
                            Koordinat: {Number(attendance.check_in_latitude).toFixed(4)}, {Number(attendance.check_in_longitude).toFixed(4)}
                          </p>
                        )}
                      </div>
                    </TableCell>
```

Change to:
```jsx
                    <TableCell className="whitespace-normal">
                      <div className="space-y-1">
                        {attendance.location_notes && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <MapPin className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            {attendance.location_notes}
                          </p>
                        )}
                        {attendance.check_in_latitude && attendance.check_in_longitude && (
                          <p className="text-xs tabular-nums text-muted-foreground">
                            Koordinat: {Number(attendance.check_in_latitude).toFixed(4)}, {Number(attendance.check_in_longitude).toFixed(4)}
                          </p>
                        )}
                        {attendance.face_verification_status === 'mismatch' && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            Wajah tidak cocok (jarak: {Number(attendance.face_match_distance).toFixed(2)})
                          </p>
                        )}
                      </div>
                    </TableCell>
```

Add the `UserRound` icon to the top imports. Change:
```jsx
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin } from 'lucide-react'
```
to:
```jsx
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin, UserRound } from 'lucide-react'
```

- [ ] **Step 2: Apply the identical change to `Supervisor/Attendance/Suspicious.jsx`**

Apply the exact same two changes (import + JSX block) to `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`, which has the byte-identical section (already using the correct `check_in`/`check_in_latitude`/`check_in_longitude` field names, confirmed during this session's earlier photo-viewing feature work).

Change the import from:
```jsx
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin } from 'lucide-react'
```
to:
```jsx
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin, UserRound } from 'lucide-react'
```

Change the same "Alasan Anomali" `<TableCell>` block using the identical before/after shown in Step 1.

- [ ] **Step 3: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Suspicious.jsx resources/js/Pages/Supervisor/Attendance/Suspicious.jsx
git commit -m "feat: Show face mismatch info on the Suspicious review pages

Adds a 'Wajah tidak cocok (jarak: X.XX)' line to the existing Alasan
Anomali column on both Admin and Supervisor Suspicious pages,
alongside the location-based flags already shown there. No new
column, no new page - reuses the review workflow built for location
anomalies."
```

---

## Task 10: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: PHP lint every touched/created backend file**

Run:
```bash
php -l app/Models/Student.php
php -l app/Models/Attendance.php
php -l app/Services/FaceVerificationService.php
php -l app/Http/Controllers/AttendanceController.php
php -l app/Http/Controllers/Student/ProfileController.php
php -l routes/web.php
```
Expected: `No syntax errors detected in ...` for every file.

- [ ] **Step 2: Confirm migrations are applied and routes registered**

Run:
```bash
php artisan migrate:status | grep face
php artisan route:list --name=student.info.profile-photo.update
```
Expected: both new migrations show `Ran`; the route appears once.

- [ ] **Step 3: Full frontend rebuild**

Run: `npm install && npm run build`
Expected: `✓ built in ...`, no errors. Check chunk sizes in the output — confirm `face-api.js`'s ~6MB+ isn't inlined into the main `app-*.js` chunk (it should only appear in chunks for pages that actually import `useFaceDetection`: `Student/Attendance/Index` and `Student/Info/Edit`).

- [ ] **Step 4: Manual QA — reference photo capture**

Start `php artisan serve --port=8000`, `rm -f public/hot`. Log in as a student, visit `/student/info`. Confirm the new "Foto Profil" card renders, "Ambil Foto" opens the camera, a captured frame with a clear face saves successfully (page reloads with a success flash message and the new photo displayed). Then test the rejection path: if possible, capture a frame with no face in it (point the camera away) and confirm the client-side alert fires and nothing is submitted.

- [ ] **Step 5: Manual QA — check-in with a match**

As the same student (with a reference photo now saved), go to `/student/attendance`, click "Check In Sekarang", capture a photo of the same face, submit. Confirm check-in succeeds. Run `php artisan tinker --execute="echo App\Models\Attendance::latest()->first()->face_verification_status;"` and confirm it prints `verified`.

- [ ] **Step 6: Manual QA — check-in with a mismatch**

Have a different person (or a photo of a different face held up to the camera) check in as the same student. Confirm check-in still succeeds (not blocked). Confirm via tinker that `face_verification_status` is `mismatch` and `requires_manual_review` is `true` on that row. Then, logged in as `admin@bakti.com` / `1` (or the relevant supervisor), visit `/admin/attendance/suspicious` (or `/supervisor/attendance/suspicious`) and confirm the row appears with a "Wajah tidak cocok (jarak: X.XX)" line.

- [ ] **Step 7: Manual QA — no reference photo**

As a student with no reference photo saved (`face_descriptor` still null), check in normally. Confirm check-in succeeds, `face_verification_status` is `no_reference`, and `requires_manual_review` is NOT set to true by this check alone (it may still be true if location flagged it independently — that's fine, this step is only confirming face verification itself doesn't force it).

- [ ] **Step 8: Manual QA — check-out is unaffected**

Confirm the check-out flow still works exactly as before — no face detection UI, no descriptor sent, `face_verification_status` remains whatever check-in already set (check-out doesn't touch that column, per Task 6's changes only touching `checkIn()`).

- [ ] **Step 9: Manual QA — dark mode and responsive**

On `/student/info`, toggle dark mode and confirm the new card renders correctly. Resize to 375px and confirm no layout breakage in the stacked-card right column.

- [ ] **Step 10: Commit (only if fixes were needed)**

If Steps 1-9 all pass cleanly with no code changes needed, there is nothing to commit here.

---

## Self-review notes (completed during plan authoring, not a task to run)

- **Spec coverage**: technology choice (Task 1-2), reference photo capture + hard reject on no-face (Task 8), check-in descriptor capture + hard reject on no-face (Task 7), check-out explicitly untouched (Task 7 Step 4's note), `FaceVerificationService` mirroring `LocationVerificationService` (Task 5), `no_reference`/`unavailable`/`verified`/`mismatch` states (Task 5, Task 6), `requires_manual_review` reuse (Task 6), Suspicious-page surfacing on both roles (Task 9), malformed-descriptor graceful degradation (Task 5's `isValidDescriptor`), model-load-failure graceful degradation (Task 2's `unavailable` status, Task 7/8's `face.status !== 'ready'` checks) — every spec section has a corresponding task.
- **Fixed during authoring**: initially wrote Task 7's rejection check as just `if (!descriptor)`, then caught that this conflates "models failed to load" (must never block) with "models loaded fine but genuinely found no face" (should block) — added the `face.status === 'ready'` guard to distinguish them, applied identically in both Task 7 (check-in) and Task 8 (profile photo).
- **Fixed during authoring**: remembered from this session's notification-bell feature that adding a new named route requires regenerating `resources/js/ziggy.js` or the frontend's `route()` calls silently fail — added that as an explicit step (Task 8 Step 4) rather than letting it be a repeat of that exact bug.
- **Type/name consistency**: `face_descriptor` (snake_case) is the field name used consistently in every FormData/request payload across Task 6 (backend read), Task 7 (check-in send), and Task 8 (profile-photo send). `faceDescriptor` (camelCase) is used consistently as the JS-side variable/prop name in Task 7's `AttendanceModal`/`Index.jsx` handoff. `face_verification_status`/`face_match_distance` (the two new `attendances` columns) are named identically across Task 3 (migration), Task 4 (`$fillable`), Task 6 (write), and Task 9 (read). `FaceVerificationService::verify()`'s return shape (`['status' => ..., 'distance' => ...]`) is defined once in Task 5 and consumed with matching keys in Task 6.
- **No placeholders**: every step has complete, exact code — no "similar to Task N" shortcuts. Task 9's two near-identical page edits are both spelled out in full (Step 1 for Admin, Step 2 explicitly repeating the same before/after for Supervisor) rather than telling the reader to copy one into the other.
