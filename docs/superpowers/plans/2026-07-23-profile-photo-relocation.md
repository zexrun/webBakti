# Profile Photo Relocation & Multi-Role Upload Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Move the face-verification reference photo/descriptor from `students` to `users` so every role can have one, replace the student-only camera-only upload flow with a shared camera-or-file-upload card on the all-roles Profile page, and remove the photo card from the student Info page entirely.

**Architecture:** A migration relocates `profile_photo`/`face_descriptor` columns (with data preserved via a copy-then-drop step), the two Eloquent models are updated to match, a new controller method on the generic `ProfileController` replaces the student-only one, and a new shared `ProfilePhotoCard` component (supporting both `useCamera`'s live capture and a plain file input, both funneling through the same `useFaceDetection` validation) replaces the old student-only component on `Profile/Edit.jsx`.

**Tech Stack:** Laravel 12 Eloquent migrations/models, Inertia.js, React 19, existing `useCamera`/`useFaceDetection` hooks (unchanged), face-api.js (already installed, no new models needed).

---

## Task 1: Migrate `profile_photo`/`face_descriptor` from `students` to `users`

**Files:**
- Create: `database/migrations/2026_07_23_000003_move_face_reference_to_users_table.php`

- [ ] **Step 1: Write the migration**

Create `database/migrations/2026_07_23_000003_move_face_reference_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->json('face_descriptor')->nullable();
        });

        // Preserve any reference photo/descriptor a student already saved
        // under the old per-Student scheme before the columns are dropped
        // from students.
        DB::table('students')
            ->whereNotNull('profile_photo')
            ->orWhereNotNull('face_descriptor')
            ->get(['user_id', 'profile_photo', 'face_descriptor'])
            ->each(function ($student) {
                DB::table('users')
                    ->where('id', $student->user_id)
                    ->update([
                        'profile_photo' => $student->profile_photo,
                        'face_descriptor' => $student->face_descriptor,
                    ]);
            });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->json('face_descriptor')->nullable();
        });

        DB::table('users')
            ->whereNotNull('profile_photo')
            ->orWhereNotNull('face_descriptor')
            ->get(['id', 'profile_photo', 'face_descriptor'])
            ->each(function ($user) {
                DB::table('students')
                    ->where('user_id', $user->id)
                    ->update([
                        'profile_photo' => $user->profile_photo,
                        'face_descriptor' => $user->face_descriptor,
                    ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_07_23_000003_move_face_reference_to_users_table ... DONE`

- [ ] **Step 3: Verify the columns moved and any existing data was preserved**

Run:
```bash
php artisan tinker --execute="
echo Schema::hasColumn('users', 'profile_photo') ? 'users.profile_photo: yes' : 'MISSING';
echo PHP_EOL;
echo Schema::hasColumn('users', 'face_descriptor') ? 'users.face_descriptor: yes' : 'MISSING';
echo PHP_EOL;
echo Schema::hasColumn('students', 'profile_photo') ? 'students.profile_photo STILL EXISTS (bug)' : 'students.profile_photo: correctly removed';
echo PHP_EOL;
echo Schema::hasColumn('students', 'face_descriptor') ? 'students.face_descriptor STILL EXISTS (bug)' : 'students.face_descriptor: correctly removed';
echo PHP_EOL;
"
```
Expected: all four lines confirm the columns exist on `users` and are gone from `students`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_23_000003_move_face_reference_to_users_table.php
git commit -m "feat: Move profile_photo/face_descriptor from students to users

Any role can now have a reference photo, not just students. Existing
data (if any) is copied across before the old columns are dropped, so
a student who already saved a reference under the old scheme doesn't
lose it."
```

---

## Task 2: Update `User` and `Student` models

**Files:**
- Modify: `app/Models/User.php`
- Modify: `app/Models/Student.php`

- [ ] **Step 1: Add the fields to `User.php`**

`app/Models/User.php` uses the newer `casts(): array` method form (not a `protected $casts` property) - match that style, don't copy `Student.php`'s older property-based style verbatim.

Change:
```php
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'activation_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
```
to:
```php
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'activation_token',
        'email_verified_at',
        'profile_photo',
        'face_descriptor',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * face_descriptor is a 128-float array read only by the backend
     * (AttendanceController::checkIn(), FaceVerificationService) - no
     * frontend page reads it directly, so it's excluded from
     * serialization to avoid bloating every User payload with data
     * nothing renders. profile_photo is superseded by the computed
     * profile_photo_url accessor below, same pattern as
     * Attendance::check_in_photo_url.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'profile_photo',
        'face_descriptor',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'face_descriptor' => 'array',
        ];
    }
