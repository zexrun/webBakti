@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Pesan Baru</h1>
        <p class="text-gray-600">Kirim pesan ke pengguna lain</p>
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

    <form action="{{ route('messages.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf

        <!-- Recipient -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Penerima</label>
            <select name="recipient_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" required>
                <option value="">Pilih penerima...</option>
                @foreach($recipients as $user)
                    <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Subject -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
            <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Masukkan subjek..." required>
        </div>

        <!-- Body -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
            <textarea name="body" rows="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Tulis pesan anda..." required>{{ old('body') }}</textarea>
            <p class="text-xs text-gray-500 mt-2">Maximum 5000 karakter</p>
        </div>

        <!-- Preview -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
            <div class="p-3 bg-white rounded border border-gray-200">
                <p class="text-sm font-semibold text-gray-900" id="previewSubject">Subjek pesan</p>
                <p class="text-sm text-gray-600 mt-3" id="previewBody">Isi pesan akan muncul di sini...</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Kirim Pesan
            </button>
            <a href="{{ route('messages.inbox') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subjectInput = document.querySelector('input[name="subject"]');
    const bodyInput = document.querySelector('textarea[name="body"]');
    const previewSubject = document.getElementById('previewSubject');
    const previewBody = document.getElementById('previewBody');

    subjectInput.addEventListener('input', function() {
        previewSubject.textContent = this.value || 'Subjek pesan';
    });

    bodyInput.addEventListener('input', function() {
        previewBody.textContent = this.value || 'Isi pesan akan muncul di sini...';
    });
});
</script>
@endpush
@endsection
