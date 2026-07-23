# Motion Animations (Modal, Page Transition, Stat Card Entrance) — Design

## Goal

Add three layers of animation using Framer Motion (`motion/react`), on top of the existing micro-interaction transitions (hover colors, sidebar slide) already in place:

1. Fade + scale enter/exit for every modal/dialog/lightbox.
2. A global fade+shift page transition on Inertia navigation.
3. A stagger fade-in for every `StatCard` instance across the app.

## Non-goals

- No change to existing hover/color transitions (`transition-colors duration-150` etc.) — those stay as plain Tailwind/CSS.
- No animation for table rows or list items (notifications, messages, announcements) — explicitly out of scope per this round; stat cards only.
- No change to toast animations — `sonner` already handles its own enter/exit.

## New dependency

`motion` (the npm package for Framer Motion's React bindings, imported as `motion/react`). Compatible with React 19 per its published peer dependencies.

## 1. Modal/dialog fade + scale

**Files:** `resources/js/Components/ConfirmDialog.jsx`, `resources/js/Components/ApprovalModal.jsx`, `resources/js/Pages/Student/Attendance/AttendanceModal.jsx`, `resources/js/Components/PhotoLightbox.jsx`

All four currently follow the same pattern: `if (!open) return null`, then a fixed-inset overlay `<div>` with a centered panel `<div>`. Each is wrapped in `<AnimatePresence>` (imported from `motion/react`) at its return point, replacing the plain conditional return:
- The overlay becomes a `motion.div` animating `opacity` 0→1 on enter, 1→0 on exit.
- The inner panel becomes a `motion.div` animating both `opacity` (0→1) and `scale` (0.95→1) on enter, reversed on exit.
- `AnimatePresence` handles the exit animation automatically (holds the component mounted until its exit transition finishes) — no manual delayed-unmount state needed, unlike a CSS-only approach.
- Transition duration ~150ms, matching the existing `duration-150` convention used everywhere else in the app for consistency of "feel."

`ApprovalModal.jsx` also wraps its nested `PhotoLightbox` the same way (no double-wrapping — each component owns its own `AnimatePresence` internally, so nesting call sites don't need to do anything extra).

## 2. Global page transition

**Files:** `resources/js/Layouts/AppShell.jsx`

`AppShell`'s `<main className="flex-1 p-4 sm:p-6 lg:p-8">{children}</main>` is wrapped:
```jsx
<AnimatePresence mode="wait">
  <motion.div
    key={url}
    initial={{ opacity: 0, y: 8 }}
    animate={{ opacity: 1, y: 0 }}
    exit={{ opacity: 0, y: -8 }}
    transition={{ duration: 0.15 }}
  >
    {children}
  </motion.div>
</AnimatePresence>
```
where `url` comes from Inertia's `usePage().url` — this gives `AnimatePresence` a changing `key` on every navigation, so it treats each page as a new element to transition between (`mode="wait"` ensures the old page fully exits before the new one enters, avoiding an overlapping/jumbled transition).

This is the single mounting point for the whole app (per the earlier Provider-placement lesson from the toast/confirm-dialog work) — every role layout (`AdminLayout`/`SupervisorLayout`/`StudentLayout`) renders through `AppShell`, so this one change covers every page.

## 3. StatCard stagger entrance

**Files:** `resources/js/Components/StatCard.jsx`

`StatCard` itself becomes a `motion.div`-wrapped `Card` (or wraps the existing `Card` in a `motion.div`), animating `opacity` (0→1) and `y` (8→0) on mount. A new optional prop, `index`, drives a small per-card `transition.delay` (e.g. `index * 0.05`s) so cards in the same grid animate in a staggered sequence rather than all at once.

**Call sites:** all 20 files currently rendering `<StatCard ... />` are updated to pass `index={0}`, `index={1}`, etc., based on their position within each stat-card grid (each grid's cards are numbered independently, restarting at 0 per grid — this is a per-page prop addition, not a global counter). `index` defaults to `0` if omitted, so any call site that isn't touched still renders correctly (single stat card with no visible stagger, not a crash).

## Error handling

None beyond what already exists — this is purely presentational, no new data flow, no new failure mode. If Framer Motion's animation fails to run for any reason, elements still render (motion components render as regular DOM elements with the animation props stripped, matching the library's documented graceful-degradation behavior).

## Testing approach

No automated test framework exists in this repo (consistent with prior features) — verification is `npm run build` plus manual browser checks: open/close each of the 4 modal types and confirm a smooth fade+scale instead of an instant pop; navigate between at least 3 different pages and confirm a brief fade+shift transition; load a page with multiple stat cards (e.g. Admin Attendance Index) and confirm they appear in a staggered sequence rather than all at once.
