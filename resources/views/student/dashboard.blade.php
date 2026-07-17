@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Student Avatar -->
                    <div class="h-16 w-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-2xl font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>

                    <!-- Welcome Message -->
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Halo, {{ Auth::user()->name }}! 👋
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Dashboard Mahasiswa • {{ now()->format('l, d F Y') }}
                        </p>
                    </div>
                </div>

                <!-- Supervisor Info -->
                <div class="mt-4 sm:mt-0 bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-500 mb-1">Pembimbing</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $supervisor->name }}</p>
                    <p class="text-xs text-gray-600">{{ $supervisor->email }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Completion Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Progress Keseluruhan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $completionPercentage }}%</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $completionPercentage }}%"></div>
                </div>
            </div>

            <!-- Tasks Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tugas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $taskStats['total'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-green-600">{{ $taskStats['completed'] }} Selesai</span> |
                    <span class="font-medium text-orange-600">{{ $taskStats['pending'] }} Pending</span>
                </div>
            </div>

            <!-- Attendance This Month -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kehadiran Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceStats['rate'] }}%</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $attendanceStats['present'] }} Hadir</span> |
                    <span class="font-medium">{{ $attendanceStats['absent'] }} Absent</span>
                </div>
            </div>

            <!-- Logbooks This Month -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Logbooks</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $logbooksThisMonth }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    <a href="{{ route('student.logbooks.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
                </p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Upcoming Tasks & Recent Grades -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Upcoming Tasks (7 days) -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tugas Mendatang (7 Hari ke Depan)
                    </h2>

                    @if($upcomingTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingTasks as $task)
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $task->title }}</h3>
                                            <p class="text-xs text-gray-600 mt-1">Pembimbing: {{ $task->supervisor->user->name }}</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $task->type === 'harian' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $task->type === 'harian' ? 'Harian' : 'Akhir' }}
                                                </span>
                            @php
                                $daysUntil = now()->diffInDays($task->due_date, false);
                            @endphp
                                                @if($daysUntil === 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Hari Ini!
                                                    </span>
                                                @elseif($daysUntil === 1)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        Besok
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ $daysUntil }} hari
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right ml-4">
                                            <p class="text-xs text-gray-500">{{ $task->due_date->format('d M, H:i') }}</p>
                                            <a href="{{ route('student.tasks.show', $task->id) }}" class="mt-2 inline-block text-xs font-medium text-blue-600 hover:text-blue-800">
                                                Lihat →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Tidak ada tugas mendatang dalam 7 hari ke depan</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Grades -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Nilai Terbaru
                    </h2>

                    @if($recentGrades->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentGrades as $submission)
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $submission->task->title }}</h3>
                                            <p class="text-xs text-gray-600 mt-1">{{ $submission->updated_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100">
                                                <span class="text-lg font-bold text-blue-600">{{ $submission->grade }}</span>
                                            </div>
                                            @if($submission->comments)
                                                <p class="text-xs text-gray-600 mt-2">{{ Str::limit($submission->comments, 50) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.tasks.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Lihat Semua Nilai →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada nilai yang diterima</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Pending Submissions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Submission Pending
                        <span class="ml-auto text-lg font-bold text-purple-600">{{ $pendingSubmissions->count() }}</span>
                    </h2>

                    @if($pendingSubmissions->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($pendingSubmissions as $submission)
                                <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-900">{{ $submission->task->title }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Disubmit: {{ $submission->created_at->format('d M, H:i') }}</p>
                                    <p class="text-xs text-purple-600 mt-2">⏳ Menunggu penilaian</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-sm text-gray-500">Semua submission dinilai!</p>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="mt-6 space-y-2 border-t border-gray-200 pt-6">
                        <a href="{{ route('student.tasks.index') }}" class="flex items-center justify-center w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Lihat Semua Tugas
                        </a>
                        <a href="{{ route('student.logbooks.create') }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Logbook
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
