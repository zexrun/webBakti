import { Link, useForm } from '@inertiajs/react'
import { Save } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import StudentPicker from './StudentPicker'

function toDatetimeLocal(value) {
  if (!value) return ''
  const date = new Date(value)
  const pad = (n) => String(n).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

export default function Edit({ task, students, assignedStudents }) {
  const { data, setData, post, processing, errors } = useForm({
    _method: 'put',
    title: task.title,
    description: task.description,
    type: task.type,
    due_date: toDatetimeLocal(task.due_date),
    file: null,
    student_ids: assignedStudents,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.tasks.update', task.id), { forceFormData: true })
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <PageHeader title="Edit Tugas" description="Perbarui detail tugas di bawah ini" />

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="title">
                  Judul Tugas <span className="text-destructive">*</span>
                </Label>
                <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} />
                {errors.title && <p className="text-sm text-destructive">{errors.title}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="description">
                  Deskripsi <span className="text-destructive">*</span>
                </Label>
                <Textarea
                  id="description"
                  rows={4}
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && <p className="text-sm text-destructive">{errors.description}</p>}
              </div>

              <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="type">
                    Tipe Tugas <span className="text-destructive">*</span>
                  </Label>
                  <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                    <SelectTrigger id="type" className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="harian">Tugas Harian</SelectItem>
                      <SelectItem value="akhir">Laporan Akhir</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.type && <p className="text-sm text-destructive">{errors.type}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="due_date">Tenggat Waktu (Opsional)</Label>
                  <Input
                    id="due_date"
                    type="datetime-local"
                    value={data.due_date}
                    onChange={(e) => setData('due_date', e.target.value)}
                  />
                  {errors.due_date && <p className="text-sm text-destructive">{errors.due_date}</p>}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="file">Lampiran File (Opsional)</Label>
                {task.file_path && (
                  <FlashBanner type="success" className="p-3 font-normal">
                    File saat ini: {task.file_path.split('/').pop()}
                  </FlashBanner>
                )}
                <input
                  id="file"
                  type="file"
                  accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
                  onChange={(e) => setData('file', e.target.files[0])}
                  className="block w-full rounded-md border-2 border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                <p className="text-xs text-muted-foreground">Kosongkan jika tidak ingin mengganti file</p>
                {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
              </div>

              <div className="space-y-2">
                <Label>
                  Tugaskan Kepada <span className="text-destructive">*</span>
                </Label>
                <StudentPicker
                  students={students}
                  selectedIds={data.student_ids}
                  onChange={(ids) => setData('student_ids', ids)}
                />
                {errors.student_ids && <p className="text-sm text-destructive">{errors.student_ids}</p>}
              </div>

              <div className="flex flex-col justify-end gap-2 border-t border-border pt-5 sm:flex-row">
                <Button asChild type="button" variant="outline" className="w-full sm:w-auto">
                  <Link href={r('supervisor.tasks.show', task.id)}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                  <Save /> Simpan Perubahan
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
