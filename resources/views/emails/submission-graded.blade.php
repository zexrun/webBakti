@extends('emails.layout', ['title' => 'Tugas Dinilai - WebBakti'])

<div class="email-title">⭐ Tugas Anda Telah Dinilai</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pembimbing Anda telah selesai menilai submission tugas Anda. Lihat hasilnya di bawah ini.
</p>

<div class="email-section">
    <div class="email-section-title">📋 Detail Tugas</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Judul:</div>
            <div class="email-info-value">{{ $submission->task->title }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Pembimbing:</div>
            <div class="email-info-value">{{ $submission->task->supervisor->user->name }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Tipe Tugas:</div>
            <div class="email-info-value">{{ $submission->task->type === 'harian' ? '📅 Tugas Harian' : '📑 Laporan Akhir' }}</div>
        </div>
    </div>
</div>

<div style="text-align: center; padding: 32px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; margin: 24px 0; color: white;">
    <p style="font-size: 14px; margin: 0 0 12px 0; opacity: 0.95; font-weight: 500;">Nilai Anda</p>
    <p style="font-size: 56px; font-weight: 800; margin: 0; letter-spacing: -1px;">{{ $submission->grade }}</p>
</div>

@if($submission->comments)
<div class="email-section">
    <div class="email-section-title">💭 Komentar Pembimbing</div>
    <div style="background: #f7fafc; padding: 16px; border-left: 4px solid #667eea; border-radius: 6px; color: #2d3748; line-height: 1.7;">
        {{ $submission->comments }}
    </div>
</div>
@endif

<div class="email-button-group">
    <a href="{{ url('/student/tasks/' . $submission->task->id) }}" class="email-button">Lihat Detail Lengkap</a>
</div>

<div class="email-alert email-alert-info">
    <strong>💡 Langkah Berikutnya</strong><br>
    Baca feedback dari pembimbing dengan seksama. Gunakan pembelajaran ini untuk meningkatkan kualitas pekerjaan Anda pada tugas-tugas selanjutnya.
</div>

<hr class="email-divider">

<p class="email-paragraph" style="text-align: center; font-size: 16px; font-weight: 600; color: #667eea;">
    Semangat untuk tugas berikutnya! 🎯
</p>
