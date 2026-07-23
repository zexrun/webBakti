# Logbook PDF Export and Feedback — Design

## Goal

Replace two non-functional/thin buttons on the Supervisor logbook review pages with real features:

1. **Cetak (Print)** — currently a raw `window.print()` of the on-screen page. Replace with a proper PDF export, for both a single logbook (detail page) and a filtered recap of multiple logbooks (list page).
2. **Kirim Feedback** — currently a decorative `mailto:` link that never touches the app (no data captured, sent, or stored). Replace with a real in-app feedback flow: supervisor writes feedback in a form, it's persisted on the logbook, and the student is notified (mail + in-app), following this codebase's existing `SubmissionGraded`-style Notification pattern.

## Non-goals

- No change to the existing `verify()` controller method's dead-route status — it's unrelated to this work (logbooks are already auto-marked verified on `show()`; feedback is a separate concern from the verified flag).
- No multi-feedback history — a logbook has at most one current feedback (the field is overwritten if the supervisor sends feedback again), not an append-only log of comments.
- No change to how students create/edit logbooks — only the Show pages (student + supervisor) and the supervisor's Index page are touched.

## 1. PDF export — single logbook

**Files:** new `resources/views/supervisor/pdf/logbook-pdf.blade.php`, `app/Http/Controllers/Supervisor/LogbookController.php`, `routes/web.php`, `resources/js/Pages/Supervisor/Logbooks/Show.jsx`

New Blade view following the same classic government-document treatment already established for `certificate-pdf.blade.php`/`grades-pdf.blade.php` (kop surat header, Times New Roman, black ruled tables/colon-aligned detail rows) — so all three official PDFs in this app read as one consistent document family. Content: student info (name, NIM, supervisor), logbook date, activity title, time range + duration, feeling, full description, and the attached photo (if any) embedded as an image (DomPDF supports local/public-disk image embedding the same way the existing attendance photo review flows already reference stored files).

New controller method `exportPdf(Logbook $logbook)`: same authorization check as `show()` (`abort(403)` if the logbook doesn't belong to one of the supervisor's students), builds the PDF via `Pdf::loadView(...)`, and `stream()`s it (never saved to storage, consistent with the certificate/grades PDFs).

New route `supervisor.logbooks.export-pdf` (`GET /supervisor/logbooks/{logbook}/export-pdf`).

`Show.jsx`'s "Cetak" button changes from `onClick={() => window.print()}` to a plain link (`<a target="_blank">` or an Inertia `<Link>` with `target="_blank"`) pointing at the new route — opens the PDF in a new tab, same pattern as the existing certificate/grades download links elsewhere in the app.

## 2. PDF export — filtered recap

**Files:** new `resources/views/supervisor/pdf/logbook-recap-pdf.blade.php`, `app/Http/Controllers/Supervisor/LogbookController.php`, `routes/web.php`, `resources/js/Pages/Supervisor/Logbooks/Index.jsx`

