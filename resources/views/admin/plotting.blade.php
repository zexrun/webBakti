@extends('layouts.app')
@section('title', 'Plotting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Plotting Pembimbing Mahasiswa</h2>
            <p class="text-gray-600">Pilih dosen pembimbing untuk setiap mahasiswa yang tersedia.</p>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $students->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sudah Ditugaskan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $students->whereNotNull('supervisor_id')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Belum Ditugaskan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $students->whereNull('supervisor_id')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Mahasiswa</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Mahasiswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIM
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                Universitas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Pembimbing
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                            <div class="text-sm text-gray-500 md:hidden">{{ $student->universitas }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $student->nim }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 hidden md:table-cell">
                                    {{ $student->universitas }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->supervisor)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $student->supervisor->user->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button type="button" 
                                            onclick="openSupervisorModal({{ $student->id }}, '{{ $student->user->name }}', {{ $student->supervisor_id ?? 'null' }}, '{{ $student->supervisor->user->name ?? '' }}')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 shadow-sm">
                                        @if($student->supervisor)
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Ubah Pembimbing
                                        @else
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Pilih Pembimbing
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data mahasiswa</h3>
                                        <p class="text-sm text-gray-500">Belum ada mahasiswa yang perlu ditugaskan pembimbing.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk memilih pembimbing -->
<div id="supervisorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Pilih Pembimbing</h3>
                        <p class="text-sm text-gray-600 mt-1">untuk <span id="studentName" class="font-medium"></span></p>
                    </div>
                    <button type="button" onclick="closeSupervisorModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 flex-1 overflow-hidden flex flex-col">
                <!-- Current Supervisor Info -->
                <div id="currentSupervisorInfo" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <p class="text-sm text-blue-800">
                        <span class="font-medium">Pembimbing saat ini:</span> 
                        <span id="currentSupervisorName"></span>
                    </p>
                </div>
                
                <!-- Search Input -->
                <div class="mb-4 flex-shrink-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               id="supervisorSearch" 
                               placeholder="Cari nama pembimbing..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <!-- Supervisor List -->
                <div class="flex-1 overflow-y-auto border border-gray-200 rounded-lg">
                    <div id="supervisorList">
                        @foreach($supervisors as $supervisor)
                            <div class="supervisor-item p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                                 data-id="{{ $supervisor->id }}" 
                                 data-name="{{ $supervisor->user->name }}"
                                 data-jabatan="{{ $supervisor->jabatan }}"
                                 data-direktorat="{{ $supervisor->direktorat }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-green-600">
                                                {{ strtoupper(substr($supervisor->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $supervisor->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $supervisor->jabatan }}</div>
                                        <div class="text-xs text-gray-400">{{ $supervisor->direktorat }}</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded-full supervisor-radio"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- No Results Message -->
                    <div id="noResults" class="hidden p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-500">Tidak ada pembimbing yang ditemukan</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3 flex-shrink-0">
                <button type="button" 
                        onclick="closeSupervisorModal()"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="button" 
                        id="assignButton"
                        onclick="assignSupervisor()"
                        disabled
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200">
                    Tugaskan Pembimbing
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStudentId = null;
let selectedSupervisorId = null;

function openSupervisorModal(studentId, studentName, currentSupervisorId, currentSupervisorName) {
    currentStudentId = studentId;
    selectedSupervisorId = null;
    
    // Set student name
    document.getElementById('studentName').textContent = studentName;
    
    // Show/hide current supervisor info
    const currentInfo = document.getElementById('currentSupervisorInfo');
    const currentNameSpan = document.getElementById('currentSupervisorName');
    
    if (currentSupervisorId && currentSupervisorName) {
        currentNameSpan.textContent = currentSupervisorName;
        currentInfo.classList.remove('hidden');
    } else {
        currentInfo.classList.add('hidden');
    }
    
    // Reset search
    document.getElementById('supervisorSearch').value = '';
    showAllSupervisors();
    
    // Reset selections
    document.querySelectorAll('.supervisor-item').forEach(item => {
        item.classList.remove('bg-blue-50', 'border-blue-200');
        const radio = item.querySelector('.supervisor-radio');
        radio.classList.remove('bg-blue-600', 'border-blue-600');
        radio.classList.add('border-gray-300');
        
        // Highlight current supervisor
        if (item.dataset.id == currentSupervisorId) {
            item.classList.add('bg-blue-50');
        }
    });
    
    // Disable assign button
    document.getElementById('assignButton').disabled = true;
    
    // Show modal
    document.getElementById('supervisorModal').classList.remove('hidden');
    document.getElementById('supervisorSearch').focus();
}

function closeSupervisorModal() {
    document.getElementById('supervisorModal').classList.add('hidden');
    document.getElementById('supervisorSearch').value = '';
    showAllSupervisors();
    currentStudentId = null;
    selectedSupervisorId = null;
}

// Search functionality
document.getElementById('supervisorSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    let hasResults = false;
    
    document.querySelectorAll('.supervisor-item').forEach(item => {
        const name = item.dataset.name.toLowerCase();
        const jabatan = item.dataset.jabatan.toLowerCase();
        const direktorat = item.dataset.direktorat.toLowerCase();
        
        if (name.includes(searchTerm) || jabatan.includes(searchTerm) || direktorat.includes(searchTerm)) {
            item.style.display = 'block';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (!hasResults && searchTerm.length > 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
});

function showAllSupervisors() {
    document.querySelectorAll('.supervisor-item').forEach(item => {
        item.style.display = 'block';
    });
    document.getElementById('noResults').classList.add('hidden');
}

// Select supervisor
document.querySelectorAll('.supervisor-item').forEach(item => {
    item.addEventListener('click', function() {
        // Remove previous selection
        document.querySelectorAll('.supervisor-item').forEach(i => {
            i.classList.remove('bg-blue-100', 'border-blue-300');
            const radio = i.querySelector('.supervisor-radio');
            radio.classList.remove('bg-blue-600', 'border-blue-600');
            radio.classList.add('border-gray-300');
        });
        
        // Add selection to clicked item
        this.classList.add('bg-blue-100', 'border-blue-300');
        const radio = this.querySelector('.supervisor-radio');
        radio.classList.remove('border-gray-300');
        radio.classList.add('bg-blue-600', 'border-blue-600');
        
        selectedSupervisorId = this.dataset.id;
        
        // Enable assign button
        document.getElementById('assignButton').disabled = false;
    });
});

function assignSupervisor() {
    if (!selectedSupervisorId) {
        alert('Silakan pilih pembimbing terlebih dahulu');
        return;
    }
    
    // Show loading state
    const assignButton = document.getElementById('assignButton');
    const originalText = assignButton.textContent;
    assignButton.textContent = 'Menyimpan...';
    assignButton.disabled = true;
    
    // Create and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.plotting.assign") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const studentInput = document.createElement('input');
    studentInput.type = 'hidden';
    studentInput.name = 'student_id';
    studentInput.value = currentStudentId;
    
    const supervisorInput = document.createElement('input');
    supervisorInput.type = 'hidden';
    supervisorInput.name = 'supervisor_id';
    supervisorInput.value = selectedSupervisorId;
    
    form.appendChild(csrfToken);
    form.appendChild(studentInput);
    form.appendChild(supervisorInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Close modal when clicking outside
document.getElementById('supervisorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSupervisorModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('supervisorModal').classList.contains('hidden')) {
        closeSupervisorModal();
    }
});
</script>
@endsection