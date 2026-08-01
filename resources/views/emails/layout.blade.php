<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f0f1f3;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #444444;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            background-color: #f0f1f3;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e2e2;
        }

        /* Header */
        .email-header {
            background: #0F2A5C;
            padding: 24px 28px;
            color: #ffffff;
        }

        .email-header-table {
            width: 100%;
        }

        .email-header-logo {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .email-header-subtitle {
            font-size: 10px;
            opacity: 0.75;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
            line-height: 1.4;
        }

        .email-header-institution {
            font-size: 9px;
            opacity: 0.6;
            line-height: 1.5;
            text-align: right;
        }

        /* Content */
        .email-body {
            padding: 28px 24px;
        }

        .email-category-label {
            font-size: 11px;
            font-weight: 700;
            color: #0F2A5C;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
        }

        .email-title {
            font-size: 17px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 12px;
            line-height: 1.35;
        }

        .email-greeting {
            font-size: 13px;
            font-weight: 500;
            color: #333333;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .email-paragraph {
            font-size: 13px;
            line-height: 1.7;
            color: #444444;
            margin-bottom: 16px;
        }

        .email-paragraph strong {
            color: #111111;
            font-weight: 600;
        }

        /* Buttons */
        .email-button-group {
            margin: 24px 0;
        }

        .email-button {
            display: inline-block;
            background: #0F2A5C;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 2px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0 8px 8px 0;
            border: none;
        }

        .email-button-secondary {
            background: #ffffff;
            color: #0F2A5C;
            border: 1px solid #0F2A5C;
        }

        /* Info rows (replaces old .email-section box) */
        .email-info-box {
            margin: 16px 0;
            padding: 0;
            border-top: 1px solid #eeeeee;
        }

        .email-info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eeeeee;
            gap: 20px;
        }

        .email-info-label {
            font-weight: 400;
            color: #666666;
            font-size: 12px;
            flex-shrink: 0;
        }

        .email-info-value {
            color: #111111;
            font-weight: 600;
            word-break: break-word;
            font-size: 12px;
            text-align: right;
        }

        /* Alert boxes */
        .email-alert {
            padding: 12px 16px;
            border-radius: 0;
            margin: 16px 0;
            border-left: 3px solid;
            font-size: 12px;
            line-height: 1.6;
        }

        .email-alert-info {
            background: #EFF3FA;
            border-left-color: #2E5FDB;
            color: #1E3A6E;
        }

        .email-alert-success {
            background: #F3F8F3;
            border-left-color: #2E7D32;
            color: #1B5E20;
        }

        .email-alert-warning {
            background: #FFFBEB;
            border-left-color: #B7791F;
            color: #7C4A03;
        }

        .email-alert-danger {
            background: #FDF2F2;
            border-left-color: #C0392B;
            color: #922B21;
        }

        /* Lists */
        .email-list {
            margin: 12px 0;
            padding-left: 20px;
        }

        .email-list li {
            margin-bottom: 8px;
            color: #444444;
            font-size: 12px;
            line-height: 1.6;
        }

        .email-list strong {
            color: #111111;
        }

        /* Divider */
        .email-divider {
            border: 0;
            height: 1px;
            background: #eeeeee;
            margin: 24px 0;
        }

        /* Footer */
        .email-footer {
            background: #fafbfc;
            padding: 24px 28px;
            border-top: 1px solid #eeeeee;
            text-align: center;
        }

        .email-footer-content {
            font-size: 10px;
            color: #8a8f97;
            line-height: 1.7;
        }

        .email-footer-content strong {
            color: #555555;
            font-weight: 600;
        }

        .email-footer-links {
            margin-top: 14px;
            border-top: 1px solid #eeeeee;
            padding-top: 14px;
        }

        .email-footer-links a {
            color: #0F2A5C;
            text-decoration: none;
            margin: 0 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .email-footer-links a:hover {
            text-decoration: underline;
        }

        .email-footer-copyright {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #eeeeee;
            font-size: 10px;
            color: #a0aec0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-header-table,
            .email-header-table tbody,
            .email-header-table tr,
            .email-header-table td {
                display: block;
                width: 100% !important;
                text-align: left !important;
            }

            .email-header-institution {
                text-align: left;
                margin-top: 10px;
            }

            .email-body {
                padding: 20px;
            }

            .email-footer {
                padding: 20px;
            }

            .email-title {
                font-size: 16px;
            }

            .email-button {
                display: block;
                width: 100%;
                text-align: center;
                margin: 0 0 10px 0;
            }

            .email-info-row {
                flex-direction: column;
                gap: 2px;
            }

            .email-info-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <table class="email-header-table" role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="vertical-align: middle;" width="60%">
                            <div class="email-header-logo">WebBakti</div>
                            <div class="email-header-subtitle">Sistem Manajemen Magang</div>
                        </td>
                        <td style="vertical-align: middle; text-align: right;" width="40%">
                            <div class="email-header-institution">KEMENTERIAN KOMUNIKASI<br>DAN DIGITAL RI</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Content -->
            <div class="email-body">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <div class="email-footer-content">
                    <strong>WebBakti System</strong><br>
                    Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI)<br>
                    Kementerian Komunikasi dan Digital Republik Indonesia
                </div>
                <div class="email-footer-links">
                    <a href="{{ url('/') }}">Kunjungi Aplikasi</a>
                    <a href="{{ url('/help') }}">Bantuan</a>
                    @if(isset($notifiable) && $notifiable)
                        <a href="{{ url('/notifications') }}">Preferensi</a>
                    @endif
                </div>
                <div class="email-footer-copyright">
                    &copy; {{ date('Y') }} BAKTI. Semua hak dilindungi.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
