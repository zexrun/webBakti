import { useState } from 'react'

function errorMessage(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      return 'Akses lokasi ditolak. Silakan izinkan akses lokasi.'
    case error.POSITION_UNAVAILABLE:
      return 'Informasi lokasi tidak tersedia.'
    case error.TIMEOUT:
      return 'Timeout mendapatkan lokasi.'
    default:
      return 'Error tidak diketahui.'
  }
}

/**
 * Wraps navigator.geolocation.getCurrentPosition with loading/error state,
 * mirroring public/js/attendance.js's getCurrentLocation()/updateLocationStatus().
 */
export function useGeolocation() {
  const [position, setPosition] = useState(null)
  const [status, setStatus] = useState('idle') // idle | loading | success | error
  const [message, setMessage] = useState('')

  function request() {
    if (!navigator.geolocation) {
      setStatus('error')
      setMessage('Browser tidak mendukung geolocation')
      return
    }

    setStatus('loading')
    setMessage('📍 Mendapatkan lokasi...')

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        setPosition({ latitude: pos.coords.latitude, longitude: pos.coords.longitude })
        setStatus('success')
        setMessage('✅ Lokasi berhasil didapatkan')
      },
      (error) => {
        setStatus('error')
        setMessage('❌ Gagal mendapatkan lokasi: ' + errorMessage(error))
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 },
    )
  }

  return { position, status, message, request }
}
