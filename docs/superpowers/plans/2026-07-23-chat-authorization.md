# Chat Authorization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restrict who a user can message based on role and supervisor relationship (admin↔everyone, supervisor↔admin+own students, student↔admin+own supervisor+peer students under the same supervisor), applied consistently to the recipient picker, sending a new message, and replying to an existing thread.

**Architecture:** A new `ChatAuthorizationService` centralizes the rule as `canMessage(User, User): bool` and `allowedRecipients(User): Collection`. `MessageController` calls it in three places: `create()` (populate the picker), `store()` (validate before creating), `reply()` (re-validate the relationship still holds, since it can change over time).

**Tech Stack:** Laravel 12 Eloquent, no new dependencies.

---

### Task 1: Create ChatAuthorizationService

**Files:**
- Create: `app/Services/ChatAuthorizationService.php`

- [ ] **Step 1: Write the service**

```php
<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Centralizes "who can message whom" so the recipient picker
 * (allowedRecipients) and the send/reply validation (canMessage) can
 * never drift out of sync - both derive from the same per-role rule:
 *
 *   - admin: everyone else.
 *   - supervisor: the admin(s), plus every student assigned to them.
 *   - student (has a supervisor): the admin(s), their own supervisor,
 *     plus every other student sharing that same supervisor.
 *   - student (no supervisor yet): the admin(s) only.
 */
class ChatAuthorizationService
{
    public function canMessage(User $sender, User $recipient): bool
    {
        return $this->allowedRecipients($sender)->contains('id', $recipient->id);
    }

    public function allowedRecipients(User $sender): Collection
    {
        if ($sender->role === 'admin') {
            return User::where('id', '!=', $sender->id)->get();
        }

        if ($sender->role === 'supervisor') {
            $supervisor = $sender->supervisor;

            if (!$supervisor) {
                return $this->admins($sender);
            }

            $studentUserIds = Student::where('supervisor_id', $supervisor->id)
                ->pluck('user_id');

            return $this->admins($sender)
                ->merge(User::whereIn('id', $studentUserIds)->get());
        }

        if ($sender->role === 'student') {
            $student = $sender->student;

            if (!$student || !$student->supervisor_id) {
                return $this->admins($sender);
            }

            $supervisorUserId = $student->supervisor->user_id ?? null;

            $peerUserIds = Student::where('supervisor_id', $student->supervisor_id)
                ->where('id', '!=', $student->id)
                ->pluck('user_id');

            $recipients = $this->admins($sender)
                ->merge(User::whereIn('id', $peerUserIds)->get());

            if ($supervisorUserId) {
                $recipients = $recipients->merge(User::where('id', $supervisorUserId)->get());
            }

            return $recipients->unique('id')->values();
        }

        return collect();
    }

    private function admins(User $excluding): Collection
    {
        return User::where('role', 'admin')
            ->where('id', '!=', $excluding->id)
            ->get();
    }
}
```

- [ ] **Step 2: Verify syntax**

```bash
php -l app/Services/ChatAuthorizationService.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Manual verification via tinker**

```bash
php artisan tinker --execute="
\$service = new App\Services\ChatAuthorizationService();
\$admin = App\Models\User::where('role', 'admin')->first();
\$student = App\Models\User::whereHas('student')->first();
echo 'Admin can message student: ' . (\$service->canMessage(\$admin, \$student) ? 'yes' : 'no') . PHP_EOL;
echo 'Student allowed recipients: ' . \$service->allowedRecipients(\$student)->pluck('name')->implode(', ') . PHP_EOL;
"
```
Expected: `Admin can message student: yes`, and the student's allowed recipients list includes at least the admin (and their supervisor/peers if assigned).

- [ ] **Step 4: Commit**

```bash
git add app/Services/ChatAuthorizationService.php
git commit -m "feat: Add ChatAuthorizationService for role-based messaging rules"
```

---

### Task 2: Wire ChatAuthorizationService into MessageController

**Files:**
- Modify: `app/Http/Controllers/MessageController.php`

- [ ] **Step 1: Add the import and inject the service**

At the top of the file, add:
```php
use App\Services\ChatAuthorizationService;
```

Change the class to hold the service via constructor injection:
```php
class MessageController extends Controller
{
    public function __construct(private ChatAuthorizationService $chatAuth)
    {
    }

