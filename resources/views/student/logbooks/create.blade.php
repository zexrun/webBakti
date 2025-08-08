@extends('layouts.app')
@section('title', 'Buat Laporan Harian')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">📝 Buat Laporan Harian</h2>

    <div class="bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <form action="{{ route('student.logbooks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="activity_date" class="block text-sm font-medium text-gray-700">Tanggal Kegiatan</label>
                        <input type="date" name="activity_date" id="activity_date"
                            value="{{ old('activity_date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label for="feeling" class="block text-sm font-medium text-gray-700">Perasaan Hari Ini</label>
                        <select name="feeling" id="feeling" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            <option value="">Pilih perasaan</option>
                            <option value="Senang" @selected(old('feeling') == 'Senang')>😊 Senang</option>
                            <option value="Biasa Saja" @selected(old('feeling') == 'Biasa Saja')>😐 Biasa Saja</option>
                            <option value="Menemukan Kendala" @selected(old('feeling') == 'Menemukan Kendala')>😥 Menemukan Kendala</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Kegiatan</label>
                    <textarea name="description" id="description" rows="6" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Lampiran Foto (Opsional)</label>
                    <input type="file" name="photo" id="photo"
                        class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition">
                    💾 Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
