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