    // ...existing methods follow, unchanged except where noted below
```

- [ ] **Step 2: Filter the recipient picker in create()**

Change:
```php
    public function create(): Response
    {
        $recipients = User::whereIn('role', ['supervisor', 'student', 'admin'])
            ->where('id', '!=', Auth::id())
            ->get();

        return Inertia::render('Messages/Create', compact('recipients'));
    }
```
to:
```php
    public function create(): Response
    {
        $recipients = $this->chatAuth->allowedRecipients(Auth::user());

        return Inertia::render('Messages/Create', compact('recipients'));
    }
```

- [ ] **Step 3: Validate the recipient in store()**

Change:
```php
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:sender_id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ], [
            'recipient_id.different' => 'Tidak bisa mengirim pesan ke diri sendiri',
        ]);

        Message::create([
```
to:
```php
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:sender_id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ], [
            'recipient_id.different' => 'Tidak bisa mengirim pesan ke diri sendiri',
        ]);

        $recipient = User::findOrFail($request->recipient_id);

        if (!$this->chatAuth->canMessage(Auth::user(), $recipient)) {
            return back()->withErrors([
                'recipient_id' => 'Anda tidak dapat mengirim pesan ke pengguna ini.',
            ])->withInput();
        }

        Message::create([
```

- [ ] **Step 4: Re-validate the relationship in reply()**

Change:
```php
    public function reply(Request $request, Message $message)
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $senderId = Auth::id();
        $recipientId = $senderId === $message->sender_id ? $message->recipient_id : $message->sender_id;

        Message::create([
```
to:
```php
    public function reply(Request $request, Message $message)
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $senderId = Auth::id();
        $recipientId = $senderId === $message->sender_id ? $message->recipient_id : $message->sender_id;

        $recipient = User::findOrFail($recipientId);

        if (!$this->chatAuth->canMessage(Auth::user(), $recipient)) {
            return back()->with('error', 'Percakapan ini tidak lagi tersedia karena hubungan Anda dengan pengguna ini telah berubah.');
        }

        Message::create([
```

- [ ] **Step 5: Verify syntax**

```bash
php -l app/Http/Controllers/MessageController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/MessageController.php
git commit -m "feat: Enforce chat authorization on create/store/reply"
```

---

### Task 3: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax check**

```bash
php -l app/Services/ChatAuthorizationService.php
php -l app/Http/Controllers/MessageController.php
```
Expected: `No syntax errors detected` for both.

- [ ] **Step 2: Manual browser check — recipient picker per role**

Log in as each of admin/supervisor/student(with supervisor)/student(without supervisor, if one exists in seeded data) and visit `/messages/create` (or equivalent route). Confirm the recipient dropdown shows exactly the expected set:
- Admin: every other user.
- Supervisor: the admin(s) + their own students only, no other supervisors' students.
- Student with supervisor: the admin(s) + their supervisor + peer students under the same supervisor, no unrelated supervisors/students.
- Student without supervisor: the admin(s) only.

- [ ] **Step 3: Manual browser check — store() rejection**

As a student, attempt to send a message directly to a supervisor who is NOT their own (e.g. by manipulating the request or testing via an account temporarily reassigned) and confirm the request is rejected with the validation message, not silently created.

- [ ] **Step 4: Manual browser check — reply() re-validation**

Find or create a message thread, then (if feasible in the seeded data) change the student's `supervisor_id` to a different supervisor and confirm replying to the old thread is now rejected with the flash error, while replying to a still-valid thread (e.g. with the admin) still works.

- [ ] **Step 5: Commit any fixes found during manual verification**

Only if Steps 2-4 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
