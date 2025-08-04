@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="min-h-screen bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Laporan Logbook</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap kegiatan magang 📘</p>
                    <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($logbook->activity_date)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Status Badge -->
                    @if($logbook->is_verified)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sudah Dilihat
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menunggu Review
                        </span>
                    @endif
                    
                    <!-- Back Button -->
                    <a href="{{ route('student.logbooks.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Title Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-blue-100 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Judul Kegiatan</h3>
                            <p class="text-gray-600">Aktivitas yang dilakukan</p>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 leading-relaxed">{{ $logbook->title }}</h2>
                </div>

                <!-- Description Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-green-100 mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Deskripsi Kegiatan</h4>
                            <p class="text-gray-600">Detail aktivitas yang dilakukan</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-6 border border-gray-200">
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                            {!! nl2br(e($logbook->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Photo Card -->
                @if($logbook->file_path)
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-purple-100 mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Dokumentasi Kegiatan</h4>
                            <p class="text-gray-600">Foto lampiran aktivitas</p>
                        </div>
                    </div>
                    <div class="relative group">
                        <img 
                            src="{{ asset('storage/' . $logbook->file_path) }}" 
                            alt="Dokumentasi {{ $logbook->title }}" 
                            class="w-full rounded-lg border border-gray-300 shadow-sm cursor-pointer transition-transform duration-300 hover:scale-105"
                            onclick="openImageModal(this.src)"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Activity Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Informasi Kegiatan</h4>
                            <p class="text-sm text-gray-600">Detail waktu dan perasaan</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Date -->
                        <div class="flex items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                            <div class="p-2 rounded-lg bg-blue-500 mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Tanggal Kegiatan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($logbook->activity_date)->format('d F Y') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($logbook->activity_date)->format('l') }}</p>
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div class="flex items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100">
                            <div class="p-2 rounded-lg bg-green-500 mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Waktu Kegiatan</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($logbook->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->end_time)->format('H:i') }}
                                </p>
                                @php
                                    $start = \Carbon\Carbon::parse($logbook->start_time);
                                    $end = \Carbon\Carbon::parse($logbook->end_time);
                                    $duration = $start->diff($end);
                                @endphp
                                <p class="text-xs text-gray-500">Durasi: {{ $duration->h }} jam {{ $duration->i }} menit</p>
                            </div>
                        </div>

                        <!-- Feeling -->
                        <div class="flex items-center p-3 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-100">
                            <div class="p-2 rounded-lg bg-yellow-500 mr-3">
                                @php
                                    $feelingIcon = match($logbook->feeling) {
                                        'Senang' => '😊',
                                        'Biasa Saja' => '😐',
                                        'Menemukan Kendala' => '😥',
                                        default => '😊'
                                    };
                                @endphp
                                <span class="text-sm">{{ $feelingIcon }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Perasaan Hari Ini</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $logbook->feeling }}</p>
                                <p class="text-xs text-gray-500">Mood saat beraktivitas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-indigo-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Timeline Aktivitas</h4>
                            <p class="text-sm text-gray-600">Riwayat kegiatan</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Laporan dibuat</p>
                                <p class="text-gray-500">{{ $logbook->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($logbook->updated_at != $logbook->created_at)
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Terakhir diupdate</p>
                                <p class="text-gray-500">{{ $logbook->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($logbook->is_verified)
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Sudah direview</p>
                                <p class="text-gray-500">Oleh pembimbing</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-gray-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Aksi Cepat</h4>
                            <p class="text-sm text-gray-600">Tindakan yang tersedia</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('student.logbooks.index') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Lihat Semua Laporan
                        </a>
                        
                        <button onclick="printReport()" 
                                class="flex items-center w-full p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak Laporan
                        </button>
                        
                        <button onclick="shareReport()" 
                                class="flex items-center w-full p-3 text-sm text-gray-700 rounded-lg hover:bg-blue-50 border border-gray-200 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            Bagikan Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <img id="modalImage" class="max-w-full max-h-full object-contain rounded-lg" alt="Full size image">
    </div>
</div>

<script>
// Image Modal Functions
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Print Function
function printReport() {
    window.print();
}

// Share Function
function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $logbook->title }}',
            text: 'Laporan kegiatan magang: {{ $logbook->title }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showSuccessMessage('Link laporan berhasil disalin ke clipboard!');
        });
    }
}

// Success Message Function
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${message}
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        alertDiv.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(alertDiv);
        }, 300);
    }, 3000);
}

// Close modal when clicking outside or pressing Escape
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Print styles
const printStyles = `
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);

// Add print-area class to main content
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.max-w-6xl');
    if (mainContent) {
        mainContent.classList.add('print-area');
    }
});
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-area {
        box-shadow: none !important;
        border: none !important;
    }
    
    .bg-gradient-to-r {
        background: #f8f9fa !important;
    }
}
</style>
@endsection