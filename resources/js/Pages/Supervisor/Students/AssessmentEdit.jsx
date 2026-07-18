import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Save } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'

const grades = [
  { value: 'A', label: 'Sangat Baik', description: 'Kinerja luar biasa', color: 'text-green-600', border: 'peer-checked:border-green-500 peer-checked:bg-green-50' },
  { value: 'B', label: 'Baik', description: 'Kinerja baik', color: 'text-blue-600', border: 'peer-checked:border-blue-500 peer-checked:bg-blue-50' },
  { value: 'C', label: 'Cukup', description: 'Kinerja cukup', color: 'text-yellow-600', border: 'peer-checked:border-yellow-500 peer-checked:bg-yellow-50' },
  { value: 'D', label: 'Kurang', description: 'Perlu perbaikan', color: 'text-red-600', border: 'peer-checked:border-red-500 peer-checked:bg-red-50' },
]

const gradeBadgeColors = {
  A: 'bg-green-100 text-green-800',
  B: 'bg-blue-100 text-blue-800',
  C: 'bg-yellow-100 text-yellow-800',
  D: 'bg-red-100 text-red-800',
}

export default function AssessmentEdit({ student, assessment }) {
  const { data, setData, patch, processing, errors } = useForm({
    final_grade: assessment.final_grade,
    overall_comments: assessment.overall_comments,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    if (!confirm('Apakah Anda yakin ingin menyimpan perubahan penilaian ini?')) return
    patch(r('supervisor.students.assessment.update', student.id))
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div className="flex items-center gap-4">
            <Link href={r('supervisor.students.list.index')}>
              <button type="button" className="inline-flex items-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm font-medium text-foreground hover:bg-accent">
                <ArrowLeft className="h-4 w-4" /> Kembali
              </button>
            </Link>
            <div className="flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-orange-500 to-red-600">
                <span className="text-lg font-semibold text-white">{student.user.name.charAt(0).toUpperCase()}</span>
              </div>
              <div>
                <h1 className="text-xl font-bold text-foreground">Edit Penilaian Akhir</h1>
                <p className="text-sm text-muted-foreground">{student.user.name} • {student.nim ?? 'NIM belum diisi'}</p>
              </div>
            </div>
          </div>
          <p className="text-sm text-muted-foreground">
            Terakhir diupdate: {new Date(assessment.updated_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
          </p>
        </div>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-foreground">Penilaian Saat Ini</h3>
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Nilai Akhir</dt>
                <dd className="mt-1">
                  <span className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ${gradeBadgeColors[assessment.final_grade] ?? 'bg-secondary text-secondary-foreground'}`}>
                    {assessment.final_grade}
                  </span>
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Tanggal Penilaian</dt>
                <dd className="mt-1 text-sm text-foreground">
                  {new Date(assessment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </dd>
              </div>
              <div className="md:col-span-2">
                <dt className="text-sm font-medium text-muted-foreground">Komentar Sebelumnya</dt>
                <dd className="mt-1 rounded-md bg-muted p-3 text-sm text-foreground">{assessment.overall_comments}</dd>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-foreground">Informasi Mahasiswa</h3>
            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Nama Lengkap</dt>
                <dd className="mt-1 text-sm font-semibold text-foreground">{student.user.name}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">NIM</dt>
                <dd className="mt-1 text-sm text-foreground">{student.nim ?? 'Belum diisi'}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Universitas</dt>
                <dd className="mt-1 text-sm text-foreground">{student.universitas ?? 'Belum diisi'}</dd>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-1 text-lg font-semibold text-foreground">Edit Penilaian Akhir</h3>
            <p className="mb-6 text-sm text-muted-foreground">Perbarui penilaian dan komentar untuk mahasiswa</p>

            <form onSubmit={handleSubmit} className="space-y-8">
              <div className="space-y-4">
                <label className="text-lg font-semibold text-foreground">
                  Nilai Akhir <span className="text-destructive">*</span>
                </label>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                  {grades.map((grade) => (
                    <label key={grade.value} className="relative">
                      <input
                        type="radio"
                        name="final_grade"
                        value={grade.value}
                        checked={data.final_grade === grade.value}
                        onChange={(e) => setData('final_grade', e.target.value)}
                        className="peer sr-only"
                        required
                      />
                      <div className={`cursor-pointer rounded-lg border-2 border-border p-4 text-center transition-all hover:bg-accent ${grade.border}`}>
                        <div className={`mb-1 text-2xl font-bold ${grade.color}`}>{grade.value}</div>
                        <div className="mb-1 text-sm font-medium text-foreground">{grade.label}</div>
                        <div className="text-xs text-muted-foreground">{grade.description}</div>
                      </div>
                    </label>
                  ))}
                </div>
                {errors.final_grade && <p className="text-sm text-destructive">{errors.final_grade}</p>}
              </div>

              <div className="space-y-3">
                <label htmlFor="overall_comments" className="text-lg font-semibold text-foreground">
                  Komentar & Saran <span className="text-destructive">*</span>
                </label>
                <Textarea
                  id="overall_comments"
                  rows={6}
                  value={data.overall_comments}
                  onChange={(e) => setData('overall_comments', e.target.value)}
                  placeholder="Berikan komentar menyeluruh tentang kinerja mahasiswa, pencapaian yang menonjol, area yang perlu diperbaiki, dan saran untuk pengembangan karir selanjutnya..."
                  required
                />
                {errors.overall_comments && <p className="text-sm text-destructive">{errors.overall_comments}</p>}
              </div>

              <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <h4 className="text-sm font-semibold text-yellow-800">Perhatian</h4>
                <p className="mt-1 text-sm text-yellow-700">
                  Perubahan pada penilaian ini akan tercatat dalam sistem. Pastikan semua informasi sudah benar sebelum menyimpan.
                </p>
              </div>

              <div className="flex flex-col gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end">
                <Link href={r('supervisor.students.list.index')}>
                  <Button type="button" variant="secondary" className="w-full sm:w-auto">Batal</Button>
                </Link>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700">
                  <Save className="h-4 w-4" /> Simpan Perubahan
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <div className="rounded-lg border border-blue-200 bg-blue-50 p-6 text-sm text-blue-800">
          <h3 className="mb-2 text-lg font-semibold text-blue-900">Panduan Penilaian</h3>
          <div className="space-y-2">
            <p><strong>Nilai A (Sangat Baik):</strong> Mahasiswa menunjukkan kinerja luar biasa, melebihi ekspektasi dalam semua aspek.</p>
            <p><strong>Nilai B (Baik):</strong> Mahasiswa menunjukkan kinerja baik dan memenuhi sebagian besar ekspektasi.</p>
            <p><strong>Nilai C (Cukup):</strong> Mahasiswa menunjukkan kinerja yang memadai dengan beberapa area yang perlu diperbaiki.</p>
            <p><strong>Nilai D (Kurang):</strong> Mahasiswa menunjukkan kinerja di bawah ekspektasi dan memerlukan perbaikan signifikan.</p>
          </div>
        </div>
      </div>
    </SupervisorLayout>
  )
}
