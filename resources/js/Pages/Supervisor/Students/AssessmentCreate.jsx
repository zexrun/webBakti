import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, CheckCircle2, XCircle, Save } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { GradeSelector, InfoRow, GradingGuide } from './assessment-shared'

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
        <PageHeader
          title="Penilaian Akhir Magang"
          description={`${student.user.name} · ${student.nim ?? 'NIM belum diisi'}`}
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.students.list.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Informasi Mahasiswa</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-3">
            <InfoRow label="Nama Lengkap" value={student.user.name} />
            <InfoRow label="NIM" value={student.nim} />
            <InfoRow label="Universitas" value={student.universitas} />
            <InfoRow label="Email" value={student.user.email} />
            <div>
              <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Status Dokumen</p>
              <Badge variant={documentsComplete ? 'success' : 'warning'} className="mt-1">
                {documentsComplete ? <CheckCircle2 /> : <XCircle />}
                {documentsComplete ? 'Lengkap' : 'Belum Lengkap'}
              </Badge>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Form Penilaian Akhir</CardTitle>
            <CardDescription>Berikan penilaian komprehensif terhadap kinerja mahasiswa selama periode magang</CardDescription>
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
                  rows={6}
                  value={data.overall_comments}
                  onChange={(e) => setData('overall_comments', e.target.value)}
                  placeholder="Berikan komentar menyeluruh tentang kinerja mahasiswa, pencapaian yang menonjol, area yang perlu diperbaiki, dan saran untuk pengembangan karir selanjutnya..."
                  required
                />
                {errors.overall_comments && <p className="text-sm text-destructive">{errors.overall_comments}</p>}
              </div>

              <div className="flex flex-col justify-end gap-2 border-t border-border pt-5 sm:flex-row">
                <Button asChild type="button" variant="outline" className="w-full sm:w-auto">
                  <Link href={r('supervisor.students.list.index')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                  <Save /> Simpan Penilaian
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <GradingGuide />
      </div>
    </SupervisorLayout>
  )
}
