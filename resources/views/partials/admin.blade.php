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
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
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
                    href="{{ route('admin.dashboard') }}"
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
            <div class="flex items-center space-x-4">
                <!-- User Info -->
                <div class="flex items-center">
                    <div class="hidden sm:block">
                        <span class="text-sm text-gray-700">
                            Halo, <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>
                        </span>
                    </div>
                    <div class="ml-3">
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
>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <nav class="space-y-2 font-medium">
            <!-- Dashboard -->
            <div>
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z"/>
                    </svg>
                    <span class="ms-3 font-medium">Dashboard</span>
                </a>
            </div>

            <!-- Plotting Pembimbing -->
            <div>
                <a 
                    href="{{ route('admin.plotting') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('admin.plotting') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('admin.plotting') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z"/>
                    </svg>
                    <span class="ms-3 font-medium">Plotting Pembimbing</span>
                </a>
            </div>

            <!-- Monitoring -->
            <div>
                <a 
                    href="{{ route('admin.monitoring.index') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('admin.monitoring.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('admin.monitoring.*') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3 font-medium">Monitoring</span>
                </a>
            </div>

            <!-- Settings -->
            <div>
                <a 
                    href="{{ route('admin.settings.index') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.570.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3 font-medium">Settings</span>
                </a>
            </div>

            <!-- Manajemen Pengguna (Dropdown) -->
            <div>
                <button
                    type="button"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : '' }}"
                    data-collapse-toggle="user-management-dropdown"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z"/>
                    </svg>
                    <span class="ms-3 font-medium">Manajemen Pengguna</span>
                    <svg class="w-3 h-3 ml-auto transition-transform duration-200" viewBox="0 0 10 6" fill="none">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                
                <ul 
                    id="user-management-dropdown" 
                    class="py-2 space-y-1 ml-6 border-l border-gray-200 {{ request()->routeIs('admin.users.*') ? '' : 'hidden' }}"
                >
                    <li>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex items-center w-full p-2 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('admin.users.index') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}"
                        >
                            Daftar Pengguna
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('admin.users.create') }}"
                            class="flex items-center w-full p-2 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ request()->routeIs('admin.users.create') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}"
                        >
                            Tambah Pengguna
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>

            <!-- Statistics Section -->
            @if(isset($adminStats))
            <div class="px-3 py-2">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-100">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                        </svg>
                        Statistik Sistem
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex flex-col">
                            <span class="text-gray-600 font-medium">Total Users</span>
                            <span class="text-gray-900 font-bold text-sm">{{ $adminStats['total_users'] }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 font-medium">Mahasiswa</span>
                            <span class="text-gray-900 font-bold text-sm">{{ $adminStats['total_students'] }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 font-medium">Pembimbing</span>
                            <span class="text-gray-900 font-bold text-sm">{{ $adminStats['total_supervisors'] }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 font-medium">Magang Aktif</span>
                            <span class="text-green-600 font-bold text-sm">{{ $adminStats['active_internships'] }}</span>
                        </div>
                    </div>
                    @if($adminStats['pending_plotting'] > 0)
                    <div class="mt-2 p-2 bg-red-50 rounded border border-red-200">
                        <div class="flex items-center text-xs text-red-700">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $adminStats['pending_plotting'] }} belum plotting
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>

            <!-- Profile -->
            <div>
                <a 
                    href="{{ route('profile.show') }}" 
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : '' }}"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 {{ request()->routeIs('profile.*') ? 'text-blue-600' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3 font-medium">Profile</span>
                </a>
            </div>

            <!-- Logout -->
            <div>
                <button
                    type="button"
                    onclick="confirmLogout()"
                    class="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-red-50 focus:text-red-700"
                >
                    <svg class="w-5 h-5 text-gray-500 transition-colors duration-200 flex-shrink-0 hover:text-red-500" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3 font-medium">Logout</span>
                </button>
            </div>
        </nav>
    </div>
</aside>

<!-- Overlay for mobile -->
<div 
    id="sidebar-overlay" 
    class="fixed inset-0 z-30 bg-black bg-opacity-50 hidden sm:hidden"
></div>