@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Pengumuman Baru</h1>
        <p class="text-gray-600">Buat pengumuman untuk semua pengguna sistem</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">Error:</h3>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.announcements.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf

        <!-- Title -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Pengumuman</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Masukkan judul..." required>
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
            <textarea name="content" rows="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Tulis konten pengumuman..." required>{{ old('content') }}</textarea>
            <p class="text-xs text-gray-500 mt-2">Maximum 5000 karakter</p>
        </div>

        <!-- Priority -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
            <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>🟢 Low - Informasi umum</option>
                <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>🟡 Normal - Informasi penting</option>
                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 High - Sangat penting</option>
                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>🔴 Urgent - Sangat mendesak</option>
            </select>
        </div>

        <!-- Publish Now -->
        <div class="mb-6 flex items-center">
            <input type="checkbox" name="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }} class="rounded">
            <label class="ml-3 text-sm font-medium text-gray-700">Publikasikan sekarang</label>
        </div>

        <!-- Preview -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
            <div class="p-3 bg-white rounded border border-gray-200">
                <p class="text-sm font-semibold text-gray-900" id="previewTitle">Judul Pengumuman</p>
                <p class="text-sm text-gray-600 mt-3 whitespace-pre-wrap" id="previewContent">Isi konten pengumuman akan muncul di sini...</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Buat Pengumuman
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="title"]');
    const contentInput = document.querySelector('textarea[name="content"]');
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');

    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Judul Pengumuman';
    });

    contentInput.addEventListener('input', function() {
        previewContent.textContent = this.value || 'Isi konten pengumuman akan muncul di sini...';
    });
});
</script>
@endpush
@endsection
