# Notification Bell Dropdown Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a bell icon with an unread-count badge to the shared app top bar; clicking it opens a dropdown of the 5 most recent notifications that marks-read-and-navigates on click, with the badge updating on navigation and via a 30-second background poll.

**Architecture:** One new Inertia shared prop (`unreadNotificationsCount`) for the badge's initial/navigation-driven value, one new plain-JSON controller endpoint (`/notifications/recent`) for the dropdown's list and the poll, one new shared `NotificationIcon` component (extracted from the existing full notifications page so both stay visually consistent), and one new `NotificationBell` component wired into `AppShell`.

**Tech Stack:** Laravel 12 (Eloquent database notifications, already in use), React 19 + Inertia.js, `window.axios` (already globally configured, used elsewhere for `mark-as-read`), Tailwind v3, lucide-react.

---

## Task 1: Add `unreadNotificationsCount` shared prop

**Files:**
- Modify: `app/Http/Middleware/HandleInertiaRequests.php`

- [ ] **Step 1: Add the shared prop**

In `app/Http/Middleware/HandleInertiaRequests.php`, change the `share()` method from:
```php
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'danger' => fn () => $request->session()->get('danger'),
                'import_errors' => fn () => $request->session()->get('import_errors'),
                'success_position' => fn () => $request->session()->get('success_position'),
                'success_university' => fn () => $request->session()->get('success_university'),
            ],
        ];
    }
```
to:
```php
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'unreadNotificationsCount' => fn () => $request->user()?->unreadNotifications()->count() ?? 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'danger' => fn () => $request->session()->get('danger'),
                'import_errors' => fn () => $request->session()->get('import_errors'),
                'success_position' => fn () => $request->session()->get('success_position'),
                'success_university' => fn () => $request->session()->get('success_university'),
            ],
        ];
    }
```

- [ ] **Step 2: Verify the file has no syntax errors**

Run: `php -l app/Http/Middleware/HandleInertiaRequests.php`
Expected output: `No syntax errors detected in app/Http/Middleware/HandleInertiaRequests.php`

- [ ] **Step 3: Verify the prop is actually shared on a real request**

Run: `php artisan tinker --execute="
\$user = App\Models\User::first();
echo 'user: ' . \$user->name . PHP_EOL;
echo 'unread count: ' . \$user->unreadNotifications()->count() . PHP_EOL;
"`
Expected: prints a user name and a numeric count (0 or more) with no error — confirms the `unreadNotifications()` relation call used in the closure is valid on a real `User` model instance.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Middleware/HandleInertiaRequests.php
git commit -m "feat: Share unreadNotificationsCount on every Inertia response

Lets the upcoming NotificationBell component show a correct badge on
first paint and update it automatically on every page navigation,
with no extra request beyond the normal page load."
```

---

## Task 2: Add the `/notifications/recent` JSON endpoint

**Files:**
- Modify: `app/Http/Controllers/NotificationController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add the `recent()` method**

In `app/Http/Controllers/NotificationController.php`, add a new method. The full file becomes:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function recent()
    {
        $user = Auth::user();

        return response()->json([
            'notifications' => $user->notifications()->latest()->limit(5)->get(),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
        }

        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        Auth::user()->notifications()->delete();

        return redirect()->back()->with('success', 'Semua notifikasi telah dihapus');
    }
}
```

(Only the new `recent()` method, inserted right after `index()`, is new — every other method is unchanged from the current file.)

- [ ] **Step 2: Register the route**

In `routes/web.php`, add the new route right after the existing `notifications.index` line (line 60), inside the same `Route::middleware('auth')->group(...)` block:

Change:
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
```
to:
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
```

- [ ] **Step 3: Verify no syntax errors**

Run:
```bash
php -l app/Http/Controllers/NotificationController.php
php -l routes/web.php
```
Expected: `No syntax errors detected in ...` for both files.

- [ ] **Step 4: Verify the route registers correctly**

Run: `php artisan route:list --name=notifications.recent`
Expected output: one row, `GET|HEAD notifications/recent ... notifications.recent › NotificationController@recent`.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/NotificationController.php routes/web.php
git commit -m "feat: Add /notifications/recent JSON endpoint

Returns the 5 most recent notifications plus the unread count, as
plain JSON (not an Inertia response) - consumed by the new
NotificationBell dropdown and its 30-second background poll."
```

