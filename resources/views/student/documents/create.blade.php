@extends('layouts.app')
@section('title', 'Upload Dokumen')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Upload Dokumen Baru</h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('student.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="document_name" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                <input type="text" name="document_name" id="document_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Tipe Dokumen</inpu>
                <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="proposal">Proposal</option>
                    <option value="laporan_akhir">Laporan Akhir</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">Pilih File</label>
                <input type="file" name="file" id="file" required class="mt-1 block w-full text-sm ...">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 ...">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection