@extends('layouts.app')

@section('title', 'Dokumen Mahasiswa - ' . $student->user->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Back Button -->
                    <a href="{{ route('supervisor.students.list.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    
                    <!-- Student Info -->
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-white">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Dokumen Mahasiswa
                            </h1>
                            <p class="text-sm text-gray-600">
                                {{ $student->user->name }} • {{ $student->nim ?? 'NIM belum diisi' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- View Toggle & Actions -->
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <!-- Document Stats -->
                    <div class="hidden sm:flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>{{ $documents->count() }} Dokumen</span>
                        </div>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center bg-white rounded-lg border border-gray-300 p-1">
                        <button 
                            id="cardViewBtn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200 bg-blue-100 text-blue-700"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Card
                        </button>
                        <button 
                            id="listViewBtn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200 text-gray-600 hover:text-gray-900"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        @if($documents->count() > 0)
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <!-- Search -->
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="searchInput"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                placeholder="Cari dokumen..."
                            >
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex items-center space-x-3">
                        <select id="categoryFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Semua Kategori</option>
                            <option value="proposal">Proposal</option>
                            <option value="laporan_akhir">Laporan Akhir</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        
                        <select id="typeFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Semua Tipe</option>
                            <option value="pdf">PDF</option>
                            <option value="doc">Word</option>
                            <option value="excel">Excel</option>
                            <option value="image">Gambar</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <!-- Documents Content -->
        @if($documents->count() > 0)
            <!-- Card View -->
            <div id="cardView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($documents as $document)
                    @php
                        $fileExtension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        $fileName = basename($document->file_path);
                        $fileSize = '';
                        if(Storage::exists('public/' . $document->file_path)) {
                            $bytes = Storage::size('public/' . $document->file_path);
                            $fileSize = formatBytes($bytes);
                        }
                        
                        $fileIcons = [
                            'pdf' => ['text-red-500', 'bg-red-50', 'PDF'],
                            'doc' => ['text-blue-500', 'bg-blue-50', 'Word'],
                            'docx' => ['text-blue-500', 'bg-blue-50', 'Word'],
                            'xls' => ['text-green-500', 'bg-green-50', 'Excel'],
                            'xlsx' => ['text-green-500', 'bg-green-50', 'Excel'],
                            'ppt' => ['text-orange-500', 'bg-orange-50', 'PowerPoint'],
                            'pptx' => ['text-orange-500', 'bg-orange-50', 'PowerPoint'],
                            'jpg' => ['text-purple-500', 'bg-purple-50', 'Image'],
                            'jpeg' => ['text-purple-500', 'bg-purple-50', 'Image'],
                            'png' => ['text-purple-500', 'bg-purple-50', 'Image'],
                            'gif' => ['text-purple-500', 'bg-purple-50', 'Image'],
                        ];
                        
                        $iconColor = $fileIcons[$fileExtension][0] ?? 'text-gray-500';
                        $bgColor = $fileIcons[$fileExtension][1] ?? 'bg-gray-50';
                        $label = $fileIcons[$fileExtension][2] ?? strtoupper($fileExtension);
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    
                    <div class="document-card bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 overflow-hidden group"
                         data-category="{{ $document->type }}"
                         data-type="{{ $isImage ? 'image' : $fileExtension }}"
                         data-name="{{ strtolower($fileName) }}">
                        
                        <!-- Document Preview -->
                        <div class="relative {{ $bgColor }} p-6 flex items-center justify-center h-32">
                            @if($isImage)
                                <img src="{{ asset('storage/' . $document->file_path) }}" 
                                     alt="Preview" 
                                     class="max-h-full max-w-full object-contain rounded border shadow-sm">
                            @else
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($fileExtension === 'pdf')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        @elseif(in_array($fileExtension, ['doc', 'docx']))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        @endif
                                    </svg>
                                    <span class="inline-block mt-2 px-2 py-1 text-xs font-medium {{ $iconColor }} {{ $bgColor }} rounded-full border">
                                        {{ $label }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white text-gray-700 shadow-sm border">
                                    {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Document Info -->
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 truncate mb-2" title="{{ $fileName }}">
                                {{ $fileName }}
                            </h3>
                            
                            <div class="space-y-1 text-sm text-gray-500">
                                @if($fileSize)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 1.79 4 4 4h8c2.21 0 4-1.79 4-4V7c0-2.21-1.79-4-4-4H8c-2.21 0-4 1.79-4 4z"/>
                                        </svg>
                                        {{ $fileSize }}
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $document->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $document->file_path) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </a>
                                
                                <a href="{{ asset('storage/' . $document->file_path) }}" 
                                   download="{{ $fileName }}" 
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View -->
            <div id="listView" class="hidden bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dokumen
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ukuran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Upload
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($documents as $document)
                                @php
                                    $fileExtension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                                    $fileName = basename($document->file_path);
                                    $fileSize = '';
                                    if(Storage::exists('public/' . $document->file_path)) {
                                        $bytes = Storage::size('public/' . $document->file_path);
                                        $fileSize = formatBytes($bytes);
                                    }
                                    
                                    $fileIcons = [
                                        'pdf' => ['text-red-500', 'PDF'],
                                        'doc' => ['text-blue-500', 'Word'],
                                        'docx' => ['text-blue-500', 'Word'],
                                        'xls' => ['text-green-500', 'Excel'],
                                        'xlsx' => ['text-green-500', 'Excel'],
                                        'ppt' => ['text-orange-500', 'PowerPoint'],
                                        'pptx' => ['text-orange-500', 'PowerPoint'],
                                        'jpg' => ['text-purple-500', 'Image'],
                                        'jpeg' => ['text-purple-500', 'Image'],
                                        'png' => ['text-purple-500', 'Image'],
                                        'gif' => ['text-purple-500', 'Image'],
                                    ];
                                    
                                    $iconColor = $fileIcons[$fileExtension][0] ?? 'text-gray-500';
                                    $label = $fileIcons[$fileExtension][1] ?? strtoupper($fileExtension);
                                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                
                                <tr class="document-row hover:bg-gray-50 transition-colors duration-150"
                                    data-category="{{ $document->category }}"
                                    data-type="{{ $isImage ? 'image' : $fileExtension }}"
                                    data-name="{{ strtolower($fileName) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($isImage)
                                                    <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $document->file_path) }}" alt="">
                                                @else
                                                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="{{ $fileName }}">
                                                    {{ $fileName }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $label }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $fileSize ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                                Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                                               download="{{ $fileName }}" 
                                               class="text-green-600 hover:text-green-900 transition-colors duration-150">
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if(method_exists($documents, 'links'))
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow px-4 py-3">
                        {{ $documents->links() }}
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 3v4a2 2 0 002 2h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada dokumen</h3>
                <p class="text-gray-500 mb-6">
                    Mahasiswa {{ $student->user->name }} belum mengupload dokumen apapun.
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('supervisor.students.list.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Kembali ke Daftar Mahasiswa
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for View Toggle and Filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Toggle
    const cardViewBtn = document.getElementById('cardViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const cardView = document.getElementById('cardView');
    const listView = document.getElementById('listView');
    
    cardViewBtn?.addEventListener('click', function() {
        cardView?.classList.remove('hidden');
        listView?.classList.add('hidden');
        
        cardViewBtn.classList.add('bg-blue-100', 'text-blue-700');
        cardViewBtn.classList.remove('text-gray-600', 'hover:text-gray-900');
        
        listViewBtn.classList.remove('bg-blue-100', 'text-blue-700');
        listViewBtn.classList.add('text-gray-600', 'hover:text-gray-900');
    });
    
    listViewBtn?.addEventListener('click', function() {
        listView?.classList.remove('hidden');
        cardView?.classList.add('hidden');
        
        listViewBtn.classList.add('bg-blue-100', 'text-blue-700');
        listViewBtn.classList.remove('text-gray-600', 'hover:text-gray-900');
        
        cardViewBtn.classList.remove('bg-blue-100', 'text-blue-700');
        cardViewBtn.classList.add('text-gray-600', 'hover:text-gray-900');
    });
    
    // Search and Filter
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    function filterDocuments() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const categoryValue = categoryFilter?.value || '';
        const typeValue = typeFilter?.value || '';
        
        // Filter cards
        const cards = document.querySelectorAll('.document-card');
        cards.forEach(card => {
            const name = card.dataset.name || '';
            const category = card.dataset.category || '';
            const type = card.dataset.type || '';
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryValue;
            const matchesType = !typeValue || type === typeValue;
            
            card.style.display = (matchesSearch && matchesCategory && matchesType) ? 'block' : 'none';
        });
        
        // Filter rows
        const rows = document.querySelectorAll('.document-row');
        rows.forEach(row => {
            const name = row.dataset.name || '';
            const category = row.dataset.category || '';
            const type = row.dataset.type || '';
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryValue;
            const matchesType = !typeValue || type === typeValue;
            
            row.style.display = (matchesSearch && matchesCategory && matchesType) ? 'table-row' : 'none';
        });
    }
    
    searchInput?.addEventListener('input', filterDocuments);
    categoryFilter?.addEventListener('change', filterDocuments);
    typeFilter?.addEventListener('change', filterDocuments);
});
</script>

<style>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}
</style>
@endsection