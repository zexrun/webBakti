@extends('layouts.app')
@section('title', 'Monitoring Supervisor & Mahasiswa')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Monitoring Aktivitas</h1>
                        <p class="text-gray-600 mt-1">Pantau aktivitas supervisor dan mahasiswa bimbingan</p>
                    </div>
                </div>
                
                <!-- Stats Summary -->
                <div class="hidden md:flex space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_supervisors'] }}</div>
                        <div class="text-sm text-gray-500">Supervisor</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['total_students'] }}</div>
                        <div class="text-sm text-gray-500">Mahasiswa</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['avg_students_per_supervisor'] }}</div>
                        <div class="text-sm text-gray-500">Rata-rata/Supervisor</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['active_submissions'] }}</div>
                        <div class="text-sm text-gray-500">Submission Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <form method="GET" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ $search ?? '' }}"
                               placeholder="Nama supervisor/mahasiswa..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>

                <!-- Filter by Directorat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Direktorat</label>
                    <select name="directorat" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Direktorat</option>
                        @foreach($directorates as $dir)
                            <option value="{{ $dir }}" {{ $directorat === $dir ? 'selected' : '' }}>{{ $dir }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <select name="position" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Jabatan</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ $position === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select name="sort_by" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="students" {{ $sortBy === 'students' ? 'selected' : '' }}>Jumlah Mahasiswa</option>
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.monitoring.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-600">
                        {{ $supervisors->total() }} hasil ditemukan
                    </div>
                    <a href="{{ route('admin.monitoring.export-csv', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0 0V8m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </form>

        <!-- Card View Container -->
        <div id="cardContainer" class="space-y-6">
            @forelse($supervisors as $supervisor)
                <div class="supervisor-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200" 
                     data-supervisor="{{ strtolower($supervisor->user->name) }}" 
                     data-direktorat="{{ $supervisor->direktorat ?? '' }}">
                    
                    <!-- Supervisor Header -->
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $supervisor->user->name }}</h3>
                                    <div class="flex items-center mt-1 space-x-4">
                                        <span class="text-sm text-gray-600">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $supervisor->direktorat ?? 'Belum ditentukan' }}
                                        </span>
                                        @if($supervisor->jabatan)
                                            <span class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H10a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h2"/>
                                                </svg>
                                                {{ $supervisor->jabatan }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Student Count Badge -->
                            <div class="flex items-center space-x-3">
                                <div class="bg-white rounded-full px-4 py-2 shadow-sm border border-gray-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-700">
                                            {{ $supervisor->students->count() }} Mahasiswa
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Detail Button -->
                                <a href="{{ route('admin.monitoring.supervisor.show', $supervisor->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="divide-y divide-gray-100">
                        @forelse($supervisor->students as $student)
                            <div class="p-4 hover:bg-gray-50 transition-colors duration-150 student-item" data-student="{{ strtolower($student->user->name) }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-green-100 mr-3">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $student->user->name }}</h4>
                                            <div class="flex items-center mt-1 space-x-3 text-sm text-gray-500">
                                                @if($student->nim)
                                                    <span>
                                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                        </svg>
                                                        {{ $student->nim }}
                                                    </span>
                                                @endif
                                                @if($student->universitas)
                                                    <span>
                                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        {{ $student->universitas }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-2">
                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Aktif
                                        </span>
                                        
                                        <!-- Detail Button -->
                                        <a href="{{ route('admin.monitoring.student.show', $student->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="text-gray-500 text-sm">Belum ada mahasiswa bimbingan</p>
                                <p class="text-gray-400 text-xs mt-1">Supervisor ini belum memiliki mahasiswa yang dibimbing</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Monitoring</h3>
                    <p class="text-gray-500 mb-4">Belum ada supervisor yang terdaftar dalam sistem</p>
                    <a href="{{ route('admin.users.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Supervisor
                    </a>
                </div>
            @endforelse
        </div>

        <!-- List View Container -->
        <div id="listContainer" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="col-span-3">Supervisor</div>
                        <div class="col-span-2">Direktorat</div>
                        <div class="col-span-2">Jabatan</div>
                        <div class="col-span-3">Mahasiswa</div>
                        <div class="col-span-1">Jumlah</div>
                        <div class="col-span-1">Aksi</div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    @forelse($supervisors as $supervisor)
                        <div class="supervisor-list-item px-6 py-4 hover:bg-gray-50 transition-colors duration-150" 
                             data-supervisor="{{ strtolower($supervisor->user->name) }}" 
                             data-direktorat="{{ $supervisor->direktorat ?? '' }}">
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <!-- Supervisor Info -->
                                <div class="col-span-3">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-blue-100 mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $supervisor->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $supervisor->user->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Direktorat -->
                                <div class="col-span-2">
                                    <span class="text-sm text-gray-900">{{ $supervisor->direktorat ?? '-' }}</span>
                                </div>

                                <!-- Jabatan -->
                                <div class="col-span-2">
                                    <span class="text-sm text-gray-900">{{ $supervisor->jabatan ?? '-' }}</span>
                                </div>

                                <!-- Students -->
                                <div class="col-span-3">
                                    @if($supervisor->students->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($supervisor->students->take(2) as $student)
                                                <div class="text-sm text-gray-900 student-item" data-student="{{ strtolower($student->user->name) }}">
                                                    {{ $student->user->name }}
                                                </div>
                                            @endforeach
                                            @if($supervisor->students->count() > 2)
                                                <div class="text-xs text-gray-500">
                                                    +{{ $supervisor->students->count() - 2 }} lainnya
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Belum ada mahasiswa</span>
                                    @endif
                                </div>

                                <!-- Count -->
                                <div class="col-span-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $supervisor->students->count() }}
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="col-span-1">
                                    <a href="{{ route('admin.monitoring.supervisor.show', $supervisor->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-gray-500">Belum ada data supervisor</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const direktoratFilter = document.getElementById('direktoratFilter');
    const cardViewBtn = document.getElementById('cardView');
    const listViewBtn = document.getElementById('listView');
    const cardContainer = document.getElementById('cardContainer');
    const listContainer = document.getElementById('listContainer');
    
    // Get all supervisor items for both views
    const supervisorCards = document.querySelectorAll('.supervisor-card');
    const supervisorListItems = document.querySelectorAll('.supervisor-list-item');
    
    // Filter functionality
    function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedDirektorat = direktoratFilter.value;
        
        // Filter card view
        supervisorCards.forEach(card => {
            const supervisorName = card.dataset.supervisor;
            const direktorat = card.dataset.direktorat;
            
            // Get student names from the card
            const studentElements = card.querySelectorAll('.student-item');
            let studentNames = '';
            studentElements.forEach(el => {
                studentNames += el.dataset.student + ' ';
            });
            
            const matchesSearch = supervisorName.includes(searchTerm) || studentNames.includes(searchTerm);
            const matchesDirektorat = !selectedDirektorat || direktorat === selectedDirektorat;
            
            if (matchesSearch && matchesDirektorat) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Filter list view
        supervisorListItems.forEach(item => {
            const supervisorName = item.dataset.supervisor;
            const direktorat = item.dataset.direktorat;
            
            // Get student names from the list item
            const studentElements = item.querySelectorAll('.student-item');
            let studentNames = '';
            studentElements.forEach(el => {
                studentNames += el.dataset.student + ' ';
            });
            
            const matchesSearch = supervisorName.includes(searchTerm) || studentNames.includes(searchTerm);
            const matchesDirektorat = !selectedDirektorat || direktorat === selectedDirektorat;
            
            if (matchesSearch && matchesDirektorat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // View toggle functionality
    function switchToCardView() {
        cardContainer.classList.remove('hidden');
        listContainer.classList.add('hidden');
        
        cardViewBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600', 'font-medium');
        cardViewBtn.classList.remove('text-gray-600');
        listViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600', 'font-medium');
        listViewBtn.classList.add('text-gray-600');
    }
    
    function switchToListView() {
        cardContainer.classList.add('hidden');
        listContainer.classList.remove('hidden');
        
        listViewBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600', 'font-medium');
        listViewBtn.classList.remove('text-gray-600');
        cardViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600', 'font-medium');
        cardViewBtn.classList.add('text-gray-600');
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterItems);
    direktoratFilter.addEventListener('change', filterItems);
    cardViewBtn.addEventListener('click', switchToCardView);
    listViewBtn.addEventListener('click', switchToListView);

    // Initialize with card view
    switchToCardView();
});
</script>

<!-- Pagination -->
<div class="mt-8 flex items-center justify-between">
    <div class="text-sm text-gray-600">
        Menampilkan <span class="font-medium">{{ $supervisors->firstItem() ?? 0 }}</span> sampai
        <span class="font-medium">{{ $supervisors->lastItem() ?? 0 }}</span> dari
        <span class="font-medium">{{ $supervisors->total() }}</span> hasil
    </div>
    <div>
        {{ $supervisors->appends(request()->query())->links() }}
    </div>
</div>
@endsection