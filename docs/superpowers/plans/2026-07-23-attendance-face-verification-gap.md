# Attendance Face Verification Gap Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Require a profile photo (face reference) before any attendance action, add face verification to check-out (currently only check-in has it), and surface check-out face mismatches to admins/supervisors — without ever blocking a check-out on a mismatch.

**Architecture:** A new migration adds `check_out_face_verification_status`/`check_out_face_match_distance` columns to `attendances`, kept separate from the existing check-in-only columns so neither action's result overwrites the other's on the same row. `AttendanceController::checkIn()` and `checkOut()` both gain an early guard rejecting the request with 422 if `$user->face_descriptor` is null. `checkOut()` gains the same face-descriptor-decode-and-verify logic `checkIn()` already has, writing to the new columns and OR-ing `requires_manual_review` (never un-setting a check-in mismatch flag). On the frontend, `Student/Attendance/Index.jsx` shows a warning banner and hides both attendance buttons when the student has no profile photo, and passes `requireFaceCheck` to the check-out modal (already a generic prop `AttendanceModal.jsx` supports, just never passed on that call site before). Both admin/supervisor Suspicious pages get one more mismatch-note block, mirroring the existing check-in one.

**Tech Stack:** Laravel 12, Inertia.js, React 19, existing `FaceVerificationService` (unchanged), face-api.js (unchanged).

---

### Task 1: Add check-out face verification columns migration

**Files:**
- Create: `database/migrations/2026_07_23_000004_add_check_out_face_verification_to_attendances_table.php`

- [ ] **Step 1: Write the migration**

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
            $table->string('check_out_face_verification_status')->nullable();
            // verified, mismatch, no_reference, unavailable - null until check-out happens

            $table->float('check_out_face_match_distance')->nullable();
            // Euclidean distance between check-out and reference descriptors, when both exist
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_out_face_verification_status', 'check_out_face_match_distance']);
        });
    }
};
```

This mirrors the existing `2026_07_23_000002_add_face_verification_to_attendances_table.php` migration exactly, just for the check-out side, and with a distinct column pair so check-out never overwrites check-in's stored result on the same `attendances` row.

- [ ] **Step 2: Run the migration**

```bash
php artisan migrate
```
Expected: output shows the new migration ran successfully, no errors.

- [ ] **Step 3: Verify the columns exist**

```bash
php artisan tinker --execute="echo implode(', ', Schema::getColumnListing('attendances'));"
```
Expected: output includes `check_out_face_verification_status` and `check_out_face_match_distance`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_23_000004_add_check_out_face_verification_to_attendances_table.php
git commit -m "feat: Add check-out face verification columns to attendances table"
```

---

### Task 2: Require a profile photo before check-in or check-out

**Files:**
- Modify: `app/Http/Controllers/AttendanceController.php`

- [ ] **Step 1: Add the guard to checkIn()**

In `AttendanceController::checkIn()`, immediately after `$user = Auth::user();` (line 56), add:

```php
            $user = Auth::user();

            if (!$user->face_descriptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus mengupload foto profil terlebih dahulu sebelum melakukan presensi.'
                ], 422);
            }

            $today = Carbon::today();
```

(i.e. the guard sits between the existing `$user = Auth::user();` line and the existing `$today = Carbon::today();` line — `$today` was previously right after `$user`, now the guard sits between them.)

- [ ] **Step 2: Add the same guard to checkOut()**

In `AttendanceController::checkOut()`, immediately after `$user = Auth::user();` (currently line 186), add the identical guard:

```php
            $user = Auth::user();

            if (!$user->face_descriptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus mengupload foto profil terlebih dahulu sebelum melakukan presensi.'
                ], 422);
            }

            $today = Carbon::today();
```

- [ ] **Step 3: Verify syntax**

```bash
php -l app/Http/Controllers/AttendanceController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/AttendanceController.php
git commit -m "feat: Require a profile photo before check-in or check-out"
```

---

### Task 3: Add face verification to checkOut()

**Files:**
- Modify: `app/Http/Controllers/AttendanceController.php`

