@extends('layouts.app')

@section('title', 'Buat Tugas Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('supervisor.tasks.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Buat Tugas Baru</h1>
                    <p class="text-gray-600 mt-1">Isi detail di bawah ini untuk memberikan tugas baru kepada mahasiswa bimbingan</p>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800 mb-2">Terjadi Kesalahan</h3>
                                <ul class="text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('supervisor.tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskForm">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul Tugas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="Masukkan judul tugas"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      required
                                      placeholder="Jelaskan detail tugas dan instruksi yang perlu diikuti"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('description') }}</textarea>
                        </div>

                        <!-- Type and Due Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Tugas <span class="text-red-500">*</span>
                                </label>
                                <select name="type" 
                                        id="type" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih tipe tugas</option>
                                    <option value="harian" {{ old('type') == 'harian' ? 'selected' : '' }}>Tugas Harian</option>
                                    <option value="akhir" disabled {{ old('type') == 'akhir' ? 'selected' : '' }}>Laporan Akhir</option>
                                </select>
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tenggat Waktu <span class="text-gray-400 text-xs">(Opsional)</span>
                                </label>
                                <input type="datetime-local" 
                                       name="due_date" 
                                       id="due_date" 
                                       value="{{ old('due_date') }}"
                                       min="{{ date('Y-m-d\TH:i') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Lampiran File <span class="text-gray-400 text-xs">(Opsional)</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors" id="dropZone">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <div class="text-sm text-gray-600">
                                    <label for="file" class="cursor-pointer text-blue-600 hover:text-blue-500 font-medium">
                                        Klik untuk upload
                                    </label>
                                    <span> atau drag file ke sini</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">PDF, DOC, PPT, XLS, gambar (max 10MB)</p>
                                <input id="file" name="file" type="file" class="hidden" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png">
                            </div>
                            
                            <!-- File Preview -->
                            <div id="filePreview" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900" id="fileName"></p>
                                            <p class="text-xs text-blue-600" id="fileSize"></p>
                                        </div>
                                    </div>
                                    <button type="button" class="text-blue-600 hover:text-blue-800" id="removeFile">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Tugaskan Kepada <span class="text-red-500">*</span>
                            </label>
                            <div class="border border-gray-300 rounded-md">
                                <!-- Select All -->
                                <div class="p-3 border-b border-gray-200 bg-gray-50">
                                    <label class="flex items-center cursor-pointer">
                                        <input id="select_all_students" 
                                               type="checkbox" 
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm font-medium text-gray-900">Pilih Semua Mahasiswa</span>
                                        <span class="ml-2 text-xs text-gray-500" id="totalStudents">({{ count($students) }} mahasiswa)</span>
                                    </label>
                                </div>
                                
                                <!-- Student List -->
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse($students as $student)
                                        <div class="p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50">
                                            <label class="flex items-center cursor-pointer">
                                                <input id="student_{{ $student->id }}" 
                                                       name="student_ids[]" 
                                                       value="{{ $student->id }}" 
                                                       type="checkbox" 
                                                       class="student-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <div class="ml-3 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="p-6 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="text-sm text-gray-500">Belum ada mahasiswa bimbingan</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <!-- Selected Count -->
                                @if(count($students) > 0)
                                    <div class="p-3 bg-blue-50 border-t border-blue-200">
                                        <p class="text-sm text-blue-700">
                                            <span id="selectedCount">0</span> dari {{ count($students) }} mahasiswa dipilih
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('supervisor.tasks.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50"
                                id="submitBtn">
                            <span id="submitText">Simpan Tugas</span>
                            <svg class="animate-spin -mr-1 ml-2 h-4 w-4 text-white hidden" id="submitSpinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.drag-over {
    border-color: #3B82F6 !important;
    background-color: #EFF6FF !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all students functionality
    const selectAllCheckbox = document.getElementById('select_all_students');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (selectedCount) {
            selectedCount.textContent = checkedCount;
        }
    }
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(studentCheckboxes).every(cb => !cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            }
            
            updateSelectedCount();
        });
    });

    // File upload functionality
    const fileInput = document.getElementById('file');
    const dropZone = document.getElementById('dropZone');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFile');
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function showFilePreview(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        filePreview.classList.remove('hidden');
    }
    
    function hideFilePreview() {
        filePreview.classList.add('hidden');
        fileInput.value = '';
    }
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            showFilePreview(this.files[0]);
        }
    });
    
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', hideFilePreview);
    }
    
    // Drag and drop functionality
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
        dropZone.classList.add('drag-over');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('drag-over');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            showFilePreview(files[0]);
        }
    }

    // Form submission with loading state
    const form = document.getElementById('taskForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.textContent = 'Menyimpan...';
        submitSpinner.classList.remove('hidden');
    });

    // Initialize selected count
    updateSelectedCount();
});
</script>
@endsection