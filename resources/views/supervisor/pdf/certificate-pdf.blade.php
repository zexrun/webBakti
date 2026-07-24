<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Surat Keterangan Magang - {{ $student->user->name }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Times New Roman", Times, serif;
                background: #ffffff;
                color: #000000;
                line-height: 1.4;
                font-size: 11pt;
                margin: 0;
                padding: 0;
            }

            .document-container {
                width: 210mm;
                margin: 0 auto;
                background: white;
                padding: 15mm 20mm;
                position: relative;
            }

            /* Header Section - Kompak */
            .document-header {
                text-align: center;
                margin-bottom: 12px;
                border-bottom: 2px solid #000;
                padding-bottom: 8px;
            }

            .header-content {
                display: block;
                width: 100%;
                text-align: center; /* Ensure all header content is centered */
            }

            .ministry-name {
                font-size: 10pt;
                font-weight: bold;
                color: #000;
                margin-bottom: 2px;
                text-transform: uppercase;
                line-height: 1.2;
                text-align: center;
            }

            .agency-name {
                font-size: 12pt;
                font-weight: bold;
                color: #000;
                margin-bottom: 2px;
                text-transform: uppercase;
                line-height: 1.2;
                text-align: center;
            }

            .agency-subtitle {
                font-size: 9pt;
                color: #4a90e2;
                font-style: italic;
                margin-bottom: 5px;
                line-height: 1.2;
                text-align: center;
            }

            .contact-info {
                font-size: 7pt;
                color: #333;
                line-height: 1.2;
                text-align: center;
            }

            /* Document Title - Kompak */
            .document-title {
                text-align: center;
                margin: 10px 0;
            }

            .title-main {
                font-size: 14pt;
                font-weight: bold;
                text-transform: uppercase;
                text-decoration: underline;
                margin-bottom: 5px;
                letter-spacing: 1px;
                text-align: center;
            }

            .document-number {
                font-size: 9pt;
                margin-bottom: 10px;
                text-align: center;
            }

            /* Content Section - Optimized untuk 1 halaman */
            .document-content {
                text-align: justify;
                line-height: 1.5;
            }

            .opening-statement {
                margin-bottom: 10px;
                font-size: 11pt;
                text-align: left;
            }

            .employee-details {
                margin: 10px 0;
                padding-left: 30px;
            }

            .detail-table {
                width: 100%;
                border-collapse: collapse;
            }

            .detail-table td {
                padding: 2px 0;
                vertical-align: top;
                font-size: 11pt;
            }

            .detail-label {
                width: 100px;
                font-weight: normal;
            }

            .detail-separator {
                width: 15px;
                text-align: center;
            }

            .detail-value {
                font-weight: normal;
            }

            .main-statement {
                margin: 12px 0;
                text-align: justify;
                line-height: 1.5;
                font-size: 11pt;
            }

            .statement-highlight {
                font-weight: bold;
            }

            .closing-statement {
                margin: 12px 0;
                text-align: justify;
                font-size: 11pt;
            }

            /* Signature Section - Kompak */
            .signature-section {
                margin-top: 15px;
                width: 100%;
            }

            .signature-table {
                width: 100%;
                border-collapse: collapse;
            }

            .signature-left {
                width: 50%;
                vertical-align: top;
            }

            .signature-right {
                width: 50%;
                text-align: center;
                vertical-align: top;
            }

            .signature-date-location {
                text-align: right;
                margin-bottom: 3px;
                font-size: 10pt;
            }

            .signature-title {
                text-align: center;
                font-weight: bold;
                margin-bottom: 40px;
                font-size: 10pt;
            }

            .signature-name {
                text-align: center;
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 2px;
                font-size: 10pt;
            }

            .signature-nip {
                text-align: center;
                font-size: 9pt;
            }

            /* Tembusan Section - Kompak */
            .tembusan-section {
                margin-top: 10px;
                border-top: 1px solid #ccc;
                padding-top: 8px;
            }

            .tembusan-title {
                font-weight: bold;
                margin-bottom: 5px;
                font-size: 10pt;
            }

            .tembusan-list {
                padding-left: 15px;
            }

            .tembusan-item {
                margin-bottom: 2px;
                font-size: 10pt;
            }

            /* CSS untuk DomPDF - Kunci untuk 1 halaman */
            @page {
                size: A4 portrait;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class="document-container">
            <!-- Header -->
            <div class="document-header">
                <div class="header-content">
                    <div class="ministry-name">
                        Kementerian Komunikasi dan Informatika Republik
                        Indonesia
                    </div>
                    <div class="agency-name">
                        Badan Aksesibilitas Telekomunikasi dan Informasi
                    </div>
                    <div class="agency-subtitle">
                        Indonesia Terkoneksi - Makin Digital, Makin Maju
                    </div>
                    <div class="contact-info">
                        Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav.
                        24-25, Jakarta 12930<br />
                        Telp. : 021-31936590 (Hunting) Fax. : 021-31936516,
                        31927516<br />
                        www.baktikominfo.id | humas@baktikominfo.id |
                        mail@baktikominfo.id
                    </div>
                </div>
            </div>

            <!-- Document Title -->
            @php
                // Deterministic per student+date so the same certificate
                // always shows the same number, even though it's streamed
                // fresh on every download rather than saved to storage.
                $certDate = $generatedAt ?? now();
                $certNumber = str_pad(($student->id * 37 + $certDate->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
            @endphp
            <div class="document-title">
                <div class="title-main">Surat Keterangan</div>
                <div class="document-number">
                    Nomor:
                    {{ $certNumber }}/KOMDIG/BAKTI/SDA/{{ $certDate->format('m/Y') }}/PKL.01.{{ $certDate->format('d/m/Y') }}
                </div>
            </div>

            <!-- Content -->
            <div class="document-content">
                <div class="opening-statement">
                    Yang bertandatangan dibawah ini :
                </div>

                <div class="employee-details">
                    <table class="detail-table">
                        <tr>
                            <td class="detail-label">Nama</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">Sudarmanto</td>
                        </tr>
                        <tr>
                            <td class="detail-label">NIP</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">196907071990031002</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Jabatan</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">
                                {{ $supervisor->position ?? 'Kepala Divisi SDM dan Humas' }}
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="main-statement">menerangkan bahwa :</div>

                <div class="employee-details">
                    <table class="detail-table">
                        <tr>
                            <td class="detail-label">Nama</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">
                                {{ $student->user->name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="detail-label">NIM</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">
                                {{ $student->nim ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="detail-label">Program Studi</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">
                                {{ $student->study_program ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="detail-label">Universitas</td>
                            <td class="detail-separator">:</td>
                            <td class="detail-value">
                                {{ $student->university ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="main-statement">
                    Telah selesai melaksanakan
                    <span class="statement-highlight">Magang/PKL</span> di Badan
                    Aksesibilitas Telekomunikasi dan Informasi (BAKTI)
                    Kementerian Komunikasi dan Digital dengan pembimbing Sdr.
                    {{ $supervisorName ?? "Dede Sukartoyo" }}
                    selaku Staf Direktorat Sumber Daya dan Administrasi
                    terhitung sejak tanggal @if(isset($student->period_start)
                    && isset($student->period_end))
                    {{ \App\Helpers\DateHelper::formatDateIndonesian($student->period_start) }}
                    sampai dengan
                    {{ \App\Helpers\DateHelper::formatDateIndonesian($student->period_end) }}.
                    @else periode yang belum ditentukan. @endif
                </div>

                <div class="closing-statement">
                    Demikian surat keterangan ini dibuat untuk dipergunakan
                    sebagaimana mestinya.
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td class="signature-left">
                            <!-- Empty space for left side -->
                        </td>
                        <td class="signature-right">
                            <div class="signature-date-location">
                                Jakarta, {{ \App\Helpers\DateHelper::formatDateIndonesian(now()) }}
                            </div>
                            <div class="signature-title">
                                Kepala Divisi SDM dan Humas
                            </div>
                            <div class="signature-name">SUDARMANTO</div>
                            <div class="signature-nip">
                                NIP
                                {{ $supervisor->employee_id ?? '196907071959031002' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tembusan Section -->
            <div class="tembusan-section">
                <div class="tembusan-title">Tembusan Yth.</div>
                <div class="tembusan-list">
                    <div class="tembusan-item">
                        1. Plt. Direktur Sumber Daya dan Administrasi BAKTI
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
