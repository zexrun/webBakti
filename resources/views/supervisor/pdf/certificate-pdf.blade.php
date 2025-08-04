<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - {{ $student->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .certificate-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            position: relative;
        }
        
        /* Decorative border pattern */
        .certificate-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 30%, rgba(103, 126, 234, 0.1) 30%, rgba(103, 126, 234, 0.1) 70%, transparent 70%),
                linear-gradient(-45deg, transparent 30%, rgba(118, 75, 162, 0.1) 30%, rgba(118, 75, 162, 0.1) 70%, transparent 70%);
            background-size: 20px 20px;
            pointer-events: none;
        }
        
        .certificate-border {
            border: 8px solid transparent;
            border-image: linear-gradient(45deg, #667eea, #764ba2, #667eea) 1;
            margin: 20px;
            border-radius: 15px;
            background: white;
            position: relative;
            z-index: 1;
        }
        
        .certificate-content {
            padding: 60px 50px;
            text-align: center;
            position: relative;
        }
        
        /* Header Section */
        .header {
            margin-bottom: 40px;
            position: relative;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            box-shadow: 0 10px 25px rgba(103, 126, 234, 0.3);
        }
        
        .logo-icon::before {
            content: '🏛️';
            font-size: 32px;
        }
        
        .organization-info {
            text-align: left;
        }
        
        .organization-name {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .organization-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 400;
            line-height: 1.4;
        }
        
        /* Certificate Title */
        .certificate-title {
            margin: 40px 0 30px;
            position: relative;
        }
        
        .certificate-title::before,
        .certificate-title::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
        }
        
        .certificate-title::before {
            left: -120px;
        }
        
        .certificate-title::after {
            right: -120px;
        }
        
        .title-main {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .title-subtitle {
            font-size: 20px;
            color: #7f8c8d;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        /* Content Section */
        .certificate-statement {
            font-size: 18px;
            color: #34495e;
            margin: 30px 0 20px;
            font-weight: 400;
        }
        
        .student-name {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: #e74c3c;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
        }
        
        .student-name::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #e74c3c, transparent);
        }
        
        /* Student Details Table */
        .student-details {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 30px;
            margin: 40px 0;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .details-table {
            width: 100%;
            font-size: 16px;
            border-collapse: separate;
            border-spacing: 0 15px;
        }
        
        .details-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        
        .details-table td:first-child {
            font-weight: 600;
            color: #2c3e50;
            width: 200px;
            text-align: left;
        }
        
        .details-table td:nth-child(2) {
            color: #7f8c8d;
            width: 20px;
            text-align: center;
        }
        
        .details-table td:last-child {
            color: #34495e;
            text-align: left;
            font-weight: 500;
        }
        
        /* Achievement Section */
        .achievement-text {
            font-size: 18px;
            color: #34495e;
            line-height: 1.6;
            margin: 30px 0;
            font-weight: 400;
        }
        
        .achievement-text strong {
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Grade Section */
        .final-grade {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
        }
        
        .grade-label {
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .grade-value {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        
        /* Comments Section */
        .supervisor-comments {
            background: #fff8e1;
            border-left: 5px solid #ffc107;
            padding: 25px;
            margin: 30px 0;
            border-radius: 0 15px 15px 0;
            text-align: left;
        }
        
        .comments-label {
            font-weight: 600;
            color: #f57c00;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .comments-text {
            font-style: italic;
            color: #5d4037;
            font-size: 16px;
            line-height: 1.6;
            position: relative;
        }
        
        .comments-text::before,
        .comments-text::after {
            font-size: 24px;
            color: #ffc107;
            font-weight: bold;
        }
        
        .comments-text::before {
            content: '"';
            margin-right: 5px;
        }
        
        .comments-text::after {
            content: '"';
            margin-left: 5px;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }
        
        .signature-box {
            flex: 1;
            text-align: center;
        }
        
        .signature-space {
            height: 80px;
            border-bottom: 2px solid #34495e;
            margin-bottom: 15px;
            position: relative;
        }
        
        .signature-space::before {
            content: '✓';
            position: absolute;
            right: 10px;
            bottom: 5px;
            color: #27ae60;
            font-size: 20px;
            font-weight: bold;
        }
        
        .signature-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .signature-title {
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 400;
        }
        
        /* Footer */
        .certificate-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #ecf0f1;
            font-size: 12px;
            color: #95a5a6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .generation-date {
            display: flex;
            align-items: center;
        }
        
        .generation-date::before {
            content: '📅';
            margin-right: 8px;
        }
        
        .certificate-id {
            font-family: 'Courier New', monospace;
            background: #ecf0f1;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
        }
        
        /* Decorative Elements */
        .decorative-corner {
            position: absolute;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            opacity: 0.1;
        }
        
        .decorative-corner.top-left {
            top: 0;
            left: 0;
            border-radius: 0 0 60px 0;
        }
        
        .decorative-corner.top-right {
            top: 0;
            right: 0;
            border-radius: 0 0 0 60px;
        }
        
        .decorative-corner.bottom-left {
            bottom: 0;
            left: 0;
            border-radius: 0 60px 0 0;
        }
        
        .decorative-corner.bottom-right {
            bottom: 0;
            right: 0;
            border-radius: 60px 0 0 0;
        }
        
        /* Responsive adjustments for PDF */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .certificate-wrapper {
                box-shadow: none;
                border-radius: 0;
            }
            
            .certificate-border {
                margin: 0;
            }
        }
        
        /* Animation for web preview */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .certificate-content > * {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .certificate-content > *:nth-child(2) { animation-delay: 0.1s; }
        .certificate-content > *:nth-child(3) { animation-delay: 0.2s; }
        .certificate-content > *:nth-child(4) { animation-delay: 0.3s; }
        .certificate-content > *:nth-child(5) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate-border">
            <div class="certificate-content">
                <!-- Decorative Corners -->
                <div class="decorative-corner top-left"></div>
                <div class="decorative-corner top-right"></div>
                <div class="decorative-corner bottom-left"></div>
                <div class="decorative-corner bottom-right"></div>
                
                <!-- Header Section -->
                <div class="header">
                    <div class="logo-section">
                        <div class="logo-icon"></div>
                        <div class="organization-info">
                            <div class="organization-name">BAKTI KOMINFO</div>
                            <div class="organization-subtitle">
                                Balai Besar Pengkajian dan Pengembangan<br>
                                Komunikasi dan Informatika
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Title -->
                <div class="certificate-title">
                    <div class="title-main">Sertifikat Kelulusan</div>
                    <div class="title-subtitle">Program Magang Industri</div>
                </div>
                
                <!-- Certificate Statement -->
                <div class="certificate-statement">
                    Dengan ini menyatakan bahwa:
                </div>
                
                <!-- Student Name -->
                <div class="student-name">{{ $student->user->name }}</div>
                
                <!-- Student Details -->
                <div class="student-details">
                    <table class="details-table">
                        <tr>
                            <td>NIM</td>
                            <td>:</td>
                            <td>{{ $student->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Universitas</td>
                            <td>:</td>
                            <td>{{ $student->universitas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>:</td>
                            <td>{{ $student->program_studi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Periode Magang</td>
                            <td>:</td>
                            <td>{{ date('d F Y', strtotime($student->periode_mulai)) }} - {{ date('d F Y', strtotime($student->periode_selesai)) }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Achievement Text -->
                <div class="achievement-text">
                    telah berhasil menyelesaikan program magang di <strong>BAKTI KOMINFO</strong> 
                    dengan memenuhi semua persyaratan yang telah ditetapkan dan menunjukkan 
                    dedikasi serta kompetensi yang excellent selama periode magang.
                </div>
                
                <!-- Final Grade -->
                <div class="final-grade">
                    <div class="grade-label">Nilai Akhir</div>
                    <div class="grade-value">{{ $assessment->final_grade }}</div>
                </div>
                
                <!-- Supervisor Comments -->
                @if($assessment->overall_comments)
                    <div class="supervisor-comments">
                        <div class="comments-label">Catatan Pembimbing:</div>
                        <div class="comments-text">{{ $assessment->overall_comments }}</div>
                    </div>
                @endif
                
                <!-- Signature Section -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ $supervisorName }}</div>
                        <div class="signature-title">Pembimbing Lapangan</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-space"></div>
                        <div class="signature-name">Kepala BAKTI</div>
                        <div class="signature-title">Kepala Balai</div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="certificate-footer">
                    <div class="generation-date">
                        Sertifikat ini di-generate pada: {{ $generatedDate }}
                    </div>
                    <div class="certificate-id">
                        ID: CERT-{{ strtoupper(substr(md5($student->user->name . $generatedDate), 0, 8)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>