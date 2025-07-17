<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js untuk interaksi dropdown -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex items-center justify-between">
            <!-- Kiri: Logo -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.dashboard') }}" class="font-semibold text-lg">Student Panel</a>
            </div>

            <!-- Kanan: Notifikasi & Logout -->
            <div class="flex items-center space-x-4">
                <!-- 🔔 Notifikasi -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="relative text-white focus:outline-none">
                        🔔
                        @if($unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white text-black rounded shadow-lg z-50">
                        @forelse ($unreadNotifications as $notification)
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b text-sm">
                                {{ $notification->data['message'] }}
                            </a>
                        @empty
                            <div class="px-4 py-2 text-sm text-gray-500">
                                Tidak ada notifikasi baru.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-red-700">Logout</button>
                </form>
            </div>
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
</html>
