import { Link, useForm, usePage } from '@inertiajs/react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Kirim Notifikasi Massal</h1>
            <p className="text-muted-foreground">Kirim pesan/notifikasi ke multiple mahasiswa sekaligus</p>
          </div>
          <Link href={r('supervisor.dashboard')} className="text-sm font-medium text-primary hover:underline">
            ← Kembali
          </Link>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}

        <Card>
          <CardContent className="space-y-6 p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-3">
                <Label>Tipe Penerima</Label>
                <label className="flex cursor-pointer items-center gap-3">
                  <input type="radio" name="type" checked={data.type === 'all'} onChange={() => setData('type', 'all')} />
                  <span className="text-foreground">Semua Mahasiswa</span>
                </label>
                <label className="flex cursor-pointer items-center gap-3">
                  <input type="radio" name="type" checked={data.type === 'selected'} onChange={() => setData('type', 'selected')} />
                  <span className="text-foreground">Pilih Mahasiswa Tertentu</span>
                </label>
              </div>

              {data.type === 'selected' && (
                <div className="space-y-2">
                  <Label>Pilih Mahasiswa</Label>
                  <div className="max-h-64 overflow-y-auto rounded-lg border border-border bg-muted p-3">
                    {students.length ? (
                      students.map((student) => (
                        <label key={student.id} className="flex cursor-pointer items-center rounded px-2 py-2 hover:bg-accent">
                          <input
                            type="checkbox"
                            checked={data.student_ids.includes(student.id)}
                            onChange={() => toggleStudent(student.id)}
                            className="rounded"
                          />
                          <span className="ml-3 text-sm text-foreground">
                            {student.user.name} ({student.nim})
                          </span>
                        </label>
                      ))
                    ) : (
                      <p className="text-sm text-muted-foreground">Belum ada mahasiswa</p>
                    )}
                  </div>
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
                <p className="text-xs text-muted-foreground">Maximum 1000 karakter</p>
                {errors.message && <p className="text-sm text-destructive">{errors.message}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="priority">Prioritas</Label>
                <Select id="priority" value={data.priority} onChange={(e) => setData('priority', e.target.value)}>
                  <option value="low">🟢 Low - Informasi umum</option>
                  <option value="normal">🟡 Normal - Pemberitahuan standar</option>
                  <option value="high">🟠 High - Perhatian dibutuhkan</option>
                  <option value="urgent">🔴 Urgent - Segera dibalas</option>
                </Select>
              </div>

              <div className="rounded-lg bg-muted p-4">
                <p className="mb-2 text-sm font-medium text-foreground">Preview:</p>
                <div className="rounded border border-border bg-background p-3">
                  <p className="text-sm font-semibold text-foreground">{data.title || 'Judul Notifikasi'}</p>
                  <p className="mt-2 text-sm text-muted-foreground">{data.message || 'Isi pesan anda akan muncul di sini...'}</p>
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit" disabled={processing} className="bg-green-600 hover:bg-green-700">
                  Kirim Notifikasi
                </Button>
                <Link href={r('supervisor.dashboard')}>
                  <Button type="button" variant="secondary">Batal</Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
