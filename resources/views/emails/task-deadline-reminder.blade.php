@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Ini adalah pengingat penting bahwa tugas Anda akan segera berakhir.</p>
</div>

@if($daysUntilDeadline === 0)
    <div class="alert alert-danger">
        <strong>🚨 URGENT!</strong> Tugas ini berakhir <strong>HARI INI</strong>. Segera selesaikan dan kumpulkan sebelum batas waktu!
    </div>
@elseif($daysUntilDeadline === 1)
    <div class="alert alert-warning">
        <strong>⏰ PENTING!</strong> Tugas ini berakhir <strong>BESOK</strong>. Pastikan Anda menyelesaikan dengan segera!
    </div>
@else
    <div class="alert alert-info">
        <strong>ℹ️ Pengingat:</strong> Tugas ini berakhir dalam <strong>{{ $daysUntilDeadline }} hari</strong>. Mulai kerjakan sekarang!
    </div>
@endif

<div class="section">
    <div class="section-title">📋 Detail Tugas</div>
    <div class="info-row">
        <span class="info-label">Judul Tugas:</span>
        <span class="info-value">{{ $task->title }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Pembimbing:</span>
        <span class="info-value">{{ $task->supervisor->user->name }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tipe:</span>
        <span class="info-value">{{ $task->type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Batas Waktu:</span>
        <span class="info-value"><strong>{{ $task->due_date->format('d M Y, H:i') }}</strong></span>
    </div>
    <div class="info-row">
        <span class="info-label">Waktu Tersisa:</span>
        <span class="info-value">
            @if($daysUntilDeadline === 0)
                <strong style="color: #d32f2f;">Hari ini (urgent!)</strong>
            @elseif($daysUntilDeadline === 1)
                <strong style="color: #f57c00;">1 hari</strong>
            @else
                <strong>{{ $daysUntilDeadline }} hari</strong>
            @endif
        </span>
    </div>
</div>

<div class="section">
    <div class="section-title">📝 Deskripsi Tugas</div>
    <p>{{ Str::limit($task->description, 300) }}</p>
</div>

@if($task->file_path)
<div class="section">
    <div class="section-title">📎 Lampiran</div>
    <p>Ada file lampiran untuk tugas ini. Silakan download dari sistem untuk melihat detail lengkap.</p>
</div>
@endif

<div class="divider"></div>

<div class="section">
    <div class="section-title">✅ Langkah Selanjutnya</div>
    <ul>
        <li><strong>1. Baca</strong> deskripsi tugas dengan seksama</li>
        <li><strong>2. Download</strong> file lampiran (jika ada)</li>
        <li><strong>3. Kerjakan</strong> tugas sesuai instruksi</li>
        <li><strong>4. Upload</strong> hasil pekerjaan Anda</li>
        <li><strong>5. Verifikasi</strong> bahwa tugas sudah tersubmit</li>
    </ul>
</div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/tasks/' . $task->id) }}" class="button">
            Buka Tugas Sekarang
        </a>
    </p>
</div>

<div class="alert alert-warning">
    <strong>⚠️ Catatan Penting:</strong>
    <ul style="margin-top: 10px; margin-left: 20px;">
        <li>Pastikan Anda upload submission sebelum batas waktu berakhir</li>
        <li>Submission yang diterima setelah deadline tidak akan diterima</li>
        <li>Hubungi pembimbing Anda jika ada pertanyaan atau kendala</li>
    </ul>
</div>

<div class="section">
    <p><strong>Semangat mengerjakan tugas! Anda pasti bisa! 💪</strong></p>
</div>
@endsection
