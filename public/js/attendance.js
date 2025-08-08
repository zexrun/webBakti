// Attendance System JavaScript
let currentPosition = null;
let checkInStream = null;
let checkOutStream = null;

// Get CSRF Token
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Attendance system initialized');
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    initializeEventListeners();
    initializeModalEventListeners();
});

// Initialize all event listeners
function initializeEventListeners() {
    // Check In Camera
    const startCheckInBtn = document.getElementById('startCheckInCamera');
    if (startCheckInBtn) {
        startCheckInBtn.addEventListener('click', () => startCamera('checkIn'));
    }
    
    const captureCheckInBtn = document.getElementById('captureCheckIn');
    if (captureCheckInBtn) {
        captureCheckInBtn.addEventListener('click', () => capturePhoto('checkIn'));
    }
    
    const retakeCheckInBtn = document.getElementById('retakeCheckIn');
    if (retakeCheckInBtn) {
        retakeCheckInBtn.addEventListener('click', () => retakePhoto('checkIn'));
    }
    
    // Check Out Camera
    const startCheckOutBtn = document.getElementById('startCheckOutCamera');
    if (startCheckOutBtn) {
        startCheckOutBtn.addEventListener('click', () => startCamera('checkOut'));
    }
    
    const captureCheckOutBtn = document.getElementById('captureCheckOut');
    if (captureCheckOutBtn) {
        captureCheckOutBtn.addEventListener('click', () => capturePhoto('checkOut'));
    }
    
    const retakeCheckOutBtn = document.getElementById('retakeCheckOut');
    if (retakeCheckOutBtn) {
        retakeCheckOutBtn.addEventListener('click', () => retakePhoto('checkOut'));
    }
    
    // Form submissions
    const checkInForm = document.getElementById('checkInForm');
    if (checkInForm) {
        checkInForm.addEventListener('submit', handleCheckIn);
    }
    
    const checkOutForm = document.getElementById('checkOutForm');
    if (checkOutForm) {
        checkOutForm.addEventListener('submit', handleCheckOut);
    }

    // Modal trigger buttons
    const checkInButtons = document.querySelectorAll('[onclick*="openCheckInModal"]');
    checkInButtons.forEach(button => {
        button.removeAttribute('onclick');
        button.addEventListener('click', function(e) {
            e.preventDefault();
            openCheckInModal();
        });
    });

    const checkOutButtons = document.querySelectorAll('[onclick*="openCheckOutModal"]');
    checkOutButtons.forEach(button => {
        button.removeAttribute('onclick');
        button.addEventListener('click', function(e) {
            e.preventDefault();
            openCheckOutModal();
        });
    });

    const exceptionButtons = document.querySelectorAll('[onclick*="openExceptionModal"]');
    exceptionButtons.forEach(button => {
        button.removeAttribute('onclick');
        button.addEventListener('click', function(e) {
            e.preventDefault();
            openExceptionModal();
        });
    });

    // Exception form validation
    const exceptionForm = document.querySelector('#exceptionModal form');
    if (exceptionForm) {
        exceptionForm.addEventListener('submit', function(e) {
            const date = document.getElementById('exceptionDate').value;
            const type = document.getElementById('exceptionType').value;
            const reason = document.getElementById('exceptionReason').value;

            if (!date || !type || !reason.trim()) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
                return false;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengirim...
                `;
            }
        });
    }
}

// Initialize modal event listeners
function initializeModalEventListeners() {
    // Close modal when clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
            openModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
}

// Update current time display
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    const currentTimeElements = document.querySelectorAll('#current-time, #current-time-status');
    currentTimeElements.forEach(element => {
        if (element) {
            element.textContent = timeString;
        }
    });
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        updateLocationStatus('loading', '📍 Mendapatkan lokasi...');
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentPosition = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                updateLocationStatus('success', '✅ Lokasi berhasil didapatkan');
                console.log('Location obtained:', currentPosition);
            },
            function(error) {
                const errorMessage = getLocationErrorMessage(error);
                updateLocationStatus('error', '❌ Gagal mendapatkan lokasi: ' + errorMessage);
                console.error('Location error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    } else {
        updateLocationStatus('error', '❌ Browser tidak mendukung geolocation');
        console.error('Geolocation not supported');
    }
}

// Update location status display
function updateLocationStatus(type, message) {
    const statusElements = ['locationStatus', 'checkOutLocationStatus'];
    statusElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            let bgClass = 'bg-gray-100';
            let textClass = 'text-gray-600';
            let iconClass = 'text-gray-600';
            
            if (type === 'success') {
                bgClass = 'bg-green-50';
                textClass = 'text-green-600';
                iconClass = 'text-green-600';
            } else if (type === 'error') {
                bgClass = 'bg-red-50';
                textClass = 'text-red-600';
                iconClass = 'text-red-600';
            } else if (type === 'loading') {
                bgClass = 'bg-blue-50';
                textClass = 'text-blue-600';
                iconClass = 'text-blue-600';
            }
            
            const iconSvg = type === 'loading' ? 
                `<svg class="w-5 h-5 ${iconClass} mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>` :
                `<svg class="w-5 h-5 ${iconClass} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>`;
            
            element.className = `${bgClass} rounded-lg p-4`;
            element.innerHTML = `
                <div class="flex items-center">
                    ${iconSvg}
                    <p class="text-sm ${textClass}">${message}</p>
                </div>
            `;
        }
    });
}

