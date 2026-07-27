@extends('emails.layout', ['title' => 'Test Email - WebBakti'])

<div class="email-title">✨ Test Email WebBakti</div>

<p class="email-greeting">Halo,</p>

<p class="email-paragraph">
    Ini adalah email test dari sistem WebBakti Anda. Email ini menunjukkan bahwa konfigurasi email sudah berfungsi dengan baik.
</p>

<div class="email-alert email-alert-success">
    <strong>✓ Email Berhasil Terkirim!</strong>
</div>

<div class="email-button-group">
    <a href="{{ url('/') }}" class="email-button">Buka WebBakti</a>
</div>

<p class="email-paragraph" style="text-align: center; font-size: 13px; color: #718096; margin-top: 20px;">
    Anda menerima email ini karena melakukan test pengiriman email melalui aplikasi WebBakti.
</p>
