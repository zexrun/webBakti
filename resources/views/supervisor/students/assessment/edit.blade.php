@extends('layouts.app')

@section('title', 'Edit Penilaian Akhir - ' . $student->user->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Back Button -->
                    <a href="{{ route('supervisor.students.list.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    
                    <!-- Student Info -->
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                            <span class="text-lg font-semibold text-white">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Edit Penilaian Akhir
                            </h1>
                            <p class="text-sm text-gray-600">
                                {{ $student->user->name }} • {{ $student->nim ?? 'NIM belum diisi' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Assessment Info -->
                <div class="mt-4 sm:mt-0">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="font-medium">Edit Mode</span>
                        <span class="text-gray-400">•</span>
                        <span>Terakhir diupdate: {{ $assessment->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assessment Summary -->
        <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Penilaian Saat Ini
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    Review penilaian yang sudah diberikan sebelumnya
                </p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nilai Akhir</dt>
                        <dd class="mt-1 flex items-center">
                            @php
                                $gradeColors = [
                                    'A' => 'bg-green-100 text-green-800',
                                    'B' => 'bg-blue-100 text-blue-800', 
                                    'C' => 'bg-yellow-100 text-yellow-800',
                                    'D' => 'bg-red-100 text-red-800'
                                ];
                                $currentGradeColor = $gradeColors[$assessment->final_grade] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $currentGradeColor }}">
                                {{ $assessment->final_grade }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Penilaian</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Komentar Sebelumnya</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                            {{ $assessment->overall_comments }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Summary Card -->
        <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informasi Mahasiswa
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $student->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">NIM</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $student->nim ?? 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Universitas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $student->universitas ?? 'Belum diisi' }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Assessment Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Penilaian Akhir
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    Perbarui penilaian dan komentar untuk mahasiswa
                </p>
            </div>

            <form action="{{ route('supervisor.students.assessment.update', $student->id) }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PATCH')

                <!-- Grade Selection -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Nilai Akhir</label>
                        <span class="text-red-500">*</span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $grades = [
                                ['value' => 'A', 'label' => 'A - Sangat Baik', 'color' => 'green', 'description' => 'Kinerja luar biasa'],
                                ['value' => 'B', 'label' => 'B - Baik', 'color' => 'blue', 'description' => 'Kinerja baik'],
                                ['value' => 'C', 'label' => 'C - Cukup', 'color' => 'yellow', 'description' => 'Kinerja cukup'],
                                ['value' => 'D', 'label' => 'D - Kurang', 'color' => 'red', 'description' => 'Perlu perbaikan']
                            ];
                        @endphp
                        
                        @foreach($grades as $grade)
                            <label class="relative">
                                <input type="radio" name="final_grade" value="{{ $grade['value'] }}" 
                                       class="sr-only peer" 
                                       {{ old('final_grade', $assessment->final_grade) == $grade['value'] ? 'checked' : '' }}
                                       required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:border-{{ $grade['color'] }}-300 hover:bg-{{ $grade['color'] }}-50 peer-checked:border-{{ $grade['color'] }}-500 peer-checked:bg-{{ $grade['color'] }}-50 peer-checked:ring-2 peer-checked:ring-{{ $grade['color'] }}-200">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-{{ $grade['color'] }}-600 mb-1">
                                            {{ $grade['value'] }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 mb-1">
                                            {{ explode(' - ', $grade['label'])[1] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $grade['description'] }}
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Assessment Criteria (if available) -->
                @if(isset($assessment->technical_skills))
                <div class="space-y-6">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Kriteria Penilaian</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $criteria = [
                                ['name' => 'technical_skills', 'label' => 'Kemampuan Teknis', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                                ['name' => 'communication', 'label' => 'Komunikasi', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                                ['name' => 'teamwork', 'label' => 'Kerja Tim', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                                ['name' => 'initiative', 'label' => 'Inisiatif & Kreativitas', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                                ['name' => 'punctuality', 'label' => 'Kedisiplinan & Kehadiran', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['name' => 'learning_ability', 'label' => 'Kemampuan Belajar', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253']
                            ];
                        @endphp

                        @foreach($criteria as $criterion)
                            @if(isset($assessment->{$criterion['name']}))
                            <div class="space-y-3">
                                <label class="flex items-center space-x-2 text-sm font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $criterion['icon'] }}"/>
                                    </svg>
                                    <span>{{ $criterion['label'] }}</span>
                                </label>
                                <div class="flex space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="flex-1">
                                            <input type="radio" name="{{ $criterion['name'] }}" value="{{ $i }}" 
                                                   class="sr-only peer"
                                                   {{ old($criterion['name'], $assessment->{$criterion['name']}) == $i ? 'checked' : '' }}
                                                   required>
                                            <div class="p-2 text-center border border-gray-300 rounded cursor-pointer transition-all duration-200 hover:border-blue-400 hover:bg-blue-50 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-700">
                                                <div class="text-sm font-medium">{{ $i }}</div>
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Kurang</span>
                                    <span>Sangat Baik</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Overall Comments -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"/>
                        </svg>
                        <label for="overall_comments" class="text-lg font-semibold text-gray-900">
                            Komentar & Saran
                        </label>
                        <span class="text-red-500">*</span>
                    </div>
                    
                    <div class="space-y-3">
                        <textarea 
                            name="overall_comments" 
                            id="overall_comments" 
                            rows="6" 
                            required 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Berikan komentar menyeluruh tentang kinerja mahasiswa, pencapaian yang menonjol, area yang perlu diperbaiki, dan saran untuk pengembangan karir selanjutnya..."
                        >{{ old('overall_comments', $assessment->overall_comments) }}</textarea>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Minimal 50 karakter</span>
                            <span id="commentCount">{{ strlen($assessment->overall_comments) }} karakter</span>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                @if(isset($assessment->recommend_for_hire) || isset($assessment->recommend_for_internship))
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <label class="text-lg font-semibold text-gray-900">Rekomendasi</label>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="recommend_for_hire" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('recommend_for_hire', $assessment->recommend_for_hire) ? 'checked' : '' }}>
                            <label class="text-sm font-medium text-gray-700">
                                Saya merekomendasikan mahasiswa ini untuk peluang kerja di masa depan
                            </label>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="recommend_for_internship" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('recommend_for_internship', $assessment->recommend_for_internship) ? 'checked' : '' }}>
                            <label class="text-sm font-medium text-gray-700">
                                Saya merekomendasikan mahasiswa ini untuk program magang lanjutan
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Change Log -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-800">Perhatian</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Perubahan pada penilaian ini akan tercatat dalam sistem. Pastikan semua informasi sudah benar sebelum menyimpan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" 
                            onclick="window.history.back()"
                            class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </button>
                    
                    <button type="submit" 
                            class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Assessment Guidelines -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Panduan Penilaian</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Nilai A (Sangat Baik):</strong> Mahasiswa menunjukkan kinerja luar biasa, melebihi ekspektasi dalam semua aspek.</p>
                        <p><strong>Nilai B (Baik):</strong> Mahasiswa menunjukkan kinerja baik dan memenuhi sebagian besar ekspektasi.</p>
                        <p><strong>Nilai C (Cukup):</strong> Mahasiswa menunjukkan kinerja yang memadai dengan beberapa area yang perlu diperbaiki.</p>
                        <p><strong>Nilai D (Kurang):</strong> Mahasiswa menunjukkan kinerja di bawah ekspektasi dan memerlukan perbaikan signifikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Form Enhancement -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for comments
    const commentTextarea = document.getElementById('overall_comments');
    const commentCount = document.getElementById('commentCount');
    
    if (commentTextarea && commentCount) {
        // Initial count
        const initialCount = commentTextarea.value.length;
        commentCount.textContent = initialCount + ' karakter';
        
        if (initialCount < 50) {
            commentCount.classList.add('text-red-500');
        } else {
            commentCount.classList.add('text-green-500');
        }
        
        commentTextarea.addEventListener('input', function() {
            const count = this.value.length;
            commentCount.textContent = count + ' karakter';
            
            if (count < 50) {
                commentCount.classList.add('text-red-500');
                commentCount.classList.remove('text-gray-500', 'text-green-500');
            } else {
                commentCount.classList.add('text-green-500');
                commentCount.classList.remove('text-gray-500', 'text-red-500');
            }
        });
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const comments = commentTextarea.value.trim();
            if (comments.length < 50) {
                e.preventDefault();
                alert('Komentar harus minimal 50 karakter.');
                commentTextarea.focus();
                return false;
            }
            
            // Confirm before submitting changes
            if (!confirm('Apakah Anda yakin ingin menyimpan perubahan penilaian ini?')) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Auto-save draft
    let autoSaveTimer;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Save draft to localStorage
                const formData = new FormData(form);
                const draftData = {};
                for (let [key, value] of formData.entries()) {
                    draftData[key] = value;
                }
                localStorage.setItem('assessment_edit_draft_{{ $student->id }}', JSON.stringify(draftData));
                
                // Show auto-save indicator
                showAutoSaveIndicator();
            }, 1000);
        });
    });
    
    // Auto-save indicator
    function showAutoSaveIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'fixed bottom-4 right-4 bg-green-100 text-green-800 px-3 py-2 rounded-md shadow-sm text-sm font-medium';
        indicator.textContent = 'Draft tersimpan otomatis';
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            indicator.remove();
        }, 2000);
    }
    
    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('assessment_edit_draft_{{ $student->id }}');
    });
});
</script>

<style>
/* Custom styles for radio buttons */
.peer:checked ~ div {
    transform: scale(1.02);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Focus styles */
input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Gradient button hover effect */
.bg-gradient-to-r:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
@endsection