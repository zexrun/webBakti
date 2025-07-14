@extends('layouts.supervisor')
@section('title', 'List Mahasiswa')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Tugas yang Diberikan</h2>
            <a href="{{ route('supervisor.tasks.create') }}"
               class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition">
               Buat Tugas Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6 relative">
                {{ session('success') }}
                <button type="button" class="absolute top-2 right-3 text-lg" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 text-left uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Judul Tugas</th>
                        <th class="px-6 py-3">Nama Mahasiswa</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Tanggal Dibuat</th>
                        <th class="px-6 py-3">Tenggat Waktu</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $task->title }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($task->students as $student)
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-md text-xs">{{ $student->user->name }}</span>
                                    @empty
                                        <span class="text-gray-500 italic">Belum ada mahasiswa yang ditugaskan.</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block bg-blue-200 text-blue-800 px-3 py-1 text-xs rounded-full">
                                    {{ ucfirst($task->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $task->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('supervisor.tasks.show', $task->id) }}"
                                   class="inline-block bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 transition text-xs">
                                   Lihat Submission
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">
                                Anda belum membuat tugas apapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center">
            {{ $tasks->links() }}
        </div>
    </div>

@endsection