`Index.jsx` gains a filter bar (mirroring the date/status filter pattern already used on the Attendance Index pages): a student picker (populated from the supervisor's own students only) and a date range (start/end date inputs). Submitting the filter reloads the page with query params (`student_id`, `date_from`, `date_to`), same as the existing Attendance filter flow. A new "Cetak Hasil Filter" button appears once submitted, linking to the export route with the same query params attached.

`LogbookController::index()` is extended to accept and apply these same optional filters (so the on-screen list and the PDF always show the same filtered set — no separate filtering logic to keep in sync).

New controller method `exportRecapPdf(Request $request)`: re-applies the same filter logic as `index()` (extracted into a small private helper both methods call, to avoid duplicating the query-building), loads all matching logbooks (no pagination for the PDF — a bounded recap, not the full unfiltered table), groups them by student, and renders `logbook-recap-pdf.blade.php`: one section per student with a header (name/NIM) followed by a ruled table of their logbooks in the filtered range (date, activity, time, feeling, verified status).

New route `supervisor.logbooks.export-recap-pdf` (`GET /supervisor/logbooks/export-recap-pdf`).

## 3. Feedback — schema

**File:** new migration `database/migrations/<timestamp>_add_feedback_to_logbooks_table.php`

Adds two nullable columns to `logbooks`: `feedback` (text) and `feedback_at` (timestamp). No `feedback_by` column — the sender is always implicitly the logbook's student's own supervisor (already enforced by the existing authorization check), so it doesn't need to be stored redundantly. Sending feedback again overwrites the existing `feedback`/`feedback_at` (single current feedback, not a history).

`Logbook` model's `$fillable` gains `feedback`, `feedback_at`; add `feedback_at` to a new `casts()` method as `datetime`.

## 4. Feedback — backend

**Files:** new `app/Notifications/LogbookFeedbackGiven.php`, new `resources/views/emails/logbook-feedback.blade.php`, `app/Http/Controllers/Supervisor/LogbookController.php`, `routes/web.php`

New controller method `sendFeedback(Request $request, Logbook $logbook)`: validates `feedback` (`required|string|max:2000`), same authorization check as `show()`, updates the logbook's `feedback`/`feedback_at` (`feedback_at` set to `now()`), then dispatches `$logbook->student->user->notify(new LogbookFeedbackGiven($logbook))`, and redirects back with a flash success message.

New route `supervisor.logbooks.feedback` (`POST /supervisor/logbooks/{logbook}/feedback`).

New `LogbookFeedbackGiven` notification, modeled directly on `SubmissionGraded`:
- `via(): ['database', 'mail']`
- `toMail()`: subject "Feedback Baru untuk Logbook: {title}", renders a new Blade view `emails.logbook-feedback` (mirroring `emails.submission-graded`'s structure) with the logbook and feedback text.
- `toArray()`: `logbook_id`, `logbook_title`, `activity_date`, `feedback`, `supervisor_name` (via `$logbook->student->supervisor->user->name`), a human-readable `message`, an `action_url` pointing at `student.logbooks.show`, `type: 'logbook_feedback'`, `priority: 'normal'`, `created_at`.

## 5. Feedback — frontend

**Files:** `resources/js/Pages/Supervisor/Logbooks/Show.jsx`, `resources/js/Pages/Student/Logbooks/Show.jsx`

**Supervisor side:** the "Kirim Feedback" button changes from a `mailto:` link to a button that opens a popup modal (same `AnimatePresence`/`motion` pattern as `ConfirmDialog`/the profile photo modal built earlier this session) containing a textarea pre-filled with any existing `logbook.feedback` text. Submitting posts to `supervisor.logbooks.feedback` via Inertia's `router.post`, closes the modal on success, and the page's existing feedback display (see below) updates to show the newly-sent text.

Both the Supervisor and Student Show pages gain a "Feedback Pembimbing" card/section, rendered only when `logbook.feedback` is present, showing the feedback text and a formatted `feedback_at` timestamp — visually consistent with the existing "Deskripsi Kegiatan" block's styling (muted background, same spacing rhythm).

## Error handling

- PDF export routes reuse the exact same `abort(403)` authorization pattern as `show()`/`index()` — a supervisor cannot export a logbook or recap that isn't theirs.
- `sendFeedback()` validation failure surfaces as a standard Inertia form error under the textarea, same convention as every other form in this app.
- If the student's notification `mail` channel fails (e.g. mail server misconfigured), Laravel's Notification system's existing behavior applies (queued failure handling is out of scope to change) — the `database` channel entry still succeeds independently, so the in-app notification is not lost even if mail delivery has an issue.

## Testing approach

No automated test framework exists in this repo (consistent with every other feature this session). Verification: generate actual PDFs via `Pdf::loadView(...)->output()` in tinker for both the single-logbook and recap templates (covering the no-photo and no-logbooks-in-range edge cases), `php -l` all touched PHP files, `npm run build` for the frontend, and manual browser checks — export both PDF types, send feedback and confirm it displays on both the supervisor's and student's Show pages, and confirm the student receives both a database and mail notification entry.
