import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react'
import { CheckCircle2, XCircle, X } from 'lucide-react'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

function DecisionOption({ value, current, onSelect, icon: Icon, label, toneClass }) {
  const selected = current === value
  return (
    <label
      className={cn(
        'flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors duration-150',
        selected ? 'border-primary bg-indigo-50 dark:bg-indigo-500/10' : 'border-border hover:bg-muted',
      )}
    >
      <input
        type="radio"
        name="decision"
        value={value}
        checked={selected}
        onChange={() => onSelect(value)}
        className="accent-[var(--primary)]"
      />
      <Icon className={cn('h-5 w-5', toneClass)} />
      <span className={cn('text-sm font-medium', toneClass)}>{label}</span>
    </label>
  )
}

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
        <div className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg">
          <div className="border-b border-border px-6 py-4">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h3 className="text-lg font-semibold text-foreground">
                  {type === 'suspicious' ? 'Review Kehadiran Mencurigakan' : 'Detail Persetujuan'}
                </h3>
                <p className="mt-1 text-sm text-muted-foreground">
                  {item.userName} &middot; {item.date}
                </p>
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
          </div>

          <form onSubmit={handleSubmit} className="space-y-5 px-6 py-5">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Keputusan</label>
              <DecisionOption
                value="approve"
                current={decision}
                onSelect={setDecision}
                icon={CheckCircle2}
                label="Setujui"
                toneClass="text-green-700 dark:text-green-400"
              />
              <DecisionOption
                value="reject"
                current={decision}
                onSelect={setDecision}
                icon={XCircle}
                label="Tolak"
                toneClass="text-destructive"
              />
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

            <div className="flex gap-2">
              <Button type="button" variant="outline" onClick={onClose} className="flex-1">
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
