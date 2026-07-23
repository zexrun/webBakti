<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Logbook</title>
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

        .document-subtitle {
            font-size: 10pt;
            margin-top: 4px;
        }

        .student-section {
            margin-top: 16px;
        }

        .student-section:first-of-type {
            margin-top: 10px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
        }

        .recap-table {
            width: 170mm;
            table-layout: fixed;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        .recap-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
            word-wrap: break-word;
        }

        .recap-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9.5pt;
            vertical-align: top;
            word-wrap: break-word;
        }

        .col-date { width: 14%; text-align: center; }
        .col-activity { width: 30%; }
        .col-time { width: 14%; text-align: center; }
        .col-feeling { width: 14%; text-align: center; }
        .col-status { width: 14%; text-align: center; }
        .col-feedback { width: 14%; }

        .empty-state {
            text-align: center;
            padding: 15px;
            font-style: italic;
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

        <div class="document-title">
            <div class="title-main">Rekap Logbook Kegiatan Magang</div>
            <div class="document-subtitle">
                Periode:
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') : 'Semua Tanggal' }}
                s.d.
                {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') : 'Sekarang' }}
            </div>
        </div>

        @forelse($logbooksByStudent as $student => $logbooks)
            <div class="student-section">
                <div class="section-title">{{ $student->user->name }} ({{ $student->nim ?? 'NIM belum diisi' }})</div>
                <table class="recap-table">
                    <thead>
                        <tr>
                            <th class="col-date">Tanggal</th>
                            <th class="col-activity">Kegiatan</th>
                            <th class="col-time">Waktu</th>
                            <th class="col-feeling">Perasaan</th>
                            <th class="col-status">Status</th>
                            <th class="col-feedback">Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $logbook)
                            <tr>
                                <td class="col-date">{{ \Carbon\Carbon::parse($logbook->activity_date)->format('d/m/Y') }}</td>
                                <td class="col-activity">{{ $logbook->title }}</td>
                                <td class="col-time">{{ substr($logbook->start_time, 0, 5) }}-{{ substr($logbook->end_time, 0, 5) }}</td>
                                <td class="col-feeling">{{ $logbook->feeling }}</td>
                                <td class="col-status">{{ $logbook->is_verified ? 'Dilihat' : 'Belum' }}</td>
                                <td class="col-feedback">{{ $logbook->feedback ? 'Ada' : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="empty-state">Tidak ada logbook yang sesuai dengan filter yang dipilih.</div>
        @endforelse

        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ $generatedAt->translatedFormat('d F Y H:i') }}
        </div>
    </div>
</body>
</html>