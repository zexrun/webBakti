<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Magang - {{ $student->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&family=Arial:wght@400;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.6;
            padding: 40px;
            font-size: 12pt;
        }
        
        .report-container {
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            background: white;
            min-height: 297mm; /* A4 height */
            position: relative;
            border: 2px solid #000000;
            padding: 30mm 25mm 25mm 25mm; /* Top, Right, Bottom, Left margins */
        }
        
        /* Official Header */
        .official-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px double #000000;
        }
        
        .institution-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #000000;
        }
        
        .institution-subtitle {
            font-size: 14pt;
            margin-bottom: 3px;
            color: #000000;
        }
        
        .institution-address {
            font-size: 11pt;
            color: #333333;
            margin-bottom: 15px;
        }
        
        .document-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 20px;
            margin-bottom: 5px;
            color: #000000;
        }
        
        .document-subtitle {
            font-size: 12pt;
            font-style: italic;
            color: #000000;
        }
        
        /* Document Number */
        .document-number {
            text-align: right;
            margin-bottom: 30px;
            font-size: 11pt;
            color: #000000;
        }
        
        /* Student Information */
        .student-information {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #000000;
            border-bottom: 1px solid #000000;
            padding-bottom: 3px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 11pt;
            color: #000000;
        }
        
        .info-table td:first-child {
            width: 180px;
            font-weight: bold;
        }
        
        .info-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .info-table td:last-child {
            font-weight: normal;
        }
        
        /* Grades Table */
        .grades-section {
            margin-bottom: 40px;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000000;
            margin-bottom: 20px;
        }
        
        .grades-table th {
            background-color: #f5f5f5;
            border: 1px solid #000000;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            color: #000000;
            text-transform: uppercase;
        }
        
        .grades-table td {
            border: 1px solid #000000;
            padding: 10px 8px;
            vertical-align: top;
            font-size: 10pt;
            color: #000000;
        }
        
        .grades-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .task-name-cell {
            text-align: left;
            font-weight: bold;
        }
        
        .grade-cell {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }
        
        .comment-cell {
            text-align: left;
            font-size: 10pt;
            line-height: 1.4;
        }
        
        /* Grade Classification */
        .grade-excellent { color: #006400; } /* Dark Green */
        .grade-good { color: #0066cc; } /* Blue */
        .grade-satisfactory { color: #cc6600; } /* Orange */
        .grade-needs-improvement { color: #cc0000; } /* Red */
        
        /* Summary Section */
        .summary-section {
            margin-bottom: 40px;
            border: 1px solid #000000;
            padding: 15px;
            background-color: #f9f9f9;
        }
        
        .summary-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: #000000;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 5px 10px;
            font-size: 11pt;
            color: #000000;
        }
        
        .summary-table td:first-child {
            font-weight: bold;
            width: 200px;
        }
        
        .summary-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            font-style: italic;
            color: #666666;
            border: 1px dashed #cccccc;
            background-color: #f9f9f9;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        
        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 60px;
            color: #000000;
        }
        
        .signature-line {
            border-bottom: 1px solid #000000;
            margin-bottom: 5px;
            height: 1px;
        }
        
        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
        }
        
        .signature-position {
            font-size: 10pt;
            color: #000000;
        }
        
        /* Footer */
        .document-footer {
            position: absolute;
            bottom: 15mm;
            left: 25mm;
            right: 25mm;
            text-align: center;
            font-size: 9pt;
            color: #666666;
            border-top: 1px solid #cccccc;
            padding-top: 10px;
        }
        
        /* Official Stamp Area */
        .stamp-area {
            position: absolute;
            top: 50mm;
            right: 30mm;
            width: 80px;
            height: 80px;
            border: 2px dashed #cccccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999999;
            text-align: center;
            line-height: 1.2;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .report-container {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 20mm;
            }
            
            .stamp-area {
                border-color: #000000;
                color: #000000;
            }
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
        
        /* Formal Table Numbering */
        .table-number {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            color: #000000;
        }
        
        /* Classification Legend */
        .grade-legend {
            margin-top: 15px;
            font-size: 9pt;
            color: #333333;
        }
        
        .grade-legend-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .legend-item {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Official Header -->
        <div class="official-header">
            <div class="institution-name">BAKTI KOMINFO</div>
            <div class="institution-subtitle">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="institution-address">
                Centennial Tower Lt.42-45, Jakarta Selatan<br>
                Telp: (021) 31936590, Fax: (021) 31936590
            </div>
            <div class="document-title">Rekap Nilai Magang</div>
            <div class="document-subtitle">Program Magang Industri Tahun {{ date('Y') }}</div>
        </div>
        
        <!-- Document Number -->
        <div class="document-number">
            Nomor: {{ sprintf('%03d', rand(100, 999)) }}/BAKTI/{{ date('m/Y') }}
        </div>
        
        <!-- Student Information -->
        <div class="student-information">
            <div class="section-title">Data Mahasiswa</div>
            <table class="info-table">
                <tr>
                    <td>Nama Lengkap</td>
                    <td>:</td>
                    <td>{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk Mahasiswa</td>
                    <td>:</td>
                    <td>{{ $student->nim }}</td>
                </tr>
                <tr>
                    <td>Perguruan Tinggi</td>
                    <td>:</td>
                    <td>{{ $student->universitas }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>{{ $student->program_studi ?? 'Tidak tercantum' }}</td>
                </tr>
                <tr>
                    <td>Pembimbing Lapangan</td>
                    <td>:</td>
                    <td>{{ $supervisor->name }}</td>
                </tr>
                <tr>
                    <td>Periode Magang</td>
                    <td>:</td>
                    <td>
                        @if(isset($student->periode_mulai) && isset($student->periode_selesai))
                            {{ date('d F Y', strtotime($student->periode_mulai)) }} s.d. {{ date('d F Y', strtotime($student->periode_selesai)) }}
                        @else
                            Tidak tercantum
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Cetak Laporan</td>
                    <td>:</td>
                    <td>{{ $date }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Grades Section -->
        <div class="grades-section">
            <div class="section-title">Rincian Penilaian Tugas</div>
            <div class="table-number">Tabel 1. Daftar Nilai Tugas Magang</div>
            
            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 45%;">Nama Tugas</th>
                        <th style="width: 10%;">Nilai</th>
                        <th style="width: 40%;">Komentar Pembimbing</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $submission)
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
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
                                <span class="{{ $gradeClass }}">{{ $grade }}</span>
                            </td>
                            <td class="comment-cell">
                                {{ $submission->comments ?? 'Tidak ada komentar khusus.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <strong>BELUM ADA PENILAIAN</strong><br>
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
                    <div class="grade-legend-title">Keterangan Klasifikasi Nilai:</div>
                    <div class="legend-item"><span class="grade-excellent">■</span> Sangat Baik (A/85-100)</div>
                    <div class="legend-item"><span class="grade-good">■</span> Baik (B/75-84)</div>
                    <div class="legend-item"><span class="grade-satisfactory">■</span> Cukup (C/65-74)</div>
                    <div class="legend-item"><span class="grade-needs-improvement">■</span> Perlu Perbaikan (D/< 65)</div>
                </div>
            @endif
        </div>
        
        <!-- Summary Section -->
        @if($submissions->count() > 0)
            <div class="summary-section">
                <div class="summary-title">Ringkasan Penilaian</div>
                @php
                    $totalTasks = $submissions->count();
                    $gradedTasks = $submissions->whereNotNull('grade')->count();
                    $numericGrades = $submissions->whereNotNull('grade')->filter(function($item) {
                        return is_numeric($item->grade);
                    });
                    $averageGrade = $numericGrades->count() > 0 ? $numericGrades->avg('grade') : null;
                    $completionRate = $totalTasks > 0 ? round(($gradedTasks / $totalTasks) * 100, 1) : 0;
                    
                    // Grade distribution
                    $excellentCount = $submissions->filter(function($item) {
                        $grade = $item->grade;
                        return (is_numeric($grade) && $grade >= 85) || in_array(strtoupper($grade), ['A', 'A+', 'A-']);
                    })->count();
                    
                    $goodCount = $submissions->filter(function($item) {
                        $grade = $item->grade;
                        return (is_numeric($grade) && $grade >= 75 && $grade < 85) || in_array(strtoupper($grade), ['B', 'B+', 'B-']);
                    })->count();
                @endphp
                
                <table class="summary-table">
                    <tr>
                        <td>Total Tugas yang Diberikan</td>
                        <td>:</td>
                        <td>{{ $totalTasks }} tugas</td>
                    </tr>
                    <tr>
                        <td>Tugas yang Telah Dinilai</td>
                        <td>:</td>
                        <td>{{ $gradedTasks }} tugas ({{ $completionRate }}%)</td>
                    </tr>
                    @if($averageGrade)
                        <tr>
                            <td>Rata-rata Nilai</td>
                            <td>:</td>
                            <td>{{ number_format($averageGrade, 1) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Nilai Sangat Baik (A)</td>
                        <td>:</td>
                        <td>{{ $excellentCount }} tugas</td>
                    </tr>
                    <tr>
                        <td>Nilai Baik (B)</td>
                        <td>:</td>
                        <td>{{ $goodCount }} tugas</td>
                    </tr>
                </table>
            </div>
        @endif
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-left">
                <div class="signature-box">
                    <div class="signature-title">Mengetahui,<br>Pembimbing Lapangan</div>
                    <div class="signature-name">{{ $supervisor->name }}</div>
                    <div class="signature-position">NIP. {{ $supervisor->nip ?? '________________' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Document Footer -->
        <div class="document-footer">
            <div>
                Dokumen ini dicetak secara otomatis pada {{ $date }} | 
                ID Dokumen: RPT-{{ strtoupper(substr(md5($student->user->name . $date), 0, 8)) }}
            </div>
        </div>
    </div>
</body>
</html>