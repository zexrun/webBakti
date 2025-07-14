<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex items-center justify-start">
            <a href="{{ route('student.dashboard') }}" class="font-semibold text-lg">Supervisor Panel</a>
                <div class="ml-4">
                    <form method="POST" action="{{  route('logout') }}">
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

</body></html>