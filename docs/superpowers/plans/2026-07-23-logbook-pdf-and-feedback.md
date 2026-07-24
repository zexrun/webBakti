# Logbook PDF Export and Feedback Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the logbook review page's raw `window.print()` button with real PDF exports (single logbook + filtered multi-logbook recap), and replace the decorative `mailto:` feedback button with a real in-app feedback flow (persisted on the logbook, notifies the student via mail + in-app), following this codebase's existing `SubmissionGraded` notification pattern and classic-document PDF style.

**Architecture:** Two new Blade PDF views (`logbook-pdf.blade.php`, `logbook-recap-pdf.blade.php`) matching the existing `certificate-pdf.blade.php`/`grades-pdf.blade.php` house style. `LogbookController` gains `exportPdf()`, `exportRecapPdf()`, and `sendFeedback()`, plus a shared private filter helper so `index()` and `exportRecapPdf()` never drift out of sync. A migration adds `feedback`/`feedback_at` to `logbooks`. A new `LogbookFeedbackGiven` notification (mirroring `SubmissionGraded`) handles the mail+database dispatch, with a new email Blade view extending the existing `emails.layout`.

**Tech Stack:** Laravel 12, DomPDF (`barryvdh/laravel-dompdf`, already used), Laravel Notifications (already used), Inertia.js + React 19, Framer Motion (`motion/react`, already used for modals).

---

### Task 1: Migration — add feedback columns to logbooks

**Files:**
- Create: `database/migrations/2026_07_23_000005_add_feedback_to_logbooks_table.php`
- Modify: `app/Models/Logbook.php`

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
        Schema::table('logbooks', function (Blueprint $table) {
            $table->text('feedback')->nullable();
            $table->timestamp('feedback_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn(['feedback', 'feedback_at']);
        });
    }
};
```

- [ ] **Step 2: Update the Logbook model**

Change `app/Models/Logbook.php` from:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'title',
        'activity_date',
        'start_time',
        'end_time',
        'description',
        'feeling',
        'file_path',
        'is_verified',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
```

to:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'title',
        'activity_date',
        'start_time',
        'end_time',
        'description',
        'feeling',
        'file_path',
        'is_verified',
        'feedback',
        'feedback_at',
    ];

    protected function casts(): array
    {
        return [
            'feedback_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
```

- [ ] **Step 3: Run the migration and verify**

```bash
php artisan migrate
php artisan tinker --execute="echo in_array('feedback', Schema::getColumnListing('logbooks')) && in_array('feedback_at', Schema::getColumnListing('logbooks')) ? 'columns present' : 'MISSING';"
```
Expected: `columns present`.

- [ ] **Step 4: Verify syntax**

```bash
php -l app/Models/Logbook.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_07_23_000005_add_feedback_to_logbooks_table.php app/Models/Logbook.php
git commit -m "feat: Add feedback/feedback_at columns to logbooks table"
```

---

### Task 2: LogbookFeedbackGiven notification + email view

**Files:**
- Create: `app/Notifications/LogbookFeedbackGiven.php`
- Create: `resources/views/emails/logbook-feedback.blade.php`

- [ ] **Step 1: Write the notification**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Logbook;

class LogbookFeedbackGiven extends Notification
{
    use Queueable;

    public $logbook;

    public function __construct(Logbook $logbook)
    {
        $this->logbook = $logbook;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Feedback Baru untuk Logbook: ' . $this->logbook->title)
            ->view('emails.logbook-feedback', [
                'logbook' => $this->logbook,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'logbook_id' => $this->logbook->id,
            'logbook_title' => $this->logbook->title,
            'activity_date' => $this->logbook->activity_date,
            'feedback' => $this->logbook->feedback,
            'supervisor_name' => $this->logbook->student->supervisor->user->name,
            'message' => 'Pembimbing memberikan feedback pada logbook "' . $this->logbook->title . '"',
            'action_url' => url('/student/logbooks/' . $this->logbook->id),
            'type' => 'logbook_feedback',
            'priority' => 'normal',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
```

- [ ] **Step 2: Write the email view**

```blade
@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Pembimbing Anda telah memberikan feedback pada logbook berikut:</p>
</div>

<div class="section">
    <div class="section-title">📋 Detail Logbook</div>
    <div class="info-row">
        <span class="info-label">Judul:</span>
        <span class="info-value">{{ $logbook->title }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tanggal:</span>
        <span class="info-value">{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Pembimbing:</span>
        <span class="info-value">{{ $logbook->student->supervisor->user->name }}</span>
    </div>
</div>

<div class="section">
    <div class="section-title">💬 Feedback Pembimbing</div>
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #667eea; border-radius: 4px;">
        <p>{{ $logbook->feedback }}</p>
    </div>
</div>

<div class="divider"></div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/logbooks/' . $logbook->id) }}" class="button">
            Lihat Logbook
        </a>
    </p>
</div>
@endsection
```

- [ ] **Step 3: Verify syntax**

```bash
php -l app/Notifications/LogbookFeedbackGiven.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 4: Commit**

```bash
git add app/Notifications/LogbookFeedbackGiven.php resources/views/emails/logbook-feedback.blade.php
git commit -m "feat: Add LogbookFeedbackGiven notification and email template"
```

---

### Task 3: Logbook PDF template (single logbook)

**Files:**
- Create: `resources/views/supervisor/pdf/logbook-pdf.blade.php`

- [ ] **Step 1: Write the template**

Follows the exact same house style established by `grades-pdf.blade.php` (kop surat header, Times New Roman, colon-aligned detail rows, black-ruled sections) — same CSS block reused verbatim for the header/title/detail-table/footer, with logbook-specific content sections replacing the grades table.

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logbook - {{ $logbook->title }} - {{ $logbook->student->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.5;
            font-size: 11pt;
        }

        .report-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 15mm 20mm;
        }

        .document-header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .ministry-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .agency-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 2px;
        }

        .agency-address {
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
            margin-top: 4px;
        }

        .document-title {
            text-align: center;
            margin: 14px 0 4px;
        }

        .title-main {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .document-number {
            font-size: 10pt;
            margin-top: 4px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 14px 0 6px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .detail-table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .detail-label {
            width: 140px;
        }

        .detail-separator {
            width: 15px;
            text-align: center;
        }

        .description-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 6px;
            min-height: 40mm;
            white-space: pre-wrap;
        }

        .attachment-photo {
            margin-top: 8px;
            max-width: 100%;
            max-height: 90mm;
            border: 1px solid #000;
        }

        .signature-section {
            margin-top: 24px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-left {
            width: 50%;
        }

        .signature-right {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 10pt;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }

        .signature-nip {
            font-size: 9pt;
            margin-top: 2px;
        }

        .document-footer {
            margin-top: 14px;
            border-top: 1px solid #999;
            padding-top: 6px;
            font-size: 8pt;
            color: #444;
            text-align: center;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="document-header">
            <div class="ministry-name">Kementerian Komunikasi dan Digital Republik Indonesia</div>
            <div class="agency-name">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="agency-address">
                Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930<br>
                Telp. 021-31936590 (Hunting) &middot; www.baktikominfo.id
            </div>
        </div>

        @php
            $logNumber = str_pad(($logbook->student->id * 37 + $logbook->id) % 900 + 100, 3, '0', STR_PAD_LEFT);
        @endphp
        <div class="document-title">
            <div class="title-main">Logbook Kegiatan Magang</div>
            <div class="document-number">Nomor: {{ $logNumber }}/BAKTI/SDA/{{ date('m/Y') }}</div>
        </div>

        <div class="section-title">Data Mahasiswa</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Nama Lengkap</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->user->name }}</td>
            </tr>
            <tr>
                <td class="detail-label">NIM</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Pembimbing</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->supervisor->user->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">Detail Kegiatan</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Judul Kegiatan</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->title }}</td>
            </tr>
            <tr>
                <td class="detail-label">Tanggal</td>
                <td class="detail-separator">:</td>
                <td>{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="detail-label">Waktu</td>
                <td class="detail-separator">:</td>
                <td>{{ substr($logbook->start_time, 0, 5) }} &ndash; {{ substr($logbook->end_time, 0, 5) }}</td>
            </tr>
            <tr>
                <td class="detail-label">Perasaan</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->feeling }}</td>
            </tr>
            <tr>
                <td class="detail-label">Status</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->is_verified ? 'Telah Diverifikasi' : 'Belum Diverifikasi' }}</td>
            </tr>
        </table>

        <div class="section-title">Deskripsi Kegiatan</div>
        <div class="description-box">{{ $logbook->description }}</div>

        @if($logbook->file_path)
            <div class="section-title">Dokumentasi Kegiatan</div>
            <img class="attachment-photo" src="{{ public_path('storage/' . $logbook->file_path) }}" alt="Dokumentasi Kegiatan">
        @endif

        @if($logbook->feedback)
            <div class="section-title">Feedback Pembimbing</div>
            <div class="description-box">{{ $logbook->feedback }}</div>
        @endif

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-left"></td>
                    <td class="signature-right">
                        <div class="signature-title">Mengetahui,<br>Pembimbing Lapangan</div>
                        <div class="signature-name">{{ $logbook->student->supervisor->user->name ?? '' }}</div>
                        <div class="signature-nip">NIP. {{ $logbook->student->supervisor->nip ?? '________________' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ now()->translatedFormat('d F Y H:i') }} &middot;
            ID Dokumen: LOG-{{ strtoupper(substr(md5($logbook->id . $logbook->activity_date), 0, 8)) }}
        </div>
    </div>
</body>
</html>
```

- [ ] **Step 2: Manual verification via tinker (render only, no controller yet)**

This step is deferred to Task 4 Step 3, once the controller method exists to supply real data — creating a template with no way to invoke it yet isn't independently testable.

- [ ] **Step 3: Commit**

```bash
git add resources/views/supervisor/pdf/logbook-pdf.blade.php
git commit -m "feat: Add single-logbook PDF template"
```

---

### Task 4: Logbook recap PDF template

**Files:**
- Create: `resources/views/supervisor/pdf/logbook-recap-pdf.blade.php`

- [ ] **Step 1: Write the template**

Expects a `$logbooksByStudent` variable: a `Collection` grouped by student, where each key is a `Student` model and each value is a `Collection` of that student's `Logbook`s (already filtered/ordered by the controller), plus `$dateFrom`/`$dateTo` (nullable strings) and `$generatedAt` (Carbon) for the header.

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Logbook</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.5;
            font-size: 11pt;
        }

        .report-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 15mm 20mm;
        }

        .document-header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .ministry-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .agency-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 2px;
        }

        .agency-address {
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
            margin-top: 4px;
        }

        .document-title {
            text-align: center;
            margin: 14px 0 4px;
        }

        .title-main {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .document-subtitle {
            font-size: 10pt;
            margin-top: 4px;
        }

        .student-section {
            margin-top: 16px;
        }

        .student-section:first-of-type {
            margin-top: 10px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        .recap-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
        }

        .recap-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9.5pt;
            vertical-align: top;
        }

        .col-date { width: 14%; text-align: center; }
        .col-activity { width: 30%; }
        .col-time { width: 14%; text-align: center; }
        .col-feeling { width: 14%; text-align: center; }
        .col-status { width: 14%; text-align: center; }
        .col-feedback { width: 14%; }

        .empty-state {
            text-align: center;
            padding: 15px;
            font-style: italic;
        }

        .document-footer {
            margin-top: 14px;
            border-top: 1px solid #999;
            padding-top: 6px;
            font-size: 8pt;
            color: #444;
            text-align: center;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="document-header">
            <div class="ministry-name">Kementerian Komunikasi dan Digital Republik Indonesia</div>
            <div class="agency-name">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="agency-address">
                Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930<br>
                Telp. 021-31936590 (Hunting) &middot; www.baktikominfo.id
            </div>
        </div>

        <div class="document-title">
            <div class="title-main">Rekap Logbook Kegiatan Magang</div>
            <div class="document-subtitle">
                Periode:
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') : 'Semua Tanggal' }}
                s.d.
                {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') : 'Sekarang' }}
            </div>
        </div>

        @forelse($logbooksByStudent as $student => $logbooks)
            <div class="student-section">
                <div class="section-title">{{ $student->user->name }} ({{ $student->nim ?? 'NIM belum diisi' }})</div>
                <table class="recap-table">
                    <thead>
                        <tr>
                            <th class="col-date">Tanggal</th>
                            <th class="col-activity">Kegiatan</th>
                            <th class="col-time">Waktu</th>
                            <th class="col-feeling">Perasaan</th>
                            <th class="col-status">Status</th>
                            <th class="col-feedback">Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $logbook)
                            <tr>
                                <td class="col-date">{{ \Carbon\Carbon::parse($logbook->activity_date)->format('d/m/Y') }}</td>
                                <td class="col-activity">{{ $logbook->title }}</td>
                                <td class="col-time">{{ substr($logbook->start_time, 0, 5) }}-{{ substr($logbook->end_time, 0, 5) }}</td>
                                <td class="col-feeling">{{ $logbook->feeling }}</td>
                                <td class="col-status">{{ $logbook->is_verified ? 'Dilihat' : 'Belum' }}</td>
                                <td class="col-feedback">{{ $logbook->feedback ? 'Ada' : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="empty-state">Tidak ada logbook yang sesuai dengan filter yang dipilih.</div>
        @endforelse

        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ $generatedAt->translatedFormat('d F Y H:i') }}
        </div>
    </div>
</body>
</html>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/supervisor/pdf/logbook-recap-pdf.blade.php
git commit -m "feat: Add logbook recap PDF template"
```

---

### Task 5: LogbookController — filter helper, exportPdf, exportRecapPdf, sendFeedback

**Files:**
- Modify: `app/Http/Controllers/Supervisor/LogbookController.php`

- [ ] **Step 1: Replace the full file**

```php
<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Notifications\LogbookFeedbackGiven;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LogbookController extends Controller
{
    public function index(Request $request): Response
    {
        $logbooks = $this->filteredQuery($request)
            ->latest('activity_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Supervisor/Logbooks/Index', [
            'logbooks' => $logbooks,
            'students' => Auth::user()->supervisor->students()->with('user')->get(),
            'filters' => [
                'student_id' => $request->get('student_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ],
        ]);
    }

    public function show(Logbook $logbook): Response
    {
        $logbook->load('student.user');

        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        if (!$logbook->is_verified) {
            $logbook->update(['is_verified' => true]);
        }

        return Inertia::render('Supervisor/Logbooks/Show', compact('logbook'));
    }

    public function verify(Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $logbook->update(['is_verified' => true]);

        return back()->with('success', 'Laporan berhasil ditandai sebagai telah dilihat');
    }

    public function sendFeedback(Request $request, Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'feedback' => 'required|string|max:2000',
        ]);

        $logbook->update([
            'feedback' => $request->feedback,
            'feedback_at' => now(),
        ]);

        $logbook->load('student.user', 'student.supervisor.user');
        $logbook->student->user->notify(new LogbookFeedbackGiven($logbook));

        return back()->with('success', 'Feedback berhasil dikirim');
    }

    public function exportPdf(Logbook $logbook)
    {
        $isAuthorized = Auth::user()->supervisor->students()->where('id', $logbook->student_id)->exists();

        if (!$isAuthorized) {
            abort(403, 'AKSES DITOLAK');
        }

        $logbook->load('student.user', 'student.supervisor.user');

        $pdf = Pdf::loadView('supervisor.pdf.logbook-pdf', compact('logbook'));

        return $pdf->stream('logbook-' . $logbook->student->user->name . '-' . $logbook->activity_date . '.pdf');
    }

    public function exportRecapPdf(Request $request)
    {
        $logbooks = $this->filteredQuery($request)
            ->with('student.user')
            ->orderBy('student_id')
            ->orderBy('activity_date')
            ->get();

        $logbooksByStudent = collect();
        foreach ($logbooks->groupBy('student_id') as $studentGroup) {
            $logbooksByStudent->put($studentGroup->first()->student, $studentGroup);
        }

        $pdf = Pdf::loadView('supervisor.pdf.logbook-recap-pdf', [
            'logbooksByStudent' => $logbooksByStudent,
            'dateFrom' => $request->get('date_from'),
            'dateTo' => $request->get('date_to'),
            'generatedAt' => now(),
        ]);

        return $pdf->stream('rekap-logbook-' . now()->format('Y-m-d') . '.pdf');
    }

    private function filteredQuery(Request $request)
    {
        $studentIds = Auth::user()->supervisor->students()->pluck('id');

        $query = Logbook::whereIn('student_id', $studentIds)->with('student.user');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->get('student_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->get('date_to'));
        }

        return $query;
    }
}
```

- [ ] **Step 2: Verify syntax**

```bash
php -l app/Http/Controllers/Supervisor/LogbookController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 3: Verify both PDF templates render via tinker**

```bash
php artisan tinker --execute="
\$logbook = App\Models\Logbook::with('student.user', 'student.supervisor.user')->first();
if (!\$logbook) { echo 'NO LOGBOOKS IN DB - seed data first'; exit; }
try {
    \$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('supervisor.pdf.logbook-pdf', compact('logbook'));
    \$output = \$pdf->output();
    echo 'Single logbook PDF OK, bytes=' . strlen(\$output) . PHP_EOL;
} catch (\Throwable \$e) {
    echo 'ERROR (single): ' . \$e->getMessage() . PHP_EOL;
}

\$student = \$logbook->student;
\$keyed = collect([\$student => collect([\$logbook])]);
try {
    \$pdf2 = Barryvdh\DomPDF\Facade\Pdf::loadView('supervisor.pdf.logbook-recap-pdf', [
        'logbooksByStudent' => \$keyed,
        'dateFrom' => null,
        'dateTo' => null,
        'generatedAt' => now(),
    ]);
    \$output2 = \$pdf2->output();
    echo 'Recap PDF OK, bytes=' . strlen(\$output2) . PHP_EOL;
} catch (\Throwable \$e) {
    echo 'ERROR (recap): ' . \$e->getMessage() . PHP_EOL;
}
"
```
Expected: both print `... OK, bytes=<some number>` with no `ERROR` lines. If there are no logbooks in the seeded database, seed them first (this repo's seeders already include a `LogbookSeeder`) rather than skipping this check.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Supervisor/LogbookController.php
git commit -m "feat: Add exportPdf, exportRecapPdf, sendFeedback to LogbookController"
```

---

### Task 6: Routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Add the three new routes**

Change:
```php
    // Rute untuk laporan harian (hanya melihat)
    Route::get('/logbooks', [SupervisorLogbookController::class, 'index'])->name('logbooks.index');
    Route::get('/logbooks/{logbook}', [SupervisorLogbookController::class, 'show'])->name('logbooks.show');
```
to:
```php
    // Rute untuk laporan harian (hanya melihat)
    Route::get('/logbooks', [SupervisorLogbookController::class, 'index'])->name('logbooks.index');
    Route::get('/logbooks/export-recap-pdf', [SupervisorLogbookController::class, 'exportRecapPdf'])->name('logbooks.export-recap-pdf');
    Route::get('/logbooks/{logbook}', [SupervisorLogbookController::class, 'show'])->name('logbooks.show');
    Route::get('/logbooks/{logbook}/export-pdf', [SupervisorLogbookController::class, 'exportPdf'])->name('logbooks.export-pdf');
    Route::post('/logbooks/{logbook}/feedback', [SupervisorLogbookController::class, 'sendFeedback'])->name('logbooks.feedback');
```

(The `export-recap-pdf` route is placed before the `{logbook}` wildcard route so Laravel doesn't try to resolve "export-recap-pdf" as a `{logbook}` route-model-binding id.)

- [ ] **Step 2: Regenerate ziggy.js and verify routes**

```bash
php artisan ziggy:generate
php artisan route:list --name=logbooks
```
Expected: shows `supervisor.logbooks.index`, `supervisor.logbooks.export-recap-pdf`, `supervisor.logbooks.show`, `supervisor.logbooks.export-pdf`, `supervisor.logbooks.feedback`, plus the existing `student.logbooks.*` resource routes untouched.

- [ ] **Step 3: Verify syntax**

```bash
php -l routes/web.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 4: Commit**

Note: `resources/js/ziggy.js` reflects the local machine's `APP_URL` and may already be a held/uncommitted local file in this working tree (per this session's earlier `.env`/CORS investigation) — check `git status` first. If it was already modified/untracked before this task, do NOT commit it as part of this task; only commit `routes/web.php`.

```bash
git add routes/web.php
git commit -m "feat: Register logbook export and feedback routes"
```

---

### Task 7: Frontend — Supervisor Show.jsx (print link + feedback modal)

**Files:**
- Modify: `resources/js/Pages/Supervisor/Logbooks/Show.jsx`

- [ ] **Step 1: Replace the full file**

```jsx
import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { AnimatePresence, motion } from 'motion/react'
import { ArrowLeft, Clock, Smile, FileText, Image as ImageIcon, Printer, MessageSquare, CheckCircle2, X } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import UserCell from '@/Components/UserCell'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Textarea } from '@/Components/ui/textarea'

const feelingEmoji = {
  Senang: '😊', Biasa: '😐', Sedih: '😢', Bingung: '😕', Semangat: '🤩',
  Lelah: '😴', 'Biasa Saja': '😐', 'Menemukan Kendala': '😥',
}

function durationLabel(start, end) {
  const [sh, sm] = start.split(':').map(Number)
  const [eh, em] = end.split(':').map(Number)
  const minutes = eh * 60 + em - (sh * 60 + sm)
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return hours > 0 ? `${hours} jam ${mins} menit` : `${mins} menit`
}

export default function Show({ logbook }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const [feedbackOpen, setFeedbackOpen] = useState(false)
  const [feedbackText, setFeedbackText] = useState(logbook.feedback ?? '')
  const [submitting, setSubmitting] = useState(false)

  function openFeedbackModal() {
    setFeedbackText(logbook.feedback ?? '')
    setFeedbackOpen(true)
  }

  function submitFeedback(e) {
    e.preventDefault()
    setSubmitting(true)
    router.post(r('supervisor.logbooks.feedback', logbook.id), { feedback: feedbackText }, {
      preserveScroll: true,
      onFinish: () => {
        setSubmitting(false)
        setFeedbackOpen(false)
      },
    })
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <PageHeader
          title="Detail Logbook"
          description="Aktivitas harian mahasiswa bimbingan"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <a href={r('supervisor.logbooks.export-pdf', logbook.id)} target="_blank" rel="noreferrer">
                  <Printer /> Cetak
                </a>
              </Button>
              <Button size="sm" onClick={openFeedbackModal}>
                <MessageSquare /> Kirim Feedback
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link href={r('supervisor.logbooks.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
            </>
          }
        />

        <Card>
          <div className="flex flex-col gap-4 border-b border-border p-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <Badge variant="outline" className="mb-2 tabular-nums">
                {new Date(logbook.activity_date).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}
              </Badge>
              <h2 className="font-heading text-lg font-semibold text-foreground">{logbook.title}</h2>
              <div className="mt-3">
                <UserCell name={logbook.student?.user?.name} subtitle={logbook.student?.nim ?? 'NIM belum diisi'} />
              </div>
            </div>
            <Badge variant="success">
              <CheckCircle2 /> Telah Diverifikasi
            </Badge>
          </div>

          <CardContent className="space-y-5 p-5">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div className="rounded-lg bg-muted p-4">
                <p className="flex items-center gap-2 text-sm font-medium text-foreground">
                  <Clock className="h-4 w-4 text-muted-foreground" /> Waktu Kegiatan
                </p>
                <p className="mt-1 text-lg font-bold tabular-nums text-foreground">
                  {logbook.start_time.slice(0, 5)}–{logbook.end_time.slice(0, 5)}
                </p>
                <p className="text-xs text-muted-foreground">Durasi: {durationLabel(logbook.start_time, logbook.end_time)}</p>
              </div>

              <div className="rounded-lg bg-muted p-4">
                <p className="flex items-center gap-2 text-sm font-medium text-foreground">
                  <Smile className="h-4 w-4 text-muted-foreground" /> Perasaan
                </p>
                <p className="mt-1 flex items-center gap-2">
                  <span className="text-2xl" aria-hidden="true">{feelingEmoji[logbook.feeling] ?? '😊'}</span>
                  <span className="text-sm font-semibold text-foreground">{logbook.feeling}</span>
                </p>
              </div>
            </div>

            <div>
              <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                <FileText className="h-4 w-4 text-muted-foreground" /> Deskripsi Kegiatan
              </p>
              <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                {logbook.description}
              </div>
            </div>

            {logbook.file_path && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <ImageIcon className="h-4 w-4 text-muted-foreground" /> Foto Lampiran
                </p>
                <a href={`/storage/${logbook.file_path}`} target="_blank" rel="noreferrer">
                  <img
                    src={`/storage/${logbook.file_path}`}
                    alt="Foto Kegiatan"
                    className="w-full rounded-lg border border-border object-cover"
                  />
                </a>
              </div>
            )}

            {logbook.feedback && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <MessageSquare className="h-4 w-4 text-muted-foreground" /> Feedback Pembimbing
                </p>
                <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                  {logbook.feedback}
                </div>
                {logbook.feedback_at && (
                  <p className="mt-1 text-xs text-muted-foreground">
                    Dikirim {new Date(logbook.feedback_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  </p>
                )}
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      <AnimatePresence>
        {feedbackOpen && (
          <motion.div
            className="fixed inset-0 z-50 bg-black/50"
            onClick={(e) => e.target === e.currentTarget && setFeedbackOpen(false)}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.15 }}
          >
            <div className="flex min-h-screen items-center justify-center p-4">
              <motion.div
                className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg"
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                transition={{ duration: 0.15 }}
              >
                <div className="flex items-center justify-between gap-4 border-b border-border px-6 py-4">
                  <h3 className="text-lg font-semibold text-foreground">Kirim Feedback</h3>
                  <button
                    type="button"
                    onClick={() => setFeedbackOpen(false)}
                    aria-label="Tutup"
                    className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
                  >
                    <X className="h-5 w-5" />
                  </button>
                </div>

                <form onSubmit={submitFeedback} className="space-y-4 p-6">
                  <Textarea
                    rows={6}
                    value={feedbackText}
                    onChange={(e) => setFeedbackText(e.target.value)}
                    placeholder="Tulis feedback untuk logbook ini..."
                    required
                  />
                  <div className="flex gap-2">
                    <Button type="button" variant="outline" onClick={() => setFeedbackOpen(false)} className="flex-1">
                      Batal
                    </Button>
                    <Button type="submit" disabled={submitting} className="flex-1">
                      {submitting ? 'Mengirim...' : 'Kirim Feedback'}
                    </Button>
                  </div>
                </form>
              </motion.div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </SupervisorLayout>
  )
}
```

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Supervisor/Logbooks/Show.jsx
git commit -m "feat: Replace print/mailto buttons with PDF export link and feedback modal"
```

---

### Task 8: Frontend — Supervisor Index.jsx (filter bar + recap export)

**Files:**
- Modify: `resources/js/Pages/Supervisor/Logbooks/Index.jsx`

- [ ] **Step 1: Add the filter bar and recap export button**

Add new imports at the top (alongside existing ones):
```jsx
import { useState } from 'react'
import { router } from '@inertiajs/react'
```
(if `router` isn't already imported — check the existing import line `import { Link } from '@inertiajs/react'` and extend it to `import { Link, router } from '@inertiajs/react'`)

Add:
```jsx
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Printer } from 'lucide-react'
```

Change the component signature and add filter state:
```jsx
export default function Index({ logbooks, students, filters }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const [studentFilter, setStudentFilter] = useState(filters?.student_id ? String(filters.student_id) : 'all')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('supervisor.logbooks.index'), {
      ...(studentFilter !== 'all' ? { student_id: studentFilter } : {}),
      ...(form.date_from.value ? { date_from: form.date_from.value } : {}),
      ...(form.date_to.value ? { date_to: form.date_to.value } : {}),
    })
  }

  function exportRecapUrl() {
    const params = new URLSearchParams()
    if (studentFilter !== 'all') params.set('student_id', studentFilter)
    if (filters?.date_from) params.set('date_from', filters.date_from)
    if (filters?.date_to) params.set('date_to', filters.date_to)
    const query = params.toString()
    return r('supervisor.logbooks.export-recap-pdf') + (query ? `?${query}` : '')
  }
