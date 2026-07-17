@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pencarian Lanjutan - Tugas</h1>
        <p class="text-gray-600">Filter dan cari tugas dengan kriteria spesifik</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('search.advanced') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Task Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas</label>
                <input type="text" name="task_search" value="{{ request('task_search') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Cari judul...">
            </div>

            <!-- Task Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="task_status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('task_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('task_status') === 'completed' ? 'selected' : '' }}>Completed</option>
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

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Judul Tugas</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Deadline</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Submitted</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Graded</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tasks as $task)
                        @php
                            $submitted = $task->submissions->count();
                            $graded = $task->submissions->filter(fn($s) => $s->grade !== null)->count();
                            $status = $submitted === 0 ? 'Pending' : ($graded === $submitted ? 'Complete' : 'Partial');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $task->title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $task->due_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $submitted }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $graded }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($status === 'Complete') bg-green-100 text-green-800
                                    @elseif($status === 'Partial') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                                Tidak ada hasil
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
