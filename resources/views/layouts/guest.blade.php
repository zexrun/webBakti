<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
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
        * {
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        
        /* Fixed background container */
        .main-background {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #eef2ff 100%);
            height: 100vh;
            width: 100vw;
            position: fixed;
            top: 0;
            left: 0;
            overflow: hidden;
        }
        
        /* Background patterns - positioned absolutely */
        .bg-pattern::before {
            content: '';
            position: absolute;
            top: -8rem;
            right: -8rem;
            width: 16rem;
            height: 16rem;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .bg-pattern::after {
            content: '';
            position: absolute;
            bottom: -8rem;
            left: -8rem;
            width: 16rem;
            height: 16rem;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        /* Content container - fixed positioning */
        .content-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            z-index: 10;
            overflow: hidden;
        }
        
        /* Content wrapper with max dimensions */
        .content-wrapper {
            width: 100%;
            max-width: 400px;
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Scrollable content area if needed */
        .scrollable-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }
        
        /* Hide scrollbar but keep functionality */
        .scrollable-content::-webkit-scrollbar {
            width: 2px;
        }
        
        .scrollable-content::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scrollable-content::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 1px;
        }
        
        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }
        
        /* Footer fixed at bottom */
        .footer-fixed {
            margin-top: auto;
            padding-top: 1rem;
            flex-shrink: 0;
        }
        
        /* Responsive adjustments */
        @media (max-height: 600px) {
            .content-container {
                justify-content: flex-start;
                padding: 0.5rem;
            }
            
            .content-wrapper {
                max-height: calc(100vh - 1rem);
            }
            
            .footer-fixed {
                padding-top: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .content-container {
                padding: 0.75rem;
            }
            
            .content-wrapper {
                max-width: 100%;
            }
        }
        
        /* Prevent text selection and dragging */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Prevent image dragging */
        img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
    </style>
</head>
<body class="antialiased h-full overflow-hidden">
    <!-- Main Background Container -->
    <div class="main-background bg-pattern">
        <!-- Content Container -->
        <div class="content-container">
            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Scrollable Content Area -->
                <div class="scrollable-content">
                    @yield('content')
                </div>
                
                <!-- Fixed Footer -->
                <footer class="footer-fixed">
                    <div class="text-center space-y-2">
                        <!-- Links -->
                        <div class="flex justify-center space-x-4 text-xs">
                            <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 no-select">
                                Bantuan
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 no-select">
                                Kontak
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 no-select">
                                Privasi
                            </a>
                        </div>
                        
                        <!-- Copyright -->
                        <p class="text-xs text-gray-400 no-select">
                            © {{ date('Y') }} Sistem Monitoring Magang
                        </p>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-4 shadow-xl max-w-xs mx-4">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium text-sm">Memproses...</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    
    <script>
        // Prevent scrolling
        document.addEventListener('DOMContentLoaded', function() {
            // Disable scroll restoration
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            
            // Prevent scroll events
            window.addEventListener('scroll', function(e) {
                window.scrollTo(0, 0);
            }, { passive: false });
            
            // Prevent touch scroll on mobile
            document.addEventListener('touchmove', function(e) {
                if (e.target.closest('.scrollable-content')) {
                    return; // Allow scroll in content area
                }
                e.preventDefault();
            }, { passive: false });
            
            // Prevent keyboard scroll
            document.addEventListener('keydown', function(e) {
                const scrollKeys = [32, 33, 34, 35, 36, 37, 38, 39, 40];
                if (scrollKeys.includes(e.keyCode)) {
                    if (!e.target.closest('.scrollable-content')) {
                        e.preventDefault();
                    }
                }
            });
            
            // Prevent mouse wheel scroll
            document.addEventListener('wheel', function(e) {
                if (!e.target.closest('.scrollable-content')) {
                    e.preventDefault();
                }
            }, { passive: false });
        });
        
        // Global loading functions
        window.showLoading = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        };
        
        window.hideLoading = function() {
            document.getElementById('loading-overlay').classList.add('hidden');
        };
        
        // Handle form submissions
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                showLoading();
            }
        });
        
        // Handle navigation
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.href && !e.target.href.startsWith('#') && !e.target.href.startsWith('javascript:')) {
                showLoading();
                setTimeout(hideLoading, 3000);
            }
        });
        
        // Hide loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
        });
        
        // Hide loading when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                hideLoading();
            }
        });
        
        // Prevent context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Prevent drag and drop
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>