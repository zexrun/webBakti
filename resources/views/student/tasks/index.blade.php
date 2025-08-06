@extends('layouts.app')

@section('title', 'Tugas')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Daftar Tugas</h1>
                    <p class="text-gray-600 mt-1">Kelola dan kerjakan tugas dari pembimbing Anda 📋</p>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Task Statistics -->
                    <div class="bg-blue-50 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-blue-700">Total Tugas: <span class="font-bold">{{ $tasks->total() }}</span></span>
                    </div>
                    @php
                        $pendingTasks = $tasks->where('submissions', '==', null)->count();
                        $completedTasks = $tasks->where('submissions', '!=', null)->count();
                    @endphp
                    <div class="bg-yellow-50 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-yellow-700">Pending: <span class="font-bold">{{ $pendingTasks }}</span></span>
                    </div>
                    <div class="bg-green-50 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-green-700">Selesai: <span class="font-bold">{{ $completedTasks }}</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <select class="border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" onchange="filterTasks(this.value)">
                            <option value="all">Semua Tugas</option>
                            <option value="pending">Belum Dikerjakan</option>
                            <option value="completed">Sudah Selesai</option>
                            <option value="overdue">Terlambat</option>
                        </select>
                    </div>
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Cari tugas..." class="pl-10 pr-4 py-2 border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 w-64" oninput="searchTasks(this.value)">
                </div>
            </div>
        </div>

        <!-- Tasks Table -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Tugas Aktif</h3>
                            <p class="text-sm text-gray-600">Tugas yang diberikan oleh pembimbing</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="toggleView('table')" id="tableViewBtn" class="p-2 rounded-lg bg-blue-500 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                        <button onclick="toggleView('card')" id="cardViewBtn" class="p-2 rounded-lg bg-gray-200 text-gray-600 hover:bg-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableView" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Judul Tugas
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                    </svg>
                                    Tipe & Status
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Pembimbing
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Deadline
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tasksTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            @php
                                $isOverdue = $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast();
                                $isSubmitted = $task->submissions()->where('student_id', auth()->id())->exists();
                                $daysLeft = $task->due_date ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($task->due_date), false) : null;
                            @endphp
                            <tr class="hover:bg-blue-50 transition-colors duration-200 task-row" 
                                data-type="{{ $task->type }}" 
                                data-status="{{ $isSubmitted ? 'completed' : ($isOverdue ? 'overdue' : 'pending') }}"
                                data-title="{{ strtolower($task->title) }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                                            <p class="text-xs text-gray-500">{{ Str::limit($task->description ?? 'Tidak ada deskripsi', 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        @php
                                            $typeConfig = [
                                                'individual' => ['label' => 'Individual', 'class' => 'bg-blue-100 text-blue-800'],
                                                'group' => ['label' => 'Kelompok', 'class' => 'bg-purple-100 text-purple-800'],
                                                'project' => ['label' => 'Proyek', 'class' => 'bg-indigo-100 text-indigo-800']
                                            ];
                                            $config = $typeConfig[$task->type] ?? ['label' => ucfirst($task->type), 'class' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                        
                                        @if($isSubmitted)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Selesai
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 rounded-full p-2 mr-3">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $task->supervisor->user->name }}</p>
                                            <p class="text-xs text-gray-500">Pembimbing</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->due_date)
                                        <div class="flex items-center">
                                            <div class="p-2 rounded-lg {{ $isOverdue ? 'bg-red-100' : ($daysLeft <= 3 ? 'bg-yellow-100' : 'bg-green-100') }} mr-3">
                                                <svg class="w-4 h-4 {{ $isOverdue ? 'text-red-600' : ($daysLeft <= 3 ? 'text-yellow-600' : 'text-green-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>
                                                <p class="text-xs {{ $isOverdue ? 'text-red-600' : ($daysLeft <= 3 ? 'text-yellow-600' : 'text-gray-500') }}">
                                                    @if($isOverdue)
                                                        Terlambat {{ abs($daysLeft) }} hari
                                                    @elseif($daysLeft == 0)
                                                        Hari ini
                                                    @elseif($daysLeft == 1)
                                                        Besok
                                                    @else
                                                        {{ $daysLeft }} hari lagi
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Tidak ada deadline</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('student.tasks.show', $task->id) }}" 
                                       class="inline-flex items-center px-4 py-2 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $isSubmitted ? 'Lihat Detail' : 'Kerjakan' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-100 rounded-full p-6 mb-4">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada tugas</h3>
                                        <p class="text-gray-500 mb-4">Tugas dari pembimbing akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card View (Hidden by default) -->
            <div id="cardView" class="hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tasks as $task)
                    @php
                        $now = \Carbon\Carbon::now();
                        $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                        
                        if ($dueDate) {
                            $isOverdue = $dueDate->isPast();
                            
                            // Hitung selisih waktu dengan presisi
                            $diffInMinutes = $isOverdue ? abs($now->diffInMinutes($dueDate)) : $dueDate->diffInMinutes($now);
                            $diffInHours = $isOverdue ? abs($now->diffInHours($dueDate)) : $dueDate->diffInHours($now);
                            $diffInDays = $isOverdue ? abs($now->diffInDays($dueDate)) : $dueDate->diffInDays($now);
                            
                            // Format waktu yang detail
                            if ($diffInDays > 0) {
                                $remainingHours = $diffInHours % 24;
                                $remainingMinutes = $diffInMinutes % 60;
                                
                                $timeText = "{$diffInDays} hari";
                                if ($remainingHours > 0) {
                                    $timeText .= " {$remainingHours} jam";
                                }
                                if ($remainingMinutes > 0 && $diffInDays <= 2) { // Tampilkan menit jika <= 2 hari
                                    $timeText .= " {$remainingMinutes} menit";
                                }
                            } elseif ($diffInHours > 0) {
                                $remainingMinutes = $diffInMinutes % 60;
                                $timeText = "{$diffInHours} jam";
                                if ($remainingMinutes > 0) {
                                    $timeText .= " {$remainingMinutes} menit";
                                }
                            } else {
                                $timeText = max(1, $diffInMinutes) . " menit";
                            }
                            
                            // Tambahkan suffix
                            $timeText .= $isOverdue ? " yang lalu" : " lagi";
                            
                            // Tentukan warna berdasarkan kondisi
                            if ($isOverdue) {
                                $colorClass = 'red';
                            } elseif ($diffInHours <= 3) {
                                $colorClass = 'red';
                            } elseif ($diffInDays <= 3) {
                                $colorClass = 'yellow';
                            } else {
                                $colorClass = 'green';
                            }
                        }
                    @endphp

                    @if($dueDate)
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-{{ $colorClass }}-100 mr-3">
                                <svg class="w-4 h-4 text-{{ $colorClass }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $dueDate->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-{{ $colorClass }}-600 font-medium">
                                    {{ $timeText }}
                                </p>
                            </div>
                        </div>
                    @else
                        <span class="text-sm text-gray-500">Tidak ada deadline</span>
                    @endif
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300 task-card" 
                             data-type="{{ $task->type }}" 
                             data-status="{{ $isSubmitted ? 'completed' : ($isOverdue ? 'overdue' : 'pending') }}"
                             data-title="{{ strtolower($task->title) }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $task->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ Str::limit($task->description ?? 'Tidak ada deskripsi', 100) }}</p>
                                </div>
                                @if($isSubmitted)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $task->supervisor->user->name }}
                                </div>
                                @if($task->due_date)
                                    <div class="flex items-center text-sm {{ $isOverdue ? 'text-red-600' : ($daysLeft <= 3 ? 'text-yellow-600' : 'text-gray-600') }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}
                                    </div>
                                @endif
                            </div>
                            
                            <a href="{{ route('student.tasks.show', $task->id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                {{ $isSubmitted ? 'Lihat Detail' : 'Kerjakan Tugas' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// View Toggle Functions