```

- [ ] **Step 2: Add the `profile_photo_url` accessor to `User.php`**

Add a `use` import and the accessor method. Change:
```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
```
to:
```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
```

Then add the method right after `getStudentOrNull()`, at the end of the class body. Change:
```php
    public function getStudentOrNull()
    {
        return $this->student()->first();
    }
}
```
to:
```php
    public function getStudentOrNull()
    {
        return $this->student()->first();
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? Storage::disk('public')->url($this->profile_photo) : null;
    }
}
```

- [ ] **Step 3: Remove the fields from `Student.php`**

Read the current full `app/Models/Student.php` first (it currently has `profile_photo`/`face_descriptor` in `$fillable`, a `$casts` array with `face_descriptor` => `array`, `$appends` with `profile_photo_url`, a `$hidden` array with `face_descriptor`, and a `getProfilePhotoUrlAttribute()` method, plus a `use Illuminate\Support\Facades\Storage;` import that was added solely to support that accessor).

Change:
```php
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

    // face_descriptor is a 128-float array read only by the backend
    // (AttendanceController::checkIn(), FaceVerificationService) - no
    // frontend page reads it, so it's excluded from serialization to
    // avoid bloating every Student payload (list/search/plotting pages
    // that never asked for it) with data they never render.
    protected $hidden = [
        'face_descriptor',
    ];
```
to:
```php
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

Then remove the `getProfilePhotoUrlAttribute()` method entirely from the end of the class (it moved to `User.php` in Step 2). The class should end with the `attendances()` method's closing brace followed directly by the class's closing brace, exactly as it was before the original face-verification feature added the photo fields.

- [ ] **Step 4: Verify both files have no syntax errors**

Run:
```bash
php -l app/Models/User.php
php -l app/Models/Student.php
```
Expected: `No syntax errors detected in ...` for both.

- [ ] **Step 5: Verify the accessor works and Student no longer has it**

Run:
```bash
php artisan tinker --execute="
\$u = App\Models\User::first();
\$u->profile_photo = 'test/path.jpg';
echo 'User profile_photo_url: ' . \$u->profile_photo_url . PHP_EOL;
\$s = App\Models\Student::first();
echo 'Student has profile_photo_url method: ' . (method_exists(\$s, 'getProfilePhotoUrlAttribute') ? 'YES (bug, should be removed)' : 'no (correct)') . PHP_EOL;
"
```
Expected: first line prints a URL ending in `/storage/test/path.jpg`; second line confirms the method no longer exists on `Student`.

- [ ] **Step 6: Commit**

```bash
git add app/Models/User.php app/Models/Student.php
git commit -m "feat: Move face reference fields from Student model to User model

User gains profile_photo_url accessor (mirrors the pattern already used
on Attendance/Student), face_descriptor cast to array and hidden from
serialization. Student loses both - fully relocated, not duplicated."
```

---

## Task 3: Add `updateProfilePhoto()` to the generic `ProfileController`

**Files:**
- Modify: `app/Http/Controllers/ProfileController.php`
- Modify: `app/Http/Controllers/Student/ProfileController.php`

- [ ] **Step 1: Read the current full `app/Http/Controllers/ProfileController.php`**

This file currently has `use` imports for `ProfileUpdateRequest`, `Directorate`, `Position`, `AbsenUser`, `RedirectResponse`, `Request`, `Auth`, `Redirect`, `Rule`, `Hash`, `Inertia`, `InertiaResponse`, and methods `show()`, `edit()`, `update()`, `destroy()`.

- [ ] **Step 2: Add the `Storage` import**

Change:
```php
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Directorate;
use App\Models\Position;
use App\Models\AbsenUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
```
to:
```php
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Directorate;
use App\Models\Position;
use App\Models\AbsenUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
```

