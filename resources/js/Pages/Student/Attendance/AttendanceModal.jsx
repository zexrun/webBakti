import { useEffect, useState } from 'react'
import { Camera, RefreshCw, X } from 'lucide-react'
import { Button } from '@/Components/ui/button'
import { Textarea } from '@/Components/ui/textarea'
import { useCamera } from '@/hooks/useCamera'
import { useGeolocation } from '@/hooks/useGeolocation'

const locationStyles = {
  loading: 'bg-blue-50 text-blue-600',
  success: 'bg-green-50 text-green-600',
  error: 'bg-red-50 text-red-600',
  idle: 'bg-muted text-muted-foreground',
}

export default function AttendanceModal({ open, onClose, title, subtitle, accent, notesPlaceholder, onSubmit, submitting }) {
  const camera = useCamera()
  const geo = useGeolocation()
  const [notes, setNotes] = useState('')

  useEffect(() => {
    if (open) {
      geo.request()
    } else {
      camera.reset()
      setNotes('')
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

    camera.canvasRef.current.toBlob(
      (blob) => onSubmit({ latitude: geo.position.latitude, longitude: geo.position.longitude, photo: blob, notes }),
      'image/jpeg',
      0.8,
    )
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-lg bg-background shadow-xl">
          <div className="flex-shrink-0 border-b border-border px-6 py-4">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="text-lg font-semibold text-foreground">{title}</h3>
                <p className="mt-1 text-sm text-muted-foreground">{subtitle}</p>
              </div>
              <button type="button" onClick={onClose} className="text-muted-foreground hover:text-foreground">
                <X className="h-6 w-6" />
              </button>
            </div>
          </div>

          <div className="flex-1 overflow-y-auto p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className={`rounded-lg p-4 ${locationStyles[geo.status]}`}>
                <p className="text-sm">{geo.message || '📍 Menunggu lokasi...'}</p>
              </div>

              <div className="space-y-4">
                <label className="block text-sm font-medium text-foreground">Foto Selfie</label>

                <video
                  ref={camera.videoRef}
                  className={`h-48 w-full rounded-lg border border-border bg-muted object-cover ${camera.phase === 'streaming' ? '' : 'hidden'}`}
                  muted
                  playsInline
                />
                <canvas ref={camera.canvasRef} className="hidden" />
                {camera.previewUrl && (
                  <img src={camera.previewUrl} alt="Preview" className="h-48 w-full rounded-lg border border-border object-cover" />
                )}
                {camera.error && <p className="text-sm text-destructive">{camera.error}</p>}

                <div className="flex gap-3">
                  {camera.phase === 'idle' && (
                    <Button type="button" onClick={camera.start} className="flex-1">
                      <Camera className="h-4 w-4" /> Buka Kamera
                    </Button>
                  )}
                  {camera.phase === 'streaming' && (
                    <Button type="button" onClick={camera.capture} variant="secondary" className="flex-1 bg-green-600 text-white hover:bg-green-700">
                      <Camera className="h-4 w-4" /> Ambil Foto
                    </Button>
                  )}
                  {camera.phase === 'captured' && (
                    <Button type="button" onClick={camera.retake} variant="secondary" className="flex-1 bg-yellow-600 text-white hover:bg-yellow-700">
                      <RefreshCw className="h-4 w-4" /> Ulangi
                    </Button>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-foreground">Catatan {notesPlaceholder.optional && '(Opsional)'}</label>
                <Textarea rows={4} value={notes} onChange={(e) => setNotes(e.target.value)} placeholder={notesPlaceholder.text} />
              </div>

              <Button type="submit" disabled={submitting || camera.phase !== 'captured'} className={`w-full ${accent}`}>
                {submitting ? 'Memproses...' : title}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
