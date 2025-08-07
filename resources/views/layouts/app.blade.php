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
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg mt-14">
            @yield('content')
        </div>
    </div>

    <!-- Form Logout (hidden) - PINDAHKAN KE ATAS -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Popup Components -->
    <x-popup-overlay 
        id="logout-popup"
        title="Konfirmasi Logout"
        type="warning"
        message="Apakah Anda yakin ingin logout?"
        confirmText="Ya, Logout"
        cancelText="Batal"
        confirmAction="executeLogout()" />

    <!-- Delete Confirmation Popups -->
    <x-popup-overlay 
        id="delete-popup"
        title="Konfirmasi Hapus"
        type="danger"
        message=""
        confirmText="Ya, Hapus"
        cancelText="Batal"
        confirmAction="executeDelete()" />

    <x-popup-overlay 
        id="delete-position-popup"
        title="Konfirmasi Hapus"
        type="danger"
        message=""
        confirmText="Ya, Hapus"
        cancelText="Batal"
        confirmAction="executeDeletePosition()" />

    <x-popup-overlay 
        id="delete-user-popup"
        title="Konfirmasi Hapus Pengguna"
        type="danger"
        message=""
        confirmText="Ya, Hapus"
        cancelText="Batal"
        confirmAction="executeDeleteUser()" />

    <!-- JavaScript -->
    <script>
    // Global delete variables
    let currentDeleteId = null;
    let currentDeleteType = null;
    let currentDeleteUserId = null;

    // Popup functions
    function showPopup(popupId) {
        const popup = document.getElementById(popupId);
        const content = document.getElementById(popupId + '-content');
        
        if (popup && content) {
            popup.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closePopup(popupId) {
        const popup = document.getElementById(popupId);
        const content = document.getElementById(popupId + '-content');
        
        if (popup && content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                popup.classList.add('hidden');
                currentDeleteId = null;
                currentDeleteType = null;
            }, 300);
        }
    }

    // Logout functions
    function confirmLogout() {
        showPopup('logout-popup');
    }

    function executeLogout() {
        document.getElementById('logout-form').submit();
    }

    // Delete functions
    function confirmDelete(itemId, itemName) {
        currentDeleteId = itemId;
        currentDeleteType = 'directorate';
        
        const popup = document.getElementById('delete-popup');
        const messageElement = popup.querySelector('.text-gray-700');
        if (messageElement) {
            messageElement.innerHTML = `Yakin ingin menghapus direktorat <strong>"${itemName}"</strong>?<br><small class="text-red-600">Tindakan ini tidak dapat dibatalkan.</small>`;
        }
        
        showPopup('delete-popup');
    }
    
    function confirmDeleteUser(userId, userName) {
    currentDeleteUserId = userId;
    
    const popup = document.getElementById('delete-user-popup');
    const messageElement = popup.querySelector('.text-gray-700');
    if (messageElement) {
        messageElement.innerHTML = `Yakin ingin menghapus pengguna <strong>"${userName}"</strong>?<br><small class="text-red-600">Semua data terkait pengguna ini akan ikut terhapus dan tidak dapat dikembalikan.</small>`;
    }
    
    showPopup('delete-user-popup');
    }
    
    
    function confirmDeletePosition(itemId, itemName) {
        currentDeleteId = itemId;
        currentDeleteType = 'position';
        
        const popup = document.getElementById('delete-position-popup');
        const messageElement = popup.querySelector('.text-gray-700');
        if (messageElement) {
            messageElement.innerHTML = `Yakin ingin menghapus jabatan <strong>"${itemName}"</strong>?<br><small class="text-red-600">Tindakan ini tidak dapat dibatalkan.</small>`;
        }
        
        showPopup('delete-position-popup');
    }
    
    function executeDeleteUser() {
        if (currentDeleteUserId) {
            const form = document.getElementById(`delete-user-form-${currentDeleteUserId}`);
            if (form) {
                form.submit();
            }
        }
    }

    function executeDelete() {
        if (currentDeleteId && currentDeleteType === 'directorate') {
            const form = document.getElementById(`delete-form-${currentDeleteId}`);
            if (form) {
                form.submit();
            }
        }
    }

    function executeDeletePosition() {
        if (currentDeleteId && currentDeleteType === 'position') {
            const form = document.getElementById(`delete-form-position-${currentDeleteId}`);
            if (form) {
                form.submit();
            }
        }
    }

    // Close popup on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePopup('logout-popup');
            closePopup('delete-popup');
            closePopup('delete-position-popup');
            closePopup('delete-user-popup');
        }
    });

    // Close popup when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.id === 'logout-popup') {
            closePopup('logout-popup');
        }
        if (e.target.id === 'delete-popup') {
            closePopup('delete-popup');
        }
        if (e.target.id === 'delete-position-popup') {
            closePopup('delete-position-popup');
        }
        if (e.target.id === 'delete-user-popup') {
        closePopup('delete-user-popup');
    }
    });

    // Your existing sidebar JavaScript code
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('logo-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                overlay?.classList.toggle('hidden');
            });
        }
        
        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar?.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
        
        // Dropdown functionality
        const dropdownToggles = document.querySelectorAll('[data-collapse-toggle]');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-collapse-toggle');
                const target = document.getElementById(targetId);
                
                if (target) {
                    target.classList.toggle('hidden');
                    
                    // Rotate arrow
                    const arrow = this.querySelector('svg:last-child');
                    if (arrow) {
                        arrow.style.transform = target.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            });
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
            }
        });
    });
    </script>

    @stack('scripts')
</body>
</html>