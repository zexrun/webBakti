@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Daftar Mahasiswa Bimbingan Anda</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Nama Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">NIM</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Universitas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($students as $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $student->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $student->nim ?? 'Belum diisi' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $student->user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $student->universitas ?? 'Belum diisi' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki mahasiswa bimbingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-center">
        {{ $students->links() }}
    </div>
</div>

@endsection
