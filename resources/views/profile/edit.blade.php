@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
                    <p class="text-gray-600 mt-1">Perbarui informasi profil Anda</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Batal
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                <!-- Profile Picture Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Profil</h3>
                        
                        <div class="text-center">
                            <!-- Current Avatar -->
                            <div class="relative inline-block mb-4">
                                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
<!--                                 <div class="absolute bottom-0 right-0 w-6 h-6 bg-blue-600 rounded-full border-2 border-white flex items-center justify-center cursor-pointer hover:bg-blue-700 transition-colors">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div> -->
                            </div>
                            
                            <!-- Upload Button -->
<!--                             <div class="mb-4">
                                <label for="profile_picture" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Upload Foto
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                            </div>
                            
                            <p class="text-xs text-gray-500">
                                JPG, PNG atau GIF. Maksimal 2MB.
                            </p> -->
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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

                <!-- Form Fields -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Profil</h3>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Informasi Dasar</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Username -->
                                    <div class="md:col-span-2">
                                        @if (is_null($user->username))
                                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                                Buat Username Anda
                                            </label>
                                            <input type="text" 
                                                   name="username" 
                                                   id="username" 
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-300 @enderror"
                                                   placeholder="Masukkan username">
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
                                                <input type="text" 
                                                       id="username" 
                                                       value="{{ $user->username }}" 
                                                       readonly 
                                                       disabled
                                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-500 cursor-not-allowed">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Username tidak dapat diubah setelah dibuat.
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="name" 
                                               id="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required 
                                               autofocus
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                                        @error('name')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                                        @error('email')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor Information -->
                            @if ($user->role === 'supervisor' && $user->supervisor)
                                <div class="pt-6 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4">Informasi Pembimbing</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- NIP -->
                                        <div>
                                            <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                                                NIP
                                            </label>
                                            <input type="text" 
                                                   name="nip" 
                                                   id="nip" 
                                                   value="{{ old('nip', $user->supervisor->nip) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nip') border-red-300 @enderror">
                                            @error('nip')
                                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Direktorat -->
                                        
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('profile.show') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Batal
                                    </a>
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview uploaded image
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add image preview logic here
                console.log('Image selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });

    // Auto-calculate periode selesai (3 months after periode mulai)
    document.getElementById('periode_mulai')?.addEventListener('change', function(e) {
        const startDate = new Date(e.target.value);
        if (startDate) {
            const endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + 3);
            
            const periodeSelesaiInput = document.getElementById('periode_selesai');
            if (periodeSelesaiInput && !periodeSelesaiInput.value) {
                periodeSelesaiInput.value = endDate.toISOString().split('T')[0];
            }
        }
    });
</script>
@endpush
@endsection
