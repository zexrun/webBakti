import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react'
import { CheckCircle2, XCircle, X } from 'lucide-react'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'

export default function ApprovalModal({ open, onClose, type, item, notesRequired = false }) {
  const [decision, setDecision] = useState('')
  const { data, setData, post, processing, errors, reset } = useForm({ action: '', notes: '' })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  useEffect(() => {
    if (!open) {
      setDecision('')
      reset()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])

  if (!open || !item) return null

  function handleSubmit(e) {
    e.preventDefault()
    if (!decision) {
      alert('Pilih keputusan terlebih dahulu')
      return
    }
    setData('action', decision)

    const endpoint = type === 'suspicious'
      ? r('admin.attendance.suspicious.review', item.id)
      : r('admin.attendance.approve', [item.approvalType, item.id])

    post(endpoint, {
      data: { action: decision, notes: data.notes },
      preserveScroll: true,
      onSuccess: () => onClose(),
    })
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-md rounded-lg bg-background shadow-xl">
          <div className="border-b border-border px-6 py-4">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="text-lg font-semibold text-foreground">
                  {type === 'suspicious' ? 'Review Kehadiran Mencurigakan' : 'Detail Persetujuan'}
                </h3>
                <p className="mt-1 text-sm text-muted-foreground">
                  {item.userName} &middot; {item.date}
                </p>
              </div>
              <button type="button" onClick={onClose} className="text-muted-foreground hover:text-foreground">
                <X className="h-6 w-6" />
              </button>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6 px-6 py-4">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Keputusan</label>
              <label className="flex cursor-pointer items-center gap-3 rounded-lg border border-border p-3 hover:bg-accent">
                <input type="radio" name="decision" value="approve" checked={decision === 'approve'} onChange={() => setDecision('approve')} />
                <CheckCircle2 className="h-5 w-5 text-green-600" />
                <span className="font-medium text-green-600">Setujui</span>
              </label>
              <label className="flex cursor-pointer items-center gap-3 rounded-lg border border-border p-3 hover:bg-accent">
                <input type="radio" name="decision" value="reject" checked={decision === 'reject'} onChange={() => setDecision('reject')} />
                <XCircle className="h-5 w-5 text-red-600" />
                <span className="font-medium text-red-600">Tolak</span>
              </label>
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">
                Catatan {notesRequired ? '' : '(Opsional)'}
              </label>
              <Textarea
                rows={4}
                value={data.notes}
                onChange={(e) => setData('notes', e.target.value)}
                placeholder="Tambahkan catatan..."
                required={notesRequired}
              />
              {errors.notes && <p className="text-sm text-destructive">{errors.notes}</p>}
            </div>

            <div className="flex gap-3">
              <Button type="button" variant="secondary" onClick={onClose} className="flex-1">
                Batal
              </Button>
              <Button type="submit" disabled={processing} className="flex-1">
                Simpan Keputusan
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
