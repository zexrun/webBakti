@extends('layouts.app')

@section('title', 'Tugas')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">
    {{-- Detail Tugas --}}
    <div class="bg-white shadow-md rounded-lg mb-6 border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-2xl font-bold text-gray-800">{{ $task->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">📌 Diberikan oleh: <span class="font-medium">{{ $task->supervisor->user->name }}</span></p>
        </div>
        <div class="px-6 py-4">
            <p class="text-gray-700 mb-4">{{ $task->description }}</p>
            @if($task->file_path)
                <a href="{{ asset('storage/' . $task->file_path) }}"
                   target="_blank"
                   class="inline-block text-sm bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 transition">
                    📎 Unduh Lampiran
                </a>
            @endif
        </div>
    </div>

    {{-- Submission --}}
    <div class="bg-white shadow-md rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h4 class="text-xl font-semibold text-gray-800">📝 Submission Anda</h4>
        </div>
        <div class="px-6 py-4">
            @if($submission)
                <div class="space-y-3">
                    <p class="text-green-600 font-semibold">✅ Anda telah mengumpulkan tugas ini pada {{ $submission->created_at->format('d M Y') }}.</p>
                    
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Laporan Teks:</p>
                        <div class="bg-gray-50 border border-gray-200 p-3 rounded text-gray-700 whitespace-pre-line">
                            {{ $submission->content ?? '-' }}
                        </div>
                    </div>

                    @if($submission->file_path)
                        <p>
                            <span class="font-medium text-sm text-gray-500">File Terlampir:</span> 
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                📄 Lihat File
                            </a>
                        </p>
                    @endif

                    <p><strong>Nilai:</strong> {{ $submission->grade ?? 'Belum dinilai' }}</p>
                    <p><strong>Komentar Pembimbing:</strong> {{ $submission->comments ?? 'Belum ada komentar' }}</p>
                </div>
            @else
                {{-- Form Submission --}}
                <form action="{{ route('student.tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Laporan Teks (jika ada)</label>
                        <textarea name="content" id="content" rows="5" class="w-full px-3 py-2 border rounded-md border-gray-300 focus:outline-none focus:ring focus:ring-blue-200"></textarea>
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Unggah File (pdf, docx, zip, dll)</label>
                        <input type="file" name="file" id="file" class="w-full text-sm file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded file:border-0 file:cursor-pointer hover:file:bg-blue-700">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition">
                        🚀 Kumpulkan Tugas
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('student.tasks.index') }}"
           class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded transition">
            ⬅️ Kembali ke Daftar Tugas
        </a>
    </div>
</div>

@endsection
