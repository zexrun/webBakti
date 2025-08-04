@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
                    <p class="text-gray-600 mt-1">{{ $user->name }} ({{ $user->email }})</p>
                </div>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Manajemen Pengguna
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit User</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan pada form:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Section -->
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Dasar
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Email
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Peran (Role)
                            <span class="text-red-500">*</span>
                        </label>
                        <select id="role" 
                                name="role" 
                                required 
                                onchange="toggleRoleFields(this.value)"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('role') border-red-300 @enderror">
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                👨‍💼 Administrator
                            </option>
                            <option value="supervisor" {{ old('role', $user->role) === 'supervisor' ? 'selected' : '' }}>
                                👨‍🏫 Pembimbing
                            </option>
                            <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>
                                🎓 Mahasiswa
                            </option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Student Fields -->
            <div id="studentFields" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Data Mahasiswa
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                            <input type="text" 
                                   name="nim" 
                                   id="nim" 
                                   value="{{ old('nim', $user->student?->nim) }}" 
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        <div>
                            <label for="universitas" class="block text-sm font-medium text-gray-700 mb-2">Universitas</label>
                            <input type="text" 
                                   name="universitas" 
                                   id="universitas" 
                                   value="{{ old('universitas', $user->student?->universitas) }}" 
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        <div>
                            <label for="program_studi" class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                            <input type="text" 
                                   name="program_studi" 
                                   id="program_studi" 
                                   value="{{ old('program_studi', $user->student?->program_studi) }}" 
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                            <input type="text" 
                                   name="semester" 
                                   id="semester" 
                                   value="{{ old('semester', $user->student?->semester) }}" 
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supervisor Fields -->
            <div id="supervisorFields" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Data Pembimbing
                    </h2>
                </div>

                <div class="p-6">
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <input type="text" 
                               name="nip" 
                               id="nip" 
                               value="{{ old('nip', $user->supervisor?->nip) }}" 
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                </div>
            </div>

            <!-- Direktorat & Jabatan Field (untuk Student & Supervisor) -->
            <div id="direktoratField" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Direktorat & Jabatan
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Direktorat -->
                        <div>
                            <label for="direktorat" class="block text-sm font-medium text-gray-700 mb-2">Direktorat</label>
                            <select name="direktorat" 
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">Pilih Direktorat</option>
                                @foreach ($directorates as $dir)
                                    <option value="{{ $dir->id }}" {{ (($user->supervisor && $user->supervisor->direktorat === $dir->name) || ($user->student && $user->student->direktorat === $dir->name)) ? 'selected' : '' }}>
                                        {{ $dir->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jabatan (hanya untuk Supervisor) -->
                        <div id="jabatanField" style="display: none;">
                            <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                            <select name="jabatan" 
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">Pilih Jabatan</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ ($user->supervisor && $user->supervisor->jabatan === $pos->name) ? 'selected' : '' }}>
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Ubah Password
                    </h2>
                    <p class="text-sm text-yellow-700 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleFields(role) {
    const studentFields = document.getElementById('studentFields');
    const supervisorFields = document.getElementById('supervisorFields');
    const direktoratField = document.getElementById('direktoratField');
    const jabatanField = document.getElementById('jabatanField');
    
    // Hide all fields first
    studentFields.style.display = 'none';
    supervisorFields.style.display = 'none';
    direktoratField.style.display = 'none';
    jabatanField.style.display = 'none';
    
    // Show relevant fields based on role
    if (role === 'student') {
        studentFields.style.display = 'block';
        direktoratField.style.display = 'block';
        // Jabatan tidak ditampilkan untuk student
    } else if (role === 'supervisor') {
        supervisorFields.style.display = 'block';
        direktoratField.style.display = 'block';
        jabatanField.style.display = 'block'; // Jabatan ditampilkan untuk supervisor
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    toggleRoleFields(roleSelect.value);
});
</script>
@endsection