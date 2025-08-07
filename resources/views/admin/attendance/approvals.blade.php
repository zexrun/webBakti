@extends('layouts.app')

@section('title', 'Persetujuan Absensi')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Persetujuan Absensi</h2>
            <p class="text-gray-600">Kelola persetujuan absensi dan pengajuan izin mahasiswa.</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded-lg mb-6 relative">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
                <button type="button" class="absolute top-4 right-4 text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Absensi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingAttendances->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Izin</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingExceptions->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disetujui Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $approvedToday ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ditolak Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedToday ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button onclick="switchTab('attendance')"
                            id="attendance-tab"
                            class="tab-button border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Absensi Pending ({{ $pendingAttendances->total() }})
                    </button>
                    <button onclick="switchTab('exceptions')"
                            id="exceptions-tab"
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Pengajuan Izin ({{ $pendingExceptions->total() }})
                    </button>
                </nav>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-wrap gap-3">
                    <button onclick="bulkApprove('attendance')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Setujui Semua
                    </button>
                    <button onclick="bulkReject('attendance')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tolak Semua
                    </button>
                    <a href="{{ route('admin.attendance.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Monitoring
                    </a>
                </div>
            </div>
        </div>

        <!-- Attendance Approvals Tab -->
        <div id="attendance-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Absensi Menunggu Persetujuan</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-attendance" onchange="toggleSelectAll('attendance')" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mahasiswa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Check In/Out
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                    Diajukan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingAttendances as $attendance)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="attendance-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $attendance->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-600">
                                                        {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $attendance->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $attendance->date->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                                        <div>In: {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</div>
                                        <div>Out: {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attendance->status_badge ?? 'bg-yellow-100 text-yellow-800' }}">
                                            @if($attendance->status === 'present')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($attendance->status === 'late')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $attendance->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <button type="button"
                                                    onclick="openApprovalModal('attendance', {{ $attendance->id }}, '{{ $attendance->user->name }}', '{{ $attendance->date->format('d/m/Y') }}')"
                                                    class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors duration-200">
                                                Detail
                                            </button>
                                            <button type="button"
                                                    onclick="quickAction('attendance', {{ $attendance->id }}, 'approve')"
                                                    class="text-green-600 hover:text-green-900 font-medium text-sm transition-colors duration-200">
                                                Setujui
                                            </button>
                                            <button type="button"
                                                    onclick="quickAction('attendance', {{ $attendance->id }}, 'reject')"
                                                    class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors duration-200">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada absensi pending</h3>
                                            <p class="text-sm text-gray-500">Semua absensi sudah diproses atau belum ada pengajuan baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $pendingAttendances->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Exceptions Approvals Tab -->
        <div id="exceptions-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Pengajuan Izin Menunggu Persetujuan</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-exceptions" onchange="toggleSelectAll('exceptions')" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mahasiswa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Jenis
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alasan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                    Diajukan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingExceptions as $exception)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="exceptions-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $exception->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-600">
                                                        {{ strtoupper(substr($exception->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $exception->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $exception->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $exception->date->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $exception->date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $exception->type_name ?? 'Izin' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $exception->reason }}">
                                            {{ $exception->reason }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $exception->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <button type="button"
                                                    onclick="openApprovalModal('exception', {{ $exception->id }}, '{{ $exception->user->name }}', '{{ $exception->date->format('d/m/Y') }}')"
                                                    class="text-blue-600 hover:text-blue-900 font-medium text-sm transition-colors duration-200">
                                                Detail
                                            </button>
                                            <button type="button"
                                                    onclick="quickAction('exception', {{ $exception->id }}, 'approve')"
                                                    class="text-green-600 hover:text-green-900 font-medium text-sm transition-colors duration-200">
                                                Setujui
                                            </button>
                                            <button type="button"
                                                    onclick="quickAction('exception', {{ $exception->id }}, 'reject')"
                                                    class="text-red-600 hover:text-red-900 font-medium text-sm transition-colors duration-200">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada pengajuan izin pending</h3>
                                            <p class="text-sm text-gray-500">Semua pengajuan izin sudah diproses atau belum ada pengajuan baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $pendingExceptions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Detail Persetujuan</h3>
                        <p class="text-sm text-gray-600 mt-1">Tinjau dan berikan keputusan</p>
                    </div>
                    <button type="button" onclick="closeModal('approvalModal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 flex-1 overflow-y-auto">
                <form id="approvalForm" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Info Section -->
                        <div id="approvalInfo" class="bg-gray-50 rounded-lg p-4">
                            <!-- Info will be populated by JavaScript -->
                        </div>
                        
                        <!-- Decision Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Keputusan</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="action" value="approve" class="mr-3 text-green-600 focus:ring-green-500">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-green-600 font-medium">Setujui</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="action" value="reject" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-red-600 font-medium">Tolak</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Notes Section -->
                        <div>
                            <label for="approvalNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea id="approvalNotes" name="notes" rows="4"
                                      class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeModal('approvalModal')"
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                                Simpan Keputusan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Tab switching
function switchTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tab + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById(tab + '-tab');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Select all checkboxes
function toggleSelectAll(type) {
    const selectAll = document.getElementById(`select-all-${type}`);
    const checkboxes = document.querySelectorAll(`.${type}-checkbox`);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Quick action (approve/reject)
function quickAction(type, id, action) {
    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
    if (confirm(`Yakin ingin ${actionText} item ini?`)) {
        submitAction(type, id, action);
    }
}

// Bulk actions
function bulkApprove(type) {
    const checkboxes = document.querySelectorAll(`.${type}-checkbox:checked`);
    if (checkboxes.length === 0) {
        alert('Pilih minimal satu item untuk disetujui');
        return;
    }
    
    if (confirm(`Yakin ingin menyetujui ${checkboxes.length} item yang dipilih?`)) {
        const ids = Array.from(checkboxes).map(cb => cb.value);
        submitBulkAction(type, ids, 'approve');
    }
}

function bulkReject(type) {
    const checkboxes = document.querySelectorAll(`.${type}-checkbox:checked`);
    if (checkboxes.length === 0) {
        alert('Pilih minimal satu item untuk ditolak');
        return;
    }
    
    if (confirm(`Yakin ingin menolak ${checkboxes.length} item yang dipilih?`)) {
        const ids = Array.from(checkboxes).map(cb => cb.value);
        submitBulkAction(type, ids, 'reject');
    }
}

// Open approval modal
function openApprovalModal(type, id, userName, date) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const info = document.getElementById('approvalInfo');
    
    form.action = `/admin/attendance/approve/${type}/${id}`;
    info.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Mahasiswa:</span>
                <span class="ml-2 font-medium">${userName}</span>
            </div>
            <div>
                <span class="text-gray-600">Tanggal:</span>
                <span class="ml-2 font-medium">${date}</span>
            </div>
            <div>
                <span class="text-gray-600">Jenis:</span>
                <span class="ml-2 font-medium">${type === 'attendance' ? 'Absensi' : 'Pengajuan Izin'}</span>
            </div>
            <div>
                <span class="text-gray-600">Status:</span>
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Menunggu Persetujuan
                </span>
            </div>
        </div>
    `;
    
    openModal('approvalModal');
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    // Add animation
    setTimeout(() => {
        const content = modal.querySelector('[id$="-content"]');
        if (content) {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = modal.querySelector('[id$="-content"]');
    
    if (content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
    }
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Submit functions
function submitAction(type, id, action, notes = '') {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/attendance/approve/${type}/${id}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    
    const notesInput = document.createElement('input');
    notesInput.type = 'hidden';
    notesInput.name = 'notes';
    notesInput.value = notes;
    
    form.appendChild(csrfToken);
    form.appendChild(actionInput);
    form.appendChild(notesInput);
    document.body.appendChild(form);
    form.submit();
}

function submitBulkAction(type, ids, action) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/attendance/bulk-approve/${type}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    
    ids.forEach(id => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'ids[]';
        idInput.value = id;
        form.appendChild(idInput);
    });
    
    form.appendChild(csrfToken);
    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
}

// Close modal when clicking outside
document.getElementById('approvalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('approvalModal');
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('approvalModal').classList.contains('hidden')) {
        closeModal('approvalModal');
    }
});

// Form submission handler
document.getElementById('approvalForm').addEventListener('submit', function(e) {
    const action = document.querySelector('input[name="action"]:checked');
    if (!action) {
        e.preventDefault();
        alert('Pilih keputusan terlebih dahulu');
        return;
    }
});
</script>
@endpush

@endsection
