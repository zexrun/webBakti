<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            padding: 50px;
            background-color: #fff;
            color: #000;
        }
        .container {
            border: 8px solid #2c3e50;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .company {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .name {
            font-size: 26px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            color: #e74c3c;
        }
        .content {
            font-size: 16px;
            margin: 20px 0;
        }
        .table {
            margin: 20px auto;
            text-align: left;
            width: 100%;
            font-size: 14px;
        }
        .table td {
            padding: 5px 0;
        }
        .grade {
            margin: 30px 0;
            font-size: 18px;
        }
        .grade strong {
            font-size: 28px;
            color: #27ae60;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .sig-box {
            width: 40%;
        }
        .sig-line {
            margin-top: 50px;
            border-top: 2px solid #000;
        }
        .sig-name {
            margin-top: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">BAKTI KOMINFO</div>
        <div class="company">Balai Besar Pengkajian dan Pengembangan Komunikasi dan Informatika</div>

        <div class="title">Sertifikat Kelulusan</div>
        <div class="subtitle">Program Magang Industri</div>

        <div class="content">Dengan ini menyatakan bahwa:</div>

        <div class="name">{{ $student->user->name }}</div>

        <table class="table">
            <tr>
                <td style="width: 180px;">NIM</td>
                <td>: {{ $student->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td>Universitas</td>
                <td>: {{ $student->universitas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $student->program_studi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Periode Magang</td>
                <td>: {{ date('d F Y', strtotime($student->periode_mulai)) }} - {{ date('d F Y', strtotime($student->periode_selesai)) }}</td>
            </tr>
        </table>

        <div class="content">
            telah berhasil menyelesaikan program magang di <strong>BAKTI KOMINFO</strong> dengan memenuhi semua persyaratan yang telah ditetapkan.
        </div>

        <div class="grade">
            Nilai Akhir: <strong>{{ $assessment->final_grade }}</strong>
        </div>

        @if($assessment->overall_comments)
            <div class="content">
                <em>Catatan Pembimbing:</em><br>
                "{{ $assessment->overall_comments }}"
            </div>
        @endif

        <div class="signature">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $supervisorName }}</div>
                <div>Pembimbing Lapangan</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">Kepala BAKTI</div>
                <div>Kepala Balai</div>
            </div>
        </div>

        <div class="footer">
            Sertifikat ini di-generate pada: {{ $generatedDate }}
        </div>
    </div>
</body>
</html>