- [ ] **Step 3: Add `updateProfilePhoto()` right after `update()`, before `destroy()`**

Insert this method between the closing `}` of `update()` and the doc-comment for `destroy()`:

```php
    /**
     * Update the authenticated user's profile photo and face descriptor.
     * Available to every role - the photo/descriptor are stored on User,
     * not on a role-specific model, and are used as the face-verification
     * reference at student check-in (irrelevant for non-student roles,
     * but harmless to store).
     */
    public function updateProfilePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
            'face_descriptor' => 'required|string',
        ]);

        $descriptor = json_decode($request->input('face_descriptor'), true);

        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return back()->with('error', 'Wajah tidak terdeteksi pada foto. Silakan coba lagi.');
        }

        $user = $request->user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $photoPath = $request->file('photo')->store('users/profile', 'public');

        $user->update([
            'profile_photo' => $photoPath,
            'face_descriptor' => $descriptor,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }
```

Note the storage path is `users/profile` (not the old `students/profile`), since this is no longer student-specific.

- [ ] **Step 4: Remove `updateProfilePhoto()` from `Student\ProfileController.php`**

In `app/Http/Controllers/Student/ProfileController.php`, remove the entire `updateProfilePhoto()` method (currently between `update()` and `generateCertificate()`):

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

Delete it entirely (including the blank line after it), so `update()`'s closing `}` is followed directly by `generateCertificate()`'s doc-comment/signature.

After removing that method, the `use Illuminate\Support\Facades\Storage;` import at the top of this file is no longer used anywhere else in the file (verify by re-reading the file after the deletion - `generateCertificate()` doesn't reference `Storage`). Remove that import line too.

- [ ] **Step 5: Verify both files have no syntax errors**

Run:
```bash
php -l app/Http/Controllers/ProfileController.php
php -l app/Http/Controllers/Student/ProfileController.php
```
Expected: `No syntax errors detected in ...` for both.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/ProfileController.php app/Http/Controllers/Student/ProfileController.php
git commit -m "feat: Add updateProfilePhoto() to the generic ProfileController

Replaces Student\ProfileController's version, which is removed. Every
role can now update their own profile photo/descriptor through the
same endpoint - the backend can't tell (and doesn't need to tell)
whether the photo came from the camera or a file upload."
```

---

## Task 4: Update routes and regenerate ziggy.js

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Remove the old student-only route**

Change:
```php
    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');
    Route::post('/info/profile-photo', [StudentProfileController::class, 'updateProfilePhoto'])->name('info.profile-photo.update');
```
to:
```php
    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');
