import { Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'
import StudentChecklist, { priorityOptions } from './StudentChecklist'

export default function SendNotification({ students }) {
  const { flash } = usePage().props
  const { data, setData, post, processing, errors } = useForm({
    type: 'all',
    student_ids: [],
    title: '',
    message: '',
    priority: 'normal',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function toggleStudent(studentId) {
    setData('student_ids', data.student_ids.includes(studentId)
      ? data.student_ids.filter((id) => id !== studentId)
      : [...data.student_ids, studentId])
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.bulk.send-notification'))
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <PageHeader
          title="Kirim Notifikasi Massal"
          description="Kirim pesan/notifikasi ke multiple mahasiswa sekaligus"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.dashboard')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label>Tipe Penerima</Label>
                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                  {[
                    { value: 'all', label: 'Semua Mahasiswa' },
                    { value: 'selected', label: 'Pilih Mahasiswa Tertentu' },
                  ].map((opt) => (
                    <label
                      key={opt.value}
                      className={cn(
                        'flex cursor-pointer items-center gap-3 rounded-lg border p-3 text-sm transition-colors duration-150',
                        data.type === opt.value ? 'border-primary bg-indigo-50 dark:bg-indigo-500/10' : 'border-border hover:bg-muted',
                      )}
                    >
                      <input
                        type="radio"
                        name="type"
                        checked={data.type === opt.value}
                        onChange={() => setData('type', opt.value)}
                        className="accent-[var(--primary)]"
                      />
                      <span className="text-foreground">{opt.label}</span>
                    </label>
                  ))}
                </div>
              </div>

              {data.type === 'selected' && (
                <div className="space-y-2">
                  <Label>Pilih Mahasiswa</Label>
                  <StudentChecklist students={students} selectedIds={data.student_ids} onToggle={toggleStudent} />
                  {errors.student_ids && <p className="text-sm text-destructive">{errors.student_ids}</p>}
                </div>
              )}

              <div className="space-y-2">
                <Label htmlFor="title">Judul Notifikasi</Label>
                <Input
                  id="title"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                  placeholder="Contoh: Penting - Update Jadwal Magang"
                  required
                />
                {errors.title && <p className="text-sm text-destructive">{errors.title}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="message">Pesan</Label>
                <Textarea
                  id="message"
                  rows={5}
                  value={data.message}
                  onChange={(e) => setData('message', e.target.value)}
                  placeholder="Ketik pesan anda..."
                  maxLength={1000}
                  required
                />
                <p className="text-xs text-muted-foreground">Maksimum 1000 karakter</p>
                {errors.message && <p className="text-sm text-destructive">{errors.message}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="priority">Prioritas</Label>
                <Select value={data.priority} onValueChange={(v) => setData('priority', v)}>
                  <SelectTrigger id="priority" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {priorityOptions.map((option) => (
                      <SelectItem key={option.value} value={option.value}>
                        <span className="flex items-center gap-2">
                          <span className={cn('h-2 w-2 shrink-0 rounded-full', option.dot)} />
                          {option.label}
                          <span className="text-muted-foreground">— {option.hint}</span>
                        </span>
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">Preview</p>
                <div className="rounded-lg border border-border bg-card p-4">
                  <p className="text-sm font-semibold text-foreground">{data.title || 'Judul Notifikasi'}</p>
                  <p className="mt-2 text-sm text-muted-foreground">{data.message || 'Isi pesan anda akan muncul di sini...'}</p>
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-5">
                <Button asChild type="button" variant="outline">
                  <Link href={r('supervisor.dashboard')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>Kirim Notifikasi</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
