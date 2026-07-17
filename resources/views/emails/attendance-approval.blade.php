@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Status kehadiran Anda telah dikaji oleh admin sistem.</p>
</div>

@if($status === 'approved')
    <div class="alert alert-success">
        <strong>✓ Kehadiran DISETUJUI</strong>
        <p style="margin-top: 10px;">Kehadiran Anda pada tanggal tersebut telah diverifikasi dan diterima oleh sistem.</p>
    </div>
@else
    <div class="alert alert-danger">
        <strong>✗ Kehadiran DITOLAK</strong>
        <p style="margin-top: 10px;">Kehadiran Anda pada tanggal tersebut tidak memenuhi kriteria verifikasi.</p>
        @if($attendance->rejection_reason)
            <p style="margin-top: 10px;"><strong>Alasan Penolakan:</strong> {{ $attendance->rejection_reason }}</p>
        @endif
    </div>
@endif

<div class="section">
    <div class="section-title">📋 Detail Kehadiran</div>
    <div class="info-row">
        <span class="info-label">Tanggal & Waktu:</span>
        <span class="info-value">
            {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y, H:i') : 'N/A' }}
        </span>
    </div>
    <div class="info-row">
        <span class="info-label">Lokasi:</span>
        <span class="info-value">{{ $attendance->location ?? 'Tidak tercatat' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Verifikasi Lokasi:</span>
        <span class="info-value">
            {{ $attendance->location_verification_status ?? 'Belum diverifikasi' }}
            @if($attendance->location_spoofing_score)
                (Skor: {{ $attendance->location_spoofing_score }})
            @endif
        </span>
    </div>
    @if($attendance->photo_exif_status)
    <div class="info-row">
        <span class="info-label">Status EXIF:</span>
        <span class="info-value">{{ $attendance->photo_exif_status }}</span>
    </div>
    @endif
</div>

@if($status === 'rejected')
<div class="section">
    <div class="section-title">💡 Informasi Penting</div>
    <p>Kehadiran Anda ditolak karena tidak memenuhi kriteria verifikasi sistem. Hal ini bisa terjadi karena:</p>
    <ul>
        <li>Lokasi tidak sesuai dengan zona yang ditentukan</li>
        <li>Data EXIF foto tidak valid atau tidak terbaca</li>
        <li>Terdapat indikasi manipulasi data lokasi</li>
        <li>Foto atau data yang dikirimkan tidak jelas atau tidak valid</li>
    </ul>
    <p style="margin-top: 15px;">Jika Anda merasa ada kesalahan, Anda dapat mengajukan pengajuan exception dengan alasan yang jelas dan bukti pendukung.</p>
</div>
@else
<div class="section">
    <div class="section-title">✅ Terima Kasih</div>
    <p>Terima kasih telah menjalankan tugas dengan baik. Kehadiran Anda telah tercatat dalam sistem dan diperhitungkan dalam evaluasi magang Anda.</p>
</div>
@endif

<div class="divider"></div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/attendance') }}" class="button">
            Lihat Riwayat Kehadiran
        </a>
    </p>
</div>

@if($status === 'rejected')
<div class="alert alert-info">
    <strong>ℹ️ Langkah Selanjutnya:</strong>
    <ul style="margin-top: 10px; margin-left: 20px;">
        <li>Tinjau kembali data yang Anda kirimkan</li>
        <li>Jika yakin tidak ada kesalahan, ajukan pengajuan exception</li>
        <li>Hubungi admin jika memerlukan bantuan: magang.baktikomdigi@gmail.com</li>
    </ul>
</div>
@endif

<div class="section">
    <p>Apabila memiliki pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi admin sistem.</p>
</div>
@endsection
