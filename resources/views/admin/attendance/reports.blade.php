@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Kehadiran</h1>
        <p class="text-gray-600">Export dan analisis data kehadiran mahasiswa</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter & Export</h2>

        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="month" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Year Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                        @for ($y = now()->year - 2; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Student Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa</label>
                    <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                        <option value="">Semua Mahasiswa</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->student->nim ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.attendance.reports') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>

        <!-- Export Buttons -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Export Data</h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.attendance.export-csv', ['month' => $month, 'year' => $year, 'user_id' => $userId, 'type' => 'detail']) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 011 1v1a1 1 0 11-2 0v-.5h-10v.5a1 1 0 11-2 0v-1zm3.172-5.829a1 1 0 00-1.414 1.414l2.5 2.5a1 1 0 001.414 0l7.5-7.5a1 1 0 00-1.414-1.414L6 12.586l-1.828-1.829z" clip-rule="evenodd"/>
                    </svg>
                    Export Detail CSV
                </a>
                <a href="{{ route('admin.attendance.export-csv', ['month' => $month, 'year' => $year, 'user_id' => $userId, 'type' => 'summary']) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 011 1v1a1 1 0 11-2 0v-.5h-10v.5a1 1 0 11-2 0v-1zm3.172-5.829a1 1 0 00-1.414 1.414l2.5 2.5a1 1 0 001.414 0l7.5-7.5a1 1 0 00-1.414-1.414L6 12.586l-1.828-1.829z" clip-rule="evenodd"/>
                    </svg>
                    Export Ringkasan CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    @if ($summary->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <!-- Total Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Total Mahasiswa</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary->count() }}</p>
            </div>

            <!-- Average Present -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Rata-rata Hadir</p>
                <p class="text-2xl font-bold text-green-600">{{ round($summary->avg('present'), 1) }}</p>
            </div>

            <!-- Average Late -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Rata-rata Terlambat</p>
                <p class="text-2xl font-bold text-orange-600">{{ round($summary->avg('late'), 1) }}</p>
            </div>

            <!-- Average Absent -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Rata-rata Tidak Hadir</p>
                <p class="text-2xl font-bold text-red-600">{{ round($summary->avg('absent'), 1) }}</p>
            </div>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Mahasiswa</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">NIM</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Pembimbing</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Total Hari</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Hadir</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Terlambat</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Tidak Hadir</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Rata-rata Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($summary as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $item['user']->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item['user']->student->nim ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item['user']->student?->supervisor?->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center font-medium">{{ $item['total_days'] }}</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">{{ $item['present'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-medium">{{ $item['late'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">{{ $item['absent'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center font-medium">{{ $item['avg_hours'] }} jam</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-600">Tidak ada data kehadiran untuk periode yang dipilih</p>
        </div>
    @endif
</div>
@endsection
