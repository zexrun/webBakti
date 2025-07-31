@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">📋 Detail Laporan Logbook</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logbook as $entry)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($entry->activity_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                {{ $entry->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($entry->is_verified)
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-200 text-green-900">
                                        ✅ Sudah Dilihat
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-200 text-yellow-900 animate-pulse">
                                        ⏳ Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('student.logbooks.show', $entry->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                    🔍 Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-sm text-gray-500">
                                Anda belum membuat logbook apapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logbook instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-6">
                {{ $logbook->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
