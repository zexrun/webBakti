@extends('emails.layout', ['title' => 'Feedback Logbook - WebBakti'])

<div class="email-category-label">Feedback Logbook</div>
<div class="email-title">Feedback Baru Diterima</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Pembimbing Anda telah memberikan feedback berharga pada salah satu logbook Anda. Bacalah feedback ini untuk meningkatkan kualitas pekerjaan Anda ke depannya.
</p>

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Judul</div>
        <div class="email-info-value">{{ $logbook->title }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Tanggal Aktivitas</div>
        <div class="email-info-value">{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Pembimbing</div>
        <div class="email-info-value">{{ $logbook->student->supervisor->user->name }}</div>
    </div>
</div>

<div style="background: #F7F8FA; padding: 14px 16px; border-left: 3px solid #0F2A5C; color: #333333; line-height: 1.7; font-size: 12px; margin: 16px 0;">
    {{ $logbook->feedback }}
</div>

<div class="email-button-group">
    <a href="{{ url('/student/logbooks/' . $logbook->id) }}" class="email-button">Lihat Logbook Lengkap</a>
</div>

<hr class="email-divider">

<p class="email-paragraph">
    Gunakan feedback ini sebagai pembelajaran untuk terus meningkatkan kualitas logbook dan pekerjaan Anda di magang. Jika ada pertanyaan terkait feedback, silakan hubungi pembimbing Anda melalui aplikasi.
</p>
