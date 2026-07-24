import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Save } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'
import { GradeSelector, InfoRow, GradingGuide, gradeBadgeClass } from './assessment-shared'
import { useConfirm } from '@/hooks/useConfirm'

export default function AssessmentEdit({ student, assessment }) {
  const { data, setData, patch, processing, errors } = useForm({
    final_grade: assessment.final_grade,
    overall_comments: assessment.overall_comments,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const confirm = useConfirm()

  async function handleSubmit(e) {
    e.preventDefault()
    const confirmed = await confirm({ title: 'Simpan perubahan penilaian ini?' })
    if (!confirmed) return
    patch(r('supervisor.students.assessment.update', student.id))
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Edit Penilaian Akhir"
          description={`${student.user.name} · ${student.nim ?? 'NIM belum diisi'}`}
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.students.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card className="lg:col-span-2">
            <CardHeader className="border-b">
              <CardTitle>Edit Penilaian Akhir</CardTitle>
              <CardDescription>Perbarui penilaian dan komentar untuk mahasiswa</CardDescription>
            </CardHeader>
            <CardContent className="pt-6">
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="space-y-3">
                  <Label>Nilai Akhir <span className="text-destructive">*</span></Label>
                  <GradeSelector value={data.final_grade} onChange={(v) => setData('final_grade', v)} />
                  {errors.final_grade && <p className="text-sm text-destructive">{errors.final_grade}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="overall_comments">Komentar & Saran <span className="text-destructive">*</span></Label>
                  <Textarea
                    id="overall_comments"
                    rows={8}
                    value={data.overall_comments}
                    onChange={(e) => setData('overall_comments', e.target.value)}
                    placeholder="Berikan komentar menyeluruh tentang kinerja mahasiswa..."
                    required
                  />
                  {errors.overall_comments && <p className="text-sm text-destructive">{errors.overall_comments}</p>}
                </div>

                <FlashBanner type="warning">
                  <strong>Perhatian:</strong> Perubahan pada penilaian ini akan tercatat dalam sistem. Pastikan semua informasi sudah benar sebelum menyimpan.
                </FlashBanner>

                <div className="flex flex-col justify-end gap-2 border-t border-border pt-5 sm:flex-row">
                  <Button asChild type="button" variant="outline" className="w-full sm:w-auto">
                    <Link href={r('supervisor.students.index')}>Batal</Link>
                  </Button>
                  <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                    <Save /> Simpan Perubahan
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>

          <div className="space-y-6 self-start">
            <Card>
              <CardHeader className="border-b">
                <CardTitle>Penilaian Saat Ini</CardTitle>
                <CardDescription>
                  Terakhir diupdate {new Date(assessment.updated_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4 pt-6">
                <div>
                  <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Nilai Akhir</p>
                  <span className={cn('mt-1 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium', gradeBadgeClass[assessment.final_grade] ?? 'bg-secondary text-secondary-foreground')}>
                    {assessment.final_grade}
                  </span>
                </div>
                <InfoRow
                  label="Tanggal Penilaian"
                  value={new Date(assessment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                />
                <div>
                  <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Komentar Sebelumnya</p>
                  <p className="mt-1 rounded-md bg-muted p-3 text-sm text-foreground">{assessment.overall_comments}</p>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="border-b">
                <CardTitle>Informasi Mahasiswa</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4 pt-6">
                <InfoRow label="Nama Lengkap" value={student.user.name} />
                <InfoRow label="NIM" value={student.nim} />
                <InfoRow label="Universitas" value={student.university} />
              </CardContent>
            </Card>

            <GradingGuide />
          </div>
        </div>
      </div>
    </SupervisorLayout>
  )
}
