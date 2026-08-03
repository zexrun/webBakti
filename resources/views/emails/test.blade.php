@extends('emails.layout', ['title' => 'Test Email - Magang BAKTI'])

@section('content')
<div class="email-category-label">Test Email</div>
<div class="email-title">Selamat! Email Terkirim</div>

<p class="email-paragraph">
    Email sandbox Anda berhasil dikonfigurasi dan bekerja dengan sempurna.
</p>

<div class="email-alert email-alert-success">
    <strong>Konfigurasi Email Berhasil.</strong> Email ini dikirim melalui mail driver Anda dan berhasil diterima.
</div>

<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Waktu Pengiriman</div>
        <div class="email-info-value">{{ now()->format('d M Y, H:i:s') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Environment</div>
        <div class="email-info-value">{{ config('app.env') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Aplikasi</div>
        <div class="email-info-value">{{ config('app.name') }}</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">URL</div>
        <div class="email-info-value">{{ config('app.url') }}</div>
    </div>
</div>

<p class="email-paragraph"><strong>Pesan Test:</strong> {{ $message ?? 'Ini adalah email test dari Magang BAKTI' }}</p>

<p class="email-paragraph">
    Email ini membuktikan bahwa sistem email Anda sudah siap untuk:
</p>

<ul class="email-list">
    <li><strong>Email Notifikasi</strong> - Untuk berbagai event aplikasi</li>
    <li><strong>Verifikasi Email</strong> - Untuk registrasi user</li>
    <li><strong>Reset Password</strong> - Untuk pemulihan akun</li>
    <li><strong>Laporan & Feedback</strong> - Untuk komunikasi user-supervisor</li>
    <li><strong>Pengingat & Deadline</strong> - Untuk task dan deadline reminder</li>
</ul>

<div class="email-button-group">
    <a href="{{ url('/') }}" class="email-button">Kembali ke Aplikasi</a>
    <a href="https://mailtrap.io" class="email-button email-button-secondary" target="_blank">Buka Mailtrap Dashboard</a>
</div>

<hr class="email-divider">

<div class="email-alert email-alert-info">
    <strong>Langkah Selanjutnya:</strong>
    <ul class="email-list" style="margin-top: 8px;">
        <li>Periksa inbox di Mailtrap untuk melihat email ini</li>
        <li>Test fitur lain seperti reset password dan notifikasi</li>
        <li>Verifikasi bahwa email terformat dengan baik di berbagai client</li>
        <li>Sebelum production, ganti provider email ke SendGrid, AWS SES, atau Gmail</li>
    </ul>
</div>
@endsection
