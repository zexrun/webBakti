@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Pengumuman</h1>
        <p class="text-gray-600">Ubah konten pengumuman</p>
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

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Pengumuman</label>
            <input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" required>
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
            <textarea name="content" rows="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" required>{{ old('content', $announcement->content) }}</textarea>
        </div>

        <!-- Priority -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
            <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                <option value="low" {{ old('priority', $announcement->priority) === 'low' ? 'selected' : '' }}>🟢 Low</option>
                <option value="normal" {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>🟡 Normal</option>
                <option value="high" {{ old('priority', $announcement->priority) === 'high' ? 'selected' : '' }}>🟠 High</option>
                <option value="urgent" {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
            </select>
        </div>

        <!-- Target Roles -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Tampilkan ke Role</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="target_roles[]" value="admin" {{ in_array('admin', old('target_roles', $announcement->target_roles ?? [])) ? 'checked' : '' }} class="rounded">
                    <span class="ml-3 text-sm text-gray-700">👨‍💼 Admin</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="target_roles[]" value="supervisor" {{ in_array('supervisor', old('target_roles', $announcement->target_roles ?? [])) ? 'checked' : '' }} class="rounded">
                    <span class="ml-3 text-sm text-gray-700">👨‍🏫 Pembimbing (Supervisor)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="target_roles[]" value="student" {{ in_array('student', old('target_roles', $announcement->target_roles ?? [])) ? 'checked' : '' }} class="rounded">
                    <span class="ml-3 text-sm text-gray-700">👨‍🎓 Mahasiswa (Student)</span>
                </label>
            </div>
            @error('target_roles')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status Info -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            @if($announcement->published_at)
                <p class="text-sm text-blue-800">
                    <strong>Status:</strong> Dipublikasikan pada {{ $announcement->published_at->format('d M Y H:i') }}
                </p>
            @else
                <p class="text-sm text-blue-800">
                    <strong>Status:</strong> Draft (belum dipublikasikan)
                </p>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
