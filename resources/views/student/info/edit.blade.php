@extends('layouts.student')
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
        });
    </script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Informasi Magang</h2>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" value="{{ $user->username }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" disabled readonly>
            </div>
                
            {{-- Bagian Ubah Password --}}
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mt-6">Ubah Password (Opsional)</h3>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Informasi Magang</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('student.info.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dosen Pembimbing</label>
                    <input type="text" value="{{ $student->supervisor->user->name ?? 'Belum Ditugaskan' }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" disabled readonly>
                </div>

                <hr>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim', $student->nim) }}" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                        <input type="number" name="semester" id="semester" value="{{ old('semester', $student->semester) }}" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div x-data="{
                    open: false,
                    selected: '{{ old('universitas', $student->universitas) }}',
                    search: '',
                    items: @js($universities),
                    get filteredItems() {
                        return this.items.filter(item => item.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }" class="relative">
                
                <label for="universitas" class="block text-sm font-medium text-gray-700 mb-1">Universitas</label>

                <button type="button"
                    @click="open = !open"
                    class="w-full text-left bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm flex justify-between items-center">
                    <span x-text="selected || 'Pilih Universitas'"></span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Search + Items -->
                <div x-show="open" @click.outside="open = false" x-transition class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-64 overflow-auto">
                    
                    <!-- Search input -->
                    <div class="px-3 py-2 border-b border-gray-200">
                        <input type="text" x-model="search" placeholder="Cari universitas..." 
                            class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Filtered result -->
                    <ul class="text-sm text-gray-700 max-h-48 overflow-y-auto">
                        <template x-for="item in filteredItems" :key="item">
                            <li>
                                <button type="button"
                                    @click="selected = item; open = false; search = ''"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                    <span x-text="item"></span>
                                </button>
                            </li>
                        </template>

                        <!-- Empty state -->
                        <li x-show="filteredItems.length === 0" class="px-4 py-2 text-gray-400 italic">Tidak ditemukan</li>
                    </ul>
                </div>

                <!-- Hidden input -->
                <input type="hidden" name="universitas" :value="selected">
            </div>


                <div>
                    <label for="program_studi" class="block text-sm font-medium text-gray-700">Program Studi</label>
                    <input type="text" name="program_studi" id="program_studi" value="{{ old('program_studi', $student->program_studi) }}" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="periode_mulai" class="block text-sm font-medium text-gray-700">Periode Mulai Magang</label>
                        <input type="date" name="periode_mulai" id="periode_mulai" value="{{ old('periode_mulai', $student->periode_mulai) }}" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="periode_selesai" class="block text-sm font-medium text-gray-700">Periode Selesai Magang</label>
                        <input type="date" name="periode_selesai" id="periode_selesai" value="{{ old('periode_selesai', $student->periode_selesai) }}" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
