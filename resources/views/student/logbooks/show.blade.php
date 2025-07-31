@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 transition hover:shadow-xl duration-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📘 Detail Laporan Logbook</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($logbook->activity_date)->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div>
                @if($logbook->is_verified)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                        ✅ Sudah Dilihat
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 animate-pulse">
                        ⏳ Menunggu
                    </span>
                @endif
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-700">{{ $logbook->title }}</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm mb-6">
            <div>
                <p class="text-gray-500">🕒 Waktu Kegiatan</p>
                <p class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($logbook->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->end_time)->format('H:i') }}
                </p>
            </div>
            <div>
                <p class="text-gray-500">😊 Perasaan</p>
                <p class="font-medium text-gray-800">{{ $logbook->feeling }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-2">📝 Deskripsi Kegiatan</h4>
            <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 rounded-md p-4 border border-gray-200">
                {!! nl2br(e($logbook->description)) !!}
            </div>
        </div>

        @if($logbook->file_path)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-2">📷 Foto Lampiran</h4>
            <img src="{{ asset('storage/' . $logbook->file_path) }}" alt="Lampiran Kegiatan" class="rounded-lg border border-gray-300 shadow-sm">
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('student.logbooks.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium text-sm transition">
                ← Kembali ke Riwayat Laporan
            </a>
        </div>
    </div>
</div>
@endsection
