<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Magang - {{ $student->user->name }}</title>
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
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 15mm 20mm;
        }

        /* Kop Surat - matches certificate-pdf.blade.php for a consistent
           document family */
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

        /* Document Title */
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

        /* Section labels */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 14px 0 6px;
        }

        /* Student information - same colon-aligned pattern as
           certificate-pdf.blade.php */
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

        /* Grades table - plain ruled table, classic government-document
           style (thick outer border, thin inner rules) */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-top: 6px;
        }

        .grades-table th {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
        }

        .grades-table td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10pt;
            vertical-align: top;
        }

        .col-index {
            text-align: center;
            width: 6%;
        }

        .col-task {
            width: 32%;
        }

        .col-grade {
            text-align: center;
            width: 12%;
            font-weight: bold;
        }

        .col-comment {
            width: 50%;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        /* Legend */
        .grade-legend {
            margin-top: 6px;
            font-size: 9pt;
        }

        /* Summary - same detail-table pattern used throughout, not a
           separate "card" widget */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .summary-table td {
            padding: 1.5px 0;
            font-size: 11pt;
        }

        .summary-label {
            width: 240px;
        }

        .summary-separator {
            width: 15px;
            text-align: center;
        }

        /* Signature - mirrors certificate-pdf.blade.php's signature block */
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

        /* Footer */
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
        <!-- Kop Surat -->
        <div class="document-header">
            <div class="ministry-name">Kementerian Komunikasi dan Digital Republik Indonesia</div>
            <div class="agency-name">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="agency-address">
                Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930<br>
                Telp. 021-31936590 (Hunting) &middot; www.baktikominfo.id
            </div>
        </div>

        <!-- Document Title -->
        @php
            // Deterministic per student+day so re-printing the same
            // report doesn't yield a different document number.
            $reportNumber = str_pad(($student->id * 37 + now()->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
        @endphp
        <div class="document-title">
            <div class="title-main">Rekap Nilai Magang</div>
            <div class="document-number">Nomor: {{ $reportNumber }}/BAKTI/SDA/{{ date('m/Y') }}</div>
        </div>

        <!-- Student Information -->
        <div class="section-title">Data Mahasiswa</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">Nama Lengkap</td>
                <td class="detail-separator">:</td>
                <td>{{ $student->user->name }}</td>
            </tr>
            <tr>
                <td class="detail-label">NIM</td>
                <td class="detail-separator">:</td>
                <td>{{ $student->nim }}</td>
            </tr>
            <tr>
                <td class="detail-label">Perguruan Tinggi</td>
                <td class="detail-separator">:</td>
                <td>{{ $student->university }}</td>
            </tr>
            <tr>
                <td class="detail-label">Program Studi</td>
                <td class="detail-separator">:</td>
                <td>{{ $student->study_program ?? 'Tidak tercantum' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Pembimbing Lapangan</td>
                <td class="detail-separator">:</td>
                <td>{{ $supervisor->name }}</td>
            </tr>
            <tr>
                <td class="detail-label">Periode Magang</td>
                <td class="detail-separator">:</td>
                <td>
                    @if(isset($student->period_start) && isset($student->period_end))
                        {{ formatDateIndonesian($student->period_start) }} s.d. {{ formatDateIndonesian($student->period_end) }}
                    @else
                        Tidak tercantum
                    @endif
                </td>
            </tr>
            <tr>
                <td class="detail-label">Tanggal Cetak</td>
                <td class="detail-separator">:</td>
                <td>{{ $date }}</td>
            </tr>
        </table>

        <!-- Grades Section -->
        <div class="section-title">Rincian Penilaian Tugas</div>

        <table class="grades-table">
            <thead>
                <tr>
                    <th class="col-index">No.</th>
                    <th class="col-task">Nama Tugas</th>
                    <th class="col-grade">Nilai</th>
                    <th class="col-comment">Komentar Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $index => $submission)
                    <tr>
                        <td class="col-index">{{ $index + 1 }}</td>
                        <td class="col-task">{{ $submission->task->title }}</td>
                        <td class="col-grade">{{ $submission->grade }}</td>
                        <td class="col-comment">{{ $submission->comments ?? 'Tidak ada komentar khusus.' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                Belum terdapat nilai yang diberikan untuk mahasiswa yang bersangkutan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($submissions->count() > 0)
            <div class="grade-legend">
                Keterangan: A = Sangat Baik (85&ndash;100) &middot; B = Baik (75&ndash;84) &middot; C = Cukup (65&ndash;74) &middot; D = Perlu Perbaikan (&lt;65)
            </div>
        @endif

        <!-- Summary Section -->
        @if($submissions->count() > 0)
            @php
                $totalTasks = $submissions->count();
                $gradedTasks = $submissions->whereNotNull('grade')->count();
                $numericGrades = $submissions->whereNotNull('grade')->filter(function($item) {
                    return is_numeric($item->grade);
                });
                $averageGrade = $numericGrades->count() > 0 ? $numericGrades->avg('grade') : null;
                $completionRate = $totalTasks > 0 ? round(($gradedTasks / $totalTasks) * 100, 1) : 0;

                $excellentCount = $submissions->filter(function($item) {
                    $grade = $item->grade;
                    return (is_numeric($grade) && $grade >= 85) || in_array(strtoupper($grade), ['A', 'A+', 'A-']);
                })->count();

                $goodCount = $submissions->filter(function($item) {
                    $grade = $item->grade;
                    return (is_numeric($grade) && $grade >= 75 && $grade < 85) || in_array(strtoupper($grade), ['B', 'B+', 'B-']);
                })->count();
            @endphp

            <div class="section-title">Ringkasan Penilaian</div>
            <table class="summary-table">
                <tr>
                    <td class="summary-label">Total Tugas yang Diberikan</td>
                    <td class="summary-separator">:</td>
                    <td>{{ $totalTasks }} tugas</td>
                </tr>
                <tr>
                    <td class="summary-label">Tugas yang Telah Dinilai</td>
                    <td class="summary-separator">:</td>
                    <td>{{ $gradedTasks }} tugas ({{ $completionRate }}%)</td>
                </tr>
                @if($averageGrade)
                    <tr>
                        <td class="summary-label">Rata-rata Nilai</td>
                        <td class="summary-separator">:</td>
                        <td>{{ number_format($averageGrade, 1) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="summary-label">Nilai Sangat Baik (A)</td>
                    <td class="summary-separator">:</td>
                    <td>{{ $excellentCount }} tugas</td>
                </tr>
                <tr>
                    <td class="summary-label">Nilai Baik (B)</td>
                    <td class="summary-separator">:</td>
                    <td>{{ $goodCount }} tugas</td>
                </tr>
            </table>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-left"></td>
                    <td class="signature-right">
                        <div class="signature-title">Mengetahui,<br>Pembimbing Lapangan</div>
                        <div class="signature-name">{{ $supervisor->name }}</div>
                        <div class="signature-nip">NIP. {{ $supervisor->employee_id ?? '________________' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ $date }} &middot;
            ID Dokumen: RPT-{{ strtoupper(substr(md5($student->user->name . $date), 0, 8)) }}
        </div>
    </div>
</body>
</html>
