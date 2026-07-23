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
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            color: #1c2530;
            line-height: 1.5;
            font-size: 10pt;
        }

        .report-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            position: relative;
            padding: 0 20mm 20mm 20mm;
        }

        /* Masthead - solid navy band, corporate report style */
        .masthead {
            background-color: #0f2a4a;
            color: #ffffff;
            margin: 0 -20mm 8mm -20mm;
            padding: 12mm 20mm 8mm 20mm;
        }

        .masthead-table {
            width: 100%;
            border-collapse: collapse;
        }

        .masthead-table td {
            vertical-align: top;
        }

        .institution-name {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .institution-subtitle {
            font-size: 9pt;
            color: #b8cbe0;
        }

        .masthead-doc-number {
            text-align: right;
            font-size: 8pt;
            color: #b8cbe0;
            padding-top: 3px;
        }

        .masthead-doc-number strong {
            display: block;
            font-size: 9pt;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Document title block */
        .title-block {
            margin-bottom: 7mm;
        }

        .document-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 17pt;
            font-weight: bold;
            color: #0f2a4a;
            margin-bottom: 2px;
        }

        .document-subtitle {
            font-size: 9.5pt;
            color: #5b6570;
        }

        .title-rule {
            height: 2px;
            background-color: #0f2a4a;
            margin-top: 5px;
            width: 42mm;
        }

        /* Section heading - consistent rhythm across the page */
        .section-heading {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f2a4a;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #d7dbe1;
        }

        /* Student information - two-column facts, not a form */
        .student-information {
            margin-bottom: 7mm;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 2.2mm 0;
            vertical-align: top;
            font-size: 9.5pt;
        }

        .info-grid .info-label {
            width: 38mm;
            color: #5b6570;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding-top: 2.8mm;
        }

        .info-grid .info-value {
            font-weight: bold;
            color: #1c2530;
        }

        /* Grades table */
        .grades-section {
            margin-bottom: 6mm;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grades-table thead th {
            background-color: #0f2a4a;
            color: #ffffff;
            padding: 3mm 3mm;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .grades-table thead th.col-center {
            text-align: center;
        }

        .grades-table tbody td {
            padding: 3mm;
            font-size: 9pt;
            border-bottom: 1px solid #e4e8ed;
            vertical-align: top;
        }

        .grades-table tbody tr:nth-child(even) {
            background-color: #f7f9fb;
        }

        .row-index {
            text-align: center;
            color: #5b6570;
            font-weight: bold;
            width: 8%;
        }

        .task-name-cell {
            font-weight: bold;
            color: #1c2530;
            width: 40%;
        }

        .grade-cell {
            text-align: center;
            width: 14%;
        }

        .comment-cell {
            color: #4a525c;
            font-size: 8.5pt;
            line-height: 1.45;
            width: 38%;
        }

        /* Grade badge - pill, semantic color separate from navy accent */
        .grade-badge {
            display: inline-block;
            min-width: 9mm;
            padding: 1.3mm 3mm;
            border-radius: 3mm;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
        }

        .grade-excellent { background-color: #e3f6ec; color: #0b7a4b; }
        .grade-good { background-color: #e5eef9; color: #1a4a8a; }
        .grade-satisfactory { background-color: #fdf1de; color: #9a5b0a; }
        .grade-needs-improvement { background-color: #fbe7e7; color: #b02525; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 12mm;
            font-style: italic;
            color: #8a94a0;
            background-color: #f7f9fb;
        }

        /* Legend */
        .grade-legend {
            margin-top: 4mm;
            font-size: 8pt;
            color: #5b6570;
        }

        .grade-legend-title {
            font-weight: bold;
            color: #1c2530;
            margin-bottom: 1.5mm;
        }

        .legend-table {
            border-collapse: collapse;
        }

        .legend-table td {
            padding-right: 6mm;
            padding-bottom: 1mm;
            white-space: nowrap;
        }

        /* Summary - stat cards, not a key-value table */
        .summary-section {
            margin-bottom: 8mm;
        }

        .summary-cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3mm 0;
        }

        .summary-cards td {
            width: 25%;
            background-color: #f7f9fb;
            border-top: 2px solid #0f2a4a;
            padding: 4mm 3mm;
            vertical-align: top;
        }

        .summary-cards td:first-child {
            border-spacing: 0;
        }

        .summary-stat-value {
            font-size: 15pt;
            font-weight: bold;
            color: #0f2a4a;
            font-family: Georgia, 'Times New Roman', serif;
        }

        .summary-stat-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #5b6570;
            margin-top: 1mm;
        }

        /* Signature */
        .signature-section {
            margin-top: 10mm;
            display: table;
            width: 100%;
        }

        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .signature-title {
            font-size: 9pt;
            color: #5b6570;
            margin-bottom: 14mm;
        }

        .signature-name {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1c2530;
            border-top: 1px solid #1c2530;
            padding-top: 2mm;
            display: inline-block;
            min-width: 60mm;
        }

        .signature-position {
            font-size: 8.5pt;
            color: #5b6570;
            margin-top: 1mm;
        }

        /* Footer */
        .document-footer {
            margin-top: 10mm;
            padding-top: 3mm;
            border-top: 1px solid #d7dbe1;
            text-align: center;
            font-size: 7.5pt;
            color: #8a94a0;
        }

        @media print {
            .report-container {
                width: 100%;
                padding: 0 20mm 15mm 20mm;
            }

            .masthead {
                margin: 0 -20mm 8mm -20mm;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Masthead -->
        <div class="masthead">
            <table class="masthead-table">
                <tr>
                    <td>
                        <div class="institution-name">BAKTI KOMINFO</div>
                        <div class="institution-subtitle">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
                    </td>
                    <td class="masthead-doc-number">
                        @php
                            // Deterministic per student+day so re-printing the same
                            // report doesn't yield a different document number.
                            $reportNumber = str_pad(($student->id * 37 + now()->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
                        @endphp
                        Dokumen Resmi
                        <strong>{{ $reportNumber }}/BAKTI/{{ date('m/Y') }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Title -->
        <div class="title-block">
            <div class="document-title">Rekap Nilai Magang</div>
            <div class="document-subtitle">Program Magang Industri &mdash; Tahun {{ date('Y') }}</div>
            <div class="title-rule"></div>
        </div>

        <!-- Student Information -->
        <div class="student-information">
            <div class="section-heading">Data Mahasiswa</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Nama Lengkap</td>
                    <td class="info-value">{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">NIM</td>
                    <td class="info-value">{{ $student->nim }}</td>
                </tr>
                <tr>
                    <td class="info-label">Perguruan Tinggi</td>
                    <td class="info-value">{{ $student->universitas }}</td>
                </tr>
                <tr>
                    <td class="info-label">Program Studi</td>
                    <td class="info-value">{{ $student->program_studi ?? 'Tidak tercantum' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Pembimbing Lapangan</td>
                    <td class="info-value">{{ $supervisor->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Periode Magang</td>
                    <td class="info-value">
                        @if(isset($student->periode_mulai) && isset($student->periode_selesai))
                            {{ date('d F Y', strtotime($student->periode_mulai)) }} s.d. {{ date('d F Y', strtotime($student->periode_selesai)) }}
                        @else
                            Tidak tercantum
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Tanggal Cetak</td>
                    <td class="info-value">{{ $date }}</td>
                </tr>
            </table>
        </div>

        <!-- Grades Section -->
        <div class="grades-section">
            <div class="section-heading">Rincian Penilaian Tugas</div>

            <table class="grades-table">
                <thead>
                    <tr>
                        <th class="col-center" style="width: 8%;">No.</th>
                        <th style="width: 40%;">Nama Tugas</th>
                        <th class="col-center" style="width: 14%;">Nilai</th>
                        <th style="width: 38%;">Komentar Pembimbing</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $submission)
                        <tr>
                            <td class="row-index">{{ $index + 1 }}</td>
                            <td class="task-name-cell">{{ $submission->task->title }}</td>
                            <td class="grade-cell">
                                @php
                                    $grade = $submission->grade;
                                    $gradeClass = '';
                                    if (is_numeric($grade)) {
                                        if ($grade >= 85) $gradeClass = 'grade-excellent';
                                        elseif ($grade >= 75) $gradeClass = 'grade-good';
                                        elseif ($grade >= 65) $gradeClass = 'grade-satisfactory';
                                        else $gradeClass = 'grade-needs-improvement';
                                    } elseif (in_array(strtoupper($grade), ['A', 'A+', 'A-'])) {
                                        $gradeClass = 'grade-excellent';
                                    } elseif (in_array(strtoupper($grade), ['B', 'B+', 'B-'])) {
                                        $gradeClass = 'grade-good';
                                    } elseif (in_array(strtoupper($grade), ['C', 'C+', 'C-'])) {
                                        $gradeClass = 'grade-satisfactory';
                                    } else {
                                        $gradeClass = 'grade-needs-improvement';
                                    }
                                @endphp
                                <span class="grade-badge {{ $gradeClass }}">{{ $grade }}</span>
                            </td>
                            <td class="comment-cell">
                                {{ $submission->comments ?? 'Tidak ada komentar khusus.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <strong>Belum ada penilaian</strong><br>
                                    Belum terdapat nilai yang diberikan untuk mahasiswa yang bersangkutan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Grade Legend -->
            @if($submissions->count() > 0)
                <div class="grade-legend">
                    <div class="grade-legend-title">Keterangan Klasifikasi Nilai</div>
                    <table class="legend-table">
                        <tr>
                            <td><span class="grade-badge grade-excellent">A</span> Sangat Baik (85&ndash;100)</td>
                            <td><span class="grade-badge grade-good">B</span> Baik (75&ndash;84)</td>
                            <td><span class="grade-badge grade-satisfactory">C</span> Cukup (65&ndash;74)</td>
                            <td><span class="grade-badge grade-needs-improvement">D</span> Perlu Perbaikan (&lt;65)</td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>

        <!-- Summary Section -->
        @if($submissions->count() > 0)
            <div class="summary-section">
                <div class="section-heading">Ringkasan Penilaian</div>
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

                <table class="summary-cards">
                    <tr>
                        <td>
                            <div class="summary-stat-value">{{ $gradedTasks }}/{{ $totalTasks }}</div>
                            <div class="summary-stat-label">Tugas Dinilai ({{ $completionRate }}%)</div>
                        </td>
                        <td>
                            <div class="summary-stat-value">{{ $averageGrade ? number_format($averageGrade, 1) : '&mdash;' }}</div>
                            <div class="summary-stat-label">Rata-rata Nilai</div>
                        </td>
                        <td>
                            <div class="summary-stat-value">{{ $excellentCount }}</div>
                            <div class="summary-stat-label">Nilai Sangat Baik (A)</div>
                        </td>
                        <td>
                            <div class="summary-stat-value">{{ $goodCount }}</div>
                            <div class="summary-stat-label">Nilai Baik (B)</div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-left">
                <div class="signature-title">Mengetahui,<br>Pembimbing Lapangan</div>
                <div class="signature-name">{{ $supervisor->name }}</div>
                <div class="signature-position">NIP. {{ $supervisor->nip ?? '________________' }}</div>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            Dokumen ini dicetak secara otomatis pada {{ $date }} &middot;
            ID Dokumen: RPT-{{ strtoupper(substr(md5($student->user->name . $date), 0, 8)) }}
        </div>
    </div>
</body>
</html>
