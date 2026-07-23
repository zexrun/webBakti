# Chat Authorization — Design

## Goal

Restrict who a user can start or continue a conversation with in the Messages feature, based on role and supervisor relationship:

- **Admin** can message any user.
- **Supervisor** can message the Admin and every student assigned to them (`supervisor_id`).
- **Student** can message the Admin, their own supervisor, and every other student who shares the same supervisor. A student with no supervisor assigned yet can only message the Admin.

Currently there is no restriction at all: `MessageController::create()` lists every user regardless of role, and `store()`/`reply()` perform no relationship check beyond "does this message thread include me."

## Non-goals

- No database schema change — this is purely an authorization layer over the existing `Student.supervisor_id` relationship.
- No change to the Message model, read/unread tracking, or the Inbox/Sent list views themselves — only who can be chosen as a recipient and who a `store()`/`reply()` call is allowed to target.
- No retroactive deletion of existing messages that would now be disallowed under the new rule (e.g., an old thread with a former supervisor) — see reply handling below for how those are treated going forward.

## 1. New `ChatAuthorizationService`

**File:** `app/Services/ChatAuthorizationService.php`

Centralizes the "who can message whom" rule in one place, used both to filter the recipient picker and to validate on send/reply, so the two can never drift out of sync.

Two methods:
- `canMessage(User $sender, User $recipient): bool` — true/false for a specific pair.
- `allowedRecipients(User $sender): Collection` — the full list of `User` models `$sender` is allowed to message (excluding themselves), used to populate `Messages/Create.jsx`'s recipient picker.

Rules (mirrored in both methods so the single-pair check and the bulk list always agree):
- Sender is `admin` → every other user.
- Sender is `supervisor` → the Admin(s), plus every `User` whose `Student.supervisor_id` equals the sender's own `Supervisor.id`.
- Sender is `student` with a `supervisor_id` → the Admin(s), their supervisor's `User`, plus every other student sharing that same `supervisor_id`.
- Sender is `student` with no `supervisor_id` → the Admin(s) only.

"The Admin(s)" is plural-safe (queries `role = 'admin'`) even though today there's typically one admin user, since nothing in the rule assumes exactly one.

## 2. MessageController changes

**File:** `app/Http/Controllers/MessageController.php`

- `create()`: replace the current `User::whereIn('role', [...])` query with `ChatAuthorizationService::allowedRecipients(Auth::user())`, so the picker only ever shows valid choices.
- `store()`: after validating the request shape (existing rules unchanged), call `canMessage()` against the resolved recipient; if false, fail validation with a clear Indonesian message rather than silently creating the message.
- `reply()`: currently only checks that the authenticated user is the sender or recipient of the original message. Add a `canMessage()` check against the other party (whichever of sender/recipient isn't the current user) — since relationships can change over time (e.g., reassigned supervisor), a reply is only allowed if the relationship is *still* valid at reply time, not just historically valid when the thread started. If the check fails, the reply is rejected with a message explaining the conversation is no longer available (e.g., the other party is no longer their supervisor/peer).

## Error handling

- `store()`'s new check surfaces as a validation error on the existing form (same pattern as `recipient_id.different` today), e.g. "Anda tidak dapat mengirim pesan ke pengguna ini."
- `reply()`'s new check redirects back with a flash error, consistent with how `reply()` already handles its existing authorization check (`abort(403)` today for out-of-thread access remains for that case; the new relationship check uses a redirect+flash since it's a business-rule rejection, not an access-control abort).

## Testing approach

No automated test framework exists in this repo (consistent with every other feature this session). Verification: manual checks per role — as each of admin/supervisor/student(with supervisor)/student(without supervisor), confirm the recipient picker in `Messages/Create.jsx` shows exactly the expected set of users, and confirm both a fresh `store()` and a `reply()` to an old thread are accepted/rejected as expected.
