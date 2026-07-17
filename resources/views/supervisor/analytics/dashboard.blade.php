@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Analytics & Performance</h1>
        <p class="text-gray-600">Analisis performa mahasiswa dan tugas secara menyeluruh</p>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Total Mahasiswa</p>
            <p class="text-3xl font-bold text-blue-600">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Total Tugas</p>
            <p class="text-3xl font-bold text-purple-600">{{ $totalTasks }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Total Submission</p>
            <p class="text-3xl font-bold text-green-600">{{ $totalSubmissions }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Sudah Dinilai</p>
            <p class="text-3xl font-bold text-orange-600">{{ $gradedSubmissions }}</p>
            <p class="text-xs text-gray-500 mt-2">
                @if($totalSubmissions > 0)
                    {{ round(($gradedSubmissions / $totalSubmissions) * 100, 1) }}% Complete
                @else
                    0% Complete
                @endif
            </p>
        </div>
    </div>

    <!-- Grade Statistics & Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Grade Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistik Nilai</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Rata-rata</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $gradeStats['average'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Nilai Tertinggi</span>
                    <span class="text-2xl font-bold text-green-600">{{ $gradeStats['highest'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Nilai Terendah</span>
                    <span class="text-2xl font-bold text-red-600">{{ $gradeStats['lowest'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Median</span>
                    <span class="text-2xl font-bold text-purple-600">{{ $gradeStats['median'] }}</span>
                </div>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Nilai</h2>
            <div class="space-y-3">
                @foreach($gradeDistribution as $range => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-700">{{ $range }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $maxCount = max($gradeDistribution);
                                $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                            @endphp
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Task Performance -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Performa Tugas</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Judul Tugas</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Dikumpul</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Dinilai</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Completion Rate</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Nilai Rata-rata</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($taskPerformance as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $task['title'] }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $task['submitted'] }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $task['graded'] }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task['completion_rate'] }}%"></div>
                                    </div>
                                    <span class="ml-2 text-xs font-medium text-gray-700">{{ $task['completion_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $task['average_grade'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <a href="{{ route('supervisor.analytics.task', $task['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-600">Belum ada data tugas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Performing Students & At Risk Students -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Mahasiswa Terbaik</h2>
            <div class="space-y-3">
                @forelse($topStudents as $student)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $student['student_name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $student['submission_count'] }} submission</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">{{ $student['average_grade'] }}</p>
                            <a href="{{ route('supervisor.analytics.student', $student['student_id']) }}" class="text-xs text-green-600 hover:text-green-800">
                                Lihat laporan
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- At Risk Students -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Mahasiswa Perlu Perhatian</h2>
            <div class="space-y-3">
                @forelse($atRiskStudents as $student)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $student['student_name'] }}</p>
                            <p class="text-xs text-red-600">{{ $student['reason'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-red-600">{{ $student['average_grade'] }}</p>
                            <a href="{{ route('supervisor.analytics.student', $student['student_id']) }}" class="text-xs text-red-600 hover:text-red-800">
                                Lihat laporan
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 text-center py-4 text-green-600">Semua mahasiswa baik-baik saja! ✓</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Performance Trend Chart Data (for frontend integration) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tren Performa (30 Hari Terakhir)</h2>
        <div id="trend-chart" class="w-full h-64 bg-gray-50 rounded-lg flex items-center justify-center">
            <p class="text-gray-600">Chart akan ditampilkan dengan data berikut:</p>
        </div>
        <div class="hidden" id="trend-data">{{ json_encode($studentTrend) }}</div>
    </div>
</div>

@push('scripts')
<script>
    // Trend data untuk chart library (Chart.js recommended)
    // const trendData = {!! json_encode($studentTrend) !!};
    // Implementasi chart bisa menggunakan Chart.js atau library lainnya
</script>
@endpush
@endsection
