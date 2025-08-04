<!-- Navigation Header -->
<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <!-- Left Section -->
            <div class="flex items-center justify-start rtl:justify-end">
                <!-- Mobile Menu Toggle -->
                <button
                    id="sidebar-toggle"
                    data-drawer-target="logo-sidebar"
                    data-drawer-toggle="logo-sidebar"
                    aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors duration-200"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            clip-rule="evenodd"
                            fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                        ></path>
                    </svg>
                </button>

                <!-- Logo and Brand -->
                <a
                    href="{{ route('student.dashboard') }}"
                    class="flex items-center ms-2 md:me-24 group"
                >
                    <img
                        src="https://baktikomdigi.id/assets/images/Logo_Bakti_Komdigi.jpg"
                        class="h-8 me-3 rounded transition-transform duration-200 group-hover:scale-105"
                        alt="Logo BAKTI"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                    />
                    <!-- Fallback logo -->
                    <div class="h-8 w-8 bg-blue-600 rounded me-3 flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                        B
                    </div>
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                        Magang BAKTI
                    </span>
                </a>
            </div>

            <!-- Right Section -->
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-700">
                            Halo, <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>
                        </span>
                        <!-- User Avatar -->
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <a href="{{ route('profile.show')}}">
                                <span class="text-sm font-medium text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside
    id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 shadow-lg"
    aria-label="Sidebar"
>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <nav class="space-y-2 font-medium">
            <!-- Dashboard -->
            <div>
                <a
                    href="{{ route('student.dashboard') }}"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('student.dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('student.dashboard') ? 'text-blue-600' : '' }}"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z"
                        />
                    </svg>
                    <span class="ms-3 font-medium">Dashboard</span>
                </a>
            </div>

            <!-- Aktivitas Magang (Dropdown) -->
            <div>
                <button
                    type="button"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('student.tasks.*') || request()->routeIs('student.logbooks.*') || request()->routeIs('student.documents.create') ? 'bg-blue-50 text-blue-700' : '' }}"
                    data-collapse-toggle="dropdown-example"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('student.tasks.*') || request()->routeIs('student.logbooks.*') || request()->routeIs('student.documents.create') ? 'text-blue-600' : '' }}"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z"
                        />
                        <path
                            d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z"
                        />
                        <path
                            d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z"
                        />
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap font-medium">
                        Aktivitas Magang
                    </span>
                    <svg
                        class="w-3 h-3 ml-auto transition-transform duration-200"
                        viewBox="0 0 10 6"
                        fill="none"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 4 4 4-4"
                        />
                    </svg>
                </button>
                
                <ul 
                    id="dropdown-example" 
                    class="py-2 space-y-1 ml-6 border-l border-gray-200 {{ request()->routeIs('student.tasks.*') || request()->routeIs('student.logbooks.*') || request()->routeIs('student.documents.create') ? '' : 'hidden' }}"
                >
                    <li>
                        <a
                            href="{{ route('student.tasks.index') }}"
                            class="flex items-center w-full p-2 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('student.tasks.*') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}"
                        >
                            Daftar Tugas
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('student.logbooks.index') }}"
                            class="flex items-center w-full p-2 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('student.logbooks.*') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}"
                        >
                            Laporan Harian
                        </a>
                    </li>
                    </ul>
            </div>

            <!-- Informasi Magang -->
            <div>
                <a
                    href="{{ route('student.info.edit') }}"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('student.info.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('student.info.*') ? 'text-blue-600' : '' }}"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="ms-3 font-medium">Informasi Magang</span>
                </a>
            </div>

            <!-- Dokumen -->
            <div>
                <a
                    href="{{ route('student.documents.index') }}"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('student.documents.index') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('student.documents.index') ? 'text-blue-600' : '' }}"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z"
                            clip-rule="evenodd"
                        />
                        <path
                            d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z"
                        />
                    </svg>
                    <span class="ms-3 font-medium">Dokumen</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>

            <!-- Profile -->
            <div>
                <a 
                    href="{{ route('profile.show') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('profile.*') ? 'text-blue-600' : '' }}"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="ms-3 font-medium">Profile</span>
                </a>
            </div>

            <!-- Logout -->
            <div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-red-50 focus:text-red-700"
                        onclick="return confirm('Apakah Anda yakin ingin logout?')"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="ms-3 font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</aside>

<!-- Overlay for mobile -->
<div 
    id="sidebar-overlay" 
    class="fixed inset-0 z-30 bg-black bg-opacity-50 hidden sm:hidden"
></div>

<script>
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