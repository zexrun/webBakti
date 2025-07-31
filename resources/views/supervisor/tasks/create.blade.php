@extends('layouts.app')
@section('title', 'Buat Tugas Baru')

@section('content')
    <div class="base-div">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Tugas Baru</h2>
                <p class="text-gray-600 mb-6">Isi detail di bawah ini untuk memberikan tugas baru kepada mahasiswa bimbingan.</p>

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                        <p class="font-bold">Terjadi Kesalahan</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('supervisor.tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" id="description" rows="5" required
                                      class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Tugas</label>
                                <select name="type" id="type" required
                                        class="block w-full px-4 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="harian">Laporan Harian</option>
                                    <option value="akhir">Laporan Akhir</option>
                                </select>
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Tenggat Waktu (Opsional)</label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                       class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Lampirkan File (Opsional)</label>
                            <input type="file" name="file" id="file"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tugaskan Kepada</label>
                            <div class="border border-gray-200 rounded-md p-4 space-y-3">
                                <div class="relative flex items-start">
                                    <div class="flex h-6 items-center">
                                        <input id="select_all_students" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="select_all_students" class="font-bold text-gray-900">Pilih Semua Mahasiswa</label>
                                    </div>
                                </div>
                                <hr>
                                @forelse($students as $student)
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="student_{{ $student->id }}" name="student_ids[]" value="{{ $student->id }}" type="checkbox" class="student-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="student_{{ $student->id }}" class="font-medium text-gray-700">{{ $student->user->name }}</label>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Anda belum memiliki mahasiswa bimbingan.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <a href="{{ route('supervisor.tasks.index') }}" class="inline-block bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300 transition">Batal</a>
                        <button type="submit" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">
                            Simpan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // JavaScript untuk 'Pilih Semua'
    document.getElementById('select_all_students').addEventListener('change', function(e) {
        document.querySelectorAll('.student-checkbox').forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });
</script>
@endsection