---

## Task 3: Extract `NotificationIcon` into a shared component

**Files:**
- Create: `resources/js/Components/NotificationIcon.jsx`
- Modify: `resources/js/Pages/Notifications/Index.jsx`

- [ ] **Step 1: Create the shared component**

Create `resources/js/Components/NotificationIcon.jsx` with the icon-mapping logic currently local to `Notifications/Index.jsx`:

```jsx
import { ClipboardList, CheckCircle2, Clock, ShieldCheck, ShieldX, Bell } from 'lucide-react'
import { cn } from '@/lib/utils'

const iconTones = {
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  amber: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
  neutral: 'bg-muted text-muted-foreground',
}

// Notification type → semantic icon + tone (§7 category colors).
function iconFor(type) {
  if (type === 'new_task') return { Icon: ClipboardList, tone: 'blue' }
  if (type === 'submission_graded') return { Icon: CheckCircle2, tone: 'green' }
  if (type?.startsWith('task_deadline')) return { Icon: Clock, tone: 'amber' }
  if (type?.startsWith('attendance_') || type?.startsWith('exception_')) {
    return type.endsWith('approved')
      ? { Icon: ShieldCheck, tone: 'green' }
      : { Icon: ShieldX, tone: 'red' }
  }
  return { Icon: Bell, tone: 'neutral' }
}

/**
 * Shared notification-type → icon/color mapping, used by both the full
 * Notifications/Index.jsx page and the NotificationBell dropdown so
 * they render identically for the same notification types.
 */
export default function NotificationIcon({ type, className }) {
  const { Icon, tone } = iconFor(type)
  return (
    <div className={cn('flex h-10 w-10 shrink-0 items-center justify-center rounded-full', iconTones[tone], className)}>
      <Icon className="h-5 w-5" />
    </div>
  )
}
```

Note the added optional `className` prop (merged via `cn`) — this lets the dropdown use a smaller size (e.g. `h-8 w-8`) than the full page's `h-10 w-10` default, without needing a second component.

- [ ] **Step 2: Update `Notifications/Index.jsx` to use the shared component**

In `resources/js/Pages/Notifications/Index.jsx`, remove the local `iconTones`, `iconFor`, and `NotificationIcon` definitions (lines 13-41) and import the shared one instead.

Change the imports at the top from:
```jsx
import { useState } from 'react'
import { router } from '@inertiajs/react'
import { ClipboardList, CheckCircle2, Clock, ShieldCheck, ShieldX, Bell, X, BellOff, Trash2 } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

const iconTones = {
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  amber: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
  neutral: 'bg-muted text-muted-foreground',
}

// Notification type → semantic icon + tone (§7 category colors).
function iconFor(type) {
  if (type === 'new_task') return { Icon: ClipboardList, tone: 'blue' }
  if (type === 'submission_graded') return { Icon: CheckCircle2, tone: 'green' }
  if (type?.startsWith('task_deadline')) return { Icon: Clock, tone: 'amber' }
  if (type?.startsWith('attendance_') || type?.startsWith('exception_')) {
    return type.endsWith('approved')
      ? { Icon: ShieldCheck, tone: 'green' }
      : { Icon: ShieldX, tone: 'red' }
  }
  return { Icon: Bell, tone: 'neutral' }
}

function NotificationIcon({ type }) {
  const { Icon, tone } = iconFor(type)
  return (
    <div className={cn('flex h-10 w-10 shrink-0 items-center justify-center rounded-full', iconTones[tone])}>
      <Icon className="h-5 w-5" />
    </div>
  )
}
```
to:
```jsx
import { useState } from 'react'
import { router } from '@inertiajs/react'
import { X, BellOff, Trash2 } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import NotificationIcon from '@/Components/NotificationIcon'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'
```

The rest of the file (from `const priorityVariant = ...` onward) stays exactly the same — `<NotificationIcon type={notification.data.type} />` at line 94 keeps working unchanged since the extracted component has the same name and the same required prop.

