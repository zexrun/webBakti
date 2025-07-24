<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Monitoring Magang</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="flex">
        @if(auth()->check())
            @if(auth()->user()->role === 'admin')
                @include('components.sidebar.admin')
            @elseif(auth()->user()->role === 'supervisor')
                @include('components.sidebar.supervisor')
            @elseif(auth()->user()->role === 'student')
                @include('components.sidebar.student')
            @endif
        @endif
    </div>
        
    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            @yield('content')
    </div>
</body>
</html>