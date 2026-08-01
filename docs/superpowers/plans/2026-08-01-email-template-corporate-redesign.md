# Email Template Corporate/Formal Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current "Minimalist & Spacious" blue-gradient email design with a Corporate/Formal navy-solid design across the shared layout and all 6 email templates, per the approved spec.

**Architecture:** All templates extend `resources/views/emails/layout.blade.php`, which owns the `<style>` block and header/footer markup. The CSS class names stay the same (`.email-title`, `.email-section`, `.email-alert-*`, etc.) so per-template Blade files don't need class renames — only the CSS rules change, plus a new `.email-category-label` class and a `.email-info-row`-based replacement for the old boxed `.email-section` pattern. Per-template files get content edits: drop emoji, add the category label, replace `.email-section` info boxes with the new label-value row structure, reword alert lead-ins.

**Tech Stack:** Laravel 12 Blade templates, inline-CSS-in-`<style>`-block emails (no build step, no email-specific CSS framework).

**Spec reference:** `docs/superpowers/specs/2026-08-01-email-template-corporate-redesign-design.md`

---

## Verification Strategy

There is no existing automated test suite for email markup — these are Blade views styled for email clients, not application logic. Verification is render-based:

1. `php artisan email:test --to=<address>` renders `emails.test_simple` through the real `Mail` facade (requires working mail config — may fail in sandbox without SMTP creds, that's fine, see Task 8).
2. A local render check that doesn't require SMTP: `php artisan tinker` and echo `view('emails.<name>', [...fake data...])->render()` to a file, then open that HTML file in a browser. This works in any environment and is the primary verification method used in this plan.
3. Existing PHP tests (`tests/Feature/Auth/EmailVerificationTest.php`, `tests/Feature/ProfileTest.php`) touch mail sending but not markup — running the full suite at the end just confirms nothing broke functionally.

---

### Task 1: Rewrite the shared layout CSS and header/footer markup

**Files:**
- Modify: `resources/views/emails/layout.blade.php`

- [ ] **Step 1: Replace the `<style>` block**

Replace the entire contents of the `<style>` tag (currently lines 7–356) with:

```css
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f0f1f3;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #444444;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            background-color: #f0f1f3;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e2e2;
        }

        /* Header */
        .email-header {
            background: #0F2A5C;
            padding: 24px 28px;
            color: #ffffff;
        }

        .email-header-table {
            width: 100%;
        }

        .email-header-logo {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .email-header-subtitle {
            font-size: 10px;
            opacity: 0.75;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
            line-height: 1.4;
        }

        .email-header-institution {
            font-size: 9px;
            opacity: 0.6;
            line-height: 1.5;
            text-align: right;
        }

        /* Content */
        .email-body {
            padding: 28px 24px;
        }

        .email-category-label {
            font-size: 11px;
            font-weight: 700;
            color: #0F2A5C;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
        }

        .email-title {
            font-size: 17px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 12px;
            line-height: 1.35;
        }

        .email-greeting {
            font-size: 13px;
            font-weight: 500;
            color: #333333;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .email-paragraph {
            font-size: 13px;
            line-height: 1.7;
            color: #444444;
            margin-bottom: 16px;
        }

        .email-paragraph strong {
            color: #111111;
            font-weight: 600;
        }

        /* Buttons */
        .email-button-group {
            margin: 24px 0;
        }

        .email-button {
            display: inline-block;
            background: #0F2A5C;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 2px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0 8px 8px 0;
            border: none;
        }

        .email-button-secondary {
            background: #ffffff;
            color: #0F2A5C;
            border: 1px solid #0F2A5C;
        }

        /* Info rows (replaces old .email-section box) */
        .email-info-box {
            margin: 16px 0;
            padding: 0;
            border-top: 1px solid #eeeeee;
        }

        .email-info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eeeeee;
            gap: 20px;
        }

        .email-info-label {
            font-weight: 400;
            color: #666666;
            font-size: 12px;
            flex-shrink: 0;
        }

        .email-info-value {
            color: #111111;
            font-weight: 600;
            word-break: break-word;
            font-size: 12px;
            text-align: right;
        }

        /* Alert boxes */
        .email-alert {
            padding: 12px 16px;
            border-radius: 0;
            margin: 16px 0;
            border-left: 3px solid;
            font-size: 12px;
            line-height: 1.6;
        }

        .email-alert-info {
            background: #EFF3FA;
            border-left-color: #2E5FDB;
            color: #1E3A6E;
        }

        .email-alert-success {
            background: #F3F8F3;
            border-left-color: #2E7D32;
            color: #1B5E20;
        }

        .email-alert-warning {
            background: #FFFBEB;
            border-left-color: #B7791F;
            color: #7C4A03;
        }

        .email-alert-danger {
            background: #FDF2F2;
            border-left-color: #C0392B;
            color: #922B21;
        }

        /* Lists */
        .email-list {
            margin: 12px 0;
            padding-left: 20px;
        }

        .email-list li {
            margin-bottom: 8px;
            color: #444444;
            font-size: 12px;
            line-height: 1.6;
        }

        .email-list strong {
            color: #111111;
        }

        /* Divider */
        .email-divider {
            border: 0;
            height: 1px;
            background: #eeeeee;
            margin: 24px 0;
        }

        /* Footer */
        .email-footer {
            background: #fafbfc;
            padding: 24px 28px;
            border-top: 1px solid #eeeeee;
            text-align: center;
        }

        .email-footer-content {
            font-size: 10px;
            color: #8a8f97;
            line-height: 1.7;
        }

        .email-footer-content strong {
            color: #555555;
            font-weight: 600;
        }

        .email-footer-links {
            margin-top: 14px;
            border-top: 1px solid #eeeeee;
            padding-top: 14px;
        }

        .email-footer-links a {
            color: #0F2A5C;
            text-decoration: none;
            margin: 0 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .email-footer-links a:hover {
            text-decoration: underline;
        }

        .email-footer-copyright {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #eeeeee;
            font-size: 10px;
            color: #a0aec0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-header-table,
            .email-header-table tbody,
            .email-header-table tr,
            .email-header-table td {
                display: block;
                width: 100% !important;
                text-align: left !important;
            }

            .email-header-institution {
                text-align: left;
                margin-top: 10px;
            }

            .email-body {
                padding: 20px;
            }

            .email-footer {
                padding: 20px;
            }

            .email-title {
                font-size: 16px;
            }

            .email-button {
                display: block;
                width: 100%;
                text-align: center;
                margin: 0 0 10px 0;
            }

            .email-info-row {
                flex-direction: column;
                gap: 2px;
            }

            .email-info-value {
                text-align: left;
            }
        }
```

- [ ] **Step 2: Replace the header markup**

Find this block (currently lines 361–365):

```html
            <!-- Header -->
            <div class="email-header">
                <div class="email-header-logo">🎓 WebBakti</div>
                <div class="email-header-subtitle">Sistem Manajemen Magang Online</div>
            </div>
```

Replace with a table-based two-column layout (tables are used instead of flexbox because flexbox support is unreliable across email clients like Outlook desktop, whereas `<table>` layouts are the email-safe standard):

```html
            <!-- Header -->
            <div class="email-header">
                <table class="email-header-table" role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="vertical-align: middle;" width="60%">
                            <div class="email-header-logo">WebBakti</div>
                            <div class="email-header-subtitle">Sistem Manajemen Magang</div>
                        </td>
                        <td style="vertical-align: middle; text-align: right;" width="40%">
                            <div class="email-header-institution">KEMENTERIAN KOMUNIKASI<br>DAN DIGITAL RI</div>
                        </td>
                    </tr>
                </table>
            </div>
```

`width="100%"` and per-`<td>` `width` percentages are set as HTML attributes (not just CSS) because Outlook desktop's Word rendering engine ignores CSS `width` on tables/cells but respects the HTML attribute.

- [ ] **Step 3: Update the footer text sizing context (no markup change needed)**

The footer markup (org name, links, copyright block) stays structurally the same — only the CSS from Step 1 changes its appearance. Confirm by reading the current footer block (lines 376–393) that no class names used there (`email-footer`, `email-footer-content`, `email-footer-links`, `email-footer-copyright`) are missing from the new CSS. All four are defined in Step 1. No edit needed here.

- [ ] **Step 4: Render-check the empty layout**

Run:
```bash
php artisan tinker --execute="file_put_contents('storage/app/preview.html', view('emails.test_simple')->render());"
```
Then open `storage/app/preview.html` in a browser. Expected: navy header with "WebBakti" left, ministry label right, sharp corners, Arial font visible. (Content below the header will still show the OLD styling of `test_simple.blade.php` — e.g. emoji title — until Task 8. That's fine for this step; we're only checking the header/footer/shell.)

- [ ] **Step 5: Commit**

```bash
git add resources/views/emails/layout.blade.php
git commit -m "refactor: Redesign email layout to corporate/formal navy style"
```

---

### Task 2: Update `attendance-approval.blade.php`

**Files:**
- Modify: `resources/views/emails/attendance-approval.blade.php`

- [ ] **Step 1: Replace the full body content**

Replace the entire file content (after the `@extends` and `@php` lines) with the redesigned structure. Full file:

```blade
@extends('emails.layout', ['title' => 'Status Kehadiran - WebBakti'])

@php
    $categoryLabel = 'Status Kehadiran';
    $title = $status === 'approved' ? 'Kehadiran Disetujui' : 'Kehadiran Ditolak';
@endphp

<div class="email-category-label">{{ $categoryLabel }}</div>
<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Status kehadiran Anda telah dikaji dan diverifikasi oleh sistem.
</p>

@if($status === 'approved')
    <div class="email-alert email-alert-success">
        <strong>Disetujui.</strong> Kehadiran Anda pada tanggal tersebut telah diverifikasi dan diterima oleh sistem. Terima kasih telah menjalankan kehadiran dengan baik.
    </div>
@else
    <div class="email-alert email-alert-danger">
        <strong>Ditolak.</strong> Kehadiran Anda pada tanggal tersebut tidak memenuhi kriteria verifikasi sistem.
        @if($attendance->rejection_reason)
            <br><strong>Alasan:</strong> {{ $attendance->rejection_reason }}
        @endif
    </div>
@endif

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Tanggal & Waktu</div>
        <div class="email-info-value">
            {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y, H:i') : 'N/A' }}
        </div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Lokasi</div>
        <div class="email-info-value">{{ $attendance->location ?? 'Tidak tercatat' }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Verifikasi Lokasi</div>
        <div class="email-info-value">
            {{ $attendance->location_verification_status ?? 'Belum diverifikasi' }}
            @if($attendance->location_spoofing_score)
                (Skor: {{ $attendance->location_spoofing_score }})
            @endif
        </div>
    </div>
    @if($attendance->photo_exif_status)
    <div class="email-info-row">
        <div class="email-info-label">Status EXIF</div>
        <div class="email-info-value">{{ $attendance->photo_exif_status }}</div>
    </div>
    @endif
</div>

@if($status === 'rejected')
<p class="email-paragraph">Kehadiran ditolak karena tidak memenuhi kriteria verifikasi sistem. Kemungkinan penyebabnya:</p>
<ul class="email-list">
    <li>Lokasi tidak sesuai dengan zona yang ditentukan</li>
    <li>Data EXIF foto tidak valid atau tidak terbaca</li>
    <li>Terdapat indikasi manipulasi data lokasi</li>
    <li>Foto atau data yang dikirimkan tidak jelas atau tidak valid</li>
</ul>

<div class="email-alert email-alert-info">
    <strong>Langkah Selanjutnya:</strong>
    <ul class="email-list" style="margin-top: 8px;">
        <li>Tinjau kembali data yang Anda kirimkan</li>
        <li>Jika yakin tidak ada kesalahan, ajukan pengajuan exception dengan alasan yang jelas</li>
        <li>Sertakan bukti pendukung jika diperlukan</li>
        <li>Hubungi admin jika memerlukan bantuan</li>
    </ul>
</div>
@else
<p class="email-paragraph">
    Kehadiran Anda telah tercatat dalam sistem dan akan diperhitungkan dalam evaluasi performa magang Anda.
</p>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/attendance') }}" class="email-button">Lihat Riwayat Kehadiran</a>
</div>

<hr class="email-divider">

<p class="email-paragraph">
    Apabila memiliki pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi admin sistem melalui fitur support di aplikasi.
</p>
```

- [ ] **Step 2: Render-check with fake data**

Run:
```bash
php artisan tinker --execute="
\$attendance = new stdClass();
\$attendance->check_in_time = now();
\$attendance->location = 'Kantor BAKTI Jakarta';
\$attendance->location_verification_status = 'Terverifikasi';
\$attendance->location_spoofing_score = null;
\$attendance->photo_exif_status = null;
\$attendance->rejection_reason = null;
\$notifiable = new stdClass();
\$notifiable->name = 'Rifqy';
file_put_contents('storage/app/preview.html', view('emails.attendance-approval', ['status' => 'approved', 'attendance' => \$attendance, 'notifiable' => \$notifiable])->render());
"
```
Open `storage/app/preview.html`. Expected: category label "STATUS KEHADIRAN" in navy above the title, green alert box, label-value rows with dividers, navy button. No emoji anywhere.

Repeat with `'status' => 'rejected'` and `\$attendance->rejection_reason = 'Lokasi tidak sesuai';` to check the danger-alert path renders too.

- [ ] **Step 3: Commit**

```bash
git add resources/views/emails/attendance-approval.blade.php
git commit -m "refactor: Update attendance-approval email to corporate style"
```

---

### Task 3: Update `exception-approval.blade.php`

**Files:**
- Modify: `resources/views/emails/exception-approval.blade.php`

- [ ] **Step 1: Replace the full body content**

```blade
@extends('emails.layout', ['title' => 'Status Pengajuan Exception - WebBakti'])

@php
    $categoryLabel = 'Status Pengajuan';
    $title = $status === 'approved' ? 'Pengajuan Disetujui' : 'Pengajuan Ditolak';
@endphp

<div class="email-category-label">{{ $categoryLabel }}</div>
<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pengajuan exception (sakit/izin) Anda telah dikaji dan diproses oleh sistem.
</p>

@if($status === 'approved')
    <div class="email-alert email-alert-success">
        <strong>Disetujui.</strong> Pengajuan exception Anda telah disetujui. Hari tersebut tidak akan dihitung sebagai ketidakhadiran dalam evaluasi magang Anda.
    </div>
@else
    <div class="email-alert email-alert-danger">
        <strong>Ditolak.</strong> Pengajuan exception Anda tidak memenuhi kriteria persetujuan.
        @if($exception->admin_notes)
            <br><strong>Alasan:</strong> {{ $exception->admin_notes }}
        @endif
    </div>
@endif

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Tanggal</div>
        <div class="email-info-value">
            {{ $exception->date ? \Carbon\Carbon::parse($exception->date)->format('d M Y') : 'N/A' }}
        </div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Tipe</div>
        <div class="email-info-value">
            @switch($exception->type)
                @case('sick')
                    Sakit
                    @break
                @case('leave')
                    Izin
                    @break
                @default
                    {{ $exception->type ?? 'N/A' }}
            @endswitch
        </div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Alasan</div>
        <div class="email-info-value">{{ $exception->reason ?? '-' }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Waktu Pengajuan</div>
        <div class="email-info-value">{{ $exception->created_at->format('d M Y, H:i') }}</div>
    </div>
</div>

@if($exception->supporting_document)
<p class="email-paragraph">Dokumen pendukung telah dicatat dan diverifikasi dalam sistem.</p>
@endif

@if($status === 'rejected')
<div class="email-alert email-alert-warning">
    <strong>Apa yang Bisa Anda Lakukan?</strong>
    <ul class="email-list" style="margin-top: 8px;">
        <li>Mengajukan exception kembali dengan informasi atau dokumen yang lebih lengkap</li>
        <li>Menghubungi admin untuk mendiskusikan penolakan ini</li>
        <li>Mengajukan banding jika merasa ada kesalahan dalam penilaian</li>
    </ul>
</div>
@else
<p class="email-paragraph">
    Status kehadiran Anda telah diperbarui dan sesuai dengan persetujuan exception ini. Anda dapat melihat detail di aplikasi WebBakti.
</p>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/exceptions') }}" class="email-button">Lihat Pengajuan Saya</a>
</div>

@if($status === 'rejected')
<div class="email-alert email-alert-info">
    <strong>Hubungi Admin:</strong> Jika Anda memiliki pertanyaan atau ingin berdiskusi lebih lanjut, silakan hubungi melalui fitur support di aplikasi atau email admin.
</div>
@endif

<hr class="email-divider">

<p class="email-paragraph">
    Terima kasih telah mengikuti prosedur pengajuan exception dengan baik dan mematuhi semua peraturan yang berlaku.
</p>
```

- [ ] **Step 2: Render-check with fake data**

Run:
```bash
php artisan tinker --execute="
\$exception = new stdClass();
\$exception->date = now();
\$exception->type = 'sick';
\$exception->reason = 'Demam tinggi';
\$exception->created_at = now();
\$exception->supporting_document = null;
\$exception->admin_notes = null;
\$notifiable = new stdClass();
\$notifiable->name = 'Rifqy';
file_put_contents('storage/app/preview.html', view('emails.exception-approval', ['status' => 'approved', 'exception' => \$exception, 'notifiable' => \$notifiable])->render());
"
```
Open `storage/app/preview.html`. Expected: same corporate structure as Task 2, "STATUS PENGAJUAN" category label, "Sakit" shown without emoji.

- [ ] **Step 3: Commit**

```bash
git add resources/views/emails/exception-approval.blade.php
git commit -m "refactor: Update exception-approval email to corporate style"
```

---

### Task 4: Update `logbook-feedback.blade.php`

**Files:**
- Modify: `resources/views/emails/logbook-feedback.blade.php`

- [ ] **Step 1: Replace the full body content**

```blade
@extends('emails.layout', ['title' => 'Feedback Logbook - WebBakti'])

<div class="email-category-label">Feedback Logbook</div>
<div class="email-title">Feedback Baru Diterima</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pembimbing Anda telah memberikan feedback berharga pada salah satu logbook Anda. Bacalah feedback ini untuk meningkatkan kualitas pekerjaan Anda ke depannya.
</p>

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Judul</div>
        <div class="email-info-value">{{ $logbook->title }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Tanggal Aktivitas</div>
        <div class="email-info-value">{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Pembimbing</div>
        <div class="email-info-value">{{ $logbook->student->supervisor->user->name }}</div>
    </div>
</div>

<div style="background: #F7F8FA; padding: 14px 16px; border-left: 3px solid #0F2A5C; color: #333333; line-height: 1.7; font-size: 12px; margin: 16px 0;">
    {{ $logbook->feedback }}
</div>

<div class="email-button-group">
    <a href="{{ url('/student/logbooks/' . $logbook->id) }}" class="email-button">Lihat Logbook Lengkap</a>
</div>

<hr class="email-divider">

<p class="email-paragraph">
    Gunakan feedback ini sebagai pembelajaran untuk terus meningkatkan kualitas logbook dan pekerjaan Anda di magang. Jika ada pertanyaan terkait feedback, silakan hubungi pembimbing Anda melalui aplikasi.
</p>
```

- [ ] **Step 2: Render-check with fake data**

Run:
```bash
php artisan tinker --execute="
\$student = new stdClass();
\$supervisor = new stdClass();
\$supervisorUser = new stdClass();
\$supervisorUser->name = 'Budi Santoso';
\$supervisor->user = \$supervisorUser;
\$student->supervisor = \$supervisor;
\$logbook = new stdClass();
\$logbook->title = 'Implementasi Fitur Login';
\$logbook->activity_date = now();
\$logbook->student = \$student;
\$logbook->feedback = 'Pekerjaan bagus, tapi tolong tambahkan lebih detail pada bagian testing.';
\$logbook->id = 1;
\$notifiable = new stdClass();
\$notifiable->name = 'Rifqy';
file_put_contents('storage/app/preview.html', view('emails.logbook-feedback', ['logbook' => \$logbook, 'notifiable' => \$notifiable])->render());
"
```
Open `storage/app/preview.html`. Expected: "FEEDBACK LOGBOOK" category label, info rows for judul/tanggal/pembimbing, feedback quote box with navy left border.

- [ ] **Step 3: Commit**

```bash
git add resources/views/emails/logbook-feedback.blade.php
git commit -m "refactor: Update logbook-feedback email to corporate style"
```

---

### Task 5: Update `submission-graded.blade.php`

**Files:**
- Modify: `resources/views/emails/submission-graded.blade.php`

- [ ] **Step 1: Replace the full body content**

```blade
@extends('emails.layout', ['title' => 'Tugas Dinilai - WebBakti'])

<div class="email-category-label">Penilaian Tugas</div>
<div class="email-title">Tugas Anda Telah Dinilai</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pembimbing Anda telah selesai menilai submission tugas Anda. Lihat hasilnya di bawah ini.
</p>

<div style="text-align: center; padding: 20px; background: #0F2A5C; margin: 16px 0;">
    <p style="font-size: 11px; margin: 0 0 6px 0; color: #c9d3e6; text-transform: uppercase; letter-spacing: 0.5px;">Nilai Anda</p>
    <p style="font-size: 40px; font-weight: 800; margin: 0; color: #ffffff;">{{ $submission->grade }}</p>
</div>

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Judul</div>
        <div class="email-info-value">{{ $submission->task->title }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Pembimbing</div>
        <div class="email-info-value">{{ $submission->task->supervisor->user->name }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Tipe Tugas</div>
        <div class="email-info-value">{{ $submission->task->type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir' }}</div>
    </div>
</div>

@if($submission->comments)
<p class="email-paragraph"><strong>Komentar Pembimbing:</strong></p>
<div style="background: #F7F8FA; padding: 14px 16px; border-left: 3px solid #0F2A5C; color: #333333; line-height: 1.7; font-size: 12px; margin: 0 0 16px;">
    {{ $submission->comments }}
</div>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/tasks/' . $submission->task->id) }}" class="email-button">Lihat Detail Lengkap</a>
</div>

<div class="email-alert email-alert-info">
    <strong>Langkah Berikutnya:</strong> Baca feedback dari pembimbing dengan seksama. Gunakan pembelajaran ini untuk meningkatkan kualitas pekerjaan Anda pada tugas-tugas selanjutnya.
</div>

<hr class="email-divider">

<p class="email-paragraph" style="text-align: center; font-weight: 600; color: #0F2A5C;">
    Semangat untuk tugas berikutnya!
</p>
```

- [ ] **Step 2: Render-check with fake data**

Run:
```bash
php artisan tinker --execute="
\$supervisor = new stdClass();
\$supervisorUser = new stdClass();
\$supervisorUser->name = 'Budi Santoso';
\$supervisor->user = \$supervisorUser;
\$task = new stdClass();
\$task->title = 'Laporan Mingguan Ke-4';
\$task->supervisor = \$supervisor;
\$task->type = 'harian';
\$task->id = 1;
\$submission = new stdClass();
\$submission->grade = 92;
\$submission->task = \$task;
\$submission->comments = 'Kerja bagus, pertahankan konsistensinya.';
\$notifiable = new stdClass();
\$notifiable->name = 'Rifqy';
file_put_contents('storage/app/preview.html', view('emails.submission-graded', ['submission' => \$submission, 'notifiable' => \$notifiable])->render());
"
```
Open `storage/app/preview.html`. Expected: "PENILAIAN TUGAS" label, solid navy grade box (not purple gradient) with "92" large and centered, info rows, comment quote box.

- [ ] **Step 3: Commit**

```bash
git add resources/views/emails/submission-graded.blade.php
git commit -m "refactor: Update submission-graded email to corporate style"
```

---

### Task 6: Update `task-deadline-reminder.blade.php`

**Files:**
- Modify: `resources/views/emails/task-deadline-reminder.blade.php`

- [ ] **Step 1: Replace the full body content**

```blade
@extends('emails.layout', ['title' => 'Pengingat Deadline Tugas - WebBakti'])

@php
    if ($daysUntilDeadline === 0) {
        $title = 'Tugas Berakhir Hari Ini';
        $urgency = 'danger';
    } elseif ($daysUntilDeadline === 1) {
        $title = 'Tugas Berakhir Besok';
        $urgency = 'warning';
    } else {
        $title = 'Pengingat Deadline Tugas';
        $urgency = 'info';
    }
@endphp

<div class="email-category-label">Pengingat Deadline</div>
<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Ini adalah pengingat penting bahwa salah satu tugas Anda akan segera berakhir. Pastikan Anda menyelesaikan dan mengumpulkan sebelum batas waktu.
</p>

@if($daysUntilDeadline === 0)
    <div class="email-alert email-alert-danger">
        <strong>URGENT!</strong> Tugas ini berakhir <strong>HARI INI</strong>. Segera selesaikan dan kumpulkan sebelum batas waktu!
    </div>
@elseif($daysUntilDeadline === 1)
    <div class="email-alert email-alert-warning">
        <strong>PENTING!</strong> Tugas ini berakhir <strong>BESOK</strong>. Pastikan Anda menyelesaikan dengan segera!
    </div>
@else
    <div class="email-alert email-alert-info">
        <strong>Pengingat Deadline:</strong> Tugas ini berakhir dalam <strong>{{ $daysUntilDeadline }} hari</strong>. Mulai kerjakan sekarang agar tidak ketinggalan!
    </div>
@endif

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Judul</div>
        <div class="email-info-value">{{ $task->title }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Pembimbing</div>
        <div class="email-info-value">{{ $task->supervisor->user->name }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Tipe</div>
        <div class="email-info-value">{{ $task->type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir' }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Batas Waktu</div>
        <div class="email-info-value"><strong>{{ $task->due_date->format('d M Y, H:i') }}</strong></div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Waktu Tersisa</div>
        <div class="email-info-value">
            @if($daysUntilDeadline === 0)
                <strong style="color: #C0392B;">Hari ini (URGENT!)</strong>
            @elseif($daysUntilDeadline === 1)
                <strong style="color: #B7791F;">1 hari</strong>
            @else
                <strong>{{ $daysUntilDeadline }} hari</strong>
            @endif
        </div>
    </div>
</div>

<p class="email-paragraph"><strong>Deskripsi Singkat:</strong> {{ Str::limit($task->description, 300) }}</p>

@if($task->file_path)
<p class="email-paragraph">File lampiran tersedia untuk tugas ini. Silakan download dari aplikasi WebBakti untuk melihat detail lengkap.</p>
@endif

<p class="email-paragraph"><strong>Langkah-Langkah Menyelesaikan:</strong></p>
<ol class="email-list">
    <li><strong>Baca</strong> deskripsi tugas dengan seksama</li>
    <li><strong>Download</strong> file lampiran (jika ada)</li>
    <li><strong>Kerjakan</strong> tugas sesuai dengan instruksi</li>
    <li><strong>Upload</strong> hasil pekerjaan Anda ke sistem</li>
    <li><strong>Verifikasi</strong> bahwa submission sudah berhasil diterima</li>
</ol>

<div class="email-button-group">
    <a href="{{ url('/student/tasks/' . $task->id) }}" class="email-button">Buka Tugas Sekarang</a>
</div>

<div class="email-alert email-alert-warning">
    <strong>Catatan Penting:</strong>
    <ul class="email-list" style="margin-top: 8px;">
        <li>Pastikan Anda upload submission sebelum batas waktu berakhir</li>
        <li>Submission yang dikirim setelah deadline tidak akan diterima</li>
        <li>Jika ada kendala, hubungi pembimbing Anda sesegera mungkin</li>
    </ul>
</div>

<hr class="email-divider">

<p class="email-paragraph" style="text-align: center; font-weight: 600; color: #0F2A5C;">
    Semangat mengerjakan tugas!
</p>
```

- [ ] **Step 2: Render-check with fake data (all 3 urgency paths)**

Run for the "today" (danger) path:
```bash
php artisan tinker --execute="
\$supervisor = new stdClass();
\$supervisorUser = new stdClass();
\$supervisorUser->name = 'Budi Santoso';
\$supervisor->user = \$supervisorUser;
\$task = new stdClass();
\$task->title = 'Laporan Mingguan Ke-4';
\$task->supervisor = \$supervisor;
\$task->type = 'harian';
\$task->due_date = now()->addHours(6);
\$task->description = 'Kerjakan laporan mingguan sesuai template yang diberikan.';
\$task->file_path = null;
\$task->id = 1;
\$notifiable = new stdClass();
\$notifiable->name = 'Rifqy';
file_put_contents('storage/app/preview.html', view('emails.task-deadline-reminder', ['task' => \$task, 'notifiable' => \$notifiable, 'daysUntilDeadline' => 0])->render());
"
```
Open `storage/app/preview.html`. Expected: red danger alert, "Hari ini (URGENT!)" in red inline within the info row.

Repeat with `'daysUntilDeadline' => 1` (expect amber warning alert) and `'daysUntilDeadline' => 3` (expect blue info alert) to confirm all three color paths render correctly.

- [ ] **Step 3: Commit**

```bash
git add resources/views/emails/task-deadline-reminder.blade.php
git commit -m "refactor: Update task-deadline-reminder email to corporate style"
```

---

### Task 7: Update `test_simple.blade.php` and `test.blade.php`

**Files:**
- Modify: `resources/views/emails/test_simple.blade.php`
- Modify: `resources/views/emails/test.blade.php`

These are QA-only templates (used by `email:test` artisan command and manual testing) but per the spec they should stay visually consistent since they're the fastest way to eyeball the layout end-to-end.

- [ ] **Step 1: Replace `test_simple.blade.php`**

```blade
@extends('emails.layout', ['title' => 'Test Email - WebBakti'])

<div class="email-category-label">Test Email</div>
<div class="email-title">Test Email WebBakti</div>

<p class="email-greeting">Halo,</p>

<p class="email-paragraph">
    Ini adalah email test dari sistem WebBakti Anda. Email ini menunjukkan bahwa konfigurasi email sudah berfungsi dengan baik.
</p>

<div class="email-alert email-alert-success">
    <strong>Email Berhasil Terkirim!</strong>
</div>

<div class="email-button-group">
    <a href="{{ url('/') }}" class="email-button">Buka WebBakti</a>
</div>

<p class="email-paragraph" style="text-align: center; margin-top: 16px;">
    Anda menerima email ini karena melakukan test pengiriman email melalui aplikasi WebBakti.
</p>
```

- [ ] **Step 2: Replace `test.blade.php`**

```blade
@extends('emails.layout', ['title' => 'Test Email - WebBakti'])

<div class="email-category-label">Test Email</div>
<div class="email-title">Selamat! Email Terkirim</div>

<p class="email-paragraph">
    Email sandbox Anda berhasil dikonfigurasi dan bekerja dengan sempurna.
</p>

<div class="email-alert email-alert-success">
    <strong>Konfigurasi Email Berhasil.</strong> Email ini dikirim melalui mail driver Anda dan berhasil diterima.
</div>

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Waktu Pengiriman</div>
        <div class="email-info-value">{{ now()->format('d M Y, H:i:s') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Environment</div>
        <div class="email-info-value">{{ config('app.env') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Aplikasi</div>
        <div class="email-info-value">{{ config('app.name') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">URL</div>
        <div class="email-info-value">{{ config('app.url') }}</div>
    </div>
</div>

<p class="email-paragraph"><strong>Pesan Test:</strong> {{ $message ?? 'Ini adalah email test dari WebBakti' }}</p>

<p class="email-paragraph">
    Email ini membuktikan bahwa sistem email Anda sudah siap untuk:
</p>

<ul class="email-list">
    <li><strong>Email Notifikasi</strong> - Untuk berbagai event aplikasi</li>
    <li><strong>Verifikasi Email</strong> - Untuk registrasi user</li>
    <li><strong>Reset Password</strong> - Untuk pemulihan akun</li>
    <li><strong>Laporan & Feedback</strong> - Untuk komunikasi user-supervisor</li>
    <li><strong>Pengingat & Deadline</strong> - Untuk task dan deadline reminder</li>
</ul>

<div class="email-button-group">
    <a href="{{ url('/') }}" class="email-button">Kembali ke Aplikasi</a>
    <a href="https://mailtrap.io" class="email-button email-button-secondary" target="_blank">Buka Mailtrap Dashboard</a>
</div>

<hr class="email-divider">

<div class="email-alert email-alert-info">
    <strong>Langkah Selanjutnya:</strong>
    <ul class="email-list" style="margin-top: 8px;">
        <li>Periksa inbox di Mailtrap untuk melihat email ini</li>
        <li>Test fitur lain seperti reset password dan notifikasi</li>
        <li>Verifikasi bahwa email terformat dengan baik di berbagai client</li>
        <li>Sebelum production, ganti provider email ke SendGrid, AWS SES, atau Gmail</li>
    </ul>
</div>
```

- [ ] **Step 3: Render-check both files**

Run:
```bash
php artisan tinker --execute="file_put_contents('storage/app/preview.html', view('emails.test_simple')->render());"
```
Open `storage/app/preview.html`. Expected: matches the corporate style, no emoji, secondary button (white with navy border) visible next to primary button.

Run:
```bash
php artisan tinker --execute="file_put_contents('storage/app/preview2.html', view('emails.test', ['message' => 'Contoh pesan test'])->render());"
```
Open `storage/app/preview2.html`. Expected: same, including the info-row block for environment details and the two-button group (primary + secondary side by side, or stacked on mobile).

- [ ] **Step 4: Commit**

```bash
git add resources/views/emails/test_simple.blade.php resources/views/emails/test.blade.php
git commit -m "refactor: Update test email templates to corporate style"
```

---

### Task 8: End-to-end send verification and cleanup

**Files:** none (verification only, plus temp file cleanup)

- [ ] **Step 1: Attempt a real send via the test command**

Run:
```bash
php artisan email:test --to=admin@webbakti.local --message="Redesign verification"
```

This requires working `MAIL_*` config in `.env`. If it succeeds, check the actual inbox (Mailtrap or configured provider) to confirm the redesign renders correctly in a real email client — this is the most reliable cross-client check since email client rendering can differ from a browser preview (particularly around the `<table>`-based header on Outlook).

If it fails because there's no mail driver configured in this environment, that's expected in a sandbox/dev setup without SMTP credentials — skip to Step 2. Do not treat a config-related failure here as a bug in this plan's changes.

- [ ] **Step 2: Run the existing test suite to confirm no functional regression**

```bash
php artisan test --filter=EmailVerificationTest
php artisan test --filter=ProfileTest
```
Expected: PASS (these tests assert mail was sent/queued, not markup content, so they should be unaffected by CSS/HTML changes — this just confirms the Blade changes didn't break rendering entirely, e.g. no syntax errors).

- [ ] **Step 3: Remove temporary preview files**

```bash
rm -f storage/app/preview.html storage/app/preview2.html
```

- [ ] **Step 4: Update the redesign documentation**

The existing `docs/EMAIL_LAYOUT_REDESIGN.md` describes the now-superseded "Minimalist & Spacious" version. Add a note at the very top of that file (don't rewrite the whole doc — it's a historical record of v2.0):

```bash
```

Use the Edit tool (not this plan) to prepend this line right after the `# Email Layout Redesign - Minimalist & Spacious` heading in `docs/EMAIL_LAYOUT_REDESIGN.md`:

```markdown
> **Superseded 1 Agustus 2026** — replaced by the Corporate/Formal navy design. See `docs/superpowers/specs/2026-08-01-email-template-corporate-redesign-design.md`.
```

- [ ] **Step 5: Commit the doc note**

```bash
git add docs/EMAIL_LAYOUT_REDESIGN.md
git commit -m "docs: Note that minimalist email design was superseded by corporate redesign"
```

---

## Self-Review Notes

- **Spec coverage:** header (Task 1), category label (Tasks 2–7), info-row replacement of `.email-section` (Tasks 2–6), alert restyle + plain-language lead-ins (Tasks 2–6), button sharp corners + uppercase (Task 1 CSS, used everywhere), grade box solid navy (Task 5), footer sizing (Task 1), emoji removal (Tasks 2–7), Arial font (Task 1), sharp corners overall (Task 1), mobile header stacking (Task 1 media query) — all covered.
- **Out-of-scope items confirmed not touched:** no Mail class changes, no new triggers, no dark mode — none of the 8 tasks modify `app/Mail/` beyond using `TestEmail` read-only in Task 8's verification step.
- **Class name consistency:** `.email-category-label`, `.email-info-box`, `.email-info-row`, `.email-info-label`, `.email-info-value` are defined once in Task 1 and reused identically (same class names, same nesting) across Tasks 2–7. `.email-button-secondary` is defined in Task 1 and used in Task 7's `test.blade.php` — verified it's the only file using that class (confirmed via grep during planning).
