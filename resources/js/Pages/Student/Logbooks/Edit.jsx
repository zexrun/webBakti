import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { NotebookPen, Save } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Edit({ logbook }) {
  const [imagePreview, setImagePreview] = useState(null)

  const { data, setData, post, processing, errors } = useForm({
    _method: 'put',
    title: logbook.title,
    activity_date: logbook.activity_date.slice(0, 10),
    feeling: logbook.feeling,
    start_time: logbook.start_time.slice(0, 5),
    end_time: logbook.end_time.slice(0, 5),
    description: logbook.description,
    file: null,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFileChange(e) {
    const file = e.target.files[0]
    setData('file', file)
    if (file) {
      const reader = new FileReader()
      reader.onload = (ev) => setImagePreview(ev.target.result)
      reader.readAsDataURL(file)
    } else {
      setImagePreview(null)
    }
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('student.logbooks.update', logbook.id), { forceFormData: true })
  }

  return (
    <StudentLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <div>
          <h1 className="flex items-center gap-2 text-2xl font-bold text-foreground">
            <NotebookPen className="h-6 w-6" /> Edit Laporan Harian
          </h1>
          <p className="text-muted-foreground">Perbarui laporan kegiatan magang Anda</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="title">Judul Kegiatan</Label>
                <Input
                  id="title"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                />
                {errors.title && <p className="text-sm text-destructive">{errors.title}</p>}
              </div>

              <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="activity_date">Tanggal Kegiatan</Label>
                  <Input
                    id="activity_date"
                    type="date"
                    value={data.activity_date}
                    onChange={(e) => setData('activity_date', e.target.value)}
                  />
                  {errors.activity_date && <p className="text-sm text-destructive">{errors.activity_date}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="feeling">Perasaan Hari Ini</Label>
                  <Select id="feeling" value={data.feeling} onChange={(e) => setData('feeling', e.target.value)}>
                    <option value="">Pilih perasaan</option>
                    <option value="Senang">😊 Senang</option>
                    <option value="Biasa Saja">😐 Biasa Saja</option>
                    <option value="Menemukan Kendala">😥 Menemukan Kendala</option>
                  </Select>
                  {errors.feeling && <p className="text-sm text-destructive">{errors.feeling}</p>}
                </div>
              </div>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-3 text-sm font-medium text-foreground">Waktu Kegiatan</p>
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="start_time">Waktu Mulai</Label>
                    <Input
                      id="start_time"
                      type="time"
                      value={data.start_time}
                      onChange={(e) => setData('start_time', e.target.value)}
                    />
                    {errors.start_time && <p className="text-sm text-destructive">{errors.start_time}</p>}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="end_time">Waktu Selesai</Label>
                    <Input
                      id="end_time"
                      type="time"
                      value={data.end_time}
                      onChange={(e) => setData('end_time', e.target.value)}
                    />
                    {errors.end_time && <p className="text-sm text-destructive">{errors.end_time}</p>}
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="description">Deskripsi Kegiatan</Label>
                <Textarea
                  id="description"
                  rows={6}
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && <p className="text-sm text-destructive">{errors.description}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="file">Ganti Lampiran Foto (Opsional)</Label>
                {logbook.file_path && !imagePreview && (
                  <img
                    src={`/storage/${logbook.file_path}`}
                    alt="Foto saat ini"
                    className="mb-2 h-40 w-full rounded-lg border border-border object-cover"
                  />
                )}
                <input
                  id="file"
                  type="file"
                  accept="image/*"
                  onChange={handleFileChange}
                  className="block w-full rounded-md border-2 border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                <p className="text-xs text-muted-foreground">Kosongkan jika tidak ingin mengganti foto</p>
                {imagePreview && (
                  <img src={imagePreview} alt="Preview" className="mt-3 h-48 w-full rounded-lg border border-border object-cover" />
                )}
                {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
              </div>

              <div className="flex justify-end gap-3 border-t border-border pt-6">
                <Link href={r('student.logbooks.show', logbook.id)}>
                  <Button type="button" variant="secondary">
                    Batal
                  </Button>
                </Link>
                <Button type="submit" disabled={processing}>
                  <Save className="h-4 w-4" /> Simpan Perubahan
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
