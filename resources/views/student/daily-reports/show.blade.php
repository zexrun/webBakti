@extends('layouts.student')
@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="mt-6 border-t pt-4">
                @if($report->is_verified)
                    <p class="text-green-600 font-semibold">Laporan ini telah dilihat oleh pembimbing.</p>
                @else
                    <p class="text-yellow-600 font-semibold">Laporan ini sedang menunggu untuk dilihat oleh pembimbing.</p>
                @endif
            </div>

            <div>
                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($report->activity_date)->isoFormat('dddd, D MMMM Y') }}</span>
                <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $report->title }}</h2>
            </div>
    
            <hr class="my-4">

            <div>
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-500">Waktu Kegiatan</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Perasaan</p>
                        <p class="font-semibold">{{ $report->feeling }}</p>
                    </div>
                </div>

                <h4 class="font-semibold text-gray-800">Deskripsi Kegiatan:</h4>
                <div class="prose max-w-none mt-2 text-gray-700">
                    {!! nl2br(e($report->description)) !!}
                </div>
            </div>

            @if($report->photo_path)
            <div class="mt-6">
                <h4 class="font-semibold text-gray-800 mb-2">Foto Lampiran:</h4>
                <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Foto Kegiatan" class="rounded-lg max-w-full h-auto">
            </div>
            @endif
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('student.daily-reports.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Kembali ke Riwayat Laporan</a>
    </div>
</div>
@endsection