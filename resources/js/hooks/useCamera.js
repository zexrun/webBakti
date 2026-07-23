import { useRef, useState } from 'react'

/**
 * Wraps getUserMedia + <video>/<canvas> capture, mirroring
 * public/js/attendance.js's startCamera()/capturePhoto()/retakePhoto().
 * Returns refs to attach to <video>/<canvas> elements plus capture state.
 */
export function useCamera() {
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const streamRef = useRef(null)

  const [phase, setPhase] = useState('idle') // idle | streaming | captured
  const [previewUrl, setPreviewUrl] = useState(null)
  const [error, setError] = useState(null)

  async function start() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 }, aspectRatio: { ideal: 16 / 9 } },
      })
      streamRef.current = stream
      if (videoRef.current) {
        videoRef.current.srcObject = stream
        await videoRef.current.play()
      }
      setPhase('streaming')
      setError(null)
    } catch (err) {
      setError('Gagal mengakses kamera: ' + err.message)
    }
  }

  function stopStream() {
    streamRef.current?.getTracks().forEach((track) => track.stop())
    streamRef.current = null
  }

  function capture() {
    return new Promise((resolve) => {
      const video = videoRef.current
      const canvas = canvasRef.current
      if (!video || !canvas) return resolve(null)

      const sourceWidth = video.videoWidth
      const sourceHeight = video.videoHeight
      const targetRatio = 16 / 9

      let cropWidth = sourceWidth
      let cropHeight = sourceWidth / targetRatio
      if (cropHeight > sourceHeight) {
        cropHeight = sourceHeight
        cropWidth = sourceHeight * targetRatio
      }
      const cropX = (sourceWidth - cropWidth) / 2
      const cropY = (sourceHeight - cropHeight) / 2

      canvas.width = cropWidth
      canvas.height = cropHeight
      canvas.getContext('2d').drawImage(video, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight)

      canvas.toBlob(
        (blob) => {
          setPreviewUrl(URL.createObjectURL(blob))
          setPhase('captured')
          stopStream()
          resolve(blob)
        },
        'image/jpeg',
        0.8,
      )
    })
  }

  function retake() {
    if (previewUrl) URL.revokeObjectURL(previewUrl)
    setPreviewUrl(null)
    setPhase('idle')
    start()
  }

  function reset() {
    stopStream()
    if (previewUrl) URL.revokeObjectURL(previewUrl)
    setPreviewUrl(null)
    setPhase('idle')
    setError(null)
  }

  return { videoRef, canvasRef, phase, previewUrl, error, start, capture, retake, reset, stopStream }
}
