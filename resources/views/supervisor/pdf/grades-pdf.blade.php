<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Magang - {{ $student->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #2c3e50;
            line-height: 1.6;
        }
        
        .report-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        
        /* Header Section */
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .report-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.05) 20px
            );
            animation: movePattern 20s linear infinite;
        }
        
        @keyframes movePattern {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .report-title {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .report-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .header-icon {
            position: absolute;
            top: 20px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            backdrop-filter: blur(10px);
        }
        
        /* Student Information Section */
        .student-info {
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        /* Grades Table Section */
        .grades-section {
            padding: 40px;
        }
        
        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .table-header th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        
        .table-header th:first-child {
            border-radius: 0;
        }
        
        .table-header th:last-child {
            border-radius: 0;
        }
        
        .grades-table tbody tr {
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .grades-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .grades-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .grades-table td {
            padding: 18px 15px;
            vertical-align: top;
            border: none;
        }
        
        .task-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .task-description {
            font-size: 12px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            height: 35px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .grade-a { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .grade-b { background: linear-gradient(135deg, #3498db, #5dade2); }
        .grade-c { background: linear-gradient(135deg, #f39c12, #f1c40f); }
        .grade-d { background: linear-gradient(135deg, #e67e22, #f39c12); }
        .grade-e { background: linear-gradient(135deg, #e74c3c, #ec7063); }
        .grade-default { background: linear-gradient(135deg, #95a5a6, #bdc3c7); }
        
        .comment-text {
            color: #34495e;
            font-size: 13px;
            line-height: 1.5;
            max-width: 250px;
        }
        
        .no-comment {
            color: #bdc3c7;
            font-style: italic;
            font-size: 12px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .empty-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #95a5a6;
        }
        
        .empty-description {
            font-size: 14px;
            color: #bdc3c7;
        }
        
        /* Statistics Section */
        .statistics-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px 40px;
            border-top: 1px solid #e9ecef;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f3f4;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        /* Footer */
        .report-footer {
            background: #2c3e50;
            color: white;
            padding: 25px 40px;
            text-align: center;
            font-size: 12px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .generation-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .report-id {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .report-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .report-header::before {
                display: none;
            }
            
            .info-card:hover,
            .grades-table tbody tr:hover {
                transform: none;
                background-color: transparent;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }
            
            .report-header,
            .student-info,
            .grades-section {
                padding: 25px 20px;
            }
            
            .report-title {
                font-size: 24px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .grades-table {
                font-size: 12px;
            }
            
            .table-header th,
            .grades-table td {
                padding: 12px 8px;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header Section -->
        <div class="report-header">
            <div class="header-icon">📊</div>
            <div class="header-content">
                <h1 class="report-title">Rekap Nilai Magang</h1>
                <p class="report-subtitle">Laporan Penilaian Komprehensif</p>
            </div>
        </div>
        
        <!-- Student Information Section -->
        <div class="student-info">
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Nama Mahasiswa</div>
                    <div class="info-value">{{ $student->user->name }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Nomor Induk Mahasiswa</div>
                    <div class="info-value">{{ $student->nim }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Universitas</div>
                    <div class="info-value">{{ $student->universitas }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Pembimbing Lapangan</div>
                    <div class="info-value">{{ $supervisor->name }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Tanggal Cetak</div>
                    <div class="info-value">{{ $date }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Status Laporan</div>
                    <div class="info-value">✅ Lengkap</div>
                </div>
            </div>
        </div>
        
        <!-- Grades Section -->
        <div class="grades-section">
            <h2 class="section-title">Detail Penilaian Tugas</h2>
            
            <div class="table-container">
                <table class="grades-table">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 40%;">Nama Tugas</th>
                            <th style="width: 15%;">Nilai</th>
                            <th style="width: 45%;">Komentar & Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td>
                                    <div class="task-name">{{ $submission->task->title }}</div>
                                    @if($submission->task->description)
                                        <div class="task-description">{{ Str::limit($submission->task->description, 100) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $grade = $submission->grade;
                                        $gradeClass = 'grade-default';
                                        if (is_numeric($grade)) {
                                            if ($grade >= 85) $gradeClass = 'grade-a';
                                            elseif ($grade >= 75) $gradeClass = 'grade-b';
                                            elseif ($grade >= 65) $gradeClass = 'grade-c';
                                            elseif ($grade >= 55) $gradeClass = 'grade-d';
                                            else $gradeClass = 'grade-e';
                                        } elseif (in_array(strtoupper($grade), ['A', 'A+', 'A-'])) {
                                            $gradeClass = 'grade-a';
                                        } elseif (in_array(strtoupper($grade), ['B', 'B+', 'B-'])) {
                                            $gradeClass = 'grade-b';
                                        } elseif (in_array(strtoupper($grade), ['C', 'C+', 'C-'])) {
                                            $gradeClass = 'grade-c';
                                        } elseif (in_array(strtoupper($grade), ['D', 'D+', 'D-'])) {
                                            $gradeClass = 'grade-d';
                                        } elseif (in_array(strtoupper($grade), ['E', 'F'])) {
                                            $gradeClass = 'grade-e';
                                        }
                                    @endphp
                                    <span class="grade-badge {{ $gradeClass }}">{{ $grade }}</span>
                                </td>
                                <td>
                                    @if($submission->comments)
                                        <div class="comment-text">{{ $submission->comments }}</div>
                                    @else
                                        <div class="no-comment">Tidak ada komentar</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-icon">📝</div>
                                        <div class="empty-title">Belum Ada Penilaian</div>
                                        <div class="empty-description">
                                            Belum ada nilai yang diberikan untuk mahasiswa ini.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Statistics Section -->
        @if($submissions->count() > 0)
            <div class="statistics-section">
                @php
                    $totalTasks = $submissions->count();
                    $gradedTasks = $submissions->whereNotNull('grade')->count();
                    $averageGrade = $submissions->whereNotNull('grade')->avg('grade');
                    $completionRate = $totalTasks > 0 ? round(($gradedTasks / $totalTasks) * 100) : 0;
                @endphp
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">{{ $totalTasks }}</div>
                        <div class="stat-label">Total Tugas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $gradedTasks }}</div>
                        <div class="stat-label">Tugas Dinilai</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $completionRate }}%</div>
                        <div class="stat-label">Tingkat Penyelesaian</div>
                    </div>
                    @if($averageGrade)
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($averageGrade, 1) }}</div>
                            <div class="stat-label">Rata-rata Nilai</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="report-footer">
            <div class="footer-content">
                <div class="generation-info">
                    <span>📅</span>
                    <span>Laporan dibuat pada: {{ $date }}</span>
                </div>
                <div class="report-id">
                    ID: RPT-{{ strtoupper(substr(md5($student->user->name . $date), 0, 8)) }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>