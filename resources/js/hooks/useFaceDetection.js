import { useCallback, useState } from 'react'
import * as faceapi from 'face-api.js'

const MODEL_URL = '/models/face-api'

let modelsLoadingPromise = null

/**
 * Loads face-api.js's TinyFaceDetector + FaceRecognitionNet models once
 * (shared across every hook instance via a module-level promise, so
 * opening the check-in modal twice doesn't re-fetch ~6.4MB of weights),
 * then exposes a function to compute a 128-number face descriptor from
 * an image/canvas/video element.
 *
 * Never throws on failure - callers get `status === 'unavailable'`
 * instead, so a browser that can't run face-api.js (unsupported,
 * blocked, offline) degrades to skipping face verification rather
 * than blocking the student from checking in at all.
 */
export function useFaceDetection() {
  const [status, setStatus] = useState('idle') // idle | loading | ready | unavailable

  const ensureModelsLoaded = useCallback(async () => {
    if (status === 'ready') return true
    if (status === 'unavailable') return false

    setStatus('loading')

    if (!modelsLoadingPromise) {
      modelsLoadingPromise = Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
      ])
    }

    try {
      await modelsLoadingPromise
      setStatus('ready')
      return true
    } catch (err) {
      modelsLoadingPromise = null
      setStatus('unavailable')
      return false
    }
  }, [status])

  /**
   * Detects a single face in the given image-like element and returns
   * its 128-number descriptor as a plain array, or null if no face was
   * detected (or the models failed to load).
   */
  const detectDescriptor = useCallback(async (imageElement) => {
    const ready = await ensureModelsLoaded()
    if (!ready) return null

    try {
      const detection = await faceapi
        .detectSingleFace(imageElement, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor()

      if (!detection) return null

      return Array.from(detection.descriptor)
    } catch (err) {
      return null
    }
  }, [ensureModelsLoaded])

  return { status, ensureModelsLoaded, detectDescriptor }
}
