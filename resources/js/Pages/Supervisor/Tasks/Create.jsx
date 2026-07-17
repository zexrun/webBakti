import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Save } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import StudentPicker from './StudentPicker'

export default function Create({ students }) {
  const { data, setData, post, processing, errors } = useForm({
    title: '',
    description: '',
    type: 'harian',
    due_date: '',
    file: null,
    student_ids: [],
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.tasks.store'), { forceFormData: true })
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <div className="flex items-center gap-4">
          <Link href={r('supervisor.tasks.index')} className="text-muted-foreground hover:text-foreground">
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-foreground">Buat Tugas Baru</h1>
            <p className="text-muted-foreground">Isi detail di bawah ini untuk memberikan tugas baru kepada mahasiswa bimbingan</p>
          </div>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="title">
                  Judul Tugas <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="title"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                  placeholder="Masukkan judul tugas"
                />
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
                  placeholder="Jelaskan detail tugas dan instruksi yang perlu diikuti"
                />
                {errors.description && <p className="text-sm text-destructive">{errors.description}</p>}
              </div>

              <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="type">
                    Tipe Tugas <span className="text-destructive">*</span>
                  </Label>
                  <Select id="type" value={data.type} onChange={(e) => setData('type', e.target.value)}>
                    <option value="harian">Tugas Harian</option>
                    <option value="akhir" disabled>
                      Laporan Akhir
                    </option>
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
                    min={new Date().toISOString().slice(0, 16)}
                  />
                  {errors.due_date && <p className="text-sm text-destructive">{errors.due_date}</p>}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="file">Lampiran File (Opsional)</Label>
                <input
                  id="file"
                  type="file"
                  accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
                  onChange={(e) => setData('file', e.target.files[0])}
                  className="block w-full rounded-md border-2 border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                <p className="text-xs text-muted-foreground">PDF, DOC, PPT, XLS, gambar (max 10MB)</p>
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

              <div className="flex flex-col justify-end gap-3 border-t border-border pt-6 sm:flex-row">
                <Link href={r('supervisor.tasks.index')}>
                  <Button type="button" variant="secondary" className="w-full sm:w-auto">
                    Batal
                  </Button>
                </Link>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                  <Save className="h-4 w-4" /> Simpan Tugas
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