// Get location error message
function getLocationErrorMessage(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            return "Akses lokasi ditolak. Silakan izinkan akses lokasi.";
        case error.POSITION_UNAVAILABLE:
            return "Informasi lokasi tidak tersedia.";
        case error.TIMEOUT:
            return "Timeout mendapatkan lokasi.";
        default:
            return "Error tidak diketahui.";
    }
}

// Open modal function
function openModal(modalId) {
    console.log('Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Add animation
        setTimeout(() => {
            const content = modal.querySelector('.bg-white');
            if (content) {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Close modal function
function closeModal(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        const content = modal.querySelector('.bg-white');
        
        if (content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
        }
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
            
            // Stop camera streams
            if (modalId === 'checkInModal' && checkInStream) {
                checkInStream.getTracks().forEach(track => track.stop());
                checkInStream = null;
            }
            if (modalId === 'checkOutModal' && checkOutStream) {
                checkOutStream.getTracks().forEach(track => track.stop());
                checkOutStream = null;
            }
            
            // Reset modal form
            resetModalForm(modalId);
        }, 300);
    }
}

// Reset modal form
function resetModalForm(modalId) {
    const form = document.querySelector(`#${modalId} form`);
    if (form && !modalId.includes('exception')) {
        form.reset();
        const prefix = modalId.replace('Modal', '');
        resetCameraElements(prefix);
    }
}

// Reset camera elements
function resetCameraElements(prefix) {
    const elements = {
        camera: document.getElementById(`${prefix}Camera`),
        canvas: document.getElementById(`${prefix}Canvas`),
        preview: document.getElementById(`${prefix}Preview`),
        startBtn: document.getElementById(`start${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Camera`),
        captureBtn: document.getElementById(`capture${prefix.charAt(0).toUpperCase() + prefix.slice(1)}`),
        retakeBtn: document.getElementById(`retake${prefix.charAt(0).toUpperCase() + prefix.slice(1)}`),
        submitBtn: document.getElementById(`submit${prefix.charAt(0).toUpperCase() + prefix.slice(1)}`)
    };
    
    if (elements.camera) elements.camera.classList.add('hidden');
    if (elements.canvas) elements.canvas.classList.add('hidden');
    if (elements.preview) elements.preview.classList.add('hidden');
    if (elements.startBtn) elements.startBtn.classList.remove('hidden');
    if (elements.captureBtn) elements.captureBtn.classList.add('hidden');
    if (elements.retakeBtn) elements.retakeBtn.classList.add('hidden');
    if (elements.submitBtn) elements.submitBtn.disabled = true;
}

// Modal opening functions
function openCheckInModal() {
    console.log('Opening check-in modal');
    openModal('checkInModal');
    if (!currentPosition) {
        getCurrentLocation();
    }
}

function openCheckOutModal() {
    console.log('Opening check-out modal');
    openModal('checkOutModal');
    if (!currentPosition) {
        getCurrentLocation();
    }
}

function openExceptionModal() {
    console.log('Opening exception modal');
    openModal('exceptionModal');
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('exceptionDate');
    if (dateInput) {
        dateInput.value = today;
    }
}

// Start camera function
function startCamera(type) {
    console.log('Starting camera for:', type);
    const video = document.getElementById(`${type}Camera`);
    const startBtn = document.getElementById(`start${type.charAt(0).toUpperCase() + type.slice(1)}Camera`);
    const captureBtn = document.getElementById(`capture${type.charAt(0).toUpperCase() + type.slice(1)}`);
    
    navigator.mediaDevices.getUserMedia({ 
        video: {
            facingMode: 'user',
            width: { ideal: 640 },
            height: { ideal: 480 }
        }
    })
    .then(function(stream) {
        if (type === 'checkIn') {
            checkInStream = stream;
        } else {
            checkOutStream = stream;
        }
        
        video.srcObject = stream;
        video.play();
        
        video.classList.remove('hidden');
        startBtn.classList.add('hidden');
        captureBtn.classList.remove('hidden');
        
        console.log('Camera started successfully');
    })
    .catch(function(error) {
        console.error('Camera error:', error);
        alert('Gagal mengakses kamera: ' + error.message);
    });
}

// Capture photo function
function capturePhoto(type) {
    console.log('Capturing photo for:', type);
    const video = document.getElementById(`${type}Camera`);
    const canvas = document.getElementById(`${type}Canvas`);
    const preview = document.getElementById(`${type}Preview`);
    const captureBtn = document.getElementById(`capture${type.charAt(0).toUpperCase() + type.slice(1)}`);
    const retakeBtn = document.getElementById(`retake${type.charAt(0).toUpperCase() + type.slice(1)}`);
    const submitBtn = document.getElementById(`submit${type.charAt(0).toUpperCase() + type.slice(1)}`);
    
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    context.drawImage(video, 0, 0);
    
    canvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        preview.src = url;
        preview.classList.remove('hidden');
        
        video.classList.add('hidden');
        canvas.classList.add('hidden');
        captureBtn.classList.add('hidden');
        retakeBtn.classList.remove('hidden');
        submitBtn.disabled = false;
        
        const stream = type === 'checkIn' ? checkInStream : checkOutStream;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        console.log('Photo captured successfully');
    }, 'image/jpeg', 0.8);
}