- [ ] **Step 3: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no import errors.

- [ ] **Step 4: Manually verify the full notifications page still renders identically**

Start `php artisan serve --port=8000` if not already running, `rm -f public/hot`. Log in as any user, visit `/notifications`. Confirm the page looks exactly as before (same icons, same colors) — this is a pure refactor with no visible change.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Components/NotificationIcon.jsx resources/js/Pages/Notifications/Index.jsx
git commit -m "refactor: Extract NotificationIcon to a shared component

Was local to Notifications/Index.jsx. The upcoming NotificationBell
dropdown needs the same type-to-icon/color mapping, so this pulls it
out once rather than duplicating the table. No visual change to the
existing full notifications page."
```

---

## Task 4: Build the `NotificationBell` component

**Files:**
- Create: `resources/js/Components/NotificationBell.jsx`

- [ ] **Step 1: Create the component**

Create `resources/js/Components/NotificationBell.jsx`:

```jsx
import { useEffect, useRef, useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { Bell } from 'lucide-react'
import NotificationIcon from '@/Components/NotificationIcon'
import { cn } from '@/lib/utils'

const POLL_INTERVAL_MS = 30000

function relativeTime(value) {
  const diffMs = Date.now() - new Date(value).getTime()
  const diffMinutes = Math.round(diffMs / 60000)
  if (diffMinutes < 1) return 'Baru saja'
  if (diffMinutes < 60) return `${diffMinutes} menit lalu`
  const diffHours = Math.round(diffMinutes / 60)
  if (diffHours < 24) return `${diffHours} jam lalu`
  const diffDays = Math.round(diffHours / 24)
  return `${diffDays} hari lalu`
}

/**
 * Top-bar bell icon + unread badge + dropdown of the 5 most recent
 * notifications. Badge count starts from the unreadNotificationsCount
 * Inertia shared prop (so it's correct on first paint / after any page
 * navigation with zero extra requests) and is kept fresh afterwards by
 * a 30-second background poll of /notifications/recent, which also
 * supplies the dropdown's item list when opened.
 */
export default function NotificationBell() {
  const { unreadNotificationsCount } = usePage().props
  const [open, setOpen] = useState(false)
  const [count, setCount] = useState(unreadNotificationsCount ?? 0)
  const [items, setItems] = useState([])
  const [loaded, setLoaded] = useState(false)
  const containerRef = useRef(null)

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  useEffect(() => {
    setCount(unreadNotificationsCount ?? 0)
  }, [unreadNotificationsCount])

  function fetchRecent() {
    window.axios
      .get(r('notifications.recent'))
      .then(({ data }) => {
        setItems(data.notifications ?? [])
        setCount(data.unread_count ?? 0)
        setLoaded(true)
      })
      .catch(() => {
        // Background refresh - fail silently, badge just stops updating
        // until the next successful poll.
      })
  }

  useEffect(() => {
    const interval = setInterval(fetchRecent, POLL_INTERVAL_MS)
    return () => clearInterval(interval)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    function handleClickOutside(e) {
      if (containerRef.current && !containerRef.current.contains(e.target)) {
        setOpen(false)
      }
    }
    if (open) {
      document.addEventListener('mousedown', handleClickOutside)
      return () => document.removeEventListener('mousedown', handleClickOutside)
    }
  }, [open])

  function handleToggle() {
    const next = !open
    setOpen(next)
    if (next) fetchRecent()
  }

  function handleItemClick(notification) {
    window.axios.post(r('notifications.mark-as-read', notification.id))
    setOpen(false)
    router.visit(notification.data.action_url ?? r('notifications.index'))
  }

  const badgeLabel = count > 9 ? '9+' : String(count)

  return (
    <div className="relative" ref={containerRef}>
      <button
        type="button"
        onClick={handleToggle}
        aria-label={count > 0 ? `Notifikasi (${count} belum dibaca)` : 'Notifikasi'}
        title="Notifikasi"
        className="relative inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
      >
        <Bell className="h-4 w-4" />
        {count > 0 && (
          <span className="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-destructive px-1 text-[10px] font-medium leading-none text-destructive-foreground">
            {badgeLabel}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 z-30 mt-2 w-80 rounded-lg border border-border bg-card shadow-lg">
          <div className="border-b border-border px-4 py-3">
            <h3 className="text-sm font-semibold text-foreground">Notifikasi</h3>
          </div>

          <div className="max-h-80 overflow-y-auto">
            {!loaded ? (
              <p className="px-4 py-6 text-center text-sm text-muted-foreground">Memuat...</p>
            ) : items.length === 0 ? (
              <p className="px-4 py-6 text-center text-sm text-muted-foreground">Tidak ada notifikasi</p>
            ) : (
              items.map((notification) => {
                const unread = !notification.read_at
                return (
                  <button
                    key={notification.id}
                    type="button"
                    onClick={() => handleItemClick(notification)}
                    className={cn(
                      'flex w-full items-start gap-3 border-b border-border px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-muted',
                      unread && 'bg-indigo-50/40 dark:bg-indigo-500/[0.06]',
                    )}
                  >
                    <NotificationIcon type={notification.data.type} className="h-8 w-8" />
                    <div className="min-w-0 flex-1">
                      <p className="line-clamp-2 text-sm text-foreground">{notification.data.message ?? 'Notifikasi'}</p>
                      <p className="mt-1 text-xs tabular-nums text-muted-foreground">{relativeTime(notification.created_at)}</p>
                    </div>
                  </button>
                )
              })
            )}
          </div>

          <div className="border-t border-border px-4 py-2.5">
            <Link
              href={r('notifications.index')}
              onClick={() => setOpen(false)}
              className="block text-center text-sm font-medium text-primary hover:underline"
            >
              Lihat Semua
            </Link>
          </div>
        </div>
      )}
    </div>
  )
}
```

- [ ] **Step 2: Build to verify no import errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors (component isn't wired into any page yet — this just guards against a syntax/import typo in the new file).

- [ ] **Step 3: Commit**

```bash
git add resources/js/Components/NotificationBell.jsx
git commit -m "feat: Add NotificationBell component

Bell icon + unread badge + dropdown of the 5 most recent
notifications. Badge starts from the unreadNotificationsCount shared
prop and refreshes via a 30-second poll of /notifications/recent,
which also supplies the dropdown's list when opened. Not wired into
AppShell yet."
```

---

## Task 5: Wire `NotificationBell` into `AppShell`

**Files:**
- Modify: `resources/js/Layouts/AppShell.jsx`

- [ ] **Step 1: Add the import**

In `resources/js/Layouts/AppShell.jsx`, change:
```jsx
import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import { LogOut, Menu } from 'lucide-react'
import { cn } from '@/lib/utils'
import ThemeToggle from '@/Components/ThemeToggle'
```
to:
```jsx
import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import { LogOut, Menu } from 'lucide-react'
import { cn } from '@/lib/utils'
import ThemeToggle from '@/Components/ThemeToggle'
import NotificationBell from '@/Components/NotificationBell'
```

- [ ] **Step 2: Render it before `ThemeToggle` in the top bar**

Change:
```jsx
          <div className="flex-1" />

          <ThemeToggle />
          <span className="hidden text-sm text-muted-foreground md:block">
```
to:
```jsx
          <div className="flex-1" />

          <NotificationBell />
          <ThemeToggle />
          <span className="hidden text-sm text-muted-foreground md:block">
```

- [ ] **Step 3: Build to verify no errors**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 4: Manually verify the bell renders and works end-to-end**

With `php artisan serve --port=8000` running and `public/hot` removed:
1. Log in as any user (e.g. `admin@bakti.com` / `1`).
2. Confirm a bell icon appears in the top bar, left of the theme toggle.
3. If the user has any unread notifications, confirm a red numeric badge appears on the bell (compare against `/notifications`'s unread items — cards with the "Baru" badge). If the user has zero unread, confirm no badge shows.
4. Click the bell — confirm a dropdown opens showing up to 5 recent notifications (or "Tidak ada notifikasi" if none exist), each with an icon, message, and relative time.
5. Click a notification with an `action_url` — confirm the dropdown closes, the item becomes marked read (check `/notifications` afterward to confirm), and the browser navigates to that URL.
6. Click "Lihat Semua" — confirm it navigates to `/notifications`.
7. Click outside the open dropdown — confirm it closes without navigating anywhere.
8. Wait 30+ seconds with the dropdown closed and a badge visible; manually mark all as read from `/notifications` in another action, then wait for the next poll cycle and confirm the badge disappears from the bell without a page reload. (If this is impractical to time exactly, at minimum confirm via browser dev tools that a GET request to `/notifications/recent` fires roughly every 30 seconds while the page is open.)
9. Toggle dark mode and confirm the bell, badge, and dropdown remain legible.
10. Narrow the browser to 375px width and confirm the dropdown doesn't overflow off-screen (it's anchored `right-0` so it should stay within the viewport; if the layout looks broken at this width, note it as a finding rather than silently accepting it).

- [ ] **Step 5: Commit**

```bash
git add resources/js/Layouts/AppShell.jsx
git commit -m "feat: Wire NotificationBell into the shared app top bar

Now visible on every authenticated page across all three roles, since
AppShell is the common layout they all render through."
```

---

## Task 6: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: PHP lint all touched backend files**

Run:
```bash
php -l app/Http/Middleware/HandleInertiaRequests.php
php -l app/Http/Controllers/NotificationController.php
php -l routes/web.php
```
Expected: `No syntax errors detected in ...` for all three.

- [ ] **Step 2: Confirm the route list is correct**

Run: `php artisan route:list --name=notifications`
Expected: 6 rows — the 5 pre-existing routes (`notifications.index`, `.mark-as-read`, `.mark-all-as-read`, `.delete`, `.delete-all`) plus the new `notifications.recent`.

- [ ] **Step 3: Full frontend rebuild**

Run: `npm run build`
Expected: `✓ built in ...`, no errors.

- [ ] **Step 4: Cross-role manual check**

Log in as each of the three roles in turn (`admin@bakti.com` / `1`, `dede@baktitest.com` / `1`, and any available student account) and confirm the bell renders correctly in each role's layout (`AdminLayout`, `SupervisorLayout`, `StudentLayout` — all of which wrap `AppShell`, so this should work automatically, but verify none of them override the top bar in a way that hides it).

- [ ] **Step 5: Commit (only if fixes were needed)**

If Steps 1-4 all pass cleanly with no code changes needed, there is nothing to commit here.

---

## Self-review notes (completed during plan authoring, not a task to run)

- **Spec coverage**: shared prop for badge (Task 1), recent-list JSON endpoint (Task 2), shared icon extraction to avoid duplicating the type-mapping table (Task 3), the bell + dropdown + poll + click-to-mark-read-and-navigate component itself (Task 4), wiring into the one shared layout all roles use (Task 5), cross-role + dark mode + responsive manual verification (Task 5 Step 4, Task 6 Step 4) — every spec section has a corresponding task. `action_url` fallback to `/notifications` (spec's error-handling section) is implemented via `notification.data.action_url ?? r('notifications.index')` in Task 4.
- **Fixed during authoring**: initially drafted `NotificationIcon` without a `className` prop, then realized the dropdown needs a smaller icon size (`h-8 w-8`) than the full page's default (`h-10 w-10`) — added an optional `className` merged via `cn()` so one component serves both call sites without a size prop enum or a second component.
- **Type/name consistency**: `unreadNotificationsCount` (camelCase) is used identically in Task 1 (PHP array key, becomes the exact Inertia prop name) and Task 4 (`usePage().props.unreadNotificationsCount`). `notifications.recent` route name matches between Task 2 (registration) and Task 4 (`r('notifications.recent')` call). The JSON response shape `{ notifications: [...], unread_count: N }` (snake_case `unread_count`, matching the existing codebase's snake_case JSON convention seen in `data.notifications`/`data.unread_count` reads in Task 4) is defined once in Task 2 and consumed identically in Task 4.
- **No placeholders**: every task has complete, exact code — no "similar to the existing X" shortcuts for anything that isn't a byte-for-byte-unchanged block explicitly called out as such (e.g. Task 2's note that only `recent()` is new, with the full resulting file shown so there's no ambiguity about where it goes).
