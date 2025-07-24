@extends('layouts.app')
@section('title', 'Plotting')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Plotting Pembimbing Mahasiswa</h2>
        <p class="text-gray-500 mb-6">Pilih dosen pembimbing untuk setiap mahasiswa yang tersedia.</p>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 border border-green-300 relative">
                {{ session('success') }}
                <button type="button" class="absolute top-2 right-2 text-green-700" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700 text-left text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Mahasiswa</th>
                        <th class="px-6 py-3">NIM</th>
                        <th class="px-6 py-3">Universitas</th>
                        <th class="px-6 py-3">Pembimbing Saat Ini</th>
                        <th class="px-6 py-3">Tugaskan Pembimbing</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $student->user->name }}</td>
                            <td class="px-6 py-4">{{ $student->nim }}</td>
                            <td class="px-6 py-4">{{ $student->universitas }}</td>
                            <td class="px-6 py-4">
                                {{ $student->supervisor->user->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.plotting.assign') }}" method="POST" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <select name="supervisor_id" class="rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 w-full sm:w-auto">
                                        <option value="">Pilih Pembimbing</option>
                                        @foreach($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}" @if($student->supervisor_id == $supervisor->id) selected @endif>
                                                {{ $supervisor->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
