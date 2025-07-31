@extends('layouts.app')
@section('title', 'Penilaian Akhir')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Penilaian Akhir untuk {{ $student->user->name }}</h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('supervisor.students.assessment.store', $student->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="final_grade" class="block text-sm font-medium text-gray-700">Nilai Akhir (cth: A, Sangat Baik)</label>
                <input type="text" name="final_grade" id="final_grade" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="mb-4">
                <label for="overall_comments" class="block text-sm font-medium text-gray-700">Komentar Keseluruhan</label>
                <textarea name="overall_comments" id="overall_comments" rows="8" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white">
                    Simpan Penilaian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection