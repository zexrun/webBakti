@extends('layouts.app')
@section('title', 'Detail Progres Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-3xl font-bold text-gray-800">{{ $student->user->name }}</h2>
        <p class="text-gray-600">{{ $student->nim }} | {{ $student->universitas }}</p>
        <p class="text-gray-600 mt-2">Dibimbing oleh: <strong>{{ $student->supervisor->user->name ?? 'Belum Ditugaskan'
                }}</strong></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Tugas yang Diberikan</h3>
                <ul class="divide-y divide-gray-200">
                    @forelse($student->tasks as $task)
                    <li class="py-3">
                        <a href="{{ route('supervisor.tasks.show', $task->id) }}" class="text-indigo-600 hover:underline">
                            <strong>{{ $task->title }}</strong>
                        </a>
                        (Tipe: {{ $task->type }})
                    </li>
                    @empty
                    <li class="py-3 text-gray-500">Belum ada tugas.</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Dokumen Penting</h3>
                <ul class="divide-y divide-gray-200">
                    @forelse($student->documents as $doc)
                    <li class="py-3 flex justify-between items-center">
                        <span>{{ $doc->document_name }} ({{ ucfirst(str_replace('_', ' ', $doc->type)) }})</span>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" class="text-indigo-600 hover:underline"
                            target="_blank">Download</a>
                    </li>
                    @empty
                    <li class="py-3 text-gray-500">Belum ada dokumen.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-4">Riwayat Logbook</h3>
                    <p>Total Logbook Dibuat: <strong>{{ $student->logbooks->count() }}</strong></p>

                    @php
                    $lastLogbook = $student->logbooks->sortByDesc('activity_date')->first();
                    @endphp

                    @if($lastLogbook)
                    <div class="mt-4 border-t pt-4">
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">📌 Laporan Terbaru:</h4>
                        <p class="text-gray-800">
                            <strong>{{ $lastLogbook->title }}</strong> <br>
                        <p>{{ $lastLogbook->description }}</strong> <br>
                            <span class="text-sm text-gray-600">
                                Tanggal: {{ \Carbon\Carbon::parse($lastLogbook->activity_date)->format('d M Y') }}
                            </span>
                        </p>
                    </div>
                    @else
                    <p class="mt-4 text-sm text-gray-500">Belum ada laporan logbook yang dibuat.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Penilaian Akhir</h3>
                @if($student->finalAssessment)
                <p><strong>Nilai:</strong> {{ $student->finalAssessment->final_grade }}</p>
                <p><strong>Komentar:</strong> {{ $student->finalAssessment->overall_comments }}</p>
                @else
                <p class="text-gray-500">Belum ada penilaian akhir.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection