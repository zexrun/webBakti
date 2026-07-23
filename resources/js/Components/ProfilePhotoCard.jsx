import { useEffect, useRef, useState } from 'react'
import { router } from '@inertiajs/react'
import { Camera, RefreshCw, UserRound, Upload } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'

/**
 * Profile photo card for any role. Supports two capture modes, both
 * validated identically by face-api.js before saving:
 *   - Camera: the existing live getUserMedia flow, capture to canvas.
 *   - Upload: a plain file input, loaded into a hidden <img> so
 *     detectDescriptor() (which accepts any image-like element) can
 *     run against it the same way it runs against the camera canvas.
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
  const uploadImgRef = useRef(null)
  const [uploadPreviewUrl, setUploadPreviewUrl] = useState(null)
  const [uploadReady, setUploadReady] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (!file) return

    camera.reset()
    setUploadReady(false)
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(URL.createObjectURL(file))
  }

  function clearUpload() {
    if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl)
    setUploadPreviewUrl(null)
    setUploadReady(false)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  async function saveFromElement(imageElement, toBlobFn) {
    setStatusMessage('Memeriksa wajah...')

    const { available, descriptor } = await face.detectDescriptor(imageElement)

    if (!available) {
      setStatusMessage('')
      alert('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba lagi atau gunakan perangkat/browser lain.')
      return
    }

    if (!descriptor) {
      setStatusMessage('')
      alert('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
      return
    }

    setStatusMessage('')
    setSubmitting(true)

    toBlobFn((blob) => {
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
    })
  }

  function handleSaveFromCamera() {
    if (camera.phase !== 'captured' || !camera.canvasRef.current) return
    saveFromElement(camera.canvasRef.current, (cb) => camera.canvasRef.current.toBlob(cb, 'image/jpeg', 0.8))
  }

  function handleSaveFromUpload() {
    if (!uploadImgRef.current) return
    saveFromElement(uploadImgRef.current, (cb) => {
      fetch(uploadPreviewUrl)
        .then((res) => res.blob())
        .then(cb)
    })
  }

  const showIdlePreview = camera.phase === 'idle' && !uploadPreviewUrl && profilePhotoUrl

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

        <video
          ref={camera.videoRef}
          className={cn('h-48 w-full rounded-lg border border-border bg-muted object-cover', camera.phase !== 'streaming' && 'hidden')}
          muted
          playsInline
        />
        <canvas ref={camera.canvasRef} className="hidden" />
        {camera.previewUrl && (
          <img src={camera.previewUrl} alt="Preview kamera" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}

        {uploadPreviewUrl && (
          <img
            ref={uploadImgRef}
            src={uploadPreviewUrl}
            alt="Preview upload"
            onLoad={() => setUploadReady(true)}
            className="h-48 w-full rounded-lg border border-border object-cover"
          />
        )}

        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex flex-wrap gap-2">
          {camera.phase === 'idle' && !uploadPreviewUrl && (
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

          {camera.phase === 'streaming' && (
            <Button type="button" onClick={camera.capture} className="flex-1">
              <Camera /> Ambil Foto
            </Button>
          )}

          {camera.phase === 'captured' && (
            <>
              <Button type="button" onClick={camera.retake} variant="outline" className="flex-1">
                <RefreshCw /> Ulangi
              </Button>
              <Button type="button" onClick={handleSaveFromCamera} disabled={submitting} className="flex-1">
                {submitting ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </>
          )}

          {uploadPreviewUrl && (
            <>
              <Button type="button" onClick={clearUpload} variant="outline" className="flex-1">
                <RefreshCw /> Batal
              </Button>
              <Button type="button" onClick={handleSaveFromUpload} disabled={submitting || !uploadReady} className="flex-1">
                {submitting ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
