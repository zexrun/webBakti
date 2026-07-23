import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { Camera, RefreshCw, UserRound } from 'lucide-react'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { useCamera } from '@/hooks/useCamera'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'

/**
 * Captures a live reference photo (not a file upload - the same
 * getUserMedia flow used for attendance) used as the face-matching
 * baseline for check-in verification. Rejects the capture client-side
 * if face-api.js can't find a face in it, since a reference without a
 * detectable face makes the matching feature useless.
 */
export default function ProfilePhotoCard({ profilePhotoUrl }) {
  const camera = useCamera()
  const face = useFaceDetection()
  const [submitting, setSubmitting] = useState(false)
  const [statusMessage, setStatusMessage] = useState('')

  useEffect(() => {
    face.ensureModelsLoaded()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  async function handleSave() {
    if (camera.phase !== 'captured' || !camera.canvasRef.current) return

    setStatusMessage('Memeriksa wajah...')

    const { available, descriptor } = await face.detectDescriptor(camera.canvasRef.current)

    if (!available) {
      setStatusMessage('')
      alert('Deteksi wajah tidak tersedia di perangkat ini. Silakan coba di perangkat/browser lain.')
      return
    }

    if (!descriptor) {
      setStatusMessage('')
      alert('Wajah tidak terdeteksi pada foto. Pastikan wajah Anda terlihat jelas dan coba lagi.')
      return
    }

    setStatusMessage('')
    setSubmitting(true)

    camera.canvasRef.current.toBlob(
      (blob) => {
        const formData = new FormData()
        formData.append('photo', blob, 'profile.jpg')
        formData.append('face_descriptor', JSON.stringify(descriptor))

        router.post(route('student.info.profile-photo.update'), formData, {
          preserveScroll: true,
          onFinish: () => {
            setSubmitting(false)
            camera.reset()
          },
        })
      },
      'image/jpeg',
      0.8,
    )
  }

  return (
    <Card className="self-start">
      <CardHeader className="border-b">
        <CardTitle className="flex items-center gap-2">
          <UserRound className="h-4 w-4 text-muted-foreground" /> Foto Profil
        </CardTitle>
        <CardDescription>Digunakan sebagai referensi verifikasi wajah saat check-in</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4 pt-6">
        {camera.phase === 'idle' && profilePhotoUrl && (
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
          <img src={camera.previewUrl} alt="Preview" className="h-48 w-full rounded-lg border border-border object-cover" />
        )}
        {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}
        {statusMessage && <p className="text-sm text-muted-foreground">{statusMessage}</p>}

        <div className="flex gap-2">
          {camera.phase === 'idle' && (
            <Button type="button" onClick={camera.start} className="flex-1">
              <Camera /> {profilePhotoUrl ? 'Ganti Foto' : 'Ambil Foto'}
            </Button>
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
              <Button type="button" onClick={handleSave} disabled={submitting} className="flex-1">
                {submitting ? 'Menyimpan...' : 'Simpan'}
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
