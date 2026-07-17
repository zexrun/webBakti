@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Back Button -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('supervisor.tasks.index') }}"
                       class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Tugas</h1>
                        <p class="text-gray-600 mt-1">Kelola dan nilai submission mahasiswa</p>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('supervisor.tasks.edit', $task) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('supervisor.tasks.destroy', $task) }}" method="POST" style="display: inline;" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                onclick="confirmDelete()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Task Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $task->title }}</h2>
                        
                        <!-- Task Meta Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="font-medium">Tipe:</span>
                                <span class="ml-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    {{ $task->type === 'harian' ? 'Harian' : 'Akhir' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium">Tenggat:</span>
                                <span class="ml-1">
                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') : 'Tidak ada' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="font-medium">Pembimbing:</span>
                                <span class="ml-1">{{ $task->supervisor->user->name }}</span>
                            </div>
                        </div>

                        <!-- Task Description -->
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Deskripsi Tugas</h3>
                            <p class="text-gray-800 leading-relaxed">{{ $task->description }}</p>
                        </div>

                        <!-- Task Attachment -->
                        @if($task->file_path)
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Lampiran</h3>
                                <a href="{{ asset('storage/' . $task->file_path) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Unduh Lampiran
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Task Statistics -->
                    <div class="lg:w-80">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Statistik Submission</h3>
                            <div class="space-y-3">
                                @php
                                    $totalStudents = count($assignedStudents);
                                    $submittedCount = $submissions->count();
                                    $gradedCount = $submissions->whereNotNull('grade')->count();
                                    $pendingCount = $submissions->whereNull('grade')->count();
                                @endphp
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Mahasiswa</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $totalStudents }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sudah Submit</span>
                                    <span class="text-sm font-medium text-green-600">{{ $submittedCount }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sudah Dinilai</span>
                                    <span class="text-sm font-medium text-blue-600">{{ $gradedCount }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Menunggu Nilai</span>
                                    <span class="text-sm font-medium text-yellow-600">{{ $pendingCount }}</span>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress Submission</span>
                                        <span>{{ $totalStudents > 0 ? round(($submittedCount / $totalStudents) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Submissions -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Status Submission Mahasiswa</h3>
        </div>

        <div class="space-y-6">
            @forelse($assignedStudents as $student)
                @php
                    $submission = $submissions->get($student->id);
                    $isSubmitted = !is_null($submission);
                    $isGraded = $isSubmitted && !is_null($submission->grade);
                    $isPending = $isSubmitted && is_null($submission->grade);
                @endphp
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Student Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-lg font-semibold text-blue-600">
                                        {{ substr($student->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $student->user->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $student->nim ?? 'NIM belum diisi' }}</p>
                                    <p class="text-xs text-gray-400">{{ $student->user->email }}</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex items-center gap-2">
                                @if($isGraded)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Sudah Dinilai
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Menunggu Penilaian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Belum Submit
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submission Content -->
                    <div class="p-6">
                        @if($submission)
                            <!-- Submission Details -->
                            <div class="mb-6">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Laporan Teks</h5>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-800 leading-relaxed">
                                        {{ $submission->content ?? 'Tidak ada laporan teks yang diberikan.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- File Attachment -->
                            @if($submission->file_path)
                                <div class="mb-6">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">File Submission</h5>
                                    <a href="{{ asset('storage/' . $submission->file_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Unduh File Submission
                                    </a>
                                </div>
                            @endif

                            <!-- Grading Section -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($submission->grade)
                                    <!-- Display Grade -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h5 class="text-sm font-medium text-gray-700">Penilaian</h5>
                                            <button onclick="toggleForm('form-nilai-{{ $submission->id }}')" 
                                                    class="text-sm text-blue-600 hover:text-blue-500 font-medium transition-colors">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit Nilai
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-sm text-gray-600">Nilai:</span>
                                                <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                                    {{ $submission->grade }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-600">Komentar:</span>
                                                <span class="ml-2 text-sm text-gray-800">
                                                    {{ $submission->comments ?? 'Tidak ada komentar' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- No Grade Yet -->
                                    <div class="text-center py-4">
                                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm text-gray-600 mb-3">Belum ada penilaian</p>
                                        <button onclick="toggleForm('form-nilai-{{ $submission->id }}')" 
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Beri Nilai
                                        </button>
                                    </div>
                                @endif

                                <!-- Grading Form -->
                                <form id="form-nilai-{{ $submission->id }}" 
                                      action="{{ route('supervisor.submissions.grade', $submission->id) }}" 
                                      method="POST" 
                                      class="mt-4 hidden">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                                            <select name="grade" 
                                                    required 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Nilai</option>
                                                @foreach(['A', 'B', 'C', 'D'] as $grade)
                                                    <option value="{{ $grade }}" 
                                                            @if($submission->grade == $grade) selected @endif>
                                                        {{ $grade }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                                            <div class="flex gap-2">
                                                <input type="text" 
                                                       name="comments" 
                                                       placeholder="Berikan komentar untuk mahasiswa (opsional)" 
                                                       value="{{ $submission->comments }}" 
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Simpan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- No Submission -->
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada submission</h3>
                                <p class="text-sm text-gray-500">Menunggu mahasiswa untuk mengumpulkan tugas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada mahasiswa yang ditugaskan</h3>
                    <p class="text-gray-500">Belum ada mahasiswa yang ditugaskan untuk tugas ini.</p>
                </div>
            @endforelse
        </div>

        <!-- Back Button -->
        <div class="mt-8 flex justify-start">
            <a href="{{ route('supervisor.tasks.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>
    </div>
</div>

<script>
function toggleForm(id) {
    const form = document.getElementById(id);
    const isHidden = form.classList.contains('hidden');
    
    // Hide all other forms first
    document.querySelectorAll('[id^="form-nilai-"]').forEach(f => {
        if (f.id !== id) {
            f.classList.add('hidden');
        }
    });
    
    // Toggle current form
    if (isHidden) {
        form.classList.remove('hidden');
        // Focus on the grade select
        const gradeSelect = form.querySelector('select[name="grade"]');
        if (gradeSelect) {
            setTimeout(() => gradeSelect.focus(), 100);
        }
    } else {
        form.classList.add('hidden');
    }
}

// Auto-hide forms when clicking outside
document.addEventListener('click', function(event) {
    const forms = document.querySelectorAll('[id^="form-nilai-"]');
    const buttons = document.querySelectorAll('button[onclick^="toggleForm"]');
    
    let clickedButton = false;
    buttons.forEach(button => {
        if (button.contains(event.target)) {
            clickedButton = true;
        }
    });
    
    if (!clickedButton) {
        forms.forEach(form => {
            if (!form.contains(event.target)) {
                form.classList.add('hidden');
            }
        });
    }
});

// Form submission feedback
document.querySelectorAll('form[id^="form-nilai-"]').forEach(form => {
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;

        // Reset after 3 seconds if form doesn't redirect
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 3000);
    });
});

// Delete confirmation
function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection