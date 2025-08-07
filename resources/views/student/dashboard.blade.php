@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

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
                
                <!-- Quick Actions -->
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v6m0 0l3-3m-3 3l-3-3"/>
                        </svg>
                        @php
                            $overdueCount = $tasks->filter(function($task) {
                                return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast();
                            })->count();
                        @endphp
                        @if($overdueCount > 0)
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $overdueCount }}</span>
                        @endif
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 bg-white px-3 py-2 rounded-lg border border-gray-300 hover:border-gray-400 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Profile</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('student.info.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Edit Profile
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.parentElement.remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @php
                $totalTasks = $tasks->count();
                $completedTasks = $tasks->where('status', 'completed')->count();
                $overdueTasks = $tasks->filter(function($task) {
                    return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed';
                })->count();
                $todayTasks = $tasks->filter(function($task) {
                    return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isToday();
                })->count();
            @endphp
            
            <!-- Total Tasks -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tugas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-blue-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>Semua tugas</span>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedTasks }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <span>Sudah dikerjakan</span>
                    </div>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $overdueTasks }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Perlu perhatian</span>
                    </div>
                </div>
            </div>

            <!-- Today's Tasks -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $todayTasks }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-orange-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Deadline hari ini</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Menu Utama
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Tasks -->
                        <a href="{{ route('student.tasks.index') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-blue-700">Tugas Saya</h4>
                                    <p class="text-sm text-gray-600">Lihat dan kerjakan tugas</p>
                                </div>
                            </div>
                        </a>

                        <!-- Logbooks -->
                        <a href="{{ route('student.logbooks.index') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-green-700">Laporan Harian</h4>
                                    <p class="text-sm text-gray-600">Kelola kegiatan harian</p>
                                </div>
                            </div>
                        </a>

                        <!-- Profile -->
                        <a href="{{ route('student.info.edit') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-purple-700">Informasi Magang</h4>
                                    <p class="text-sm text-gray-600">Edit profil magang</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Tasks Overview with Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" 
                     x-data="{
                         filter: 'all',
                         tasks: {{ $tasks->toJson() }},
                         get filteredTasks() {
                             const today = new Date().setHours(0,0,0,0);
                             const nextWeek = new Date(today);
                             nextWeek.setDate(nextWeek.getDate() + 7);
                             
                             if (this.filter === 'all') return this.tasks;
                             
                             return this.tasks.filter(task => {
                                 if (!task.due_date) return false;
                                 const dueDate = new Date(task.due_date).setHours(0,0,0,0);
                                 
                                 if (this.filter === 'overdue') return dueDate < today && task.status !== 'completed';
                                 if (this.filter === 'today') return dueDate === today;
                                 if (this.filter === 'week') return dueDate > today && dueDate <= nextWeek;
                                 if (this.filter === 'completed') return task.status === 'completed';
                             });
                         }
                     }">
                    
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Tugas Mendatang
                        </h3>
                        
                        <a href="{{ route('student.tasks.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Lihat semua →
                        </a>
                    </div>
                    
                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2 border-b border-gray-200 mb-6">
                        <button @click="filter = 'all'" 
                                :class="{ 'border-indigo-600 text-indigo-600 bg-indigo-50': filter === 'all' }" 
                                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-indigo-600 transition-colors duration-200">
                            Semua
                        </button>
                        <button @click="filter = 'overdue'" 
                                :class="{ 'border-red-600 text-red-600 bg-red-50': filter === 'overdue' }" 
                                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-red-600 transition-colors duration-200">
                            Terlambat <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full">{{ $overdueTasks }}</span>
                        </button>
                        <button @click="filter = 'today'" 
                                :class="{ 'border-orange-600 text-orange-600 bg-orange-50': filter === 'today' }" 
                                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-orange-600 transition-colors duration-200">
                            Hari Ini <span class="ml-1 px-2 py-0.5 bg-orange-100 text-orange-800 text-xs rounded-full">{{ $todayTasks }}</span>
                        </button>
                        <button @click="filter = 'week'" 
                                :class="{ 'border-blue-600 text-blue-600 bg-blue-50': filter === 'week' }" 
                                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-blue-600 transition-colors duration-200">
                            Minggu Ini
                        </button>
                        <button @click="filter = 'completed'" 
                                :class="{ 'border-green-600 text-green-600 bg-green-50': filter === 'completed' }" 
                                class="px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-green-600 transition-colors duration-200">
                            Selesai <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">{{ $completedTasks }}</span>
                        </button>
                    </div>
                    
                    <!-- Tasks List -->
                    <div class="space-y-4">
                        <template x-for="task in filteredTasks.slice(0, 5)" :key="task.id">
                            <div class="p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-3 h-3 rounded-full" 
                                                     :class="{
                                                         'bg-green-500': task.status === 'completed',
                                                         'bg-red-500': task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed',
                                                         'bg-orange-500': task.due_date && new Date(task.due_date).toDateString() === new Date().toDateString(),
                                                         'bg-blue-500': !task.due_date || (new Date(task.due_date) > new Date() && new Date(task.due_date).toDateString() !== new Date().toDateString())
                                                     }">
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 truncate" x-text="task.title"></p>
                                                <div class="flex items-center space-x-4 mt-1">
                                                    <p class="text-sm text-gray-500" x-show="task.due_date">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span x-text="new Date(task.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                                                    </p>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                          :class="{
                                                              'bg-green-100 text-green-800': task.status === 'completed',
                                                              'bg-yellow-100 text-yellow-800': task.status === 'in_progress',
                                                              'bg-gray-100 text-gray-800': task.status === 'pending'
                                                          }"
                                                          x-text="task.status === 'completed' ? 'Selesai' : task.status === 'in_progress' ? 'Dikerjakan' : 'Belum Mulai'">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <a :href="`/student/tasks/${task.id}`" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="filteredTasks.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm italic">Tidak ada tugas dalam kategori ini.</p>
                            </div>
                        </template>
                        
                        <template x-if="filteredTasks.length > 5">
                            <div class="text-center pt-4 border-t border-gray-200">
                                <a href="{{ route('student.tasks.index') }}" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    <span x-text="`Lihat ${filteredTasks.length - 5} tugas lainnya`"></span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Progress Overview -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Progress Tugas
                    </h3>
                    
                    @php
                        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp
                    
                    <div class="space-y-4">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-600">Penyelesaian</span>
                                <span class="font-semibold text-gray-900">{{ $progressPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Selesai</span>
                                <span class="text-sm font-semibold text-green-600">{{ $completedTasks }}/{{ $totalTasks }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Terlambat</span>
                                <span class="text-sm font-semibold text-red-600">{{ $overdueTasks }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Hari Ini</span>
                                <span class="text-sm font-semibold text-orange-600">{{ $todayTasks }}</span>
                            </div>
                        </div>
                        
                        @if($overdueTasks > 0)
                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ route('student.tasks.index') }}" 
                                   class="inline-flex items-center text-sm text-red-600 hover:text-red-700 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    Kerjakan tugas terlambat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Akses Cepat
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('student.logbooks.index') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Lihat Laporan Harian
                        </a>
                        
                        @if(Route::has('student.logbooks.create'))
                            <a href="{{ route('student.logbooks.create') }}" 
                               class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Tulis Laporan Harian
                            </a>
                        @else
                            <a href="{{ route('student.logbooks.index') }}" 
                               class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Kelola Laporan Harian
                            </a>
                        @endif
                        
                        <a href="{{ route('student.info.edit') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan Profil
                        </a>
                        
                        <a href="{{ route('student.tasks.index') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Lihat Semua Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
[x-cloak] {
    display: none !important;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
@endsection
