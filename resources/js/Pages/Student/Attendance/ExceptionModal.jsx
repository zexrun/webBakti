import { useEffect } from 'react'
import { useForm } from '@inertiajs/react'
import { Send, X } from 'lucide-react'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import UploadProgress from '@/Components/UploadProgress'

export default function ExceptionModal({ open, onClose }) {
  const { data, setData, post, processing, progress, errors, reset } = useForm({
    date: new Date().toISOString().slice(0, 10),
    type: '',
    reason: '',
    attachment: null,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  useEffect(() => {
    if (!open) reset()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])

  if (!open) return null

  function handleSubmit(e) {
    e.preventDefault()
    post(r('student.attendance.exception'), {
      forceFormData: true,
      onSuccess: () => onClose(),
    })
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-xl border border-border bg-card shadow-lg">
          <div className="flex shrink-0 items-start justify-between gap-4 border-b border-border px-6 py-4">
            <div>
              <h3 className="text-lg font-semibold text-foreground">Ajukan Izin/Sakit</h3>
              <p className="mt-1 text-sm text-muted-foreground">Buat pengajuan izin atau sakit</p>
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
              <div className="space-y-2">
                <Label htmlFor="date">Tanggal</Label>
                <Input
                  id="date"
                  type="date"
                  max={new Date().toISOString().slice(0, 10)}
                  value={data.date}
                  onChange={(e) => setData('date', e.target.value)}
                />
                {errors.date && <p className="text-sm text-destructive">{errors.date}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="type">Jenis</Label>
                <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                  <SelectTrigger id="type" className="w-full">
                    <SelectValue placeholder="Pilih jenis..." />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="sick">Sakit</SelectItem>
                    <SelectItem value="leave">Cuti</SelectItem>
                    <SelectItem value="permit">Izin</SelectItem>
                    <SelectItem value="official">Dinas</SelectItem>
                  </SelectContent>
                </Select>
                {errors.type && <p className="text-sm text-destructive">{errors.type}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="reason">Alasan</Label>
                <Textarea
                  id="reason"
                  rows={4}
                  value={data.reason}
                  onChange={(e) => setData('reason', e.target.value)}
                  placeholder="Jelaskan alasan pengajuan..."
                />
                {errors.reason && <p className="text-sm text-destructive">{errors.reason}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="attachment">Lampiran (Opsional)</Label>
                <input
                  id="attachment"
                  type="file"
                  accept="image/*,.pdf"
                  onChange={(e) => setData('attachment', e.target.files[0])}
                  className="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                <p className="text-xs text-muted-foreground">Format: JPG, PNG, PDF (Max: 2MB)</p>
                {errors.attachment && <p className="text-sm text-destructive">{errors.attachment}</p>}
              </div>

              <UploadProgress progress={progress} />

              <Button type="submit" disabled={processing} className="w-full">
                <Send /> Ajukan Permohonan
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
