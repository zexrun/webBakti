@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Import Nilai Massal</h1>
                <p class="text-gray-600">Upload file CSV untuk mengimport nilai multiple submission sekaligus</p>
            </div>
            <a href="{{ route('supervisor.submissions.index') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('errors'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">Error Log:</h3>
            <ul class="text-red-700 space-y-1 text-sm">
                @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload File CSV</h2>

                <form action="{{ route('supervisor.bulk.import-grades') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50 transition" id="dropZone">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-600 font-medium">Drag dan drop file CSV atau klik untuk memilih</p>
                            <p class="text-sm text-gray-500 mt-1">File harus berformat CSV, max 5MB</p>
                            <input type="file" name="file" class="hidden" accept=".csv,.txt" id="fileInput">
                        </div>
                        <p class="text-sm text-gray-600 mt-2" id="fileName">File belum dipilih</p>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        Import Nilai
                    </button>
                </form>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Panduan Format CSV</h2>

            <div class="space-y-4">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Struktur File:</h3>
                    <div class="bg-gray-50 p-3 rounded font-mono text-xs text-gray-700 overflow-x-auto">
                        <p>submission_id,grade,feedback</p>
                        <p>1,85,Bagus</p>
                        <p>2,92,Excellent work</p>
                        <p>3,78,Perlu perbaikan</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Penjelasan Kolom:</h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li><strong>submission_id:</strong> ID submission (wajib)</li>
                        <li><strong>grade:</strong> Nilai 0-100 (wajib)</li>
                        <li><strong>feedback:</strong> Catatan/feedback (opsional)</li>
                    </ul>
                </div>

                <div class="p-3 bg-blue-50 rounded border border-blue-200">
                    <p class="text-sm text-blue-800">
                        <strong>Tips:</strong> Submission ID bisa dilihat di halaman penilaian atau dari detail submission.
                    </p>
                </div>

                <a href="{{ route('supervisor.submissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    → Lihat submission
                </a>
            </div>
        </div>
    </div>

    <!-- Download Template -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-yellow-800">
            <strong>Download template CSV:</strong>
            <code class="bg-yellow-100 px-2 py-1 rounded">submission_id,grade,feedback</code>
        </p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');

    // Click to open file picker
    dropZone.addEventListener('click', () => fileInput.click());

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-gray-100', 'border-gray-400');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-gray-100', 'border-gray-400');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-gray-100', 'border-gray-400');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileName();
        }
    });

    // Handle file selection
    fileInput.addEventListener('change', updateFileName);

    function updateFileName() {
        if (fileInput.files.length > 0) {
            fileName.textContent = '✓ ' + fileInput.files[0].name;
            fileName.classList.remove('text-gray-600');
            fileName.classList.add('text-green-600');
        } else {
            fileName.textContent = 'File belum dipilih';
            fileName.classList.remove('text-green-600');
            fileName.classList.add('text-gray-600');
        }
    }
});
</script>
@endpush
@endsection
