import { useState } from 'react'
import { useForm } from '@inertiajs/react'
import { Pencil, Award } from 'lucide-react'
import { Select } from '@/Components/ui/select'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'

export default function GradingForm({ submission }) {
  const [open, setOpen] = useState(false)
  const { data, setData, post, processing, errors } = useForm({
    grade: submission.grade ?? '',
    comments: submission.comments ?? '',
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.submissions.grade', submission.id), {
      preserveScroll: true,
      onSuccess: () => setOpen(false),
    })
  }

  return (
    <div className="rounded-lg bg-muted p-4">
      {submission.grade ? (
        <div className="mb-4 flex items-center justify-between">
          <div className="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <span className="text-sm text-muted-foreground">Nilai:</span>
              <Badge className="ml-2">{submission.grade}</Badge>
            </div>
            <div>
              <span className="text-sm text-muted-foreground">Komentar:</span>
              <span className="ml-2 text-sm text-foreground">{submission.comments ?? 'Tidak ada komentar'}</span>
            </div>
          </div>
          <button type="button" onClick={() => setOpen(!open)} className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
            <Pencil className="h-4 w-4" /> Edit Nilai
          </button>
        </div>
      ) : (
        <div className="py-4 text-center">
          <Award className="mx-auto mb-2 h-8 w-8 text-muted-foreground" />
          <p className="mb-3 text-sm text-muted-foreground">Belum ada penilaian</p>
          {!open && (
            <Button size="sm" onClick={() => setOpen(true)}>
              <Pencil className="h-4 w-4" /> Beri Nilai
            </Button>
          )}
        </div>
      )}

      {open && (
        <form onSubmit={handleSubmit} className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
          <div>
            <label className="mb-1 block text-sm font-medium text-foreground">Nilai</label>
            <Select value={data.grade} onChange={(e) => setData('grade', e.target.value)}>
              <option value="">Pilih Nilai</option>
              {['A', 'B', 'C', 'D'].map((g) => (
                <option key={g} value={g}>{g}</option>
              ))}
            </Select>
            {errors.grade && <p className="text-sm text-destructive">{errors.grade}</p>}
          </div>
          <div className="md:col-span-2">
            <label className="mb-1 block text-sm font-medium text-foreground">Komentar</label>
            <div className="flex gap-2">
              <Input
                value={data.comments}
                onChange={(e) => setData('comments', e.target.value)}
                placeholder="Berikan komentar untuk mahasiswa (opsional)"
              />
              <Button type="submit" disabled={processing}>Simpan</Button>
            </div>
            {errors.comments && <p className="text-sm text-destructive">{errors.comments}</p>}
          </div>
        </form>
      )}
    </div>
  )
}
