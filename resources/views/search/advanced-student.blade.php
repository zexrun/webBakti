@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pencarian Lanjutan - Tugas</h1>
        <p class="text-gray-600">Filter dan cari tugas anda dengan kriteria spesifik</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('search.advanced') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Task Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas</label>
                <input type="text" name="task_search" value="{{ request('task_search') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Cari judul...">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum Submit</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Sudah Submit</option>
                    <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                </select>
            </div>

            <!-- Due Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deadline Dari</label>
                <input type="date" name="due_date_from" value="{{ request('due_date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <!-- Due Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deadline Sampai</label>
                <input type="date" name="due_date_to" value="{{ request('due_date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                🔍 Cari
            </button>
            <a href="{{ route('search.advanced') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Reset
            </a>
        </div>
    </form>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-600">
        Menampilkan <strong>{{ $tasks->count() }}</strong> dari <strong>{{ $tasks->total() }}</strong> hasil
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tasks as $task)
            @php
                $submission = $task->submissions->firstWhere('student_id', $student->id);
                $status = $submission ? ($submission->grade !== null ? 'Dinilai' : 'Pending') : 'Belum Submit';
                $statusColor = $status === 'Dinilai' ? 'green' : ($status === 'Belum Submit' ? 'red' : 'yellow');
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 flex-1">{{ $task->title }}</h3>
                    <span class="px-2 py-1 rounded text-xs font-medium
                        @if($statusColor === 'green') bg-green-100 text-green-800
                        @elseif($statusColor === 'yellow') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $status }}
                    </span>
                </div>

                @if($task->description)
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($task->description, 100) }}</p>
                @endif

                <div class="space-y-2 text-sm mb-4">
                    <p class="text-gray-600">
                        <strong>Deadline:</strong> {{ $task->due_date->format('d M Y') }}
                    </p>
                    @if($submission && $submission->grade !== null)
                        <p class="text-gray-600">
                            <strong>Nilai:</strong> <span class="text-lg font-bold text-blue-600">{{ $submission->grade }}</span>
                        </p>
                    @endif
                </div>

                <a href="{{ route('student.tasks.show', $task->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                    Lihat Detail →
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-600">Tidak ada hasil</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