// Retake photo function
function retakePhoto(type) {
    console.log('Retaking photo for:', type);
    const preview = document.getElementById(`${type}Preview`);
    const retakeBtn = document.getElementById(`retake${type.charAt(0).toUpperCase() + type.slice(1)}`);
    const submitBtn = document.getElementById(`submit${type.charAt(0).toUpperCase() + type.slice(1)}`);
    
    preview.classList.add('hidden');
    retakeBtn.classList.add('hidden');
    submitBtn.disabled = true;
    
    startCamera(type);
}

// Handle check-in form submission
function handleCheckIn(e) {
    e.preventDefault();
    console.log('Handling check-in submission');
    
    if (!currentPosition) {
        alert('Lokasi belum didapatkan, silakan tunggu...');
        getCurrentLocation();
        return;
    }
    
    const canvas = document.getElementById('checkInCanvas');
    const notes = document.getElementById('checkInNotes').value;
    const submitBtn = document.getElementById('submitCheckIn');
    const buttonText = document.getElementById('checkInButtonText');
    const spinner = document.getElementById('checkInSpinner');
    
    submitBtn.disabled = true;
    buttonText.textContent = 'Memproses...';
    spinner.classList.remove('hidden');
    
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        const csrfToken = getCSRFToken();
        
        formData.append('_token', csrfToken);
        formData.append('latitude', currentPosition.latitude);
        formData.append('longitude', currentPosition.longitude);
        formData.append('photo', blob, 'checkin.jpg');
        formData.append('notes', notes);
        
        fetch('/student/attendance/check-in', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Check-in berhasil!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Check-in error:', error);
            alert('Terjadi kesalahan saat check-in.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            buttonText.textContent = 'Check In';
            spinner.classList.add('hidden');
        });
    }, 'image/jpeg', 0.8);
}

// Handle check-out form submission
function handleCheckOut(e) {
    e.preventDefault();
    console.log('Handling check-out submission');
    
    if (!currentPosition) {
        alert('Lokasi belum didapatkan, silakan tunggu...');
        getCurrentLocation();
        return;
    }
    
    const canvas = document.getElementById('checkOutCanvas');
    const notes = document.getElementById('checkOutNotes').value;
    const submitBtn = document.getElementById('submitCheckOut');
    const buttonText = document.getElementById('checkOutButtonText');
    const spinner = document.getElementById('checkOutSpinner');
    
    submitBtn.disabled = true;
    buttonText.textContent = 'Memproses...';
    spinner.classList.remove('hidden');
    
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        const csrfToken = getCSRFToken();
        
        formData.append('_token', csrfToken);
        formData.append('latitude', currentPosition.latitude);
        formData.append('longitude', currentPosition.longitude);
        formData.append('photo', blob, 'checkout.jpg');
        formData.append('notes', notes);
        
        fetch('/student/attendance/check-out', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Check-out berhasil!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Check-out error:', error);
            alert('Terjadi kesalahan saat check-out.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            buttonText.textContent = 'Check Out';
            spinner.classList.add('hidden');
        });
    }, 'image/jpeg', 0.8);
}

// Debug function for testing
function testModal() {
    console.log('Testing exception modal...');
    openExceptionModal();
}

// Make functions globally available
window.openCheckInModal = openCheckInModal;
window.openCheckOutModal = openCheckOutModal;
window.openExceptionModal = openExceptionModal;
window.closeModal = closeModal;
window.testModal = testModal;

// Legacy support for onclick attributes (will be removed by event listeners)
window.openModal = openModal;
