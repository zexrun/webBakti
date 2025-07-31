<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Monitoring Magang')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="w-screen h-screen flex flex-col md:flex-row bg-gray-50">
    {{-- Left: Login Box --}}
    <div class="w-full md:w-1/2 h-screen flex items-center justify-center p-6 bg-white">
        <div class="w-full max-w-lg bg-white border rounded-xl shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Selamat Datang Di MABA!</h1>
                <p class="text-gray-500 text-sm mt-1">Silahkan masuk dengan akun anda</p>
            </div>
            @yield('content')
        </div>
    </div>

    {{-- Right: Illustration & Branding --}}
    <div class="hidden md:flex w-full md:w-1/2 h-screen bg-gradient-to-br from-blue-600 to-purple-600 items-center justify-center text-white p-6">
        <div class="text-center space-y-4">
            <h1 class="text-4xl font-bold">WEB MAGANG BAKTI</h1>
            <p class="text-lg">Your internship progress, tracked efficiently.</p>
            {{-- Optional: Illustration --}}
            <svg class="w-40 h-40 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v6h4m-4 6a9 9 0 100-18 9 9 0 000 18z"></path>
            </svg>
        </div>
    </div>
</body>
</html>
