@extends('layouts.app')

@section('title', 'Informasi Magang Saya')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil data universitas yang dikirim dari controller (pastikan JSON valid)
        const universityData = {!! json_encode($universities ?? []) !!};
        // Buat list unik & terurut (opsional tapi bagus)
        const uniqueUniversities = [...new Set(universityData)].sort();
        // Format untuk TomSelect
        const universityOptions = uniqueUniversities.map(function(name) {
            return { value: name, text: name };
        });
        // Cek apakah elemen ada sebelum inisialisasi
        const select = document.querySelector('#select-universitas');
        if (select) {
            new TomSelect(select, {
                create: true,
                options: universityOptions,
                placeholder: 'Ketik untuk mencari atau menambah universitas...'
            });
        }

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Informasi Magang Saya</h1>
                    <p class="text-gray-600 mt-1">Kelola informasi dan data magang Anda 🎓</p>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-50 rounded-lg px-4 py-2">
                        <span class="text-sm font-medium text-blue-700">Status: <span class="font-bold">Aktif</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-8 mb-6 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-6">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Data Mahasiswa</h3>
                    <p class="text-gray-600">Informasi pribadi dan akademik</p>
                </div>
            </div>

            <form action="{{ route('student.info.update') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-8">
                    <!-- Dosen Pembimbing Section -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="p-2 rounded-lg bg-gray-500 mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-md font-semibold text-gray-900">Dosen Pembimbing</h4>
                                <p class="text-sm text-gray-600">Pembimbing yang ditugaskan</p>
                            </div>
                        </div>
                        <input
                            type="text"
                            value="{{ $student->supervisor->user->name ?? 'Belum Ditugaskan' }}"
                            class="w-full px-4 py-3 rounded-md border-2 border-gray-200 bg-gray-100 shadow-sm text-gray-700 font-medium"
                            disabled
                            readonly
                        />
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIM -->
                        <div>
                            <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                    </svg>
                                    NIM
                                </div>
                            </label>
                            <input
                                type="text"
                                name="nim"
                                id="nim"
                                value="{{ old('nim', $student->nim) }}"
                                required
                                class="w-full px-4 py-3 rounded-md border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Masukkan NIM"
                            />
                        </div>

                        <!-- Semester -->
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Semester
                                </div>
                            </label>
                            <input
                                type="number"
                                name="semester"
                                id="semester"
                                value="{{ old('semester', $student->semester) }}"
                                required
                                min="1"
                                max="14"
                                class="w-full px-4 py-3 rounded-md border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Semester saat ini"
                            />
                        </div>
                    </div>

                    <!-- Universitas Dropdown -->
                    <div x-data="{
                        open: false,
                        selected: '{{ old('universitas', $student->universitas) }}',
                        search: '',
                        items: @js($universities),
                        get filteredItems() {
                            return this.items.filter(item => item.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }" class="relative">
                        <label for="universitas" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Universitas
                            </div>
                        </label>
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full text-left bg-white border-2 border-gray-200 text-gray-700 px-4 py-3 rounded-md shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 flex justify-between items-center hover:border-gray-300"
                        >
                            <span x-text="selected || 'Pilih Universitas'" class="truncate"></span>
                            <svg
                                class="w-5 h-5 ml-2 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown -->
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-hidden"
                        >
                            <!-- Search input -->
                            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input
                                        type="text"
                                        x-model="search"
                                        placeholder="Cari universitas..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>
                            
                            <!-- Filtered results -->
                            <ul class="text-sm text-gray-700 max-h-48 overflow-y-auto">
                                <template x-for="item in filteredItems" :key="item">
                                    <li>
                                        <button
                                            type="button"
                                            @click="selected = item; open = false; search = ''"
                                            class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors duration-150 flex items-center"
                                            :class="{ 'bg-blue-50 text-blue-700': selected === item }"
                                        >
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span x-text="item"></span>
                                        </button>
                                    </li>
                                </template>
                                
                                <!-- Empty state -->
                                <li x-show="filteredItems.length === 0" class="px-4 py-6 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-gray-400 text-sm">Universitas tidak ditemukan</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Hidden input -->
                        <input type="hidden" name="universitas" :value="selected" />
                    </div>

                    <!-- Program Studi -->
                    <div>
                        <label for="program_studi" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Program Studi
                            </div>
                        </label>
                        <input
                            type="text"
                            name="program_studi"
                            id="program_studi"
                            value="{{ old('program_studi', $student->program_studi) }}"
                            required
                            class="w-full px-4 py-3 rounded-md border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                            placeholder="Contoh: Teknik Informatika"
                        />
                    </div>

                    <!-- Periode Magang -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="p-2 rounded-lg bg-blue-500 mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-md font-semibold text-gray-900">Periode Magang</h4>
                                <p class="text-sm text-gray-600">Tentukan waktu pelaksanaan magang</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Periode Mulai -->
                            <div>
                                <label for="periode_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Tanggal Mulai
                                    </div>
                                </label>
                                <input
                                    type="date"
                                    name="periode_mulai"
                                    id="periode_mulai"
                                    value="{{ old('periode_mulai', $student->periode_mulai) }}"
                                    required
                                    class="w-full px-4 py-3 rounded-md border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                />
                            </div>

                            <!-- Periode Selesai -->
                            <div>
                                <label for="periode_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Tanggal Selesai
                                    </div>
                                </label>
                                <input
                                    type="date"
                                    name="periode_selesai"
                                    id="periode_selesai"
                                    value="{{ old('periode_selesai', $student->periode_selesai) }}"
                                    required
                                    class="w-full px-4 py-3 rounded-md border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button
                            type="submit"
                            class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Certificate Section -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-8 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-6">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sertifikat Magang</h3>
                    <p class="text-gray-600">Status dan unduhan sertifikat kelulusan</p>
                </div>
            </div>

            @if($student->finalAssessment && $student->finalAssessment->certificate_generated_at)
                <!-- Certificate Available -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-green-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold text-green-900">Sertifikat Tersedia!</h4>
                            <p class="text-sm text-green-700">Selamat! Sertifikat kelulusan magang Anda sudah siap diunduh</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-green-600">
                            <p>Dibuat pada: {{ $student->finalAssessment->certificate_generated_at->format('d F Y, H:i') }}</p>
                        </div>
                        <a
                            href="{{ route('student.pdf.certificate.download') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                            target="_blank"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Sertifikat
                        </a>
                    </div>
                </div>
            @else
                <!-- Certificate Not Available -->
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-gray-400 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold text-gray-900">Sertifikat Belum Tersedia</h4>
                            <p class="text-sm text-gray-600">Menunggu proses penilaian akhir dari pembimbing</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
    <div class="flex-1">
        <div class="bg-gray-200 rounded-full h-2">
            @php
                $progress = 0;
                $progressText = 'Belum memulai';
                $progressColor = 'bg-gray-400';
                
                // Cek apakah sudah mengumpulkan proposal
                $hasProposal = $student->documents()->where('type', 'proposal')->exists();
                
                // Cek apakah sudah mengumpulkan laporan akhir
                $hasLaporanAkhir = $student->documents()->where('type', 'laporan_akhir')->exists();
                
                // Cek apakah sudah dinilai (ada final assessment)
                $hasAssessment = $student->finalAssessment && $student->finalAssessment->total_score !== null;
                
                // Cek apakah sertifikat sudah di-generate
                $hasCertificate = $student->finalAssessment && $student->finalAssessment->certificate_generated_at;
                
                if ($hasCertificate) {
                    $progress = 100;
                    $progressText = 'Selesai - Sertifikat tersedia';
                    $progressColor = 'bg-green-500';
                } elseif ($hasAssessment) {
                    $progress = 75;
                    $progressText = 'Sudah dinilai - Menunggu sertifikat';
                    $progressColor = 'bg-blue-500';
                } elseif ($hasLaporanAkhir) {
                    $progress = 50;
                    $progressText = 'Laporan akhir sudah dikumpulkan';
                    $progressColor = 'bg-yellow-500';
                } elseif ($hasProposal) {
                    $progress = 25;
                    $progressText = 'Proposal sudah dikumpulkan';
                    $progressColor = 'bg-orange-500';
                } else {
                    $progress = 0;
                    $progressText = 'Belum mengumpulkan proposal';
                    $progressColor = 'bg-gray-400';
                }
            @endphp
            
            <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $progress }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-2">
            <p class="text-xs text-gray-500">Progress: {{ $progressText }}</p>
            
            <!-- Progress indicators -->
            <div class="flex items-center space-x-1">
                <!-- Proposal indicator -->
                <div class="flex items-center">
                    @if($hasProposal)
                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="text-xs text-gray-400 ml-1">P</span>
                </div>
                
                <!-- Laporan Akhir indicator -->
                <div class="flex items-center">
                    @if($hasLaporanAkhir)
                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="text-xs text-gray-400 ml-1">L</span>
                </div>
                
                <!-- Assessment indicator -->
                <div class="flex items-center">
                    @if($hasAssessment)
                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="text-xs text-gray-400 ml-1">N</span>
                </div>
                
                <!-- Certificate indicator -->
                <div class="flex items-center">
                    @if($hasCertificate)
                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="text-xs text-gray-400 ml-1">S</span>
                </div>
            </div>
        </div>
    </div>
    <div class="text-sm text-gray-500">
        <span class="font-medium">{{ $progress }}%</span>
    </div>
</div>

<!-- Progress Legend -->
<div class="mt-4 p-3 bg-gray-50 rounded-lg">
    <p class="text-xs font-medium text-gray-700 mb-2">Keterangan Progress:</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs text-gray-600">
        <div class="flex items-center">
            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
            <span>P: Proposal (25%)</span>
        </div>
        <div class="flex items-center">
            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
            <span>L: Laporan (50%)</span>
        </div>
        <div class="flex items-center">
            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
            <span>N: Dinilai (75%)</span>
        </div>
        <div class="flex items-center">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
            <span>S: Sertifikat (100%)</span>
        </div>
    </div>
</div>
                    
                    <p class="text-sm text-gray-500 mt-4">
                        Sertifikat akan tersedia setelah pembimbing menyelesaikan penilaian akhir dan men-generate sertifikat kelulusan Anda.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection