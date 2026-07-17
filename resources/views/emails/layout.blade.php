<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .email-body {
            padding: 30px 20px;
        }

        .email-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #eee;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }

        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            margin: 20px 0;
        }

        .button:hover {
            background-color: #764ba2;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: 500;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            color: #1565c0;
        }

        .alert-warning {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            color: #e65100;
        }

        .alert-success {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }

        .alert-danger {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }

        ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        li {
            margin-bottom: 5px;
        }

        .divider {
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        strong {
            color: #333;
        }

        em {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ config('app.name') }}</h1>
            <p>Sistem Monitoring Magang Online</p>
        </div>

        <div class="email-body">
            @yield('content')
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
            <p>
                Email ini dikirim ke <strong>{{ $notifiable->email }}</strong><br>
                <a href="{{ url('/') }}">Kunjungi Aplikasi</a> |
                <a href="{{ url('/notifications') }}">Notifikasi Saya</a>
            </p>
        </div>
    </div>
</body>
</html>
