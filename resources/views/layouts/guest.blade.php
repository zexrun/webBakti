<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Monitoring Magang - Platform digital untuk mengelola dan memantau kegiatan magang mahasiswa">
    <meta name="keywords" content="magang, monitoring, mahasiswa, sistem, dashboard">
    <meta name="author" content="Sistem Monitoring Magang">
    
    <title>@yield('title', 'Sistem Monitoring Magang')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    @stack('styles')
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-indigo-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-purple-400/20 to-pink-600/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Container -->
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            @yield('content')
            
            <!-- Footer -->
            <div class="text-center space-y-4 mt-8">
                <!-- Links -->
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">
                        Bantuan
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">
                        Kontak
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">
                        Kebijakan Privasi
                    </a>
                </div>
                
                <!-- Copyright -->
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} Sistem Monitoring Magang. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium">Memproses...</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    
    <script>
        // Global loading functions
        window.showLoading = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        };
        
        window.hideLoading = function() {
            document.getElementById('loading-overlay').classList.add('hidden');
        };
        
        // Auto-hide loading after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(hideLoading, 5000);
        });
        
        // Handle form submissions
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                showLoading();
            }
        });
        
        // Handle navigation
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.href && !e.target.href.startsWith('#')) {
                showLoading();
            }
        });
    </script>
</body>
</html>
