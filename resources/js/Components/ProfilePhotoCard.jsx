import { useCallback, useEffect, useRef, useState } from 'react'
import { router } from '@inertiajs/react'
import Cropper from 'react-easy-crop'
import { Camera, RefreshCw, UserRound, Upload, Check } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { getCroppedImg } from '@/lib/cropImage'
import { cn } from '@/lib/utils'
import { toast } from '@/lib/toast'

/**
 * Profile photo card for any role. Supports two capture modes, both
 * validated identically by face-api.js before saving:
 *   - Camera: the existing live getUserMedia flow, capture to canvas.
 *   - Upload: a plain file input, loaded into a hidden <img> so
 *     detectDescriptor() (which accepts any image-like element) can
 *     run against it the same way it runs against the camera canvas.
 *
 * Both modes route their raw photo through an interactive crop step
 * (pan/zoom, 1:1 output) before face detection runs - the cropped
 * result is what gets face-detected and uploaded, so the stored photo
 * and the descriptor used for verification are always the same pixels.
 *
 * Neither mode saves a photo without a valid descriptor - a reference
 * photo the matching feature can't use is worse than no reference at
 * all, so unlike check-in (which tolerates detection being
 * unavailable), this always rejects on `!available` too.
 */
export default function ProfilePhotoCard({ profilePhotoUrl }) {
  const camera = useCamera()
  const face = useFaceDetection()
  const fileInputRef = useRef(null)
  const [uploadPreviewUrl, setUploadPreviewUrl] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  const [cropSource, setCropSource] = useState(null) // data/object URL being cropped, or null
  const [crop, setCrop] = useState({ x: 0, y: 0 })
  const [zoom, setZoom] = useState(1)
  const [croppedAreaPixels, setCroppedAreaPixels] = useState(null)

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    if (camera.phase === 'captured' && camera.previewUrl) {
      setCrop({ x: 0, y: 0 })
      setZoom(1)
      setCropSource(camera.previewUrl)
    }
  }, [camera.phase, camera.previewUrl])

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (!file) return

    camera.reset()
    const url = URL.createObjectURL(file)
    setUploadPreviewUrl(url)
    setCrop({ x: 0, y: 0 })
    setZoom(1)
    setCropSource(url)
  }

  function clearUpload() {
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(null)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  function cancelCrop() {
    setCropSource(null)
    setCroppedAreaPixels(null)
    camera.reset()
    clearUpload()
  }

  const onCropComplete = useCallback((_croppedArea, croppedAreaPixelsValue) => {
    setCroppedAreaPixels(croppedAreaPixelsValue)
  }, [])

  async function confirmCrop() {
    if (!croppedAreaPixels) return

    setStatusMessage('Memproses gambar...')

    let blob
    try {
      blob = await getCroppedImg(cropSource, croppedAreaPixels)
    } catch (err) {
      setStatusMessage('')
      toast.error(err.message)
      return
    }

    const objectUrl = URL.createObjectURL(blob)
    const img = new Image()
    img.onload = async () => {
      setStatusMessage('Memeriksa wajah...')
      const { available, descriptor } = await face.detectDescriptor(img)
      URL.revokeObjectURL(objectUrl)

      if (!available) {
        setStatusMessage('')
        toast.error('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba lagi atau gunakan perangkat/browser lain.')
        return
      }

      if (!descriptor) {
        setStatusMessage('')
        toast.error('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
        return
      }

      setStatusMessage('')
      setSubmitting(true)
      setCropSource(null)

      const formData = new FormData()
      formData.append('photo', blob, 'profile.jpg')
      formData.append('face_descriptor', JSON.stringify(descriptor))

      router.post(route('profile.photo.update'), formData, {
        preserveScroll: true,
        onFinish: () => {
          setSubmitting(false)
          camera.reset()
          clearUpload()
        },
      })
    }
    img.src = objectUrl
  }

  const showIdlePreview = camera.phase === 'idle' && !uploadPreviewUrl && !cropSource && profilePhotoUrl

  return (
    <Card>
      <CardHeader className="border-b">
        <CardTitle className="flex items-center gap-2">
          <UserRound className="h-4 w-4 text-muted-foreground" /> Foto Profil
        </CardTitle>
        <CardDescription>Digunakan sebagai referensi verifikasi wajah saat check-in</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4 pt-6">
        {showIdlePreview && (
          <img src={profilePhotoUrl} alt="Foto profil" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}

        {!cropSource && (
          <video
            ref={camera.videoRef}
            className={cn('h-48 w-full rounded-lg border border-border bg-muted object-cover', camera.phase !== 'streaming' && 'hidden')}
            muted
            playsInline
          />
        )}
        <canvas ref={camera.canvasRef} className="hidden" />

        {cropSource && (
          <div className="space-y-3">
            <div className="relative h-64 w-full overflow-hidden rounded-lg border border-border bg-muted">
              <Cropper
                image={cropSource}
                crop={crop}
                zoom={zoom}
                aspect={1}
                cropShape="round"
                onCropChange={setCrop}
                onZoomChange={setZoom}
                onCropComplete={onCropComplete}
              />
            </div>
            <input
              type="range"
              min={1}
              max={3}
              step={0.05}
              value={zoom}
              onChange={(e) => setZoom(Number(e.target.value))}
              className="w-full"
              aria-label="Perbesar"
            />
          </div>
        )}

        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex flex-wrap gap-2">
          {camera.phase === 'idle' && !uploadPreviewUrl && !cropSource && (
            <>
              <Button type="button" onClick={camera.start} className="flex-1">
                <Camera /> Ambil Foto
              </Button>
              <Button type="button" variant="outline" onClick={() => fileInputRef.current?.click()} className="flex-1">
                <Upload /> Upload Foto
              </Button>
              <input
                ref={fileInputRef}
                type="file"
                accept="image/*"
                onChange={handleFileChange}
                className="hidden"
              />
            </>
          )}

          {camera.phase === 'streaming' && !cropSource && (
            <Button type="button" onClick={camera.capture} className="flex-1">
              <Camera /> Ambil Foto
            </Button>
          )}

          {cropSource && (
            <>
              <Button type="button" onClick={cancelCrop} variant="outline" className="flex-1">
                <RefreshCw /> Batal
              </Button>
              <Button type="button" onClick={confirmCrop} disabled={submitting || !croppedAreaPixels} className="flex-1">
                {submitting ? 'Menyimpan...' : <><Check /> Pakai Foto Ini</>}
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