function toggleView(viewType) {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardBtn = document.getElementById('cardViewBtn');
    
    if (viewType === 'table') {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        tableBtn.classList.add('bg-blue-500', 'text-white');
        tableBtn.classList.remove('bg-gray-200', 'text-gray-600');
        cardBtn.classList.add('bg-gray-200', 'text-gray-600');
        cardBtn.classList.remove('bg-blue-500', 'text-white');
    } else {
        tableView.classList.add('hidden');
        cardView.classList.remove('hidden');
        cardBtn.classList.add('bg-blue-500', 'text-white');
        cardBtn.classList.remove('bg-gray-200', 'text-gray-600');
        tableBtn.classList.add('bg-gray-200', 'text-gray-600');
        tableBtn.classList.remove('bg-blue-500', 'text-white');
    }
}

// Filter Functions
function filterTasks(status) {
    const rows = document.querySelectorAll('.task-row');
    const cards = document.querySelectorAll('.task-card');
    
    [...rows, ...cards].forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    
    updateEmptyState();
}

function filterByType(type) {
    const rows = document.querySelectorAll('.task-row');
    const cards = document.querySelectorAll('.task-card');
    
    [...rows, ...cards].forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    
    updateEmptyState();
}

function searchTasks(query) {
    const rows = document.querySelectorAll('.task-row');
    const cards = document.querySelectorAll('.task-card');
    
    [...rows, ...cards].forEach(item => {
        const title = item.dataset.title;
        if (title.includes(query.toLowerCase())) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    
    updateEmptyState();
}

function updateEmptyState() {
    const visibleRows = document.querySelectorAll('.task-row:not([style*="display: none"])');
    const visibleCards = document.querySelectorAll('.task-card:not([style*="display: none"])');
    const emptyState = document.getElementById('emptyState');
    
    if (visibleRows.length === 0 && visibleCards.length === 0) {
        if (emptyState) emptyState.style.display = '';
    } else {
        if (emptyState) emptyState.style.display = 'none';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateEmptyState();
});
</script>
@endsection