@extends('layouts.app')

@section('title', 'Daftar Mahasiswa Bimbingan')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Daftar Mahasiswa Bimbingan
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Kelola dan pantau progress mahasiswa yang Anda bimbing
                    </p>
                </div>
                
                <!-- Stats Cards -->
                <div class="mt-4 sm:mt-0 flex space-x-4">
                    <div class="bg-white rounded-lg shadow px-4 py-3 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500">Total Mahasiswa</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $students->total() }}</div>
                    </div>
                    @php
                        $completedDocs = $students->filter(function($student) {
                            $hasProposal = $student->documents->where('type', 'proposal')->isNotEmpty();
                            $hasLaporanAkhir = $student->documents->where('type', 'laporan_akhir')->isNotEmpty();
                            return $hasProposal && $hasLaporanAkhir;
                        })->count();
                    @endphp
                    <div class="bg-white rounded-lg shadow px-4 py-3 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500">Dokumen Lengkap</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $completedDocs }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex-1 max-w-lg">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                            placeholder="Cari mahasiswa..."
                            id="searchInput"
                        >
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option>Semua Status</option>
                        <option>Dokumen Lengkap</option>
                        <option>Dokumen Belum Lengkap</option>
                        <option>Sudah Dinilai</option>
                        <option>Belum Dinilai</option>
                    </select>
                    
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Students Grid/Cards -->
        <div class="space-y-4">
            @forelse($students as $student)
                @php
                    $hasProposal = $student->documents->where('type', 'proposal')->isNotEmpty();
                    $hasLaporanAkhir = $student->documents->where('type', 'laporan_akhir')->isNotEmpty();
                    $documentsComplete = $hasProposal && $hasLaporanAkhir;
                    $hasFinalAssessment = $student->finalAssessment !== null;
                    $certificateGenerated = $student->finalAssessment && $student->finalAssessment->certificate_generated_at;
                    
                    // Calculate progress percentage
                    $progress = 0;
                    if ($hasProposal) $progress += 25;
                    if ($hasLaporanAkhir) $progress += 25;
                    if ($hasFinalAssessment) $progress += 25;
                    if ($certificateGenerated) $progress += 25;
                @endphp
                
                <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <!-- Student Info -->
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                        <span class="text-lg font-semibold text-white">
                                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $student->user->name }}
                                        </h3>
                                        @if($certificateGenerated)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Selesai
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            <span class="font-medium">NIM:</span>
                                            <span class="ml-1">{{ $student->nim ?? 'Belum diisi' }}</span>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                            <span class="font-medium">Email:</span>
                                            <span class="ml-1 truncate">{{ $student->user->email }}</span>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span class="font-medium">Universitas:</span>
                                            <span class="ml-1 truncate">{{ $student->universitas ?? 'Belum diisi' }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-700">Progress Magang</span>
                                            <span class="text-gray-600">{{ $progress }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Indicators -->
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <div class="flex items-center text-xs">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $hasProposal ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                            <span class="text-gray-600">Proposal</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $hasLaporanAkhir ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                            <span class="text-gray-600">Laporan Akhir</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $hasFinalAssessment ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                            <span class="text-gray-600">Penilaian</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $certificateGenerated ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                            <span class="text-gray-600">Sertifikat</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex-shrink-0 ml-4">
                                <div x-data="{ open: false }" class="relative">
                                    <button 
                                        @click="open = !open"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    >
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                        Aksi
                                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                                        x-cloak
                                    >
                                        <div class="py-1">
                                            <!-- View Documents -->
                                            <a href="{{ route('supervisor.students.documents', $student->id) }}" 
                                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                <svg class="h-4 w-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            <!-- Assessment Actions -->
                                            @if($hasFinalAssessment)
                                                <a href="{{ route('supervisor.students.assessment.edit', $student->id) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 hover:text-orange-900 transition-colors duration-150">
                                                    <svg class="h-4 w-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit Penilaian
                                                </a>
                                            @else
                                                <a href="{{ route('supervisor.students.assessment.create', $student->id) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 hover:text-orange-900 transition-colors duration-150">
                                                    <svg class="h-4 w-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                    </svg>
                                                    Berikan Penilaian
                                                </a>
                                            @endif
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            <!-- Certificate Actions -->
                                            @if($documentsComplete && $hasFinalAssessment)
                                                @if($certificateGenerated)
                                                    <a href="{{ route('supervisor.pdf.certificate.generate', $student->id) }}" 
                                                       target="_blank"
                                                       class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50 hover:text-green-900 transition-colors duration-150">
                                                        <svg class="h-4 w-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Download Sertifikat
                                                    </a>
                                                @else
                                                    <a href="{{ route('supervisor.pdf.certificate.generate', $student->id) }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50 hover:text-green-900 transition-colors duration-150">
                                                        <svg class="h-4 w-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Generate Sertifikat
                                                    </a>
                                                @endif
                                            @else
                                                <div class="px-4 py-2 text-sm text-gray-400 cursor-not-allowed">
                                                    <div class="flex items-center">
                                                        <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Generate Sertifikat
                                                    </div>
                                                    <div class="text-xs text-red-500 mt-1 ml-7">
                                                        @if(!$documentsComplete)
                                                            Dokumen belum lengkap
                                                        @elseif(!$hasFinalAssessment)
                                                            Belum dinilai
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada mahasiswa bimbingan</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Anda belum memiliki mahasiswa yang dibimbing. Mahasiswa akan muncul di sini setelah admin melakukan plotting pembimbing.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="bg-white rounded-lg shadow px-4 py-3">
                    {{ $students->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Search Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const studentCards = document.querySelectorAll('[data-student-card]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            studentCards.forEach(card => {
                const studentName = card.querySelector('[data-student-name]')?.textContent.toLowerCase() || '';
                const studentNim = card.querySelector('[data-student-nim]')?.textContent.toLowerCase() || '';
                const studentEmail = card.querySelector('[data-student-email]')?.textContent.toLowerCase() || '';
                
                const isVisible = studentName.includes(searchTerm) || 
                                studentNim.includes(searchTerm) || 
                                studentEmail.includes(searchTerm);
                
                card.style.display = isVisible ? 'block' : 'none';
            });
        });
    }
});
</script>

<style>
[x-cloak] {
    display: none !important;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
@endsection