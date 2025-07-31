@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Dokumen Mahasiswa: {{ $student->user->name }}</h2>
        <a href="{{ route('supervisor.students.list.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md shadow">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($documents->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($documents as $document)
                @php
                    $fileExtension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    $fileName = basename($document->file_path);
                    $fileSize = '';
                    if(Storage::exists('public/' . $document->file_path)) {
                        $bytes = Storage::size('public/' . $document->file_path);
                        $fileSize = formatBytes($bytes);
                    }

                    $fileIcons = [
                        'pdf' => ['fa-file-pdf', 'text-red-600', 'PDF'],
                        'doc' => ['fa-file-word', 'text-blue-600', 'Word'],
                        'docx' => ['fa-file-word', 'text-blue-600', 'Word'],
                        'xls' => ['fa-file-excel', 'text-green-600', 'Excel'],
                        'xlsx' => ['fa-file-excel', 'text-green-600', 'Excel'],
                        'ppt' => ['fa-file-powerpoint', 'text-yellow-500', 'PowerPoint'],
                        'pptx' => ['fa-file-powerpoint', 'text-yellow-500', 'PowerPoint'],
                    ];

                    $icon = $fileIcons[$fileExtension][0] ?? 'fa-file';
                    $color = $fileIcons[$fileExtension][1] ?? 'text-gray-400';
                    $label = $fileIcons[$fileExtension][2] ?? strtoupper($fileExtension);
                @endphp

                <div class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition duration-300">
                    <div class="px-4 py-3 border-b border-gray-100 bg-blue-50 rounded-t-lg">
                        <h6 class="text-sm font-medium text-blue-800">
                            <i class="fas fa-file-alt mr-1"></i> {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                        </h6>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-center mb-4">
                            @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ asset('storage/' . $document->file_path) }}" alt="Preview" class="max-h-40 rounded border">
                            @else
                                <i class="fas {{ $icon }} {{ $color }} text-6xl"></i>
                            @endif
                        </div>
                        <div class="text-center mb-2">
                            <span class="inline-block px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded-full">
                                <i class="fas {{ $icon }} mr-1"></i> {{ $label }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Nama:</strong> {{ $fileName }}</p>
                            @if($fileSize)
                                <p><strong>Ukuran:</strong> {{ $fileSize }}</p>
                            @endif
                            <p><strong>Upload:</strong> {{ $document->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 rounded-b-lg flex justify-between text-sm">
                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                            <i class="fas fa-eye mr-1"></i> Lihat
                        </a>
                        <a href="{{ asset('storage/' . $document->file_path) }}" download="{{ $fileName }}" class="text-green-600 hover:underline">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                        @if($fileExtension === 'pdf')
                            <a href="{{ asset('storage/' . $document->file_path) }}#toolbar=1" target="_blank" class="text-indigo-600 hover:underline">
                                <i class="fas fa-external-link-alt mr-1"></i> Buka PDF
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if(method_exists($documents, 'links'))
            <div class="mt-6 flex justify-center">
                {{ $documents->links() }}
            </div>
        @endif

    @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-folder-open text-6xl mb-4"></i>
            <h4 class="text-xl font-semibold">Tidak ada dokumen</h4>
            <p class="mt-2">Mahasiswa belum mengupload dokumen apapun.</p>
        </div>
    @endif
</div>
@endsection
