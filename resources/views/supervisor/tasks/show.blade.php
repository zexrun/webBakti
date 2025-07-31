@extends('layouts.app')
@section('title', 'Daftar Penugasan')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- CARD TUGAS --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-2">{{ $task->title }}</h2>
        <p class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Tipe:</span> {{ ucfirst($task->type) }} &bull;
            <span class="font-medium">Tenggat:</span> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : 'Tidak ada' }}
        </p>
        <p class="text-sm text-gray-600 mb-4">
            <span class="font-medium">Pembimbing:</span> {{ $task->supervisor->user->name }}
        </p>
        <p class="text-gray-800 mb-4">{{ $task->description }}</p>
        @if($task->file_path)
            <a href="{{ asset('storage/' . $task->file_path) }}" target="_blank"
                class="inline-block bg-gray-200 hover:bg-gray-300 text-sm text-gray-700 px-4 py-2 rounded transition">
                📎 Unduh Lampiran
            </a>
        @endif
    </div>

    {{-- LIST MAHASISWA --}}
    <h3 class="text-xl font-semibold mb-4">Status Submission Mahasiswa</h3>

    @forelse($assignedStudents as $student)
        @php
            $submission = $submissions->get($student->id);
        @endphp

        <div class="bg-white shadow rounded-lg p-5 mb-6">
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-semibold text-lg">{{ $student->user->name }}</h4>
                @if($submission && is_null($submission->grade))
                    <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-3 py-1 rounded">⏳ Menunggu Penilaian</span>
                @elseif($submission)
                    <span class="bg-green-200 text-green-800 text-xs font-semibold px-3 py-1 rounded">✅ Sudah Mengumpulkan</span>
                @else
                    <span class="bg-red-200 text-red-800 text-xs font-semibold px-3 py-1 rounded">❌ Belum Mengumpulkan</span>
                @endif
            </div>

            <hr class="my-3">

            @if($submission)
                <p class="mb-2"><strong>Laporan Teks:</strong> {{ $submission->content ?? '-' }}</p>

                @if($submission->file_path)
                    <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-blue-600 underline text-sm">📄 Unduh File Submission</a>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada file yang diunggah.</p>
                @endif

                <div class="bg-gray-100 mt-4 p-4 rounded">
                    @if($submission->grade)
                        <p><strong>Nilai:</strong> {{ $submission->grade }}</p>
                        <p><strong>Komentar:</strong> {{ $submission->comments ?? '-' }}</p>
                        <button onclick="toggleForm('form-nilai-{{ $submission->id }}')" class="mt-2 text-sm text-blue-600 hover:underline">✏️ Edit Nilai</button>
                    @else
                        <button onclick="toggleForm('form-nilai-{{ $submission->id }}')" class="mt-2 text-sm text-blue-600 hover:underline">📝 Beri Nilai</button>
                    @endif

                    <form id="form-nilai-{{ $submission->id }}" action="{{ route('supervisor.submissions.grade', $submission->id) }}" method="POST" class="mt-4 hidden">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-2 items-center">
                            <select name="grade" required class="w-full md:w-1/6 border rounded px-2 py-1 text-sm">
                                <option value="">-- Nilai --</option>
                                @foreach(['A', 'B', 'C', 'D'] as $grade)
                                    <option value="{{ $grade }}" @if($submission->grade == $grade) selected @endif>{{ $grade }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="comments" placeholder="Komentar (opsional)" value="{{ $submission->comments }}" class="w-full md:w-3/5 border rounded px-3 py-1 text-sm">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-1 rounded">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Menunggu mahasiswa untuk mengumpulkan tugas.</p>
            @endif
        </div>
    @empty
        <div class="text-center text-gray-500 italic mt-4">Tidak ada mahasiswa yang ditugaskan.</div>
    @endforelse

    <a href="{{ route('supervisor.tasks.index') }}" class="inline-block bg-gray-300 hover:bg-gray-400 text-sm text-gray-900 px-4 py-2 mt-6 rounded">← Kembali</a>
</div>

<script>
    function toggleForm(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }
</script>
@endsection
