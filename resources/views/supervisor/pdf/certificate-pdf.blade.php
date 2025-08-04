<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - {{ $student->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&family=Garamond:wght@400;700&display=swap');
        
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
            padding: 20px;
        }
        
        .certificate-container {
            max-width: 297mm; /* A4 landscape width */
            height: 210mm; /* A4 landscape height */
            margin: 0 auto;
            background: white;
            position: relative;
            border: 4px solid #000000;
            padding: 20mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* Decorative Border */
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 2px solid #000000;
            pointer-events: none;
        }
        
        .certificate-container::after {
            content: '';
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 1px solid #666666;
            pointer-events: none;
        }
        
        /* Official Header */
        .certificate-header {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            z-index: 10;
        }
        
        .republic-header {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #000000;
        }
        
        .ministry-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            color: #000000;
        }
        
        .institution-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            color: #000000;
        }
        
        .institution-subtitle {
            font-size: 12pt;
            margin-bottom: 3px;
            color: #000000;
        }
        
        .institution-address {
            font-size: 10pt;
            color: #333333;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .divider-line {
            width: 100%;
            height: 3px;
            background: #000000;
            margin: 10px 0;
        }
        
        /* Certificate Title */
        .certificate-title-section {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .certificate-number {
            font-size: 10pt;
            color: #000000;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .certificate-title {
            font-family: 'Garamond', serif;
            font-size: 28pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #000000;
            margin-bottom: 8px;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 8px;
        }
        
        .certificate-subtitle {
            font-size: 14pt;
            color: #000000;
            font-style: italic;
            margin-bottom: 15px;
        }
        
        /* Certificate Content */
        .certificate-content {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 15px 0;
        }
        
        .certificate-statement {
            font-size: 12pt;
            color: #000000;
            margin-bottom: 15px;
            line-height: 1.8;
        }
        
        .student-name {
            font-family: 'Garamond', serif;
            font-size: 24pt;
            font-weight: bold;
            color: #000000;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 8px;
        }
        
        /* Student Details */
        .student-details {
            margin: 20px auto;
            max-width: 600px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            color: #000000;
        }
        
        .details-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .details-table td:first-child {
            width: 150px;
            font-weight: bold;
            text-align: left;
        }
        
        .details-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .details-table td:last-child {
            text-align: left;
        }
        
        /* Achievement Statement */
        .achievement-statement {
            font-size: 11pt;
            color: #000000;
            line-height: 1.6;
            margin: 20px 0;
            text-align: justify;
            text-align-last: center;
        }
        
        .achievement-statement strong {
            font-weight: bold;
        }
        
        /* Grade Section */
        .grade-section {
            margin: 20px 0;
            text-align: center;
        }
        
        .grade-statement {
            font-size: 12pt;
            color: #000000;
            margin-bottom: 8px;
        }
        
        .final-grade {
            font-family: 'Garamond', serif;
            font-size: 18pt;
            font-weight: bold;
            color: #000000;
            border: 2px solid #000000;
            padding: 8px 16px;
            display: inline-block;
            margin: 8px 0;
        }
        
        /* Comments Section */
        .comments-section {
            margin: 15px 0;
            font-size: 10pt;
            color: #000000;
            font-style: italic;
            text-align: center;
        }
        
        /* Signature Section */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
            position: relative;
        }
        
        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 30px;
            position: relative;
        }
        
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 30px;
            position: relative;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-location-date {
            font-size: 11pt;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .signature-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 50px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000000;
            margin-bottom: 8px;
            height: 1px;
        }
        
        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
        }
        
        .signature-position {
            font-size: 10pt;
            color: #000000;
            margin-bottom: 2px;
        }
        
        .signature-nip {
            font-size: 9pt;
            color: #000000;
        }
        
        /* Official Stamps - Positioned over signatures */
        .stamp-area-left {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%) rotate(-15deg);
            width: 50mm;
            height: 50mm;
            border: 2px dashed #666666;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #666666;
            text-align: center;
            line-height: 1.1;
            background: rgba(255, 255, 255, 0.9);
            z-index: 5;
        }
        
        .stamp-area-right {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%) rotate(15deg);
            width: 50mm;
            height: 50mm;
            border: 2px dashed #666666;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #666666;
            text-align: center;
            line-height: 1.1;
            background: rgba(255, 255, 255, 0.9);
            z-index: 5;
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 36pt;
            color: rgba(0, 0, 0, 0.03);
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 8px;
        }
        
        /* Certificate Footer */
        .certificate-footer {
            text-align: center;
            font-size: 8pt;
            color: #666666;
            margin-top: 15px;
            border-top: 1px solid #cccccc;
            padding-top: 8px;
        }
        
        /* Security Features */
        .security-code {
            position: absolute;
            bottom: 8mm;
            right: 15mm;
            font-size: 7pt;
            color: #999999;
            font-family: 'Courier New', monospace;
        }
        
        .qr-placeholder {
            position: absolute;
            bottom: 6mm;
            left: 15mm;
            width: 12mm;
            height: 12mm;
            border: 1px solid #cccccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5pt;
            color: #999999;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .certificate-container {
                border-color: #000000;
                box-shadow: none;
                margin: 0;
            }
            
            .stamp-area-left,
            .stamp-area-right {
                border-color: #333333;
                color: #333333;
            }
            
            .watermark {
                color: rgba(0, 0, 0, 0.02);
            }
        }
        
        /* Decorative Elements */
        .corner-ornament {
            position: absolute;
            width: 25mm;
            height: 25mm;
            background-image: 
                radial-gradient(circle at center, transparent 40%, #000000 40%, #000000 45%, transparent 45%),
                linear-gradient(45deg, transparent 48%, #000000 48%, #000000 52%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, #000000 48%, #000000 52%, transparent 52%);
            opacity: 0.08;
        }
        
        .corner-ornament.top-left {
            top: 15mm;
            left: 15mm;
        }
        
        .corner-ornament.top-right {
            top: 15mm;
            right: 15mm;
            transform: rotate(90deg);
        }
        
        .corner-ornament.bottom-left {
            bottom: 15mm;
            left: 15mm;
            transform: rotate(-90deg);
        }
        
        .corner-ornament.bottom-right {
            bottom: 15mm;
            right: 15mm;
            transform: rotate(180deg);
        }
        
        /* Responsive for smaller screens */
        @media (max-width: 1200px) {
            .certificate-container {
                max-width: 100%;
                height: auto;
                min-height: 210mm;
            }
            
            .certificate-title {
                font-size: 24pt;
            }
            
            .student-name {
                font-size: 20pt;
            }
            
            .stamp-area-left,
            .stamp-area-right {
                width: 40mm;
                height: 40mm;
                font-size: 6pt;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Watermark -->
        <div class="watermark">BAKTI KOMINFO</div>
        
        <!-- Corner Ornaments -->
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>
        
        <!-- Certificate Header -->
        <div class="certificate-header">
            <div class="ministry-name">Kementerian Komunikasi dan Informatika</div>
            <div class="republic-header">Republik Indonesia</div>
            <div class="institution-name">BAKTI KOMINFO</div>
            <div class="institution-subtitle">Badan Aksesibilitas Telekomunikasi dan Informasi</div>
            <div class="institution-address">
                Centennial Tower Lt.42-45, Jakarta Selatan<br>
                Telp: (021) 31936590, Fax: (021) 31936590<br>
                Website: www.baktikomdigi.id | Email: humas@baktikominfo.id
            </div>
            <div class="divider-line"></div>
        </div>
        
        <!-- Certificate Title -->
        <div class="certificate-title-section">
            <div class="certificate-number">
                Nomor: {{ sprintf('%03d', rand(100, 999)) }}/BAKTI-SERTIFIKAT/{{ date('m/Y') }}
            </div>
            <div class="certificate-title">Sertifikat</div>
            <div class="certificate-subtitle">Program Magang Industri</div>
        </div>
        
        <!-- Certificate Content -->
        <div class="certificate-content">
            <div class="certificate-statement">
                Dengan ini menyatakan bahwa:
            </div>
            
            <div class="student-name">{{ strtoupper($student->user->name) }}</div>
            
            <div class="student-details">
                <table class="details-table">
                    <tr>
                        <td>Nomor Induk Mahasiswa</td>
                        <td>:</td>
                        <td>{{ $student->nim ?? 'Tidak tercantum' }}</td>
                    </tr>
                    <tr>
                        <td>Perguruan Tinggi</td>
                        <td>:</td>
                        <td>{{ $student->universitas ?? 'Tidak tercantum' }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>{{ $student->program_studi ?? 'Tidak tercantum' }}</td>
                    </tr>
                    <tr>
                        <td>Periode Pelaksanaan</td>
                        <td>:</td>
                        <td>
                            @if(isset($student->periode_mulai) && isset($student->periode_selesai))
                                {{ date('d F Y', strtotime($student->periode_mulai)) }} sampai dengan {{ date('d F Y', strtotime($student->periode_selesai)) }}
                            @else
                                Tidak tercantum
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="achievement-statement">
                telah <strong>BERHASIL MENYELESAIKAN</strong> Program Magang Industri di lingkungan 
                <strong>Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI KOMINFO)</strong> 
                dengan memenuhi seluruh persyaratan yang telah ditetapkan dan menunjukkan dedikasi, 
                kompetensi, serta profesionalisme yang tinggi selama periode pelaksanaan magang.
            </div>
            
            <div class="grade-section">
                <div class="grade-statement">dengan predikat nilai akhir:</div>
                <div class="final-grade">{{ strtoupper($assessment->final_grade) }}</div>
            </div>
            
            @if($assessment->overall_comments)
                <div class="comments-section">
                    <strong>Catatan Pembimbing:</strong><br>
                    "{{ $assessment->overall_comments }}"
                </div>
            @endif
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-left">
                <!-- Official Stamp for Supervisor -->
                <div class="stamp-area-left">
                    STEMPEL<br>
                    PEMBIMBING<br>
                    LAPANGAN
                </div>
                <div class="signature-box">
                    <div class="signature-location-date">Jakarta, {{ date('d F Y') }}</div>
                    <div class="signature-title">Pembimbing Lapangan</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ strtoupper($supervisorName) }}</div>
                    <div class="signature-position">{{ $supervisor->position ?? 'Pembimbing Lapangan' }}</div>
                    <div class="signature-nip">NIP. {{ $supervisor->nip ?? '________________' }}</div>
                </div>
            </div>
            <div class="signature-right">
                <!-- Official Stamp for Head -->
                <div class="stamp-area-right">
                    STEMPEL<br>
                    KEPALA<br>
                    BALAI
                </div>
                <div class="signature-box">
                    <div class="signature-location-date">Jakarta, {{ date('d F Y') }}</div>
                    <div class="signature-title">Kepala Balai</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">DR. [NAMA KEPALA BAKTI]</div>
                    <div class="signature-position">Kepala BAKTI</div>
                    <div class="signature-nip">NIP. ________________</div>
                </div>
            </div>
        </div>
        
        <!-- Security Features -->
        <div class="qr-placeholder">QR</div>
        <div class="security-code">
            SEC: {{ strtoupper(substr(md5($student->user->name . $generatedDate), 0, 12)) }}
        </div>
        
        <!-- Certificate Footer -->
        <div class="certificate-footer">
            Sertifikat ini diterbitkan secara resmi pada {{ $generatedDate }} dan telah terdaftar dalam sistem database BAKTI KOMINFO
        </div>
    </div>
</body>
</html>