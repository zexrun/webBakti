<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body>
<nav class="w-full h-12 bg-blue-500 text-white flex items-center">
    <div class="w-full flex items-center justify-between px-6">
        <!-- Logo atau Judul -->
        <a href="{{ route('admin.dashboard') }}" class="font-semibold text-xl">
            Admin Panel
        </a>

        <!-- Menu Navigasi -->
        <div class="flex gap-6">
            <a href="{{ route('admin.plotting') }}" class="font-semibold text-lg">
                Plotting Pembimbing
            </a>
            <a href="{{ route('admin.users.index') }}" class="font-semibold text-lg">
                Manajemen User
            </a>
        </div>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="hover:text-red-700">
                Logout
            </button>
        </form>
    </div>
</nav>


    <div class="container mx-auto py-6">
        <main class="bg-white shadow-md rounded-md overflow-hidden">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>


{{-- <body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex items-center justify-start">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold text-lg">Admin Panel</a>
                <div class="ml-4">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-300">Dashboard</a>
                    <a href="{{ route('admin.plotting') }}" class="hover:text-blue-300">Plotting</a>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-blue-300">Manajemen User</a>
                    <form method="POST" action="{{  route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-red-700">Logout</button>
                    </form>
                    </div>
        </div>
    </nav>

</body> --}}
</html>
