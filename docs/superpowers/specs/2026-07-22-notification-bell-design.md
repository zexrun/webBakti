# Notification Bell Dropdown — Design Spec

## Background

A full notifications page already exists (`resources/js/Pages/Notifications/Index.jsx`, routes `notifications.index`/`.mark-as-read`/`.mark-all-as-read`/`.delete`/`.delete-all`, backed by Laravel's built-in database-notifications system via `App\Http\Controllers\NotificationController`). It works well but is only reachable by fully navigating to `/notifications` — there is no indicator anywhere else in the app that unread notifications exist. `resources/js/Layouts/AppShell.jsx` (the shared authenticated shell used by all three roles) has a top bar with a theme toggle and a profile avatar link, but nothing notification-related.

Every notification's `data` payload (set by each class in `app/Notifications/*.php`) consistently includes `type` and `message`; `action_url` is present on most classes (`NewTaskAssigned`, `SubmissionGraded`, `TaskDeadlineReminder`, `AttendanceApprovalNotification`, `ExceptionApprovalNotification`, `BulkTaskAssignedNotification`, `TaskSubmitted`) but is absent from `BulkNotification`. This must be treated as optional.

## Goal

Every authenticated page shows a bell icon in the top bar with an unread-count badge. Clicking it opens a dropdown with the 5 most recent notifications; clicking a notification marks it read and navigates to its `action_url` (or `/notifications` if none). The badge count updates automatically as the user navigates between pages, and also refreshes on a 30-second background poll so it stays current even if the user stays on one page for a while.

## Non-goals

- No changes to the existing `/notifications` full-page UI or its mark-as-read/delete routes.
- No changes to how notifications are created/sent (`Notification::send(...)` calls throughout the app stay as-is).
- No real-time push (WebSockets/Pusher/Echo) — the 30-second poll is a plain interval-based HTTP request, not a persistent connection.
- No notification preferences/settings UI.

## Architecture

**Backend — shared prop** (`app/Http/Middleware/HandleInertiaRequests.php`): add `'unreadNotificationsCount' => fn () => $request->user()?->unreadNotifications()->count() ?? 0` to the array returned by `share()`. This makes the count available as `usePage().props.unreadNotificationsCount` on every Inertia page with zero extra requests — it updates automatically on every normal Inertia navigation (visiting any page re-evaluates shared props).

**Backend — recent-list endpoint** (`app/Http/Controllers/NotificationController.php`): add a `recent()` method returning plain JSON (not an Inertia response, since this is fetched via `axios` for the dropdown and the 30-second poll, not a page visit):
```php
public function recent()
{
    $user = Auth::user();

    return response()->json([
        'notifications' => $user->notifications()->latest()->limit(5)->get(),
        'unread_count' => $user->unreadNotifications()->count(),
    ]);
}
```
New route: `GET /notifications/recent` → `notifications.recent`, registered inside the same authenticated middleware group as the existing notification routes (so it inherits `auth` protection — no new middleware needed).

**Frontend — new component** (`resources/js/Components/NotificationBell.jsx`):
- Reads `unreadNotificationsCount` from `usePage().props` for the badge's *initial* value (so the badge is correct on first paint with zero extra requests) and keeps its own local `count` state that's overwritten whenever a poll or dropdown-open fetch completes.
- Badge: a small red circle with the count, capped display at `"9+"` when count > 9; badge is hidden entirely when count is 0.
- Bell icon click toggles a dropdown (click-outside-to-close, same interaction pattern as other dropdowns/modals in the app).
- On open, fetches `GET /notifications/recent` via `window.axios` and renders the 5 items (reusing the same icon-per-type mapping logic already in `Notifications/Index.jsx` — extracted into a small shared helper so both places stay in sync, see below).
- A `setInterval` polls the same endpoint every 30 seconds for as long as the component is mounted (i.e., for the whole authenticated session), updating just the badge count — not re-rendering the dropdown's item list unless the dropdown happens to be open, in which case the open list also refreshes. Interval is cleared on unmount via the `useEffect` cleanup function.
- Each dropdown item, on click: fires `window.axios.post('/notifications/{id}/mark-as-read')` (fire-and-forget, not awaited — the navigation below doesn't need to wait for it) and calls `router.visit(item.data.action_url ?? '/notifications')`.
- Footer link: "Lihat Semua" → `Link href={route('notifications.index')}`.
- Empty state: small centered message ("Tidak ada notifikasi") when the fetched list is empty.

**Shared icon-per-type logic**: `Notifications/Index.jsx` currently has local `iconFor(type)` + `iconTones` + a `NotificationIcon` component (lines 13-40). Extract these into `resources/js/Components/NotificationIcon.jsx` (props: `type`) so `NotificationBell` and the full `Notifications/Index.jsx` page render identical icons/colors for the same notification types, without duplicating the mapping table in two places.

**Wiring**: `resources/js/Layouts/AppShell.jsx`'s `<header>` gets `<NotificationBell />` inserted right before the existing `<ThemeToggle />` (both are simple icon-button-style controls, so this matches the existing visual rhythm of the top bar).

## Data flow / error handling

- If `GET /notifications/recent` fails (network error, 401 after session expiry, etc.), the dropdown shows its empty state rather than crashing — the fetch is wrapped in a `.catch()` that just leaves the list empty; the badge count simply stops updating until the next successful poll (no error toast, since this is a low-stakes background refresh, consistent with how the app has no global error-toast system for background requests elsewhere).
- `action_url` may be absent (`BulkNotification`) — falls back to `/notifications`.
- The mark-as-read POST on click is fire-and-forget; if it fails, the item will simply still show as unread next time the dropdown or full page is opened — same as any other transient network failure, no special handling needed since the existing full-page `markAsRead` has no error handling either (consistent with existing conventions).
- Unmounting `NotificationBell` (e.g., during logout/page teardown) clears the poll interval — no orphaned timers.

## Testing

No automated test suite exists in this project (established convention). Verification is manual:
- `php -l` on the modified middleware and controller.
- `php artisan route:list --name=notifications` to confirm the new route registers correctly.
- `npm run build`.
- Manual: log in as any role, confirm the bell renders in the top bar with a correct initial badge count (compare against the full `/notifications` page's unread items), open the dropdown and confirm the 5 most recent items render with correct icons/messages, click one and confirm it marks read + navigates, confirm the badge decrements after a page navigation (shared prop) and after 30 seconds (poll) without a page navigation, and confirm dark mode / mobile width don't break the dropdown's layout.

## Open items resolved during brainstorming

1. Dropdown shows 5 most recent notifications + a "Lihat Semua" link to the full page.
2. Badge count updates via Inertia shared prop on navigation AND via a 30-second background poll (not navigation-only).
3. Clicking a notification marks it read and navigates directly to its `action_url` (fallback to `/notifications` if absent).
4. Badge shows the actual unread count as a number, capped at "9+" for readability.
