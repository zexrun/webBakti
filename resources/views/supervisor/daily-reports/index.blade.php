@extends('layouts.app')
@section('title', 'Laporan Harian Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laporan Harian Mahasiswa Bimbingan</h2>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($report->activity_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report->student->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $report->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($report->is_verified)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Telah Dilihat
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Dilihat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('supervisor.daily-reports.show', $report->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-sm text-gray-500">Belum ada laporan harian yang dikumpulkan oleh mahasiswa bimbingan Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection