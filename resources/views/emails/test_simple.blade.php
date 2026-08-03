@extends('emails.layout', ['title' => 'Test Email - Magang BAKTI'])

@section('content')
<div class="email-category-label">Test Email</div>
<div class="email-title">Test Email Magang BAKTI</div>

<p class="email-greeting">Halo,</p>

<p class="email-paragraph">
    Ini adalah email test dari sistem Magang BAKTI Anda. Email ini menunjukkan bahwa konfigurasi email sudah berfungsi dengan baik.
</p>

<div class="email-alert email-alert-success">
    <strong>Email Berhasil Terkirim!</strong>
</div>

<div class="email-button-group">
    <a href="{{ url('/') }}" class="email-button">Buka Magang BAKTI</a>
</div>

<p class="email-paragraph" style="text-align: center; margin-top: 16px;">
    Anda menerima email ini karena melakukan test pengiriman email melalui aplikasi Magang BAKTI.
</p>
@endsection
