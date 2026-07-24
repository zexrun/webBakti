<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logbook - {{ $logbook->title }} - {{ $logbook->student->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.5;
            font-size: 11pt;
        }

        .report-container {
            width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15mm 20mm;
        }

        .document-header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .ministry-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .agency-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 2px;
        }

        .agency-address {
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
            margin-top: 4px;
        }

        .document-title {
            text-align: center;
            margin: 14px 0 4px;
        }

        .title-main {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .document-number {
            font-size: 10pt;
            margin-top: 4px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 14px 0 6px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .detail-table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .detail-label {
            width: 140px;
        }

        .detail-separator {
            width: 15px;
            text-align: center;
        }

        .description-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 6px;
            min-height: 40mm;
            white-space: pre-wrap;
        }

        .attachment-photo {
            margin-top: 8px;
            max-width: 100%;
            max-height: 90mm;
            border: 1px solid #000;
        }

        .signature-section {
            margin-top: 24px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-left {
            width: 50%;
        }

        .signature-right {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 10pt;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }

        .signature-nip {
            font-size: 9pt;
            margin-top: 2px;
        }

        .document-footer {
            margin-top: 14px;
            border-top: 1px solid #999;
            padding-top: 6px;
            font-size: 8pt;
            color: #444;
            text-align: center;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="document-header">
            <div class="ministry-name">Kementerian Komunikasi dan Digital Republik Indonesia</div>
            <div class="agency-name">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="agency-address">
                Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930<br>
                Telp. 021-31936590 (Hunting) &middot; www.baktikominfo.id
            </div>
        </div>

        @php
            $logNumber = str_pad(($logbook->student->id * 37 + $logbook->id) % 900 + 100, 3, '0', STR_PAD_LEFT);
        @endphp
        <div class="document-title">
            <div class="title-main">Logbook Kegiatan Magang</div>
            <div class="document-number">Nomor: {{ $logNumber }}/BAKTI/SDA/{{ date('m/Y') }}</div>
        </div>

        <div class="section-title">Data Mahasiswa</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Nama Lengkap</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->user->name }}</td>
            </tr>
            <tr>
                <td class="detail-label">NIM</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Pembimbing</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->student->supervisor->user->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">Detail Kegiatan</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Judul Kegiatan</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->title }}</td>
            </tr>
            <tr>
                <td class="detail-label">Tanggal</td>
                <td class="detail-separator">:</td>
                <td>{{ \Carbon\Carbon::parse($logbook->activity_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="detail-label">Waktu</td>
                <td class="detail-separator">:</td>
                <td>{{ substr($logbook->start_time, 0, 5) }} &ndash; {{ substr($logbook->end_time, 0, 5) }}</td>
            </tr>
            <tr>
                <td class="detail-label">Perasaan</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->feeling }}</td>
            </tr>
            <tr>
                <td class="detail-label">Status</td>
                <td class="detail-separator">:</td>
                <td>{{ $logbook->is_verified ? 'Telah Diverifikasi' : 'Belum Diverifikasi' }}</td>
            </tr>
        </table>

        <div class="section-title">Deskripsi Kegiatan</div>
        <div class="description-box">{{ $logbook->description }}</div>

        @if($logbook->file_path)
            <div class="section-title">Dokumentasi Kegiatan</div>
            <img class="attachment-photo" src="{{ public_path('storage/' . $logbook->file_path) }}" alt="Dokumentasi Kegiatan">
        @endif

        @if($logbook->feedback)
            <div class="section-title">Feedback Pembimbing</div>
            <div class="description-box">{{ $logbook->feedback }}</div>
        @endif

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-left"></td>
                    <td class="signature-right">
                        <div class="signature-title">Mengetahui,<br>Pembimbing Lapangan</div>
                        <div class="signature-name">{{ $logbook->student->supervisor->user->name ?? '' }}</div>
                        <div class="signature-nip">NIP. {{ $logbook->student->supervisor->employee_id ?? '________________' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ now()->translatedFormat('d F Y H:i') }} &middot;
            ID Dokumen: LOG-{{ strtoupper(substr(md5($logbook->id . $logbook->activity_date), 0, 8)) }}
        </div>
    </div>
</body>
</html>
