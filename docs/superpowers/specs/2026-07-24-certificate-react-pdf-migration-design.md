# Certificate PDF Migration to React-PDF — Design

## Goal

Replace DomPDF-based rendering of the internship completion certificate (`resources/views/supervisor/pdf/certificate-pdf.blade.php`) with `@react-pdf/renderer`, to eliminate a DomPDF-specific text-wrapping bug: long Indonesian sentences in `.main-statement` truncate mid-line (confirmed via isolated reproduction — the same container/CSS renders Lorem Ipsum correctly but truncates real Indonesian prose of any length), a bug DomPDF's CSS-string layout engine exhibits that a proper flexbox layout engine (React-PDF's Yoga-based layout) should not.

This is a proof-of-concept for a new PDF-rendering architecture. If successful, the same pattern will later be applied to the other three DomPDF templates (`logbook-pdf.blade.php`, `logbook-recap-pdf.blade.php`, `grades-pdf.blade.php`) — but that is out of scope for this design; only the certificate is migrated here.

## Non-goals

- No change to `generateCertificate()`/`studentDownload()`'s public signatures, authorization checks, or business logic (assessment-exists check, document-completeness check, `certificate_generated_at` bookkeeping) — only the PDF-rendering internals (`streamCertificatePdf()`'s body) change.
- No migration of the other three DomPDF templates — this design covers the certificate only.
- No pixel-perfect visual parity requirement — approved to differ slightly where React-PDF's conventions make it easier (e.g., standard `Times-Roman`/`Times-Bold` base fonts instead of embedding a Times New Roman font file).
- No long-running Node service — rendering happens via a one-shot `node` process invocation per PDF request, consistent with how `php artisan serve` already runs without extra managed processes.

## 1. Node render script

**File:** new `resources/pdf-renderers/render-certificate.js`

