import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, CheckCircle2, ExternalLink } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'

export default function Edit({ submission }) {
  const [gradePreview, setGradePreview] = useState(submission.grade ?? '-')
  const { data, setData, put, processing, errors } = useForm({
    grade: submission.grade ?? '',
    comments: submission.comments ?? '',
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleGradeChange(value) {
    setData('grade', value)
    setGradePreview(value || '-')
  }

  function handleSubmit(e) {
    e.preventDefault()
    put(r('supervisor.submissions.update', submission.id))
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div className="flex items-center gap-4">
          <Link href={r('supervisor.submissions.index')} className="text-muted-foreground hover:text-foreground">
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-foreground">Beri Nilai Submission</h1>
            <p className="text-muted-foreground">Tinjau dan nilai submission dari mahasiswa</p>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-2">
            <Card>
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Informasi Submission</h2>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm text-muted-foreground">Mahasiswa</p>
                    <p className="text-lg font-medium text-foreground">{submission.student.user.name}</p>
                    <p className="text-xs text-muted-foreground">{submission.student.user.email}</p>
                  </div>
                  <div>
                    <p className="text-sm text-muted-foreground">Tugas</p>
                    <p className="text-lg font-medium text-foreground">{submission.task.title}</p>
                    <p className="text-xs text-muted-foreground">{submission.task.type === 'harian' ? 'Tugas Harian' : 'Laporan Akhir'}</p>
                  </div>
                  <div>
                    <p className="text-sm text-muted-foreground">Disubmit</p>
                    <p className="text-lg font-medium text-foreground">
                      {new Date(submission.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </p>
                    <p className="text-xs text-muted-foreground">{new Date(submission.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
                  </div>
                  <div>
                    <p className="text-sm text-muted-foreground">Status</p>
                    <div className="mt-1">
                      {submission.grade ? <Badge variant="success">Sudah Dinilai</Badge> : <Badge variant="warning">Menunggu Nilai</Badge>}
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            {(submission.content || submission.file_path) && (
              <Card>
                <CardContent className="p-6">
                  <h2 className="mb-4 text-lg font-semibold text-foreground">Konten Submission</h2>
                  {submission.content && (
                    <div className="mb-4">
                      <h3 className="mb-2 text-sm font-medium text-foreground">Teks Submission</h3>
                      <div className="rounded-lg border border-border bg-muted p-4">
                        <p className="whitespace-pre-wrap text-foreground">{submission.content}</p>
                      </div>
                    </div>
                  )}
                  {submission.file_path && (
                    <div>
                      <h3 className="mb-2 text-sm font-medium text-foreground">File Submission</h3>
                      <a
                        href={`/storage/${submission.file_path}`}
                        target="_blank"
                        rel="noreferrer"
                        className="inline-flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-4 py-2 hover:bg-blue-100"
                      >
                        <ExternalLink className="h-4 w-4 text-blue-600" />
                        <span className="text-sm font-medium text-blue-600">{submission.file_path.split('/').pop()}</span>
                      </a>
                    </div>
                  )}
                </CardContent>
              </Card>
            )}

            <Card>
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Deskripsi Tugas</h2>
                <p className="leading-relaxed text-foreground/80">{submission.task.description}</p>
              </CardContent>
            </Card>
          </div>

          <div>
            <Card className="sticky top-6">
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Beri Nilai</h2>
                <form onSubmit={handleSubmit} className="space-y-4">
                  <div>
                    <label htmlFor="grade" className="mb-2 block text-sm font-medium text-foreground">
                      Nilai <span className="text-destructive">*</span>
                    </label>
                    <Select value={data.grade} onValueChange={(v) => handleGradeChange(v)}>
                      <SelectTrigger id="grade" className="w-full">
                        <SelectValue placeholder="Pilih Nilai" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="A">A (Sangat Baik)</SelectItem>
                        <SelectItem value="B">B (Baik)</SelectItem>
                        <SelectItem value="C">C (Cukup)</SelectItem>
                        <SelectItem value="D">D (Kurang)</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.grade && <p className="mt-1 text-sm text-destructive">{errors.grade}</p>}
                  </div>

                  <div>
                    <label htmlFor="comments" className="mb-2 block text-sm font-medium text-foreground">
                      Komentar <span className="text-xs text-muted-foreground">(Opsional)</span>
                    </label>
                    <Textarea
                      id="comments"
                      rows={6}
                      value={data.comments}
                      onChange={(e) => setData('comments', e.target.value)}
                      placeholder="Berikan feedback dan komentar untuk mahasiswa..."
                    />
                    {errors.comments && <p className="mt-1 text-sm text-destructive">{errors.comments}</p>}
                  </div>

                  <div className="rounded-lg border border-blue-200 bg-blue-50 p-3">
                    <div className="flex items-center justify-between">
                      <span className="text-sm text-gray-700">Preview Nilai:</span>
                      <span className="text-2xl font-bold text-blue-600">{gradePreview}</span>
                    </div>
                  </div>

                  <div className="flex flex-col gap-2 border-t border-border pt-4">
                    <Button type="submit" disabled={processing}>
                      <CheckCircle2 className="h-4 w-4" /> Simpan Nilai
                    </Button>
                    <Link href={r('supervisor.submissions.index')}>
                      <Button type="button" variant="secondary" className="w-full">Batal</Button>
                    </Link>
                  </div>
                </form>

                <div className="mt-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                  <p className="text-xs text-yellow-800">
                    <strong>Tips:</strong> Berikan feedback yang konstruktif dan spesifik untuk membantu mahasiswa meningkatkan pekerjaan mereka.
                  </p>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </SupervisorLayout>
  )
}
