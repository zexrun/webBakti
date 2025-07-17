@extends('layouts.student') {{-- Pastikan Anda punya layout untuk student --}}
@section('title', 'Buat Laporan Harian')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Buat Laporan Harian Baru</h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('student.daily-reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="activity_date" class="block text-sm font-medium text-gray-700">Tanggal Kegiatan</label>
                        <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="feeling" class="block text-sm font-medium text-gray-700">Bagaimana perasaanmu hari ini?</label>
                        <select name="feeling" id="feeling" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Senang">😊 Senang</option>
                            <option value="Biasa Saja">😐 Biasa Saja</option>
                            <option value="Menemukan Kendala">😥 Menemukan Kendala</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Kegiatan</label>
                    <textarea name="description" id="description" rows="8" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Lampirkan Foto (Opsional)</label>
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection