@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Pembimbing Anda telah selesai menilai submission tugas. Berikut adalah detailnya:</p>
</div>

<div class="section">
    <div class="section-title">📋 Detail Tugas</div>
    <div class="info-row">
        <span class="info-label">Judul Tugas:</span>
        <span class="info-value">{{ $submission->task->title }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Pembimbing:</span>
        <span class="info-value">{{ $submission->task->supervisor->user->name }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tipe Tugas:</span>
        <span class="info-value">{{ $submission->task->type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir' }}</span>
    </div>
</div>

<div class="section">
    <div class="section-title">⭐ Hasil Penilaian</div>
    <div style="text-align: center; padding: 20px; background-color: #f0f4ff; border-radius: 8px; margin: 15px 0;">
        <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Nilai Anda</p>
        <p style="font-size: 48px; font-weight: bold; color: #667eea; margin: 0;">{{ $submission->grade }}</p>
    </div>
</div>

@if($submission->comments)
<div class="section">
    <div class="section-title">💬 Komentar Pembimbing</div>
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #667eea; border-radius: 4px;">
        <p>{{ $submission->comments }}</p>
    </div>
</div>
@endif

<div class="divider"></div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/tasks/' . $submission->task->id) }}" class="button">
            Lihat Detail Lengkap
        </a>
    </p>
</div>

<div class="alert alert-info">
    <strong>💡 Tips:</strong> Baca komentar pembimbing dengan seksama untuk meningkatkan kualitas pekerjaan Anda di tugas-tugas selanjutnya.
</div>

<div class="section">
    <p>Semangat untuk tugas berikutnya! 🎯</p>
</div>
@endsection