Depends on Task 1 (new columns must exist) and Task 2 (this task's edits land in the same method, after the guard added in Task 2).

- [ ] **Step 1: Accept face_descriptor in checkOut()'s validation**

In `checkOut()`, change:

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

- [ ] **Step 2: Decode the descriptor and run verification**

After the existing block:

```php
            if ($attendance->check_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-out hari ini!'
                ]);
            }
```

add:

```php
            $checkOutDescriptor = null;
            if ($request->filled('face_descriptor')) {
                $decoded = json_decode($request->input('face_descriptor'), true);
                if (is_array($decoded)) {
                    $checkOutDescriptor = $decoded;
                }
            }

            $faceService = new FaceVerificationService();
            $faceResult = $faceService->verify($checkOutDescriptor, $user->face_descriptor, $user->id);

            $requiresManualReview = $attendance->requires_manual_review || $faceResult['status'] === 'mismatch';
```

Note: `$attendance->requires_manual_review` is read here (before `$attendance->update()` runs later in the method) specifically so a check-in mismatch that already set it to `true` is never flipped back to `false` by check-out's own (possibly clean) result.

- [ ] **Step 3: Store the result in the new columns and updated review flag**

Change the existing `$updateData` array from:

```php
            $updateData = [
                'check_out' => Carbon::now(),
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_out_photo' => $photoPath,
            ];
```

to:

```php
            $updateData = [
                'check_out' => Carbon::now(),
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_out_photo' => $photoPath,
                'requires_manual_review' => $requiresManualReview,
                'check_out_face_verification_status' => $faceResult['status'],
                'check_out_face_match_distance' => $faceResult['distance'],
            ];
```

- [ ] **Step 4: Verify syntax**

```bash
php -l app/Http/Controllers/AttendanceController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 5: Manual verification**

Using `php artisan tinker`, confirm the full method reads correctly end-to-end (no automated test framework exists in this repo — this matches the verification approach used for the rest of the attendance feature):

```bash
php artisan tinker --execute="echo (new ReflectionMethod('App\\Http\\Controllers\\AttendanceController', 'checkOut'))->getName();"
```
Expected: prints `checkOut` (confirms the class/method still parses and is autoloadable after the edits).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/AttendanceController.php
git commit -m "feat: Run face verification on check-out, mirroring check-in"
```

---

### Task 4: Frontend guard — block attendance actions without a profile photo

**Files:**
- Modify: `resources/js/Pages/Student/Attendance/Index.jsx`

- [ ] **Step 1: Read the shared auth prop and add the banner**

In `resources/js/Pages/Student/Attendance/Index.jsx`, add `usePage` to the existing `@inertiajs/react` import and import `FlashBanner`:

```jsx
import { useEffect, useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { LogIn, LogOut, FileText, History, CheckCircle2, Circle, AlertTriangle, Clock } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import AttendanceModal from './AttendanceModal'
import ExceptionModal from './ExceptionModal'
```

- [ ] **Step 2: Read profilePhotoUrl inside the component**

In the `Index` component, right after the existing `const r = (name) => ...` line, add:

```jsx
  const { auth } = usePage().props
  const hasProfilePhoto = Boolean(auth?.user?.profile_photo_url)
```

- [ ] **Step 3: Render the banner and gate the CTA buttons**

Immediately after the `<PageHeader ... />` block (before the "Status Hari Ini" `<Card>`), add:

```jsx
        {!hasProfilePhoto && (
          <FlashBanner type="warning">
            Anda belum mengupload foto profil. Foto profil diperlukan untuk verifikasi wajah saat presensi.{' '}
            <Link href={r('profile.edit')} className="underline">Upload foto profil sekarang</Link>.
          </FlashBanner>
        )}
```

Then wrap the two existing CTA buttons so they only render when `hasProfilePhoto` is true. Change:

```jsx
                    cta={
                      <Button onClick={() => setCheckInOpen(true)} className="w-full">
                        <LogIn /> Check In Sekarang
                      </Button>
                    }
```

to:

```jsx
                    cta={
                      hasProfilePhoto ? (
                        <Button onClick={() => setCheckInOpen(true)} className="w-full">
                          <LogIn /> Check In Sekarang
                        </Button>
                      ) : (
                        <p className="text-center text-sm text-muted-foreground">Upload foto profil untuk dapat check-in</p>
                      )
                    }
```

And change:

```jsx
                    subtitle={todayAttendance.check_out && `Durasi: ${todayAttendance.working_hours ? Number(todayAttendance.working_hours).toFixed(1) : '0'} jam`}
                    cta={
                      todayAttendance.check_in ? (
                        <Button onClick={() => setCheckOutOpen(true)} variant="destructive" className="w-full">
                          <LogOut /> Check Out Sekarang
                        </Button>
                      ) : (
                        <p className="text-center text-sm text-muted-foreground">Check in terlebih dahulu</p>
                      )
                    }
```

to:

```jsx
                    subtitle={todayAttendance.check_out && `Durasi: ${todayAttendance.working_hours ? Number(todayAttendance.working_hours).toFixed(1) : '0'} jam`}
                    cta={
                      !hasProfilePhoto ? (
                        <p className="text-center text-sm text-muted-foreground">Upload foto profil untuk dapat check-out</p>
                      ) : todayAttendance.check_in ? (
                        <Button onClick={() => setCheckOutOpen(true)} variant="destructive" className="w-full">
                          <LogOut /> Check Out Sekarang
                        </Button>
                      ) : (
                        <p className="text-center text-sm text-muted-foreground">Check in terlebih dahulu</p>
                      )
                    }
```

Finally, the `EmptyState`'s own "Check In Sekarang" action button (rendered when there's no `todayAttendance` row at all yet) needs the same gate. Change:

