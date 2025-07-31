<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Monitoring Magang</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</head>

<body>
    <div class="flex">
        @if(auth()->check())
            @if(auth()->user()->role == 'admin')
                @include('partials.admin')
            @elseif(auth()->user()->role == 'supervisor')
                @include('partials.supervisor')
            @elseif(auth()->user()->role == 'student')
                @include('partials.student')
            @endif
        @endif
    </div>
        
    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            @yield('content')
    </div>
</body>
</html>