@extends('layouts.app')

@section('title', 'Tugas')

@section('content')

<div class="base-div">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">📋 Daftar Tugas Anda</h2>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700 text-left">
                <tr>
                    <th class="px-6 py-4 font-semibold">Judul Tugas</th>
                    <th class="px-6 py-4 font-semibold">Tipe</th>
                    <th class="px-6 py-4 font-semibold">Diberikan Oleh</th>
                    <th class="px-6 py-4 font-semibold">Tenggat Waktu</th>
                    <th class="px-6 py-4 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ $task->title }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-blue-600 rounded">
                                {{ ucfirst($task->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $task->supervisor->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('student.tasks.show', $task->id) }}"
                               class="inline-flex items-center text-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition">
                                📄 Lihat Detail & Submit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada tugas yang diberikan kepada Anda.</td>
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
