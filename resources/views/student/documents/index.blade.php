@extends('layouts.app')
@section('title', 'Manajemen Dokumen')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Manajemen Dokumen Magang</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Upload --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-lg font-semibold mb-4">Upload Dokumen Baru</h3>
        <form action="{{ route('student.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Nama Dokumen</label>
                    <input type="text" name="document_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Tipe Dokumen</label>
                    <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="proposal">Proposal</option>
                        <option value="laporan_akhir">Laporan Akhir</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">File</label>
                    <input type="file" name="file" required class="mt-1 block w-full text-sm">
                </div>
            </div>
            <div class="mt-4 text-right">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Upload
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Dokumen --}}
    <div class="bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Upload</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($documents as $document)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $document->document_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $document->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm space-x-3">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat</a>

                            <form action="{{ route('student.documents.destroy', $document->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-sm text-gray-500">Belum ada dokumen yang di-upload.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
