@extends('layouts.app')
@section('title', 'Edit Profil')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
                    <p class="text-gray-600 mt-1">Perbarui informasi profil Anda</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium">{{ session("success") }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium">Terdapat kesalahan pada form:</p>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Profile Picture --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Profil</h3>
                        <div class="text-center">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>

                    {{-- Account Info --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Role</span>
                                <span class="font-medium text-gray-900">
                                    @switch($user->role)
                                        @case('supervisor') Pembimbing @break
                                        @case('student') Mahasiswa @break
                                        @case('admin') Administrator @break
                                        @default {{ ucfirst($user->role) }}
                                    @endswitch
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bergabung</span>
                                <span class="font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Basic Information --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            {{-- Username --}}
                            <div>
                                @if (is_null($user->username))
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                    Buat Username Anda
                                </label>
                                <input type="text" name="username" id="username" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-300 @enderror"
                                    placeholder="Masukkan username" />
                                <p class="text-xs text-gray-500 mt-1">
                                    Username hanya bisa dibuat satu kali dan tidak bisa diubah.
                                </p>
                                @error('username')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                @else
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                    Username
                                </label>
                                <div class="relative">
                                    <input type="text" id="username" value="{{ $user->username }}" readonly disabled
                                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-500 cursor-not-allowed" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Username tidak dapat diubah setelah dibuat.
                                </p>
                                @endif
                            </div>

                            {{-- Name and Email --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" />
                                    @error('name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror" />
                                    @error('email')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Password Section --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Ubah Password</h3>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password"
                                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror"
                                            placeholder="Masukkan password baru" />
                                        <button type="button" onclick="togglePassword('password')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Konfirmasi Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Konfirmasi password baru" />
                                        <button type="button" onclick="togglePassword('password_confirmation')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <p class="text-sm font-medium text-blue-900 mb-2">Syarat Password:</p>
                                <ul class="text-xs text-blue-800 space-y-1">
                                    <li class="flex items-center">
                                        <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Minimal 8 karakter
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Mengandung huruf besar dan kecil
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Mengandung angka dan karakter khusus
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Supervisor Information --}}
                    @if ($user->role === 'supervisor' && $user->supervisor)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Pembimbing</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                                    <input type="text" name="nip" id="nip" value="{{ old('nip', $user->supervisor->nip) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nip') border-red-300 @enderror" />
                                    @error('nip')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Submit Buttons --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('profile.show') }}" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }
</script>
@endpush
@endsection
