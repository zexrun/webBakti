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
   * Detects a single face in the given image-like element. Returns
   * { available, descriptor }: `available` is false when the models
   * couldn't be loaded (caller must not treat this as "no face found" -
   * it means detection itself never ran); when `available` is true,
   * `descriptor` is either the 128-number array or null if no face was
   * detected in the frame.
   *
   * Returning `available` here (rather than making the caller re-read
   * the hook's `status` after this promise resolves) avoids a stale-
   * closure trap: an async caller that captured `status` before this
   * call would still see the pre-await value even after the models
   * finish loading during this call.
   */
  const detectDescriptor = useCallback(async (imageElement) => {
    const ready = await ensureModelsLoaded()
    if (!ready) return { available: false, descriptor: null }

    try {
      const detection = await faceapi
        .detectSingleFace(imageElement, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor()

      return { available: true, descriptor: detection ? Array.from(detection.descriptor) : null }
    } catch (err) {
      return { available: true, descriptor: null }
    }
  }, [ensureModelsLoaded])

  return { status, ensureModelsLoaded, detectDescriptor }
}
