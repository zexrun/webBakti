@extends('layouts.app')

@section('title', 'Pengaturan Absensi')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Pengaturan Absensi</h2>
            <p class="text-gray-600">Kelola pengaturan sistem absensi dan konfigurasi operasional.</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded-lg mb-6 relative">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
                <button type="button" class="absolute top-4 right-4 text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Error Alert -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-6 py-4 rounded-lg mb-6 relative">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
                <button type="button" class="absolute top-4 right-4 text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-6 py-4 rounded-lg mb-6 relative">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium mb-2">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="absolute top-4 right-4 text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.attendance.settings.update') }}" method="POST">
            @csrf
            
            <!-- Working Hours Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Jam Kerja</h3>
                        <p class="text-sm text-gray-600">Atur jam kerja dan toleransi keterlambatan</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Masuk
                        </label>
                        <input type="time" 
                               id="work_start_time" 
                               name="work_start_time" 
                               value="{{ old('work_start_time', substr($settings->work_start_time ?? '08:00:00', 0, 5)) }}" 
                               required
                               class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                    
                    <div>
                        <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Pulang
                        </label>
                        <input type="time" 
                               id="work_end_time" 
                               name="work_end_time" 
                               value="{{ old('work_end_time', substr($settings->work_end_time ?? '17:00:00', 0, 5)) }}" 
                               required
                               class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                    
                    <div>
                        <label for="late_tolerance_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Toleransi Keterlambatan (Menit)
                        </label>
                        <input type="number" 
                               id="late_tolerance_minutes" 
                               name="late_tolerance_minutes" 
                               value="{{ old('late_tolerance_minutes', $settings->late_tolerance_minutes ?? 15) }}" 
                               min="0" max="120" required
                               class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                </div>
            </div>

            <!-- Location Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Pengaturan Lokasi</h3>
                        <p class="text-sm text-gray-600">Konfigurasi verifikasi lokasi untuk absensi</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="require_location" value="0">
                        <input type="checkbox" 
                               id="require_location" 
                               name="require_location" 
                               value="1"
                               {{ old('require_location', $settings->require_location ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="require_location" class="ml-3 text-sm font-medium text-gray-700">
                            Wajibkan Verifikasi Lokasi
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="office_latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Latitude Kantor
                            </label>
                            <input type="number" 
                                   id="office_latitude" 
                                   name="office_latitude" 
                                   value="{{ old('office_latitude', $settings->office_latitude) }}" 
                                   step="0.00000001"
                                   placeholder="Contoh: -6.200000"
                                   class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        
                        <div>
                            <label for="office_longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Longitude Kantor
                            </label>
                            <input type="number" 
                                   id="office_longitude" 
                                   name="office_longitude" 
                                   value="{{ old('office_longitude', $settings->office_longitude) }}" 
                                   step="0.00000001"
                                   placeholder="Contoh: 106.816666"
                                   class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        
                        <div>
                            <label for="location_radius_meters" class="block text-sm font-medium text-gray-700 mb-2">
                                Radius Lokasi (Meter)
                            </label>
                            <input type="number" 
                                   id="location_radius_meters" 
                                   name="location_radius_meters" 
                                   value="{{ old('location_radius_meters', $settings->location_radius_meters ?? 100) }}" 
                                   min="10" max="5000" required
                                   class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        
                        <div>
                            <label for="office_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Kantor
                            </label>
                            <input type="text" 
                                   id="office_address" 
                                   name="office_address" 
                                   value="{{ old('office_address', $settings->office_address) }}"
                                   class="block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   placeholder="Masukkan alamat kantor">
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" 
                                onclick="getCurrentLocation()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Photo Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-orange-100 mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Pengaturan Foto</h3>
                        <p class="text-sm text-gray-600">Konfigurasi persyaratan foto untuk absensi</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <input type="hidden" name="require_photo" value="0">
                    <input type="checkbox" 
                           id="require_photo" 
                           name="require_photo" 
                           value="1"
                           {{ old('require_photo', $settings->require_photo ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="require_photo" class="ml-3 text-sm font-medium text-gray-700">
                        Wajibkan Foto Selfie saat Absen
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.attendance.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function getCurrentLocation() {
    if (navigator.geolocation) {
        // Show loading state
        const button = event.target;
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mendapatkan Lokasi...
        `;
        button.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('office_latitude').value = lat;
                document.getElementById('office_longitude').value = lng;
                
                // Create success notification
                showNotification('success', 'Lokasi berhasil didapatkan!', `Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}`);
                
                // Reset button
                button.innerHTML = originalHTML;
                button.disabled = false;
            },
            function(error) {
                showNotification('error', 'Gagal mendapatkan lokasi', error.message);
                
                // Reset button
                button.innerHTML = originalHTML;
                button.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        showNotification('error', 'Browser tidak mendukung', 'Browser Anda tidak mendukung fitur geolocation');
    }
}

function showNotification(type, title, message) {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-400 text-green-800' : 'bg-red-50 border-red-400 text-red-800';
    const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
    const icon = type === 'success' ? 
        `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>` :
        `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>`;
    
    const notification = document.createElement('div');
    notification.className = `${alertClass} border-l-4 px-6 py-4 rounded-lg mb-6 relative`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                ${icon}
            </svg>
            <div>
                <p class="font-medium">${title}</p>
                <p class="text-sm">${message}</p>
            </div>
        </div>
        <button type="button" class="absolute top-4 right-4 ${iconColor.replace('text-', 'text-').replace('-500', '-600')} hover:${iconColor.replace('text-', 'hover:text-').replace('-500', '-800')}" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;
    
    // Insert after header
    const header = document.querySelector('.bg-white.rounded-lg.shadow-sm.border.border-gray-200.p-6.mb-6');
    header.parentNode.insertBefore(notification, header.nextSibling);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush
@endsection