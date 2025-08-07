@extends('layouts.app')
@section('title', 'Konfigurasi Sistem')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Konfigurasi Sistem</h1>
                    <p class="text-gray-600">Kelola data direktorat, jabatan, dan universitas untuk sistem</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <div class="bg-blue-50 px-3 py-2 rounded-lg">
                        <span class="text-sm font-medium text-blue-800">{{ $directorates->total() }} Direktorat</span>
                    </div>
                    <div class="bg-green-50 px-3 py-2 rounded-lg">
                        <span class="text-sm font-medium text-green-800">{{ $positions->total() }} Jabatan</span>
                    </div>
                    <div class="bg-purple-50 px-3 py-2 rounded-lg">
                        <span class="text-sm font-medium text-purple-800">{{ $universities->total() }} Universitas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alerts -->
        @if(session('success') || session('success_position') || session('success_university'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded-lg mb-6 relative">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') ?? session('success_position') ?? session('success_university') }}
                </div>
                <button type="button" class="absolute top-4 right-4 text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <a href="{{ route('admin.settings.index', ['tab' => 'directorates']) }}" 
                       class="tab-link {{ $activeTab === 'directorates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Direktorat
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $directorates->total() }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index', ['tab' => 'positions']) }}" 
                       class="tab-link {{ $activeTab === 'positions' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0h2a2 2 0 012 2v6.5"/>
                        </svg>
                        Jabatan
                        <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $positions->total() }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index', ['tab' => 'universities']) }}" 
                       class="tab-link {{ $activeTab === 'universities' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Universitas
                        <span class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $universities->total() }}</span>
                    </a>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                
                <!-- DIRECTORATES TAB -->
                @if($activeTab === 'directorates')
                <div id="directorates-tab">
                    <!-- Header with Add Button and Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Kelola Direktorat</h3>
                            <p class="mt-1 text-sm text-gray-500">Tambah, edit, atau hapus direktorat</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex space-x-3">
                            <!-- Search -->
                            <form method="GET" action="{{ route('admin.settings.index') }}" class="flex">
                                <input type="hidden" name="tab" value="directorates">
                                <div class="relative">
                                    <input type="text" name="search_directorate" value="{{ $searchDirectorate }}" 
                                           placeholder="Cari direktorat..." 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                    Cari
                                </button>
                            </form>
                            <!-- Add Button -->
                            <button onclick="openModal('addDirectorateModal')" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Direktorat
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Direktorat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($directorates as $index => $dir)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ($directorates->currentPage() - 1) * $directorates->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $dir->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editDirectorate({{ $dir->id }}, '{{ $dir->name }}')" 
                                                        class="text-blue-600 hover:text-blue-900">Edit</button>
                                                <button onclick="confirmDelete('{{ $dir->id }}', '{{ $dir->name }}')" 
                                                        class="text-red-600 hover:text-red-900">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <h3 class="text-sm font-medium text-gray-900 mb-1">
                                                    {{ $searchDirectorate ? 'Tidak ada hasil' : 'Belum ada direktorat' }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    {{ $searchDirectorate ? 'Coba kata kunci lain' : 'Tambahkan direktorat pertama' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($directorates->hasPages())
                        <div class="mt-6">
                            {{ $directorates->links() }}
                        </div>
                    @endif
                </div>
                @endif

                <!-- POSITIONS TAB -->
                @if($activeTab === 'positions')
                <div id="positions-tab">
                    <!-- Header with Add Button and Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Kelola Jabatan</h3>
                            <p class="mt-1 text-sm text-gray-500">Tambah, edit, atau hapus jabatan</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex space-x-3">
                            <!-- Search -->
                            <form method="GET" action="{{ route('admin.settings.index') }}" class="flex">
                                <input type="hidden" name="tab" value="positions">
                                <div class="relative">
                                    <input type="text" name="search_position" value="{{ $searchPosition }}" 
                                           placeholder="Cari jabatan..." 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                    Cari
                                </button>
                            </form>
                            <!-- Add Button -->
                            <button onclick="openModal('addPositionModal')" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Jabatan
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jabatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($positions as $index => $pos)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ($positions->currentPage() - 1) * $positions->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $pos->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editPosition({{ $pos->id }}, '{{ $pos->name }}')" 
                                                        class="text-blue-600 hover:text-blue-900">Edit</button>
                                                <button onclick="confirmDeletePosition('{{ $pos->id }}', '{{ $pos->name }}')" 
                                                        class="text-red-600 hover:text-red-900">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0h2a2 2 0 012 2v6.5"/>
                                                </svg>
                                                <h3 class="text-sm font-medium text-gray-900 mb-1">
                                                    {{ $searchPosition ? 'Tidak ada hasil' : 'Belum ada jabatan' }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    {{ $searchPosition ? 'Coba kata kunci lain' : 'Tambahkan jabatan pertama' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($positions->hasPages())
                        <div class="mt-6">
                            {{ $positions->links() }}
                        </div>
                    @endif
                </div>
                @endif

                <!-- UNIVERSITIES TAB -->
                @if($activeTab === 'universities')
                <div id="universities-tab">
                    <!-- Header with Add Button and Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Kelola Universitas</h3>
                            <p class="mt-1 text-sm text-gray-500">Tambah, edit, atau hapus universitas</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex space-x-3">
                            <!-- Search -->
                            <form method="GET" action="{{ route('admin.settings.index') }}" class="flex">
                                <input type="hidden" name="tab" value="universities">
                                <div class="relative">
                                    <input type="text" name="search_university" value="{{ $searchUniversity }}" 
                                           placeholder="Cari universitas..." 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                    Cari
                                </button>
                            </form>
                            <!-- Add Button -->
                            <button onclick="openModal('addUniversityModal')" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Universitas
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Universitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($universities as $index => $uni)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ($universities->currentPage() - 1) * $universities->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $uni->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $uni->domain ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($uni->website)
                                                <a href="{{ $uni->website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                                    {{ Str::limit($uni->website, 25) }}
                                                    <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editUniversity({{ $uni->id }}, '{{ $uni->name }}', '{{ $uni->domain }}', '{{ $uni->website }}')" 
                                                        class="text-blue-600 hover:text-blue-900">Edit</button>
                                                <button onclick="confirmDeleteUniversity('{{ $uni->id }}', '{{ $uni->name }}')" 
                                                        class="text-red-600 hover:text-red-900">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                                </svg>
                                                <h3 class="text-sm font-medium text-gray-900 mb-1">
                                                    {{ $searchUniversity ? 'Tidak ada hasil' : 'Belum ada universitas' }}
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    {{ $searchUniversity ? 'Coba kata kunci lain' : 'Tambahkan universitas pertama' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($universities->hasPages())
                        <div class="mt-6">
                            {{ $universities->links() }}
                        </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- MODALS -->
@include('admin.settings.modals')

<!-- Hidden Forms for Delete -->
@foreach($directorates as $dir)
    <form id="delete-form-{{ $dir->id }}" action="{{ route('admin.settings.deleteDirectorate', $dir->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@foreach($positions as $pos)
    <form id="delete-form-position-{{ $pos->id }}" action="{{ route('admin.settings.deletePosition', $pos->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@foreach($universities as $uni)
    <form id="delete-form-university-{{ $uni->id }}" action="{{ route('admin.settings.deleteUniversity', $uni->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@endsection

@push('scripts')
<script src="{{ asset('js/settings-modal.js') }}"></script>
@endpush