@extends('layouts.app')
@section('title', 'Edit Penilaian Akhir')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold ...">Edit Penilaian Akhir untuk {{ $student->user->name }}</h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('supervisor.students.assessment.update', $student->id) }}" method="POST">
            @csrf
            @method('PATCH') {{-- Gunakan method PATCH untuk update --}}
            <div class="mb-4">
                <label for="final_grade" class="block ...">Nilai Akhir</label>
                <input type="text" name="final_grade" id="final_grade" value="{{ old('final_grade', $assessment->final_grade) }}" required class="mt-1 block w-full ...">
            </div>
            <div class="mb-4">
                <label for="overall_comments" class="block ...">Komentar Keseluruhan</label>
                <textarea name="overall_comments" id="overall_comments" rows="8" required class="mt-1 block w-full ...">{{ old('overall_comments', $assessment->overall_comments) }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex ...">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection