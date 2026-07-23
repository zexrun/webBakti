import { useEffect, useState } from 'react'
import { Camera, RefreshCw, X, MapPin } from 'lucide-react'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { useCamera } from '@/hooks/useCamera'
import { useGeolocation } from '@/hooks/useGeolocation'
import { useFaceDetection } from '@/hooks/useFaceDetection'
import { cn } from '@/lib/utils'

// Built-in Tailwind palette here (safe with opacity, unlike custom vars).
const locationStyles = {
  loading: 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300',
  success: 'border-green-200 bg-green-50 text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-300',
  error: 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300',
  idle: 'border-border bg-muted text-muted-foreground',
}

export default function AttendanceModal({ open, onClose, title, subtitle, destructive, notesPlaceholder, onSubmit, submitting, requireFaceCheck = false }) {
  const camera = useCamera()
  const geo = useGeolocation()
  const face = useFaceDetection()
  const [notes, setNotes] = useState('')
  const [faceCheckMessage, setFaceCheckMessage] = useState('')

  useEffect(() => {
    if (open) {
      geo.request()
      if (requireFaceCheck) face.ensureModelsLoaded()
    } else {
      camera.reset()
      setNotes('')
      setFaceCheckMessage('')
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])

  if (!open) return null

  async function handleSubmit(e) {
    e.preventDefault()
    if (!geo.position) {
      alert('Lokasi belum didapatkan, silakan tunggu...')
      geo.request()
      return
    }
    if (camera.phase !== 'captured' || !camera.canvasRef.current) {
      alert('Silakan ambil foto terlebih dahulu')
      return
    }

    let faceDescriptor = null

    if (requireFaceCheck) {
      setFaceCheckMessage('Memeriksa wajah...')
      const { available, descriptor } = await face.detectDescriptor(camera.canvasRef.current)
      setFaceCheckMessage('')

      if (available && !descriptor) {
        alert('Wajah tidak terdeteksi pada foto. Silakan pastikan wajah Anda terlihat jelas dan coba lagi.')
        return
      }

      faceDescriptor = descriptor
    }

    camera.canvasRef.current.toBlob(
      (blob) => onSubmit({
        latitude: geo.position.latitude,
        longitude: geo.position.longitude,
        photo: blob,
        notes,
        faceDescriptor,
      }),
      'image/jpeg',
      0.8,
    )
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-xl border border-border bg-card shadow-lg">
          <div className="flex shrink-0 items-start justify-between gap-4 border-b border-border px-6 py-4">
            <div>
              <h3 className="text-lg font-semibold text-foreground">{title}</h3>
              <p className="mt-1 text-sm text-muted-foreground">{subtitle}</p>
            </div>
            <button
              type="button"
              onClick={onClose}
              aria-label="Tutup"
              className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
            >
              <X className="h-5 w-5" />
            </button>
          </div>

          <div className="flex-1 overflow-y-auto p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className={cn('flex items-center gap-2 rounded-lg border p-3 text-sm', locationStyles[geo.status])}>
                <MapPin className="h-4 w-4 shrink-0" />
                <span>{geo.message || 'Menunggu lokasi...'}</span>
              </div>

              <div className="space-y-3">
                <Label>Foto Selfie</Label>

                <div className={cn('relative w-full overflow-hidden rounded-lg border border-border bg-muted', camera.phase !== 'streaming' && 'hidden')}>
                  <div className="aspect-video">
                    <video ref={camera.videoRef} className="h-full w-full object-cover" muted playsInline />
                  </div>
                </div>
                <canvas ref={camera.canvasRef} className="hidden" />
                {camera.previewUrl && (
                  <img src={camera.previewUrl} alt="Preview" className="aspect-video w-full rounded-lg border border-border object-cover" />
                )}
                {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}

                <div className="flex gap-2">
                  {camera.phase === 'idle' && (
                    <Button type="button" onClick={camera.start} className="flex-1">
                      <Camera /> Buka Kamera
                    </Button>
                  )}
                  {camera.phase === 'streaming' && (
                    <Button type="button" onClick={camera.capture} className="flex-1">
                      <Camera /> Ambil Foto
                    </Button>
                  )}
                  {camera.phase === 'captured' && (
                    <Button type="button" onClick={camera.retake} variant="outline" className="flex-1">
                      <RefreshCw /> Ulangi
                    </Button>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="notes">Catatan {notesPlaceholder.optional && '(Opsional)'}</Label>
                <Textarea id="notes" rows={4} value={notes} onChange={(e) => setNotes(e.target.value)} placeholder={notesPlaceholder.text} />
              </div>

              {faceCheckMessage && <p className="text-sm text-muted-foreground">{faceCheckMessage}</p>}

              <Button
                type="submit"
                variant={destructive ? 'destructive' : 'default'}
                disabled={submitting || camera.phase !== 'captured'}
                className="w-full"
              >
                {submitting ? 'Memproses...' : title}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
