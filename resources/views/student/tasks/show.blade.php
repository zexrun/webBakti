@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium mb-2">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Tugas</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap dan submission tugas 📝</p>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @php
                        $isSubmitted = $submission ? true : false;
                        $isOverdue = $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast();
                        $daysLeft = $task->due_date ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($task->due_date), false) : null;
                    @endphp
                    
                    <!-- Status Badge -->
                    @if($isSubmitted)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sudah Dikumpulkan
                        </span>
                    @elseif($isOverdue)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Terlambat
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Belum Dikumpulkan
                        </span>
                    @endif
                    
                    <!-- Back Button -->
                    <a href="{{ route('student.tasks.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Task Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-blue-100 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Detail Tugas</h3>
                            <p class="text-gray-600">Informasi lengkap tugas</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $task->title }}</h2>
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4 border border-gray-200">
                                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                    {!! nl2br(e($task->description)) !!}
                                </div>
                            </div>
                        </div>
                        
                        @if($task->file_path)
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-gray-500 mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Lampiran Tugas</p>
                                            <p class="text-xs text-gray-500">File pendukung dari pembimbing</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $task->file_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Unduh Lampiran
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submission Section -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-6">
                        <div class="p-3 rounded-full {{ $isSubmitted ? 'bg-green-100' : 'bg-yellow-100' }} mr-4">
                            @if($isSubmitted)
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $isSubmitted ? 'Submission Anda' : 'Kumpulkan Tugas' }}
                            </h3>
                            <p class="text-gray-600">
                                {{ $isSubmitted ? 'Tugas telah dikumpulkan' : 'Upload jawaban tugas Anda' }}
                            </p>
                        </div>
                    </div>

                    @if($submission)
                        <!-- Submitted Content -->
                        <div class="space-y-6">
                            <!-- Success Message -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-green-800 font-semibold">Tugas berhasil dikumpulkan!</p>
                                        <p class="text-green-700 text-sm">Dikumpulkan pada {{ $submission->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Content -->
                            @if($submission->content)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                            </svg>
                                            Laporan Teks
                                        </div>
                                    </label>
                                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 border border-gray-200 rounded-lg p-4">
                                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                                            {{ $submission->content }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submitted File -->
                            @if($submission->file_path)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            File Terlampir
                                        </div>
                                    </label>
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center">
                                            <div class="p-2 rounded-lg bg-blue-500 mr-3">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">File Submission</p>
                                                <p class="text-xs text-gray-500">Klik untuk melihat file</p>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $submission->file_path) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat File
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Grade and Comments -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                            Nilai
                                        </div>
                                    </label>
                                    <div class="p-4 {{ $submission->grade ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded-lg">
                                        <p class="text-lg font-bold {{ $submission->grade ? 'text-green-800' : 'text-gray-600' }}">
                                            {{ $submission->grade ?? 'Belum dinilai' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            Status Review
                                        </div>
                                    </label>
                                    <div class="p-4 {{ $submission->comments ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }} border rounded-lg">
                                        <p class="text-sm {{ $submission->comments ? 'text-blue-800' : 'text-gray-600' }}">
                                            {{ $submission->comments ?? 'Belum ada komentar dari pembimbing' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Submission Form -->
                        <form id="submissionForm" action="{{ route('student.tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            <!-- Text Content -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                        </svg>
                                        Laporan Teks (Opsional)
                                    </div>
                                </label>
                                <textarea 
                                    name="content" 
                                    id="content" 
                                    rows="6" 
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 resize-none"
                                    placeholder="Tulis laporan atau penjelasan mengenai tugas yang Anda kerjakan..."
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">Jelaskan proses pengerjaan atau hasil yang Anda peroleh</p>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        Upload File (Opsional)
                                    </div>
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload file</span>
                                                <input id="file" name="file" type="file" class="sr-only" onchange="previewFile(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOCX, ZIP, atau format lainnya hingga 10MB</p>
                                    </div>
                                </div>
                                <div id="filePreview" class="hidden mt-4">
                                    <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span id="fileName" class="text-sm text-blue-800 font-medium"></span>
                                        <button type="button" onclick="removeFile()" class="ml-auto text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button
                                    type="button"
                                    onclick="resetForm()"
                                    class="px-6 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Reset Form
                                </button>
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                    </svg>
                                    <span id="submitText">Kumpulkan Tugas</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Task Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Informasi Tugas</h4>
                            <p class="text-sm text-gray-600">Detail dan deadline</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Supervisor -->
                        <div class="flex items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                            <div class="p-2 rounded-lg bg-blue-500 mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Pembimbing</p>
                                <p class="text-xs text-gray-500">Diberikan oleh</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $task->supervisor->user->name }}</p>
                            </div>
                        </div>

                        <!-- Task Type -->
                        <div class="flex items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                            <div class="p-2 rounded-lg bg-purple-500 mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Tipe Tugas</p>
                                <p class="text-xs text-gray-500">Kategori tugas</p>
                                <p class="text-sm font-semibold text-gray-900">{{ ucfirst($task->type) }}</p>
                            </div>
                        </div>

                        <!-- Deadline -->
                        @if($task->due_date)
                        @php
                            $now = \Carbon\Carbon::now();
                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                            $isOverdue = $dueDate->isPast();
                            
                            if ($isOverdue) {
                                // Untuk overdue, gunakan diffForHumans dengan opsi yang tepat
                                $timeText = "Terlambat " . $dueDate->diffForHumans($now, [
                                    'parts' => 2,
                                    'short' => false,
                                    'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
                                ]);
                                $colorClass = 'red';
                            } else {
                                // Untuk yang belum overdue
                                $diffInDays = $dueDate->diffInDays($now);
                                $diffInHours = $dueDate->diffInHours($now);
                                $diffInMinutes = $dueDate->diffInMinutes($now);
                                
                                if ($diffInDays > 3) {
                                    $timeText = $dueDate->diffForHumans($now, [
                                        'parts' => 1,
                                        'short' => false
                                    ]);
                                    $colorClass = 'green';
                                } elseif ($diffInDays > 0) {
                                    $timeText = $dueDate->diffForHumans($now, [
                                        'parts' => 2,
                                        'short' => false
                                    ]);
                                    $colorClass = 'yellow';
                                } elseif ($diffInHours > 0) {
                                    $timeText = $dueDate->diffForHumans($now, [
                                        'parts' => 2,
                                        'short' => false
                                    ]);
                                    $colorClass = $diffInHours <= 3 ? 'red' : 'yellow';
                                } else {
                                    $minutes = max(1, floor($diffInMinutes));
                                    $timeText = "{$minutes} menit lagi";
                                    $colorClass = 'red';
                                }
                            }
                            
                            $gradientFrom = $colorClass === 'red' ? 'red' : ($colorClass === 'yellow' ? 'yellow' : 'green');
                            $gradientTo = $colorClass === 'red' ? 'pink' : ($colorClass === 'yellow' ? 'orange' : 'emerald');
                        @endphp
                        
                        <div class="flex items-center p-3 bg-gradient-to-r from-{{ $gradientFrom }}-50 to-{{ $gradientTo }}-50 rounded-lg border border-{{ $colorClass }}-100">
                            <div class="p-2 rounded-lg bg-{{ $colorClass }}-500 mr-3">
                                @if($isOverdue)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Deadline</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $dueDate->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-{{ $colorClass }}-600 font-medium">{{ $timeText }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center p-3 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-100">
                            <div class="p-2 rounded-lg bg-gray-500 mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Deadline</p>
                                <p class="text-sm font-semibold text-gray-900">Tidak ada deadline</p>
                                <p class="text-xs text-gray-500">Kerjakan sesuai kemampuan</p>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

                <!-- Progress Card -->
                @if($submission)
                    <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="p-2 rounded-lg bg-green-500 mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Progress Tugas</h4>
                                <p class="text-sm text-gray-600">Status pengerjaan</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Tugas dikumpulkan</p>
                                    <p class="text-gray-500">{{ $submission->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($submission->grade)
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">Sudah dinilai</p>
                                        <p class="text-gray-500">Nilai: {{ $submission->grade }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">Menunggu penilaian</p>
                                        <p class="text-gray-500">Pembimbing sedang review</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-gray-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Aksi Cepat</h4>
                            <p class="text-sm text-gray-600">Tindakan yang tersedia</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('student.tasks.index') }}" 
                           class="flex items-center w-full p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Lihat Semua Tugas
                        </a>
                        
                        @if($submission)
                            <button onclick="printSubmission()" 
                                    class="flex items-center w-full p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                                <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Submission
                            </button>
                        @endif
                        
                        <button onclick="shareTask()" 
                                class="flex items-center w-full p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            Bagikan Tugas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File Preview Functions
function previewFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('filePreview').classList.remove('hidden');
    }
}

function removeFile() {
    document.getElementById('file').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

// Real-time countdown untuk deadline yang dekat
function updateCountdown() {
    const countdownElements = document.querySelectorAll('[id^="countdown-"]');
    
    countdownElements.forEach(element => {
        const deadline = new Date(element.dataset.deadline);
        const now = new Date();
        const diff = deadline - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            if (hours > 0) {
                element.textContent = `${hours}j ${minutes}m ${seconds}d tersisa`;
            } else if (minutes > 0) {
                element.textContent = `${minutes}m ${seconds}d tersisa`;
            } else {
                element.textContent = `${seconds}d tersisa`;
            }
        } else {
            element.textContent = 'Deadline terlewat!';
            element.className = element.className.replace(/text-\w+-500/, 'text-red-600');
        }
    });
}

// Update setiap detik untuk deadline yang dekat
if (document.querySelectorAll('[id^="countdown-"]').length > 0) {
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

function resetForm() {
    document.getElementById('submissionForm').reset();
    document.getElementById('filePreview').classList.add('hidden');
}

// Form Submission with Loading State
document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const originalText = submitText.textContent;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Mengumpulkan...</span>
    `;

    // Reset after 3 seconds if form doesn't submit (fallback)
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <span>${originalText}</span>
            `;
        }
    }, 10000);
});

// Print Function
function printSubmission() {
    window.print();
}

// Share Function
function shareTask() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $task->title }}',
            text: 'Tugas: {{ $task->title }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showSuccessMessage('Link tugas berhasil disalin ke clipboard!');
        });
    }
}

// Success Message Function
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${message}
        </div>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        alertDiv.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(alertDiv);
        }, 300);
    }, 3000);
}

// Drag and Drop File Upload
const fileInput = document.getElementById('file');
const dropZone = fileInput?.closest('.border-dashed');

if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            previewFile(fileInput);
        }
    }
}

// Auto-save draft (optional enhancement)
let autoSaveTimer;
const contentTextarea = document.getElementById('content');

if (contentTextarea) {
    contentTextarea.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Save draft to localStorage
            localStorage.setItem('task_draft_{{ $task->id }}', this.value);
        }, 2000);
    });

    // Load draft on page load
    const savedDraft = localStorage.getItem('task_draft_{{ $task->id }}');
    if (savedDraft && !contentTextarea.value) {
        contentTextarea.value = savedDraft;
    }
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .print-area {
        box-shadow: none !important;
        border: none !important;
    }
    
    .bg-gradient-to-r {
        background: #f8f9fa !important;
    }
}
</style>
@endsection