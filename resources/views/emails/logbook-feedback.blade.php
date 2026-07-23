@extends('emails.layout')

@section('content')
<div class="section">
    <p>Halo <strong>{{ $notifiable->name }}</strong>,</p>
    <p>Pembimbing Anda telah memberikan feedback pada logbook berikut:</p>
</div>

<div class="section">
    <div class="section-title">📋 Detail Logbook</div>
    <div class="info-row">
        <span class="info-label">Judul:</span>
        <span class="info-value">{{ $logbook->title }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tanggal:</span>
        <span class="info-value">{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Pembimbing:</span>
        <span class="info-value">{{ $logbook->student->supervisor->user->name }}</span>
    </div>
</div>

<div class="section">
    <div class="section-title">💬 Feedback Pembimbing</div>
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #667eea; border-radius: 4px;">
        <p>{{ $logbook->feedback }}</p>
    </div>
</div>

<div class="divider"></div>

<div class="section">
    <p style="text-align: center;">
        <a href="{{ url('/student/logbooks/' . $logbook->id) }}" class="button">
            Lihat Logbook
        </a>
    </p>
</div>
@endsection
