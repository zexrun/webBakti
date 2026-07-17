@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Selamat datang kembali, {{ Auth::user()->name }} 🎉</p>
                    <div class="flex items-center mt-2 text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">{{ now()->format('l, d F Y') }}</span>
                        <span class="mx-2">•</span>
                        <span class="text-sm" id="current-time"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-6">
            <!-- Total Pengguna -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $userCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="text-xs text-green-600 font-medium">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Mahasiswa -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $studentCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="text-xs text-green-600 font-medium">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pembimbing -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Pembimbing</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $supervisorCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs text-blue-600 font-medium">Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Admin -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Admin</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $adminCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-yellow-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs text-yellow-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Direktorat -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Direktorat</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $directorateCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-yellow-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs text-yellow-600 font-medium">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tugas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Tugas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $taskCount }}</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-3 h-3 text-purple-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs text-purple-600 font-medium">Progress</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Statistics Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Submission Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Submission</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Submission</span>
                        <span class="text-lg font-bold text-gray-900">{{ $totalSubmissions }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Sudah Dinilai</span>
                        <span class="text-lg font-bold text-green-600">{{ $submissionsWithGrades }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Menunggu Nilai</span>
                        <span class="text-lg font-bold text-orange-600">{{ $pendingSubmissions }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $submissionGradeRate }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">{{ $submissionGradeRate }}% Selesai</p>
                </div>
            </div>

            <!-- Attendance Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Kehadiran</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Attendance</span>
                        <span class="text-lg font-bold text-gray-900">{{ $totalAttendance }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Disetujui</span>
                        <span class="text-lg font-bold text-green-600">{{ $approvedAttendance }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pending</span>
                        <span class="text-lg font-bold text-orange-600">{{ $pendingAttendance }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $attendanceApprovalRate }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">{{ $attendanceApprovalRate }}% Diproses</p>
                </div>
            </div>

            <!-- Today's Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Hari Ini</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Check-in</span>
                        <span class="text-lg font-bold text-blue-600">{{ $todayAttendance }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Submission</span>
                        <span class="text-lg font-bold text-purple-600">{{ $todaySubmissions }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Approval</span>
                        <span class="text-lg font-bold text-green-600">{{ $todayApprovals }}</span>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-800">
                            <strong>Update realtime</strong><br>
                            Diperbarui {{ now()->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- This Month Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Bulan Ini</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Mahasiswa Baru</span>
                        <span class="text-lg font-bold text-blue-600">{{ $thisMonthStudents }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Tugas Baru</span>
                        <span class="text-lg font-bold text-purple-600">{{ $thisMonthTasks }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Kehadiran</span>
                        <span class="text-lg font-bold text-green-600">{{ $thisMonthAttendance }}</span>
                    </div>
                    <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs text-purple-800">
                            <strong>Periode</strong><br>
                            {{ now()->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Supervisor Metrics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Metrik Pembimbing</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Rata-rata Mahasiswa/Pembimbing</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $averageStudentsPerSupervisor }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Rata-rata Tugas/Pembimbing</span>
                        <span class="text-2xl font-bold text-purple-600">{{ $averageTasksPerSupervisor }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                        <span class="text-sm text-red-800 font-medium">Mahasiswa Tanpa Pembimbing</span>
                        <span class="text-2xl font-bold text-red-600">{{ $studentWithoutSupervisor }}</span>
                    </div>
                </div>
            </div>

            <!-- Department Rankings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Direktorat</h3>
                <div class="space-y-2">
                    @forelse($departmentStats as $dept)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $dept['name'] }}</p>
                                <p class="text-xs text-gray-600">{{ $dept['supervisors'] }} pembimbing • {{ $dept['students'] }} mahasiswa</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600">{{ $dept['students'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                    <p class="text-sm text-gray-500 mt-1">Akses cepat ke fitur utama sistem</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kelola Pengguna -->
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors duration-200">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Kelola Pengguna</h3>
                        <p class="text-sm text-gray-500">Manajemen user sistem</p>
                    </div>
                </a>

                <!-- Plotting Mahasiswa -->
                <a href="{{ route('admin.plotting') }}" 
                   class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors duration-200">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Plotting Mahasiswa</h3>
                        <p class="text-sm text-gray-500">Assign pembimbing</p>
                    </div>
                </a>

                <!-- Monitoring -->
                <a href="{{ route('admin.monitoring.index') }}" 
                   class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition-colors duration-200">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Monitoring</h3>
                        <p class="text-sm text-gray-500">Monitor aktivitas magang</p>
                    </div>
                </a>

                <!-- Laporan Kehadiran -->
                <a href="{{ route('admin.attendance.reports') }}"
                class="flex items-center p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors duration-200">
                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Laporan Kehadiran</h3>
                        <p class="text-sm text-gray-500">Export & analisis kehadiran</p>
                    </div>
                </a>

                <!-- Kelola Tugas -->
                <a href="{{ route('admin.settings.index') }}"
                class="flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition-colors duration-200">
                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Pengaturan</h3>
                        <p class="text-sm text-gray-500">Konfigurasi sistem</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

updateTime();
setInterval(updateTime, 1000);
</script>
@endpush