```

- [ ] **Step 2: Add the new all-roles route**

Change:
```php
Route::middleware('auth')->group(function () {
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
```
to:
```php
Route::middleware('auth')->group(function () {
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
});
```

- [ ] **Step 3: Verify syntax and route registration**

Run:
```bash
php -l routes/web.php
php artisan route:list --name=profile.photo.update
php artisan route:list --name=student.info.profile-photo.update
```
Expected: no syntax errors; the first `route:list` shows one row (`POST profile/photo ... profile.photo.update`); the second shows an empty result (no matching routes - the old route is gone).

- [ ] **Step 4: Regenerate ziggy.js**

This project's frontend calls routes via a checked-in static `resources/js/ziggy.js` file that must be regenerated whenever routes change - missing this step previously caused a silent bug in an earlier feature this session.

Run: `php artisan ziggy:generate`

Verify:
```bash
grep -c "profile.photo.update" resources/js/ziggy.js
grep -c "student.info.profile-photo.update" resources/js/ziggy.js
```
Expected: first command prints `1`, second prints `0`.

- [ ] **Step 5: Commit**

```bash
git add routes/web.php resources/js/ziggy.js
git commit -m "feat: Register profile.photo.update route, remove the student-only one

Available to any authenticated role now, not gated to role:student."
```

---

## Task 5: Build the shared `ProfilePhotoCard` component

**Files:**
- Create: `resources/js/Components/ProfilePhotoCard.jsx`
- Delete: `resources/js/Pages/Student/Info/ProfilePhotoCard.jsx`

- [ ] **Step 1: Read the current `resources/js/Pages/Student/Info/ProfilePhotoCard.jsx`**

This file currently implements the camera-only version: uses `useCamera` + `useFaceDetection`, has `phase`-based UI (`idle`/`streaming`/`captured`), a `handleSave()` that runs `detectDescriptor` on the captured canvas, rejects with an alert if `!available` or `!descriptor`, then POSTs via `router.post(route('student.info.profile-photo.update'), formData, ...)`.

- [ ] **Step 2: Create the new shared component**

Create `resources/js/Components/ProfilePhotoCard.jsx`:

```jsx
import { useEffect, useRef, useState } from 'react'
import { router } from '@inertiajs/react'
import { Camera, RefreshCw, UserRound, Upload } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'

/**
 * Profile photo card for any role. Supports two capture modes, both
 * validated identically by face-api.js before saving:
 *   - Camera: the existing live getUserMedia flow, capture to canvas.
 *   - Upload: a plain file input, loaded into a hidden <img> so
 *     detectDescriptor() (which accepts any image-like element) can
 *     run against it the same way it runs against the camera canvas.
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
  const uploadImgRef = useRef(null)
  const [uploadPreviewUrl, setUploadPreviewUrl] = useState(null)
  const [uploadReady, setUploadReady] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (!file) return

    camera.reset()
    setUploadReady(false)
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(URL.createObjectURL(file))
  }

  function clearUpload() {
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(null)
    setUploadReady(false)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  async function saveFromElement(imageElement, toBlobFn) {
    setStatusMessage('Memeriksa wajah...')

    const { available, descriptor } = await face.detectDescriptor(imageElement)

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

    toBlobFn((blob) => {
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
    })
  }

  function handleSaveFromCamera() {
    if (camera.phase !== 'captured' || !camera.canvasRef.current) return
    saveFromElement(camera.canvasRef.current, (cb) => camera.canvasRef.current.toBlob(cb, 'image/jpeg', 0.8))
  }

  function handleSaveFromUpload() {
    if (!uploadImgRef.current) return
    saveFromElement(uploadImgRef.current, (cb) => {
      fetch(uploadPreviewUrl)
        .then((res) => res.blob())
        .then(cb)
    })
  }

  const showIdlePreview = camera.phase === 'idle' && !uploadPreviewUrl && profilePhotoUrl

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

        <video
          ref={camera.videoRef}
          className={cn('h-48 w-full rounded-lg border border-border bg-muted object-cover', camera.phase !== 'streaming' && 'hidden')}
          muted
          playsInline
        />
        <canvas ref={camera.canvasRef} className="hidden" />
        {camera.previewUrl && (
          <img src={camera.previewUrl} alt="Preview kamera" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}

        {uploadPreviewUrl && (
          <img
            ref={uploadImgRef}
            src={uploadPreviewUrl}
            alt="Preview upload"
            onLoad={() => setUploadReady(true)}
            className="h-48 w-full rounded-lg border border-border object-cover"
          />
        )}

        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex flex-wrap gap-2">
          {camera.phase === 'idle' && !uploadPreviewUrl && (
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
              <Button type="button" onClick={handleSaveFromCamera} disabled={submitting} className="flex-1">
                {submitting ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </>
          )}

          {uploadPreviewUrl && (
            <>
              <Button type="button" onClick={clearUpload} variant="outline" className="flex-1">
                <RefreshCw /> Batal
              </Button>
              <Button type="button" onClick={handleSaveFromUpload} disabled={submitting || !uploadReady} className="flex-1">
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

Note on `handleSaveFromUpload()`: it re-fetches `uploadPreviewUrl` (a blob: URL created from the original `File`) to get a fresh `Blob` for the form submission, rather than reusing the original `File` object directly - this keeps the "get a blob to submit" step symmetric with the camera path's `canvas.toBlob()`, and avoids holding onto the original `File` reference across the async `detectDescriptor()` call.

- [ ] **Step 3: Delete the old student-only component**

```bash
rm "resources/js/Pages/Student/Info/ProfilePhotoCard.jsx"
```

- [ ] **Step 4: Build to verify no syntax errors**

Run: `npm run build`
Expected: `✓ built in ...` (this component isn't wired into any page yet in this task - Tasks 6-7 do that - so this only guards against a syntax typo).

- [ ] **Step 5: Commit**

```bash
git add resources/js/Components/ProfilePhotoCard.jsx
git rm resources/js/Pages/Student/Info/ProfilePhotoCard.jsx
git commit -m "feat: Add shared ProfilePhotoCard with camera + upload modes

Replaces the student-only, camera-only version. Both capture modes
funnel through the same face-api.js validation before saving - a
reference photo without a usable descriptor is rejected either way,
consistent with the design decision that profile photos (unlike
check-in itself) never save with a missing/failed detection."
```

---

## Task 6: Remove the photo card from `Student/Info/Edit.jsx`

**Files:**
- Modify: `resources/js/Pages/Student/Info/Edit.jsx`

- [ ] **Step 1: Read the current full file**

The file currently imports `ProfilePhotoCard` from `./ProfilePhotoCard` and renders it inside a `<div className="space-y-6 self-start">` wrapper alongside the certificate `<Card>`, in the right column of the page's 3-column grid.

- [ ] **Step 2: Remove the import**

Change:
```jsx
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import ProfilePhotoCard from './ProfilePhotoCard'
```
to:
```jsx
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
```

- [ ] **Step 3: Remove the wrapping div and the component usage, restoring the plain certificate Card**

Change:
```jsx
          <div className="space-y-6 self-start">
            <ProfilePhotoCard profilePhotoUrl={student.profile_photo_url} />

            <Card>
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Award className="h-4 w-4 text-muted-foreground" /> Sertifikat Magang
              </CardTitle>
```
to:
```jsx
          <Card className="self-start">
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Award className="h-4 w-4 text-muted-foreground" /> Sertifikat Magang
              </CardTitle>
```

Then find the matching closing tags at the end of that card (currently `</CardContent>` followed by `</Card>` then `</div>` then the grid's closing `</div>`) and change:
```jsx
            </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </StudentLayout>
```
to:
```jsx
            </CardContent>
          </Card>
        </div>
      </div>
    </StudentLayout>
```

This restores the exact pre-photo-feature structure: the certificate `Card` is once again a direct grid child with `className="self-start"`, not wrapped in an extra `<div>`.

- [ ] **Step 4: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors, no reference to the deleted `ProfilePhotoCard` import remaining anywhere.

- [ ] **Step 5: Manually verify the page**

Log in as a student, visit `/student/info`. Confirm no photo-related UI appears anywhere on the page, and the certificate card renders normally in the right column exactly as it did before the original face-verification feature.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Student/Info/Edit.jsx
git commit -m "refactor: Remove profile photo card from Student Info page

Photo management is now exclusively on the shared Profile/Edit.jsx
page for every role. This page returns to its pre-photo-feature
structure."
```

---

## Task 7: Add `ProfilePhotoCard` to `Profile/Edit.jsx`

**Files:**
- Modify: `resources/js/Pages/Profile/Edit.jsx`
- Modify: `app/Http/Controllers/ProfileController.php`

The `edit()` method currently doesn't pass `profile_photo_url` to the page at all (it wasn't needed before this feature) - it needs to, since `User`'s `$appends` only auto-includes it when the model itself is serialized, and `edit()` passes `'user' => $user` directly, so this should already work without changes... but verify this assumption explicitly rather than assuming, since `$user = $request->user()` combined with conditional `$user->load(...)` calls needs checking for whether `$appends` still applies after `->load()`.

- [ ] **Step 1: Verify `$appends` survives `Auth::user()->load(...)` calls used in `edit()`**

Run:
```bash
php artisan tinker --execute="
\$user = App\Models\User::first();
\$user->load('student');
echo array_key_exists('profile_photo_url', \$user->toArray()) ? 'profile_photo_url present after load(): yes' : 'MISSING - investigate before proceeding';
echo PHP_EOL;
"
```
Expected: `profile_photo_url present after load(): yes` - `$appends` is a model-level configuration that survives eager-loading additional relations, so no controller change should be needed. If this check fails, stop and re-investigate before continuing (do not guess a fix).

- [ ] **Step 2: Add the import to `Profile/Edit.jsx`**

Change:
```jsx
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
```
to:
```jsx
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import ProfilePhotoCard from '@/Components/ProfilePhotoCard'
```

- [ ] **Step 3: Replace the static initial-letter avatar card with the photo card**

Change:
```jsx
            <div className="space-y-6 lg:col-span-1">
              <Card>
                <CardContent className="flex flex-col items-center p-6 text-center">
                  <div className="flex h-20 w-20 items-center justify-center rounded-full bg-sidebar text-3xl font-bold text-sidebar-primary-foreground">
                    {user.name.charAt(0).toUpperCase()}
                  </div>
                  <p className="mt-3 text-sm font-medium text-foreground">{user.name}</p>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Informasi Akun</CardTitle>
                </CardHeader>
```
to:
```jsx
            <div className="space-y-6 lg:col-span-1">
              <ProfilePhotoCard profilePhotoUrl={user.profile_photo_url} />

              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Informasi Akun</CardTitle>
                </CardHeader>
```

The static initial-letter circle is fully replaced by `ProfilePhotoCard` (which itself falls back to showing nothing but the upload/camera buttons when `profilePhotoUrl` is null - there is no separate "initial letter fallback" inside `ProfilePhotoCard`, since the card's own empty state, per Task 5's implementation, is simply the capture/upload controls with no preview image shown until a photo exists).

- [ ] **Step 4: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 5: Manually verify for all three roles**

With `php artisan serve --port=8000` running and `public/hot` removed if present:
1. Log in as `admin@bakti.com` / `1`, visit `/profile/edit`. Confirm the photo card renders with "Ambil Foto" and "Upload Foto" buttons (no photo set yet, so no preview image shows).
2. Log in as `dede@baktitest.com` / `1` (supervisor), visit `/profile/edit`, confirm the same.
3. Log in as a student, visit `/profile/edit`, confirm the same.
4. For at least one role, test the upload path end-to-end: click "Upload Foto", select an image file with a clear face, confirm a preview appears, click "Simpan", confirm success (page reloads with a success flash, and the card now shows the saved photo as its idle-state preview).
5. Test the upload rejection path: select an image with no face in it (e.g. a photo of a wall/object), confirm the client-side alert "Wajah tidak terdeteksi..." fires and nothing is submitted.
6. Test the camera path still works exactly as it did on the old student-only card (open camera, capture, save with a real face succeeds).

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Profile/Edit.jsx
git commit -m "feat: Add profile photo management to the shared Profile page

Every role can now set/change their photo from one place (Profile/Edit.jsx),
replacing the static initial-letter avatar placeholder with the real
photo (or the capture/upload controls when none is set yet)."
```

---

## Task 8: Update `AttendanceController::checkIn()` to read the reference from `User`

**Files:**
- Modify: `app/Http/Controllers/AttendanceController.php`

- [ ] **Step 1: Change the reference descriptor source**

Change:
```php
            $student = $user->student;
            $faceResult = $faceService->verify($checkInDescriptor, $student?->face_descriptor, $user->id);
```
to:
```php
            $faceResult = $faceService->verify($checkInDescriptor, $user->face_descriptor, $user->id);
```

Note: `$student` is removed entirely here since it was only used for `$student?->face_descriptor` - grep the rest of `checkIn()` first to confirm no other line in this method reads `$student` (it shouldn't; the method reads `$user` throughout), then remove the now-unused variable rather than leaving dead code.

- [ ] **Step 2: Verify no syntax errors**

Run: `php -l app/Http/Controllers/AttendanceController.php`
Expected: `No syntax errors detected in app/Http/Controllers/AttendanceController.php`

- [ ] **Step 3: Manually verify check-in still reads the reference correctly**

1. Log in as the student whose profile photo was set in Task 7's manual verification, visit `/student/attendance`, do a check-in with the same face.
2. Run `php artisan tinker --execute="echo App\Models\Attendance::latest()->first()->face_verification_status;"` and confirm it prints `verified` (not `no_reference` - if it prints `no_reference`, the reference isn't being read from the right place, and this needs investigation before moving on, not a guessed fix).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/AttendanceController.php
git commit -m "fix: Read the face-verification reference from User, not Student

Matches Task 1's data relocation - $user->face_descriptor is now where
the reference photo's descriptor lives. No other change to the
verification logic itself."
```

---

## Task 9: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: PHP lint every touched/created backend file**

Run:
```bash
php -l database/migrations/2026_07_23_000003_move_face_reference_to_users_table.php
php -l app/Models/User.php
php -l app/Models/Student.php
php -l app/Http/Controllers/ProfileController.php
php -l app/Http/Controllers/Student/ProfileController.php
php -l app/Http/Controllers/AttendanceController.php
php -l routes/web.php
```
Expected: `No syntax errors detected in ...` for every file.

- [ ] **Step 2: Confirm routes are correct**

Run: `php artisan route:list --name=profile`
Expected: shows `profile.show`, `profile.edit`, `profile.update`, `profile.photo.update` - no `student.info.profile-photo.update` anywhere.

- [ ] **Step 3: Full frontend rebuild**

Run: `npm run build`
Expected: `✓ built in ...`, no errors. Check chunk output - `ProfilePhotoCard` (now imported by `Profile/Edit.jsx` rather than `Student/Info/Edit.jsx`) should still only pull face-api.js into chunks for pages that actually use it, not the main `app-*.js` bundle.

- [ ] **Step 4: Cross-role manual regression check**

Confirm none of the other fields on `Profile/Edit.jsx` (name, email, username, password, NIP for supervisors) broke - submit a normal profile update (not photo-related) for at least one role and confirm it still works exactly as before this change.

- [ ] **Step 5: Confirm `Student/Info/Edit.jsx` has zero photo-related code remaining**

Run: `grep -n "ProfilePhotoCard\|profile_photo\|face_descriptor" resources/js/Pages/Student/Info/Edit.jsx`
Expected: no output (zero matches).

- [ ] **Step 6: Commit (only if fixes were needed)**

If Steps 1-5 all pass cleanly with no code changes needed, there is nothing to commit here.

---

## Self-review notes (completed during plan authoring, not a task to run)

- **Spec coverage**: column relocation with data preservation (Task 1), model updates matching `User.php`'s actual `casts()`-method style rather than blindly copying `Student.php`'s older property style (Task 2), controller relocation (Task 3), route relocation + required ziggy regeneration (Task 4), shared component with both camera and upload modes funneling through identical validation (Task 5), removal from Student Info (Task 6), addition to Profile Edit (Task 7), check-in's reference source update (Task 8), full verification (Task 9) - every spec section has a corresponding task. The "reject on `!available` for profile photos, unlike check-in's tolerance" design decision from brainstorming is implemented in Task 5's `saveFromElement()`, applied identically to both capture modes.
- **Fixed during authoring**: initially planned to copy `Student.php`'s `protected $casts` property style directly onto `User.php`, then caught (by actually reading the current file) that `User.php` uses the newer `casts(): array` method form - Task 2 Step 1 now matches the file's actual existing style instead of introducing an inconsistent mix of both styles in the same file.
- **Fixed during authoring**: added an explicit verification step (Task 7 Step 1) for the assumption that `$appends` still applies after `Auth::user()->load(...)` is called in `edit()`, rather than assuming it silently works - if this check fails, the plan directs stopping to investigate rather than guessing a fix, consistent with this session's debugging approach.
- **Type/name consistency**: `profile_photo_url` (accessor name) is identical between Task 2 (definition) and Tasks 6-7 (both pages reading it off `student`/`user` respectively). `profile.photo.update` (route name) is identical between Task 4 (registration) and Task 5 (`route('profile.photo.update')` call in the new component). `face_descriptor`/`photo` (form field names) are identical between Task 3 (backend validation) and Task 5 (FormData keys) - unchanged from the pre-existing student-only implementation's field names, so no mismatch risk there.
- **No placeholders**: every step has complete, exact code - the full `ProfilePhotoCard.jsx` is written out in Task 5 rather than described, both migration directions (`up`/`down`) are complete rather than a stubbed `down()`.