```jsx
              <EmptyState
                icon={AlertTriangle}
                title="Belum Absen Hari Ini"
                description="Silakan lakukan check-in untuk memulai absensi."
                action={
                  <Button onClick={() => setCheckInOpen(true)}>
                    <LogIn /> Check In Sekarang
                  </Button>
                }
              />
```

to:

```jsx
              <EmptyState
                icon={AlertTriangle}
                title="Belum Absen Hari Ini"
                description="Silakan lakukan check-in untuk memulai absensi."
                action={
                  hasProfilePhoto ? (
                    <Button onClick={() => setCheckInOpen(true)}>
                      <LogIn /> Check In Sekarang
                    </Button>
                  ) : (
                    <Button asChild variant="outline">
                      <Link href={r('profile.edit')}>Upload Foto Profil</Link>
                    </Button>
                  )
                }
              />
```

- [ ] **Step 4: Add requireFaceCheck to the check-out modal**

Change:

```jsx
      <AttendanceModal
        open={checkOutOpen}
        onClose={() => setCheckOutOpen(false)}
        title="Check Out"
        subtitle="Lakukan absensi keluar"
        destructive
        notesPlaceholder={{ optional: false, text: 'Ringkasan kegiatan yang telah dikerjakan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-out'), payload)}
      />
```

to:

```jsx
      <AttendanceModal
        open={checkOutOpen}
        onClose={() => setCheckOutOpen(false)}
        title="Check Out"
        subtitle="Lakukan absensi keluar"
        destructive
        notesPlaceholder={{ optional: false, text: 'Ringkasan kegiatan yang telah dikerjakan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-out'), payload)}
        requireFaceCheck
      />
```

No changes are needed inside `AttendanceModal.jsx` itself — it already reads `requireFaceCheck` as a plain prop (defaulting to `false`) and already gates its "reject if no face detected" logic and calls `face.ensureModelsLoaded()` on that prop; the check-in modal already demonstrates this working.

