@extends('layouts.app')
@section('title', 'Tambah User Baru')
@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">➕ Tambah Pengguna Baru</h2>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-md">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="contoh: user@email.com"
                    required
                >
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Peran (Role)</label>
                <select
                    name="role"
                    id="role"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Pembimbing</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    Buat Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
