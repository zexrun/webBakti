<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css')}}">

    <title>@yield('title', 'Sistem Monitoring Magang')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="w-screen h-screen flex flex-row bg-white">
    <div class="w-screen h-screen flex items-center justify-center">
        <div class="w-1/2 h-fit flex flex-col p-5 border-2 shadow-md rounded-xl">
            <div class="flex items-center justify-center">
                <h1 class="font-bold">LOGIN</h1>
            </div>
            <div class="w-full h-full flex items-center justify-center ">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="w-screen h-screen flex items-center justify-center">
        <h1>WEB MAGANG BAKTI</h1>
        </div>
    </div>
</body>
</html>