@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pencarian Lanjutan - Mahasiswa</h1>
        <p class="text-gray-600">Filter dan cari mahasiswa dengan kriteria spesifik</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('search.advanced') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama / NIM</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Cari...">
            </div>

            <!-- Directorate -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Direktorat</label>
                <select name="direktorat" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Semua</option>
                    @foreach($directorates as $dir)
                        <option value="{{ $dir->name }}" {{ request('direktorat') === $dir->name ? 'selected' : '' }}>
                            {{ $dir->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Supervisor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pembimbing</label>
                <select name="supervisor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Semua</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->id }}" {{ request('supervisor_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- University -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Universitas</label>
                <select name="universitas" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Semua</option>
                    @foreach($universities as $uni)
                        <option value="{{ $uni->name }}" {{ request('universitas') === $uni->name ? 'selected' : '' }}>
                            {{ $uni->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Sort -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select name="sort_by" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Terbaru</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nama</option>
                    <option value="nim" {{ request('sort_by') === 'nim' ? 'selected' : '' }}>NIM</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                <select name="sort_dir" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
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
        Menampilkan <strong>{{ $students->count() }}</strong> dari <strong>{{ $students->total() }}</strong> hasil
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">NIM</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Universitas</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Pembimbing</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Direktorat</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $student->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $student->nim }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $student->universitas ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $student->supervisor?->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $student->direktorat ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <a href="{{ route('admin.monitoring.student.show', $student->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
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
        {{ $students->links() }}
    </div>
</div>
@endsection
