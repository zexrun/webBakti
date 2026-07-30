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
            background-color: #f5f7fa;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #2d3748;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            background-color: #f5f7fa;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #2E5FDB 0%, #1E3FA0 100%);
            padding: 60px 40px;
            text-align: center;
            color: white;
        }

        .email-header-logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .email-header-subtitle {
            font-size: 15px;
            opacity: 0.92;
            font-weight: 500;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        /* Content */
        .email-body {
            padding: 48px 40px;
        }

        .email-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 28px;
            line-height: 1.35;
            letter-spacing: -0.3px;
        }

        .email-greeting {
            font-size: 16px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .email-paragraph {
            font-size: 15px;
            line-height: 1.75;
            color: #555a64;
            margin-bottom: 20px;
            letter-spacing: 0.2px;
        }

        .email-paragraph strong {
            color: #2d3748;
            font-weight: 600;
        }

        /* Buttons */
        .email-button-group {
            margin: 40px 0;
            text-align: center;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #2E5FDB 0%, #1E3FA0 100%);
            color: white;
            padding: 16px 48px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(46, 95, 219, 0.2);
            margin: 0 8px;
            border: none;
            cursor: pointer;
            letter-spacing: 0.3px;
        }

        .email-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 95, 219, 0.3);
        }

        .email-button-secondary {
            background: #e2e8f0;
            color: #2d3748;
            box-shadow: none;
        }

        .email-button-secondary:hover {
            background: #cbd5e0;
        }

        /* Sections */
        .email-section {
            margin: 36px 0;
            padding: 28px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #2E5FDB;
        }

        .email-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.95;
        }

        /* Alert boxes */
        .email-alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin: 24px 0;
            border-left: 4px solid;
            font-size: 14px;
            line-height: 1.6;
        }

        .email-alert-info {
            background: #eff6ff;
            border-left-color: #3b82f6;
            color: #1e40af;
        }

        .email-alert-success {
            background: #f0fdf4;
            border-left-color: #22c55e;
            color: #166534;
        }

        .email-alert-warning {
            background: #fffbeb;
            border-left-color: #f59e0b;
            color: #92400e;
        }

        .email-alert-danger {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        /* Info boxes */
        .email-info-box {
            margin: 20px 0;
            padding: 0;
        }

        .email-info-row {
            display: flex;
            padding: 14px 0;
            border-bottom: 1px solid #e8eaed;
            gap: 20px;
        }

        .email-info-row:last-child {
            border-bottom: none;
        }

        .email-info-label {
            font-weight: 600;
            color: #555a64;
            width: 110px;
            flex-shrink: 0;
            font-size: 14px;
        }

        .email-info-value {
            color: #1a202c;
            word-break: break-word;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Lists */
        .email-list {
            margin: 16px 0;
            padding-left: 24px;
        }

        .email-list li {
            margin-bottom: 10px;
            color: #4a5568;
        }

        .email-list strong {
            color: #2d3748;
        }

        /* Divider */
        .email-divider {
            border: 0;
            height: 1px;
            background: #e8eaed;
            margin: 40px 0;
        }

        /* Footer */
        .email-footer {
            background: #fafbfc;
            padding: 40px;
            border-top: 1px solid #e8eaed;
            text-align: center;
        }

        .email-footer-content {
            font-size: 12px;
            color: #7a7f87;
            line-height: 1.9;
            letter-spacing: 0.2px;
        }

        .email-footer-content strong {
            color: #555a64;
            font-weight: 600;
        }

        .email-footer-links {
            margin-top: 20px;
            border-top: 1px solid #e8eaed;
            padding-top: 20px;
        }

        .email-footer-links a {
            color: #2E5FDB;
            text-decoration: none;
            margin: 0 16px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .email-footer-links a:hover {
            text-decoration: underline;
            opacity: 0.8;
        }

        .email-footer-social {
            margin-top: 16px;
        }

        .email-footer-social a {
            display: inline-block;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            background: #cbd5e0;
            color: #2d3748;
            border-radius: 50%;
            margin: 0 6px;
            transition: all 0.2s;
        }

        .email-footer-social a:hover {
            background: #2E5FDB;
            color: white;
        }

        .email-footer-copyright {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #a0aec0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-header {
                padding: 30px 20px;
            }

            .email-body {
                padding: 20px;
            }

            .email-footer {
                padding: 20px;
            }

            .email-title {
                font-size: 18px;
            }

            .email-button-group {
                text-align: center;
            }

            .email-button {
                display: block;
                width: 100%;
                margin: 10px 0;
            }

            .email-info-row {
                flex-direction: column;
            }

            .email-info-label {
                width: 100%;
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="email-header-logo">🎓 WebBakti</div>
                <div class="email-header-subtitle">Sistem Manajemen Magang Online</div>
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
