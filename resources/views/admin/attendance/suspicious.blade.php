@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">⚠️ Kehadiran Mencurigakan</h1>
                <p class="text-gray-600">Kehadiran yang memerlukan review manual karena anomali terdeteksi</p>
            </div>
            <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                ← Kembali
            </a>
        </div>
    </div>

    @if($suspiciousAttendances->isEmpty())
        <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
            <p class="text-green-800 text-lg">✓ Tidak ada kehadiran mencurigakan</p>
            <p class="text-green-600 text-sm mt-2">Semua data kehadiran terlihat normal</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Waktu Check-in</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Alasan Anomali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($suspiciousAttendances as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-xs font-semibold text-red-700">
                                                {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $attendance->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $attendance->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="
                                        px-3 py-1 rounded-full text-xs font-medium
                                        {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $attendance->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="space-y-1">
                                        @if($attendance->location_notes)
                                            <p class="text-red-600">📍 {{ $attendance->location_notes }}</p>
                                        @endif
                                        @if($attendance->latitude && $attendance->longitude)
                                            <p class="text-gray-600 text-xs">
                                                Koordinat: {{ number_format($attendance->latitude, 4) }}, {{ number_format($attendance->longitude, 4) }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button
                                        onclick="openReviewModal({{ $attendance->id }}, '{{ $attendance->user->name }}')"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium transition-colors"
                                    >
                                        Review
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $suspiciousAttendances->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Review Kehadiran Mencurigakan</h3>
            <p class="text-sm text-gray-600 mt-1">Nama: <span id="modalUserName" class="font-medium"></span></p>
        </div>

        <form id="reviewForm" method="POST" action="" class="px-6 py-4">
            @csrf
            @method('POST')

            <!-- Action -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="action" value="approve" class="mr-2" required>
                        <span class="text-sm text-gray-700">✓ Terima - Kehadiran valid</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="action" value="reject" class="mr-2" required>
                        <span class="text-sm text-gray-700">✗ Tolak - Ada anomali</span>
                    </label>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Jelaskan hasil review Anda..."
                    required
                ></textarea>
                @error('notes')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button
                    type="button"
                    onclick="closeReviewModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors"
                >
                    Simpan Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openReviewModal(attendanceId, userName) {
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('reviewForm').action = `/admin/attendance/suspicious/${attendanceId}/review`;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('reviewForm').reset();
}

// Close modal when clicking outside
document.getElementById('reviewModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewModal();
    }
});
</script>
@endpush
@endsection
