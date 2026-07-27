@extends('emails.layout', ['title' => 'Status Pengajuan Exception - WebBakti'])

@php
    $title = $status === 'approved' ? '✅ Pengajuan Disetujui' : '⚠️ Pengajuan Ditolak';
    $icon = $status === 'approved' ? '✓' : '✗';
@endphp

<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pengajuan exception (sakit/izin) Anda telah dikaji dan diproses oleh sistem.
</p>

@if($status === 'approved')
    <div class="email-alert email-alert-success">
        <strong>{{ $icon }} Pengajuan DISETUJUI</strong><br>
        Pengajuan exception Anda telah disetujui. Hari tersebut tidak akan dihitung sebagai ketidakhadiran dalam evaluasi magang Anda.
    </div>
@else
    <div class="email-alert email-alert-danger">
        <strong>{{ $icon }} Pengajuan DITOLAK</strong><br>
        Pengajuan exception Anda tidak memenuhi kriteria persetujuan.
        @if($exception->admin_notes)
            <br><strong>Alasan:</strong> {{ $exception->admin_notes }}
        @endif
    </div>
@endif

<div class="email-section">
    <div class="email-section-title">📋 Detail Pengajuan</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Tanggal:</div>
            <div class="email-info-value">
                {{ $exception->date ? \Carbon\Carbon::parse($exception->date)->format('d M Y') : 'N/A' }}
            </div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Tipe:</div>
            <div class="email-info-value">
                @switch($exception->type)
                    @case('sick')
                        🏥 Sakit
                        @break
                    @case('leave')
                        📋 Izin
                        @break
                    @default
                        {{ $exception->type ?? 'N/A' }}
                @endswitch
            </div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Alasan:</div>
            <div class="email-info-value">{{ $exception->reason ?? '-' }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Waktu Pengajuan:</div>
            <div class="email-info-value">{{ $exception->created_at->format('d M Y, H:i') }}</div>
        </div>
    </div>
</div>

@if($exception->supporting_document)
<div class="email-section">
    <div class="email-section-title">📎 Dokumen Pendukung</div>
    <p class="email-paragraph">Dokumen pendukung telah dicatat dan diverifikasi dalam sistem.</p>
</div>
@endif

@if($status === 'rejected')
<div class="email-alert email-alert-warning">
    <strong>💡 Apa yang Bisa Anda Lakukan?</strong>
    <ul class="email-list" style="margin-top: 10px;">
        <li>Mengajukan exception kembali dengan informasi atau dokumen yang lebih lengkap</li>
        <li>Menghubungi admin untuk mendiskusikan penolakan ini</li>
        <li>Mengajukan banding jika merasa ada kesalahan dalam penilaian</li>
    </ul>
</div>
@else
<div class="email-section">
    <div class="email-section-title">✅ Terima Kasih</div>
    <p class="email-paragraph">
        Status kehadiran Anda telah diperbarui dan sesuai dengan persetujuan exception ini. Anda dapat melihat detail di aplikasi WebBakti.
    </p>
</div>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/exceptions') }}" class="email-button">Lihat Pengajuan Saya</a>
</div>

@if($status === 'rejected')
<div class="email-alert email-alert-info">
    <strong>📞 Hubungi Admin</strong><br>
    Jika Anda memiliki pertanyaan atau ingin berdiskusi lebih lanjut, silakan hubungi melalui fitur support di aplikasi atau email admin.
</div>
@endif

<hr class="email-divider">

<p class="email-paragraph">
    Terima kasih telah mengikuti prosedur pengajuan exception dengan baik dan mematuhi semua peraturan yang berlaku.
</p>
