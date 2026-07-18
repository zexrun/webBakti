import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, CheckCircle2, XCircle, Save } from 'lucide-react'
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

export default function AssessmentCreate({ student }) {
  const { data, setData, post, processing, errors } = useForm({
    final_grade: '',
    overall_comments: '',
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const hasProposal = student.documents.some((d) => d.type === 'proposal')
  const hasLaporanAkhir = student.documents.some((d) => d.type === 'laporan_akhir')
  const documentsComplete = hasProposal && hasLaporanAkhir

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.students.assessment.store', student.id))
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <div className="flex items-center gap-4">
          <Link href={r('supervisor.students.list.index')}>
            <button type="button" className="inline-flex items-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm font-medium text-foreground hover:bg-accent">
              <ArrowLeft className="h-4 w-4" /> Kembali
            </button>
          </Link>
          <div className="flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600">
              <span className="text-lg font-semibold text-white">{student.user.name.charAt(0).toUpperCase()}</span>
            </div>
            <div>
              <h1 className="text-xl font-bold text-foreground">Penilaian Akhir Magang</h1>
              <p className="text-sm text-muted-foreground">{student.user.name} • {student.nim ?? 'NIM belum diisi'}</p>
            </div>
          </div>
        </div>

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
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Email</dt>
                <dd className="mt-1 text-sm text-foreground">{student.user.email}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Status Dokumen</dt>
                <dd className="mt-1">
                  {documentsComplete ? (
                    <span className="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                      <CheckCircle2 className="h-3 w-3" /> Lengkap
                    </span>
                  ) : (
                    <span className="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                      <XCircle className="h-3 w-3" /> Belum Lengkap
                    </span>
                  )}
                </dd>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-1 text-lg font-semibold text-foreground">Form Penilaian Akhir</h3>
            <p className="mb-6 text-sm text-muted-foreground">Berikan penilaian komprehensif terhadap kinerja mahasiswa selama periode magang</p>

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

              <div className="flex flex-col gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end">
                <Link href={r('supervisor.students.list.index')}>
                  <Button type="button" variant="secondary" className="w-full sm:w-auto">Batal</Button>
                </Link>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                  <Save className="h-4 w-4" /> Simpan Penilaian
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
