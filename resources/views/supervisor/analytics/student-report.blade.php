@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Performa Mahasiswa</h1>
                <p class="text-gray-600">{{ $studentInfo['name'] }} ({{ $studentInfo['nim'] }})</p>
            </div>
            <a href="{{ route('supervisor.analytics.dashboard') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nama</p>
                <p class="text-lg font-medium text-gray-900">{{ $studentInfo['name'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">NIM</p>
                <p class="text-lg font-medium text-gray-900">{{ $studentInfo['nim'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Universitas</p>
                <p class="text-lg font-medium text-gray-900">{{ $studentInfo['universitas'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Program Studi</p>
                <p class="text-lg font-medium text-gray-900">{{ $studentInfo['program_studi'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Pembimbing</p>
                <p class="text-lg font-medium text-gray-900">{{ $studentInfo['supervisor'] }}</p>
            </div>
        </div>
    </div>

    <!-- Task & Submission Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Tugas Diberikan</p>
            <p class="text-3xl font-bold text-blue-600">{{ $taskStats['assigned'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Dikumpulkan</p>
            <p class="text-3xl font-bold text-green-600">{{ $taskStats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Dinilai</p>
            <p class="text-3xl font-bold text-purple-600">{{ $taskStats['graded'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-600 mb-1">Completion Rate</p>
            <p class="text-3xl font-bold text-orange-600">{{ $taskStats['completion_rate'] }}%</p>
        </div>
    </div>

    <!-- Grade Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Grade Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistik Nilai</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Rata-rata Nilai</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $gradePerformance['average_grade'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Nilai Tertinggi</span>
                    <span class="text-2xl font-bold text-green-600">{{ $gradePerformance['highest_grade'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Nilai Terendah</span>
                    <span class="text-2xl font-bold text-red-600">{{ $gradePerformance['lowest_grade'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Total Dinilai</span>
                    <span class="text-2xl font-bold text-purple-600">{{ $gradePerformance['total_graded'] }}</span>
                </div>
            </div>
        </div>

        <!-- Attendance Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistik Kehadiran (Bulan Ini)</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm text-gray-600">Hadir</span>
                    <span class="text-2xl font-bold text-green-600">{{ $attendanceStats['present'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <span class="text-sm text-gray-600">Terlambat</span>
                    <span class="text-2xl font-bold text-orange-600">{{ $attendanceStats['late'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-sm text-gray-600">Tidak Hadir</span>
                    <span class="text-2xl font-bold text-red-600">{{ $attendanceStats['absent'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm text-gray-600">Kehadiran Rate</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $attendanceStats['rate'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Timeline -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Timeline Submission</h2>
        <div class="space-y-3">
            @forelse($submissionTimeline as $submission)
                <div class="flex items-start justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $submission['task_title'] }}</p>
                        <p class="text-sm text-gray-600">
                            Submitted: {{ $submission['submitted_at']->format('d M Y H:i') }}
                        </p>
                        @if($submission['feedback'])
                            <p class="text-sm text-gray-700 mt-2 italic">Feedback: {{ $submission['feedback'] }}</p>
                        @endif
                    </div>
                    <div class="text-right ml-4">
                        @if($submission['status'] === 'Graded')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                {{ $submission['grade'] }}
                            </span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-600 py-8">Belum ada submission</p>
            @endforelse
        </div>
    </div>

    <!-- Final Assessment -->
    @if($finalAssessment)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Penilaian Akhir</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Nilai Akhir</p>
                    <p class="text-4xl font-bold text-blue-600">{{ $finalAssessment->final_grade }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Grade</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $finalAssessment->grade }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600 mb-1">Catatan</p>
                    <p class="text-gray-700">{{ $finalAssessment->notes ?? '-' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
