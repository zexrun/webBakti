@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Pengajuan exception (sakit/izin) Anda telah dikaji oleh admin sistem.</p>
</div>

@if($status === 'approved')
    <div class="alert alert-success">
        <strong>✓ Pengajuan DISETUJUI</strong>
        <p style="margin-top: 10px;">Pengajuan exception Anda telah disetujui. Hari tersebut tidak akan dihitung sebagai ketidakhadiran.</p>
    </div>
@else
    <div class="alert alert-danger">
        <strong>✗ Pengajuan DITOLAK</strong>
        <p style="margin-top: 10px;">Pengajuan exception Anda tidak memenuhi kriteria persetujuan.</p>
        @if($exception->admin_notes)
            <p style="margin-top: 10px;"><strong>Alasan Penolakan:</strong> {{ $exception->admin_notes }}</p>
        @endif
    </div>
@endif

<div class="section">
    <div class="section-title">📋 Detail Pengajuan</div>
    <div class="info-row">
        <span class="info-label">Tanggal:</span>
        <span class="info-value">
            {{ $exception->date ? \Carbon\Carbon::parse($exception->date)->format('d M Y') : 'N/A' }}
        </span>
    </div>
    <div class="info-row">
        <span class="info-label">Tipe Exception:</span>
        <span class="info-value">
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
        </span>
    </div>
    <div class="info-row">
        <span class="info-label">Alasan:</span>
        <span class="info-value">{{ $exception->reason ?? '-' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tanggal Pengajuan:</span>
        <span class="info-value">{{ $exception->created_at->format('d M Y, H:i') }}</span>
    </div>
</div>

@if($exception->supporting_document)
<div class="section">
    <div class="section-title">📎 Dokumen Pendukung</div>
    <p>Dokumen pendukung telah dicatat dalam sistem.</p>
</div>
@endif

@if($status === 'rejected')
<div class="section">
    <div class="section-title">💡 Informasi Penting</div>
    <p>Pengajuan exception Anda ditolak karena alasan tertentu yang dijelaskan di atas. Anda dapat:</p>
    <ul>
        <li>Mengajukan exception kembali dengan informasi atau dokumen yang lebih lengkap</li>
        <li>Menghubungi admin untuk mendiskusikan penolakan ini</li>
        <li>Mengajukan banding jika merasa ada kesalahan dalam penilaian</li>
    </ul>
</div>
@else
<div class="section">
    <div class="section-title">✅ Terima Kasih</div>
    <p>Pengajuan exception Anda telah disetujui. Status kehadiran Anda telah diperbarui sesuai dengan persetujuan ini.</p>
</div>
@endif

<div class="divider"></div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/exceptions') }}" class="button">
            Lihat Pengajuan Saya
        </a>
    </p>
</div>

@if($status === 'rejected')
<div class="alert alert-info">
    <strong>📞 Hubungi Admin:</strong>
    <p style="margin-top: 10px;">Jika Anda memiliki pertanyaan atau ingin berdiskusi lebih lanjut, silakan hubungi:</p>
    <p style="margin-top: 10px;">Email: <strong>magang.baktikomdigi@gmail.com</strong></p>
</div>
@endif

<div class="section">
    <p>Terima kasih telah mengikuti prosedur pengajuan exception dengan baik.</p>
</div>
@endsection
