@extends('layouts.app')
@section('title', 'Daftar Penugasan')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Penugasan per Mahasiswa</h2>
            <a href="{{ route('supervisor.tasks.create') }}"
               class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition">
               Buat Tugas Baru
            </a>
        </div>

        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Loop utama sekarang berdasarkan mahasiswa, bukan tugas --}}
        <div class="space-y-6">
            @forelse($students as $student)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    {{-- Header Kartu Mahasiswa --}}
                    <div class="bg-gray-50 p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $student->user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $student->nim ?? 'NIM belum diisi' }}</p>
                    </div>

                    {{-- Isi Kartu (Tabel Tugas) --}}
                    <div class="overflow-x-auto">
                        @if($student->tasks->isNotEmpty())
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600">
                                    <tr>
                                        <th class="px-6 py-3">Judul Tugas</th>
                                        <th class="px-6 py-3">Tipe</th>
                                        <th class="px-6 py-3">Tenggat Waktu</th>
                                        <th class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    {{-- Loop kedua untuk menampilkan tugas dari mahasiswa tersebut --}}
                                    @foreach($student->tasks as $task)
                                        <tr class="border-t border-gray-100">
                                            <td class="px-6 py-4 font-medium">{{ $task->title }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 text-xs rounded-full">{{ ucfirst($task->type) }}</span>
                                            </td>
                                            <td class="px-6 py-4">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-gray-500 italic py-6">Belum ada tugas yang diberikan untuk mahasiswa ini.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                    <p>Anda belum memiliki mahasiswa bimbingan.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginasi untuk Mahasiswa --}}
        <div class="mt-8">
            {{ $students->links() }}
        </div>
    </div>

@endsection