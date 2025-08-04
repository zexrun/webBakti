@extends('layouts.app')
@section('title', 'Profil Pengguna')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Profil Pengguna</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap akun Anda</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ url()->previous() }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="text-center">
                        <!-- Avatar -->
                        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        
                        <!-- Name & Role -->
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if ($user->role == 'admin')
                                bg-red-100 text-red-800
                            @elseif ($user->role == 'supervisor')
                                bg-blue-100 text-blue-800
                            @elseif ($user->role == 'student')
                                bg-green-100 text-green-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            @switch($user->role)
                                @case('supervisor') Pembimbing @break
                                @case('student') Mahasiswa @break
                                @case('admin') Administrator @break
                                @default {{ ucfirst($user->role) }}
                            @endswitch
                        </span>
                        
                        <!-- Username -->
                        @if($user->username)
                            <p class="text-gray-600 mt-2">{{ $user->username }}</p>
                        @endif
                    </div>
                </div>

                <!-- Account Status -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Akun</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Email</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($user->email_verified_at)
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Bergabung</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Detail</h3>
                    </div>
                    
                    <div class="p-6">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Informasi Dasar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                        {{ $user->email }}
                                    </div>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->username ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $user->username ?? 'Belum diisi' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Specific Information -->
                        @if($user->role === 'student' && $user->student)
                            <div class="border-t border-gray-200 pt-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Informasi Mahasiswa</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->student->nim ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->student->nim ?? 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->student->semester ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->student->semester ? 'Semester ' . $user->student->semester : 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Universitas</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->student->universitas ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->student->universitas ?? 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->student->program_studi ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->student->program_studi ?? 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Direktorat</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                            {{ $user->student->direktorat ?? '-' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->student->supervisor?->user?->name ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->student->supervisor?->user?->name ?? 'Belum ditugaskan' }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Periode Magang -->
                                @php
                                    $periodeMulai = $user->student->periode_mulai;
                                    $periodeSelesai = $user->student->periode_selesai;
                                @endphp
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Magang</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $periodeMulai && $periodeSelesai ? 'text-gray-900' : 'text-gray-400' }}">
                                        @if($periodeMulai && $periodeSelesai)
                                            {{ \Carbon\Carbon::parse($periodeMulai)->translatedFormat('d F Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($periodeSelesai)->translatedFormat('d F Y') }}
                                        @else
                                            Periode belum ditentukan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($user->role === 'supervisor' && $user->supervisor)
                            <div class="border-t border-gray-200 pt-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Informasi Pembimbing</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->supervisor->nip ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->supervisor->nip ?? 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm {{ $user->supervisor->jabatan ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $user->supervisor->jabatan ?? 'Belum diisi' }}
                                        </div>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Direktorat</label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                            {{ $user->supervisor->direktorat ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
