@extends('emails.layout', ['title' => 'Status Kehadiran - WebBakti'])

@php
    $title = $status === 'approved' ? '✅ Kehadiran Disetujui' : '⚠️ Kehadiran Ditolak';
    $icon = $status === 'approved' ? '✓' : '✗';
@endphp

<div class="email-title">{{ $title }}</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Status kehadiran Anda telah dikaji dan diverifikasi oleh sistem.
</p>

@if($status === 'approved')
    <div class="email-alert email-alert-success">
        <strong>{{ $icon }} Kehadiran Anda DISETUJUI</strong><br>
        Kehadiran Anda pada tanggal tersebut telah diverifikasi dan diterima oleh sistem. Terima kasih telah menjalankan kehadiran dengan baik.
    </div>
@else
    <div class="email-alert email-alert-danger">
        <strong>{{ $icon }} Kehadiran Anda DITOLAK</strong><br>
        Kehadiran Anda pada tanggal tersebut tidak memenuhi kriteria verifikasi sistem.
        @if($attendance->rejection_reason)
            <br><strong>Alasan:</strong> {{ $attendance->rejection_reason }}
        @endif
    </div>
@endif

<div class="email-section">
    <div class="email-section-title">📋 Detail Kehadiran</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Tanggal & Waktu:</div>
            <div class="email-info-value">
                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y, H:i') : 'N/A' }}
            </div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Lokasi:</div>
            <div class="email-info-value">{{ $attendance->location ?? 'Tidak tercatat' }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Verifikasi Lokasi:</div>
            <div class="email-info-value">
                {{ $attendance->location_verification_status ?? 'Belum diverifikasi' }}
                @if($attendance->location_spoofing_score)
                    (Skor: {{ $attendance->location_spoofing_score }})
                @endif
            </div>
        </div>
        @if($attendance->photo_exif_status)
        <div class="email-info-row">
            <div class="email-info-label">Status EXIF:</div>
            <div class="email-info-value">{{ $attendance->photo_exif_status }}</div>
        </div>
        @endif
    </div>
</div>

@if($status === 'rejected')
<div class="email-section">
    <div class="email-section-title">💡 Mengapa Kehadiran Ditolak?</div>
    <p class="email-paragraph">Kehadiran ditolak karena tidak memenuhi kriteria verifikasi sistem. Kemungkinan penyebabnya:</p>
    <ul class="email-list">
        <li>Lokasi tidak sesuai dengan zona yang ditentukan</li>
        <li>Data EXIF foto tidak valid atau tidak terbaca</li>
        <li>Terdapat indikasi manipulasi data lokasi</li>
        <li>Foto atau data yang dikirimkan tidak jelas atau tidak valid</li>
    </ul>
</div>

<div class="email-alert email-alert-info">
    <strong>📌 Langkah Selanjutnya:</strong>
    <ul class="email-list" style="margin-top: 10px;">
        <li>Tinjau kembali data yang Anda kirimkan</li>
        <li>Jika yakin tidak ada kesalahan, ajukan pengajuan exception dengan alasan yang jelas</li>
        <li>Sertakan bukti pendukung jika diperlukan</li>
        <li>Hubungi admin jika memerlukan bantuan</li>
    </ul>
</div>
@else
<div class="email-section">
    <div class="email-section-title">✅ Terima Kasih</div>
    <p class="email-paragraph">
        Kehadiran Anda telah tercatat dalam sistem dan akan diperhitungkan dalam evaluasi performa magang Anda.
    </p>
</div>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/attendance') }}" class="email-button">Lihat Riwayat Kehadiran</a>
</div>

<hr class="email-divider">

<p class="email-paragraph">
    Apabila memiliki pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi admin sistem melalui fitur support di aplikasi.
</p>