```

Insert the filter form + export button right after `<PageHeader ... />` and before the stat card grid:
```jsx
        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label htmlFor="student_id" className="block text-sm font-medium text-foreground">Mahasiswa</label>
            <Select value={studentFilter} onValueChange={setStudentFilter}>
              <SelectTrigger id="student_id" className="w-52">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Mahasiswa</SelectItem>
                {students.map((student) => (
                  <SelectItem key={student.id} value={String(student.id)}>
                    {student.user?.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label htmlFor="date_from" className="block text-sm font-medium text-foreground">Dari Tanggal</label>
            <Input id="date_from" name="date_from" type="date" defaultValue={filters?.date_from ?? ''} className="w-44" />
          </div>
          <div className="space-y-2">
            <label htmlFor="date_to" className="block text-sm font-medium text-foreground">Sampai Tanggal</label>
            <Input id="date_to" name="date_to" type="date" defaultValue={filters?.date_to ?? ''} className="w-44" />
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button asChild variant="outline">
            <a href={exportRecapUrl()} target="_blank" rel="noreferrer">
              <Printer /> Cetak Hasil Filter
            </a>
          </Button>
        </form>
```

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Supervisor/Logbooks/Index.jsx
git commit -m "feat: Add student/date filter and recap PDF export to logbooks Index"
```

---

### Task 9: Frontend — Student Show.jsx (display feedback)

**Files:**
- Modify: `resources/js/Pages/Student/Logbooks/Show.jsx`

- [ ] **Step 1: Add the feedback display section**

Add `MessageSquare` to the existing lucide-react import:
```jsx
import { ArrowLeft, Clock, Smile, FileText, Image as ImageIcon, Pencil, MessageSquare } from 'lucide-react'
```

Insert a new block right after the existing "Foto Lampiran" / attachment section (i.e., right before the closing `</CardContent>`):
```jsx
            {logbook.feedback && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <MessageSquare className="h-4 w-4 text-muted-foreground" /> Feedback Pembimbing
                </p>
                <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                  {logbook.feedback}
                </div>
                {logbook.feedback_at && (
                  <p className="mt-1 text-xs text-muted-foreground">
                    Dikirim {new Date(logbook.feedback_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  </p>
                )}
              </div>
            )}
```

- [ ] **Step 2: Manual verification**

```bash
npm run build
```
Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Student/Logbooks/Show.jsx
git commit -m "feat: Display supervisor feedback on the student logbook detail page"
```

---

### Task 10: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax/build check every touched file**

```bash
php -l app/Models/Logbook.php
php -l app/Notifications/LogbookFeedbackGiven.php
php -l app/Http/Controllers/Supervisor/LogbookController.php
php -l routes/web.php
npm run build
```
Expected: all succeed with no errors.

- [ ] **Step 2: Confirm routes**

```bash
php artisan route:list --name=logbooks
```
Expected: `supervisor.logbooks.index`, `supervisor.logbooks.export-recap-pdf`, `supervisor.logbooks.show`, `supervisor.logbooks.export-pdf`, `supervisor.logbooks.feedback`, plus all `student.logbooks.*` resource routes.

- [ ] **Step 3: Manual browser check — single logbook PDF**

As a supervisor, open a logbook detail page, click "Cetak", confirm a PDF opens in a new tab with correct student/activity/description/feedback (if any)/photo (if any) content, matching the classic document style of the certificate/grades PDFs.

- [ ] **Step 4: Manual browser check — recap PDF**

On the Logbooks Index page, apply a student and/or date filter, click "Cetak Hasil Filter", confirm the resulting PDF only includes logbooks matching the filter, grouped correctly per student.

- [ ] **Step 5: Manual browser check — feedback flow**

As a supervisor, open a logbook, click "Kirim Feedback", submit some text, confirm the modal closes and the feedback now displays on the page. Log in as the student who owns that logbook, confirm the feedback appears on their Show page, and confirm they received a database notification (check the notifications bell/page) and — if mail is actually configured to a real driver rather than `log` — an email.

- [ ] **Step 6: Commit any fixes found during manual verification**

Only if Steps 3-5 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
