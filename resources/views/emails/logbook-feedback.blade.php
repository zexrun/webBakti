@extends('emails.layout', ['title' => 'Feedback Logbook - WebBakti'])

<div class="email-title">💬 Feedback Logbook</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pembimbing Anda telah memberikan feedback berharga pada salah satu logbook Anda. Bacalah feedback ini untuk meningkatkan kualitas pekerjaan Anda ke depannya.
</p>

<div class="email-section">
    <div class="email-section-title">📋 Detail Logbook</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Judul:</div>
            <div class="email-info-value">{{ $logbook->title }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Tanggal Aktivitas:</div>
            <div class="email-info-value">{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Pembimbing:</div>
            <div class="email-info-value">{{ $logbook->student->supervisor->user->name }}</div>
        </div>
    </div>
</div>

<div class="email-section">
    <div class="email-section-title">💭 Feedback dari Pembimbing</div>
    <div style="background: #f7fafc; padding: 16px; border-left: 4px solid #667eea; border-radius: 6px; color: #2d3748; line-height: 1.7;">
        {{ $logbook->feedback }}
    </div>
</div>

<div class="email-button-group">
    <a href="{{ url('/student/logbooks/' . $logbook->id) }}" class="email-button">Lihat Logbook Lengkap</a>
</div>

<hr class="email-divider">

<p class="email-paragraph">
    Gunakan feedback ini sebagai pembelajaran untuk terus meningkatkan kualitas logbook dan pekerjaan Anda di magang. Jika ada pertanyaan terkait feedback, silakan hubungi pembimbing Anda melalui aplikasi.
</p>
