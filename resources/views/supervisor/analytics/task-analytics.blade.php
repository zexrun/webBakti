@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Analisis Tugas</h1>
                <p class="text-gray-600">{{ $taskInfo['title'] }}</p>
            </div>
            <a href="{{ route('supervisor.analytics.dashboard') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Task Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tugas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Judul</p>
                <p class="text-lg font-medium text-gray-900">{{ $taskInfo['title'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Dibuat</p>
                <p class="text-lg font-medium text-gray-900">{{ $taskInfo['created_at']->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Deadline</p>
                <p class="text-lg font-medium text-gray-900">{{ $taskInfo['due_date']->format('d M Y') }}</p>
            </div>
        </div>
        @if($taskInfo['description'])
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">Deskripsi</p>
                <p class="text-gray-700">{{ $taskInfo['description'] }}</p>
            </div>
        @endif
    </div>

    <!-- Submission Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Total Assigned</p>
            <p class="text-3xl font-bold text-blue-600">{{ $submissionStats['total_assigned'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Submitted</p>
            <p class="text-3xl font-bold text-green-600">{{ $submissionStats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Not Submitted</p>
            <p class="text-3xl font-bold text-red-600">{{ $submissionStats['not_submitted'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Submission Rate</p>
            <p class="text-3xl font-bold text-orange-600">{{ $submissionStats['submission_rate'] }}%</p>
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
                    <span class="text-sm text-gray-600">Tertinggi</span>
                    <span class="text-2xl font-bold text-green-600">{{ $gradeStats['highest'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Terendah</span>
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

    <!-- Grading Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Penilaian</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm text-gray-600">Sudah Dinilai</span>
                    <span class="text-2xl font-bold text-green-600">{{ $submissionStats['graded'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm text-gray-600">Menunggu Penilaian</span>
                    <span class="text-2xl font-bold text-yellow-600">{{ $submissionStats['pending_grade'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mt-4">
                    @php
                        $totalSubmitted = $submissionStats['graded'] + $submissionStats['pending_grade'];
                        $gradedPercentage = $totalSubmitted > 0 ? ($submissionStats['graded'] / $totalSubmitted) * 100 : 0;
                    @endphp
                    <div class="bg-green-600 h-3 rounded-full" style="width: {{ $gradedPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-600 text-center mt-2">{{ round($gradedPercentage, 1) }}% Complete</p>
            </div>
        </div>
    </div>

    <!-- Student Performance Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Performa Mahasiswa</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Mahasiswa</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Waktu Submit</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Nilai</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Feedback</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($studentPerformance as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('supervisor.analytics.student', $submission['student_id']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $submission['student_name'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600">
                                {{ $submission['submitted_at']->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($submission['grade'] !== null)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        {{ $submission['grade'] }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $submission['feedback'] ? substr($submission['feedback'], 0, 50) . '...' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($submission['status'] === 'Graded')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Graded</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-600">Belum ada submission</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Not Submitted Students -->
    @if($notSubmittedStudents->count() > 0)
        <div class="bg-red-50 rounded-lg border border-red-200 p-6">
            <h2 class="text-lg font-semibold text-red-900 mb-4">Mahasiswa Belum Submit</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($notSubmittedStudents as $student)
                    <div class="p-3 bg-white rounded-lg border border-red-200">
                        <p class="font-medium text-gray-900">{{ $student['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $student['nim'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
