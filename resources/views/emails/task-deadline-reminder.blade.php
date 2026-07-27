@extends('emails.layout', ['title' => 'Pengingat Deadline Tugas - WebBakti'])

@php
    if ($daysUntilDeadline === 0) {
        $title = '🚨 URGENT! Tugas Berakhir Hari Ini';
        $urgency = 'danger';
    } elseif ($daysUntilDeadline === 1) {
        $title = '⏰ PENTING! Tugas Berakhir Besok';
        $urgency = 'warning';
    } else {
        $title = '📅 Pengingat Deadline Tugas';
        $urgency = 'info';
    }
@endphp

<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Ini adalah pengingat penting bahwa salah satu tugas Anda akan segera berakhir. Pastikan Anda menyelesaikan dan mengumpulkan sebelum batas waktu.
</p>

@if($daysUntilDeadline === 0)
    <div class="email-alert email-alert-danger">
        <strong>🚨 URGENT!</strong> Tugas ini berakhir <strong>HARI INI</strong>. Segera selesaikan dan kumpulkan sebelum batas waktu!
    </div>
@elseif($daysUntilDeadline === 1)
    <div class="email-alert email-alert-warning">
        <strong>⏰ PENTING!</strong> Tugas ini berakhir <strong>BESOK</strong>. Pastikan Anda menyelesaikan dengan segera!
    </div>
@else
    <div class="email-alert email-alert-info">
        <strong>ℹ️ Pengingat Deadline</strong><br>
        Tugas ini berakhir dalam <strong>{{ $daysUntilDeadline }} hari</strong>. Mulai kerjakan sekarang agar tidak ketinggalan!
    </div>
@endif

<div class="email-section">
    <div class="email-section-title">📋 Detail Tugas</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Judul:</div>
            <div class="email-info-value">{{ $task->title }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Pembimbing:</div>
            <div class="email-info-value">{{ $task->supervisor->user->name }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Tipe:</div>
            <div class="email-info-value">{{ $task->type === 'harian' ? '📅 Tugas Harian' : '📑 Laporan Akhir' }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Batas Waktu:</div>
            <div class="email-info-value"><strong>{{ $task->due_date->format('d M Y, H:i') }}</strong></div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Waktu Tersisa:</div>
            <div class="email-info-value">
                @if($daysUntilDeadline === 0)
                    <strong style="color: #ef4444;">Hari ini (URGENT!)</strong>
                @elseif($daysUntilDeadline === 1)
                    <strong style="color: #f59e0b;">1 hari</strong>
                @else
                    <strong>{{ $daysUntilDeadline }} hari</strong>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="email-section">
    <div class="email-section-title">📝 Deskripsi Singkat</div>
    <p class="email-paragraph">{{ Str::limit($task->description, 300) }}</p>
</div>

@if($task->file_path)
<div class="email-section">
    <div class="email-section-title">📎 File Lampiran</div>
    <p class="email-paragraph">File lampiran tersedia untuk tugas ini. Silakan download dari aplikasi WebBakti untuk melihat detail lengkap.</p>
</div>
@endif

<div class="email-section">
    <div class="email-section-title">✅ Langkah-Langkah Menyelesaikan</div>
    <ol class="email-list">
        <li><strong>Baca</strong> deskripsi tugas dengan seksama</li>
        <li><strong>Download</strong> file lampiran (jika ada)</li>
        <li><strong>Kerjakan</strong> tugas sesuai dengan instruksi</li>
        <li><strong>Upload</strong> hasil pekerjaan Anda ke sistem</li>
        <li><strong>Verifikasi</strong> bahwa submission sudah berhasil diterima</li>
    </ol>
</div>

<div class="email-button-group">
    <a href="{{ url('/student/tasks/' . $task->id) }}" class="email-button">Buka Tugas Sekarang</a>
</div>

<div class="email-alert email-alert-warning">
    <strong>⚠️ Catatan Penting</strong>
    <ul class="email-list" style="margin-top: 10px;">
        <li>Pastikan Anda upload submission sebelum batas waktu berakhir</li>
        <li>Submission yang dikirim setelah deadline tidak akan diterima</li>
        <li>Jika ada kendala, hubungi pembimbing Anda sesegera mungkin</li>
    </ul>
</div>

<hr class="email-divider">

<p class="email-paragraph" style="text-align: center; font-size: 16px; font-weight: 600; color: #667eea;">
    Semangat mengerjakan tugas! Anda pasti bisa! 💪
</p>
