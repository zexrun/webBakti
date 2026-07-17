@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kirim Notifikasi Massal</h1>
                <p class="text-gray-600">Kirim pesan/notifikasi ke multiple mahasiswa sekaligus</p>
            </div>
            <a href="{{ route('supervisor.dashboard') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">Terjadi Kesalahan:</h3>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supervisor.bulk.send-notification') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Notification Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Tipe Penerima</label>
                <div class="space-y-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="all" class="rounded" checked>
                        <span class="ml-3 text-gray-700">Semua Mahasiswa</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="selected" class="rounded">
                        <span class="ml-3 text-gray-700">Pilih Mahasiswa Tertentu</span>
                    </label>
                </div>
            </div>

            <!-- Student Selection (hidden by default) -->
            <div id="studentSelection" style="display: none;" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mahasiswa</label>
                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-gray-50">
                    @forelse($students as $student)
                        <label class="flex items-center py-2 cursor-pointer hover:bg-gray-100 px-2 rounded">
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="rounded">
                            <span class="ml-3 text-sm text-gray-700">
                                {{ $student->user->name }} ({{ $student->nim }})
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-600">Belum ada mahasiswa</p>
                    @endforelse
                </div>
            </div>

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Notifikasi</label>
                <input type="text" name="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Contoh: Penting - Update Jadwal Magang" required>
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                <textarea name="message" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" rows="5" placeholder="Ketik pesan anda..." required></textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 1000 karakter</p>
            </div>

            <!-- Priority -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Prioritas</label>
                <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                    <option value="low">🟢 Low - Informasi umum</option>
                    <option value="normal" selected>🟡 Normal - Pemberitahuan standar</option>
                    <option value="high">🟠 High - Perhatian dibutuhkan</option>
                    <option value="urgent">🔴 Urgent - Segera dibalas</option>
                </select>
            </div>

            <!-- Preview -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                <div class="p-3 bg-white rounded border border-gray-200">
                    <p class="text-sm font-semibold text-gray-900" id="previewTitle">Judul Notifikasi</p>
                    <p class="text-sm text-gray-600 mt-2" id="previewMessage">Isi pesan anda akan muncul di sini...</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Kirim Notifikasi
                </button>
                <a href="{{ route('supervisor.dashboard') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const studentSelection = document.getElementById('studentSelection');
    const titleInput = document.querySelector('input[name="title"]');
    const messageInput = document.querySelector('textarea[name="message"]');
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');

    // Toggle student selection
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            studentSelection.style.display = this.value === 'selected' ? 'block' : 'none';
        });
    });

    // Update preview
    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Judul Notifikasi';
    });

    messageInput.addEventListener('input', function() {
        previewMessage.textContent = this.value || 'Isi pesan anda akan muncul di sini...';
    });
});
</script>
@endpush
@endsection