A plain Node script (CommonJS or ESM `.mjs`, whichever `@react-pdf/renderer`'s package resolves most simply — decide during implementation, not JSX) using `React.createElement(...)` directly rather than JSX, so it can run via `node script.js` with no transpile/build step.

Invoked as: `node render-certificate.js <input-json-path> <output-pdf-path>`

Reads the input JSON file, which contains exactly the fields the current Blade template consumes:
```json
{
  "certNumber": "161",
  "certDateFormatted": "24/07/2026",
  "documentNumber": "161/KOMDIG/BAKTI/SDA/07/2026/PKL.01.24/07/2026",
  "supervisorName": "Pak Dede",
  "signerName": "SUDARMANTO",
  "signerNip": "196907071959031002",
  "signerPosition": "Kepala Divisi SDM dan Humas",
  "studentName": "Rifqy",
  "studentNim": "4854524627",
  "studentProgramStudi": "S1 Informatika",
  "studentUniversitas": "Telkom University",
  "periodeMulaiFormatted": "23 July 2026",
  "periodeSelesaiFormatted": "24 July 2026",
  "hasPeriode": true,
  "signatureDateFormatted": "24 July 2026"
}
```

All date formatting and the deterministic certificate-number calculation (`($student->id * 37 + $certDate->day) % 900 + 100`, unchanged formula) happen in PHP before writing this JSON — the Node script only receives already-formatted display strings, so it has zero date-math or business logic, keeping it a pure rendering layer.

Builds the PDF document tree with `@react-pdf/renderer`'s `Document`/`Page`/`View`/`Text` components and a `StyleSheet.create({...})` mirroring the current template's visual sections (header, title, two detail blocks, main statement, closing statement, signature block, tembusan list) as described in the approved layout below. Renders via `ReactPDF.render(<Document>...</Document>, outputPath)` (the Node-only file-writing API `@react-pdf/renderer` exposes, not the browser `pdf()` blob API), then exits 0. On any thrown error, prints the error message/stack to stderr and exits 1.

## 2. Layout (React-PDF components)

Single A4 page, one column, black text throughout, base font `Times-Roman` (`Times-Bold` for bold runs) — React-PDF's built-in standard fonts, no font-file embedding.

- **Header**: three centered `Text` lines — ministry name (bold, uppercase), agency name (bold, larger), italic subtitle, then a smaller contact-info block — all inside a `View` with a `borderBottom` matching the current 2px black rule.
- **Title block**: "SURAT KETERANGAN" (bold, underlined via `textDecoration: 'underline'`, larger size) centered, with `documentNumber` beneath it in a smaller size.
- **Two detail blocks** (signer info, then student info): each row is a `View` with `flexDirection: 'row'` containing three `Text` children — a fixed-width label, a colon, and the value — replicating the current colon-aligned table look without an actual HTML table (React-PDF has no `<table>`).
- **Main statement**: a single `Text` block containing the "Telah selesai melaksanakan..." sentence, with the `Magang/PKL` phrase wrapped in a nested bold `Text` run; relies on Yoga's normal text-wrapping (the exact mechanism DomPDF was failing at) rather than any manual line-splitting.
- **Closing statement**: one short `Text` paragraph.
- **Signature block**: right-aligned `View` — date/location line, position title, bold underlined signer name, NIP.
- **Tembusan**: small left-aligned list at the bottom.

## 3. Laravel side

**File:** `app/Http/Controllers/Supervisor/FinalAssessmentController.php`

`streamCertificatePdf(Student $student, FinalAssessment $assessment, string $supervisorName, $generatedAt)` is rewritten internally (same signature, same two callers `generateCertificate()`/`studentDownload()` untouched) to:

1. Compute the same values the Blade view currently computes inline (`$certNumber`, `$documentNumber`, date formatting for `periode_mulai`/`periode_selesai`/signature date) — this logic moves from the Blade `@php` block into this PHP method, unchanged in formula.
2. Write those values as JSON to a new temp file under `storage/app/tmp/` (directory created if missing), named uniquely per request (e.g. `Str::uuid()`-based filename) to avoid collisions under concurrent requests.
3. Run `node resources/pdf-renderers/render-certificate.js <input.json> <output.pdf>` via Symfony `Process` (`Illuminate\Support\Facades\Process` — Laravel's built-in wrapper), with a reasonable timeout (e.g. 30s).
4. If the process fails (non-zero exit), throw an exception including the process's stderr output, so failures surface clearly in logs rather than silently producing an empty/broken response.
5. On success, read the output PDF file's contents, return it via `response(...)->header('Content-Type', 'application/pdf')` with the same filename convention as today (`certificate_<name>.pdf`), matching the current `stream()` behavior (inline display, not forced download).
6. Delete both temp files (input JSON and output PDF) after the response content has been read into memory, using a `finally`-style cleanup so temp files don't accumulate even if a later step throws.

**File:** `package.json` — add `@react-pdf/renderer` as a regular `dependency` (not `devDependency`), since it's required at runtime by the Node render script, not only during `npm run build`.

## Error handling

- Node script errors (thrown exceptions, missing input fields) exit 1 with a message on stderr; Laravel surfaces this as a thrown PHP exception with the stderr text included, so it appears in Laravel's log with enough detail to debug without needing to reproduce manually.
- If `node` itself isn't found/fails to start (e.g. misconfigured `PATH` in whatever environment runs this later), Symfony `Process` throws a clear "command not found"-style exception, which propagates the same way.
- Temp file cleanup happens regardless of success or failure, so a failed render doesn't leave orphaned files in `storage/app/tmp/`.

## Testing approach

No automated test framework exists in this repo (consistent with every other feature this session). Verification:
1. Run the Node script directly with a hand-written sample JSON input, confirm it produces a valid PDF (`file` command reports "PDF document", page count via the `/Count` marker check used throughout this session).
2. Specifically re-test the exact sentence that broke DomPDF (`Telah selesai melaksanakan Magang/PKL di Badan Aksesibilitas...`) at full length, confirming via `pdftotext` that the text extracts complete and unbroken, unlike the DomPDF output.
3. Exercise the full flow through the actual routes (`supervisor.pdf.certificate.generate`, `student.pdf.certificate.download`) in the browser, confirming the PDF opens correctly and content matches the student/assessment data.
4. Confirm temp files in `storage/app/tmp/` are cleaned up after a request (directory listing before/after).
