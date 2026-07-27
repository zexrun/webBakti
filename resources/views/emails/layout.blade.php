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
            padding: 50px 40px;
            text-align: center;
            color: white;
        }

        .email-header-logo {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .email-header-subtitle {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Content */
        .email-body {
            padding: 40px;
        }

        .email-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 24px;
            line-height: 1.3;
        }

        .email-greeting {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .email-paragraph {
            font-size: 15px;
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 16px;
        }

        .email-paragraph strong {
            color: #2d3748;
            font-weight: 600;
        }

        /* Buttons */
        .email-button-group {
            margin: 32px 0;
            text-align: center;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #2E5FDB 0%, #1E3FA0 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(46, 95, 219, 0.3);
            margin: 0 8px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .email-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46, 95, 219, 0.4);
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
            margin: 32px 0;
            padding: 24px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 4px solid #2E5FDB;
        }

        .email-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 12px;
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
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .email-info-row:last-child {
            border-bottom: none;
        }

        .email-info-label {
            font-weight: 600;
            color: #4a5568;
            width: 120px;
            flex-shrink: 0;
        }

        .email-info-value {
            color: #2d3748;
            word-break: break-word;
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
            background: #e2e8f0;
            margin: 32px 0;
        }

        /* Footer */
        .email-footer {
            background: #f7fafc;
            padding: 32px 40px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .email-footer-content {
            font-size: 13px;
            color: #718096;
            line-height: 1.8;
        }

        .email-footer-content strong {
            color: #4a5568;
        }

        .email-footer-links {
            margin-top: 16px;
        }

        .email-footer-links a {
            color: #2E5FDB;
            text-decoration: none;
            margin: 0 12px;
            font-size: 13px;
            font-weight: 500;
        }

        .email-footer-links a:hover {
            text-decoration: underline;
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