- [ ] **Step 5: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Student/Attendance/Index.jsx
git commit -m "feat: Block attendance actions until a profile photo is uploaded, require face check on check-out"
```

---

### Task 5: Surface check-out mismatches on the admin Suspicious page

**Files:**
- Modify: `resources/js/Pages/Admin/Attendance/Suspicious.jsx`

- [ ] **Step 1: Add the check-out mismatch note**

In `resources/js/Pages/Admin/Attendance/Suspicious.jsx`, immediately after the existing block:

```jsx
                        {attendance.face_verification_status === 'mismatch' && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            Wajah tidak cocok (jarak: {Number(attendance.face_match_distance).toFixed(2)})
                          </p>
                        )}
```

add:

```jsx
                        {attendance.check_out_face_verification_status === 'mismatch' && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            Wajah check-out tidak cocok (jarak: {Number(attendance.check_out_face_match_distance).toFixed(2)})
                          </p>
                        )}
```

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Admin/Attendance/Suspicious.jsx
git commit -m "feat: Show check-out face mismatches on the admin Suspicious page"
```

---

### Task 6: Surface check-out mismatches on the supervisor Suspicious page

**Files:**
- Modify: `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`

- [ ] **Step 1: Add the identical check-out mismatch note**

In `resources/js/Pages/Supervisor/Attendance/Suspicious.jsx`, apply the exact same change as Task 5 Step 1 (this file has an identical structure at the same location):

```jsx
                        {attendance.check_out_face_verification_status === 'mismatch' && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <UserRound className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            Wajah check-out tidak cocok (jarak: {Number(attendance.check_out_face_match_distance).toFixed(2)})
                          </p>
                        )}
```

placed right after the existing check-in mismatch block, same as the admin page.

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Supervisor/Attendance/Suspicious.jsx
git commit -m "feat: Show check-out face mismatches on the supervisor Suspicious page"
```

---

### Task 7: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax/build check every touched file**

```bash
php -l app/Http/Controllers/AttendanceController.php
npm run build
```
Expected: both succeed with no errors.

- [ ] **Step 2: Confirm migration applied and columns present**

```bash
php artisan tinker --execute="echo implode(', ', Schema::getColumnListing('attendances'));"
```
Expected: includes both `face_verification_status`/`face_match_distance` (check-in, pre-existing) and `check_out_face_verification_status`/`check_out_face_match_distance` (new).

- [ ] **Step 3: Manual browser check — blocked without a profile photo**

Using a student account with no `profile_photo` set (or temporarily clear one via `php artisan tinker`: `App\Models\User::find(<id>)->update(['profile_photo' => null, 'face_descriptor' => null]);`):
- Visit `/student/attendance`.
- Confirm the warning banner appears with a working link to `/profile/edit`.
- Confirm neither the check-in nor check-out button is clickable/visible as an actionable button (replaced by the "Upload foto profil..." text).
- Using a tool like `curl` or the browser devtools network tab, confirm a direct POST to `/student/attendance/check-in` without a valid `face_descriptor` reference on the account returns HTTP 422 with the Indonesian message.

- [ ] **Step 4: Manual browser check — normal flow with a profile photo**

Using a student account with a profile photo already saved:
- Confirm the banner is gone and both buttons render normally.
- Complete a check-in successfully (existing behavior, unchanged).
- Complete a check-out: confirm the face-guide overlay/oval appears (from the earlier attendance-camera-and-profile-crop feature), confirm submitting a photo with no face visible is rejected client-side with an alert (same UX as check-in), and confirm a normal check-out with a matching face completes successfully.

- [ ] **Step 5: Manual browser check — check-out mismatch flagging**

This requires presenting a different face at check-out than the student's reference photo (e.g., a colleague's face, or a photo of another person on-screen). Confirm:
- Check-out still completes successfully (never blocked).
- The resulting attendance row has `check_out_face_verification_status = 'mismatch'` and `requires_manual_review = true` (verify via `php artisan tinker`: `App\Models\Attendance::latest()->first(['check_out_face_verification_status', 'check_out_face_match_distance', 'requires_manual_review'])`).
- The row appears on `/admin/attendance/suspicious` and `/supervisor/attendance/suspicious` (for that student's supervisor) with the new "Wajah check-out tidak cocok" note visible.

- [ ] **Step 6: Commit any fixes found during manual verification**

Only if Steps 3-5 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
