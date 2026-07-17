@extends('layouts.app')

@section('title', 'Beri Nilai Submission')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('supervisor.submissions.index') }}"
                   class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Beri Nilai Submission</h1>
                    <p class="text-gray-600 mt-1">Tinjau dan nilai submission dari mahasiswa</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Student & Task Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Submission</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Mahasiswa</p>
                            <p class="text-lg font-medium text-gray-900">{{ $submission->student->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->student->user->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Tugas</p>
                            <p class="text-lg font-medium text-gray-900">{{ $submission->task->title }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->task->type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Disubmit</p>
                            <p class="text-lg font-medium text-gray-900">{{ $submission->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            @if($submission->grade)
                                <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                    Sudah Dinilai
                                </p>
                            @else
                                <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                    Menunggu Nilai
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submission Content -->
                @if($submission->content || $submission->file_path)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Konten Submission</h2>

                        @if($submission->content)
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Teks Submission</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $submission->content }}</p>
                                </div>
                            </div>
                        @endif

                        @if($submission->file_path)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">File Submission</h3>
                                <a href="{{ asset('storage/' . $submission->file_path) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ basename($submission->file_path) }}
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Task Description -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi Tugas</h2>
                    <p class="text-gray-800 leading-relaxed">{{ $submission->task->description }}</p>
                </div>
            </div>

            <!-- Grading Sidebar -->
            <div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Beri Nilai</h2>

                    <form action="{{ route('supervisor.submissions.update', $submission) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <!-- Grade Selection -->
                            <div>
                                <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai <span class="text-red-500">*</span>
                                </label>
                                <select id="grade"
                                        name="grade"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Nilai</option>
                                    <option value="A" {{ $submission->grade === 'A' ? 'selected' : '' }}>A (Sangat Baik)</option>
                                    <option value="B" {{ $submission->grade === 'B' ? 'selected' : '' }}>B (Baik)</option>
                                    <option value="C" {{ $submission->grade === 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                    <option value="D" {{ $submission->grade === 'D' ? 'selected' : '' }}>D (Kurang)</option>
                                </select>
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comments -->
                            <div>
                                <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                                    Komentar <span class="text-gray-400 text-xs">(Opsional)</span>
                                </label>
                                <textarea id="comments"
                                          name="comments"
                                          rows="6"
                                          placeholder="Berikan feedback dan komentar untuk mahasiswa..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ $submission->comments }}</textarea>
                                @error('comments')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Grade Preview -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">Preview Nilai:</span>
                                    <span id="gradePreview" class="text-2xl font-bold text-blue-600">-</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-2 pt-4 border-t border-gray-200">
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Nilai
                                </button>

                                <a href="{{ route('supervisor.submissions.index') }}"
                                   class="w-full px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-center">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs text-yellow-800">
                            <strong>Tips:</strong> Berikan feedback yang konstruktif dan spesifik untuk membantu mahasiswa meningkatkan pekerjaan mereka.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade');
    const gradePreview = document.getElementById('gradePreview');

    function updateGradePreview() {
        gradePreview.textContent = gradeSelect.value || '-';
    }

    gradeSelect.addEventListener('change', updateGradePreview);
    updateGradePreview();
});
</script>
@endsection
