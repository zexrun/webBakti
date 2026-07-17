@extends('layouts.app')

@section('title', 'Dashboard Penilaian Submission')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Penilaian</h1>
                    <p class="text-gray-600 mt-1">Kelola dan nilai semua submission dari mahasiswa bimbingan</p>
                </div>
                <a href="{{ route('supervisor.tasks.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 alert-success">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Submission</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalSubmissions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sudah Dinilai</p>
                        <p class="text-2xl font-bold text-green-600">{{ $gradedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Nilai</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('supervisor.submissions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Nilai</option>
                            <option value="graded" {{ $status === 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                        </select>
                    </div>

                    <!-- Task Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tugas</label>
                        <select name="task_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tugas</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}" {{ $taskId == $task->id ? 'selected' : '' }}>
                                    {{ $task->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa</label>
                        <select name="student_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Mahasiswa</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="updated_at" {{ $sortBy === 'updated_at' ? 'selected' : '' }}>Terakhir Diupdate</option>
                            <option value="grade" {{ $sortBy === 'grade' ? 'selected' : '' }}>Nilai</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 justify-end">
                    <a href="{{ route('supervisor.submissions.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Reset
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mahasiswa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tugas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Disubmit
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Student Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold text-blue-600">
                                                    {{ substr($submission->student->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $submission->student->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $submission->student->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Task -->
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $submission->task->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $submission->task->type === 'harian' ? 'Harian' : 'Akhir' }}</p>
                                    </td>

                                    <!-- Submitted Date -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $submission->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $submission->created_at->format('H:i') }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @if($submission->grade)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Dinilai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Grade -->
                                    <td class="px-6 py-4">
                                        @if($submission->grade)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100">
                                                <span class="text-sm font-semibold text-blue-600">{{ $submission->grade }}</span>
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('supervisor.submissions.edit', $submission) }}"
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                                Nilai
                                            </a>
                                            <a href="{{ route('supervisor.tasks.show', $submission->task) }}"
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-500 transition-colors">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        {{ $submissions->links('pagination::bootstrap-4') }}
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan <span class="font-medium">{{ $submissions->firstItem() }}</span> ke
                                <span class="font-medium">{{ $submissions->lastItem() }}</span> dari
                                <span class="font-medium">{{ $submissions->total() }}</span> hasil
                            </p>
                        </div>
                        <div>
                            {{ $submissions->links() }}
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada submission</h3>
                    <p class="text-gray-500">Belum ada submission yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success message after 5 seconds
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-10px)';
            successAlert.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                successAlert.remove();
            }, 300);
        }, 5000);
    }
});
</script>
@endsection
