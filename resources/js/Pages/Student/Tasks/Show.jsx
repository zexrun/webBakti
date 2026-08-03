import { useRef, useState } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, FileText, Paperclip, Download, User, Tag, Clock, Send, CheckCircle2 } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { formatDeadline, deadlineToneClass } from '@/lib/deadline'
import { cn } from '@/lib/utils'

function InfoTile({ icon: Icon, label, children }) {
  return (
    <div className="flex items-start gap-3 rounded-lg bg-muted p-3">
      <Icon className="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
      <div className="min-w-0">
        <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">{label}</p>
        {children}
      </div>
    </div>
  )
}

export default function Show({ task, submission }) {
  const { flash } = usePage().props
  const fileInputRef = useRef(null)
  const [fileName, setFileName] = useState(null)

  const { data, setData, post, processing, errors } = useForm({ content: '', file: null })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const isSubmitted = Boolean(submission)
  const deadline = formatDeadline(task.due_date)

  function handleFileChange(e) {
    const file = e.target.files[0]
    setData('file', file)
    setFileName(file?.name ?? null)
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('student.tasks.submit', task.id), { forceFormData: true })
  }

  const statusBadge = isSubmitted
    ? <Badge variant="success"><CheckCircle2 /> Sudah Dikumpulkan</Badge>
    : deadline.isOverdue
      ? <Badge variant="destructive">Terlambat</Badge>
      : <Badge variant="warning">Belum Dikumpulkan</Badge>

  return (
    <StudentLayout>
      <div className="mx-auto max-w-5xl space-y-6">
        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}
        {flash?.error && <FlashBanner type="danger">{flash.error}</FlashBanner>}

        <PageHeader
          title="Detail Tugas"
          description="Informasi lengkap dan submission tugas"
          actions={
            <>
              {statusBadge}
              <Button asChild variant="outline" size="sm">
                <Link href={r('student.tasks.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
            </>
          }
        />

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-2">
            <Card>
              <CardHeader className="border-b">
                <CardTitle>{task.title}</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4 pt-6">
                <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                  {task.description}
                </div>
                {task.file_path && (
                  <div className="flex flex-col gap-3 rounded-lg border border-border p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-3">
                      <Paperclip className="h-5 w-5 text-muted-foreground" />
                      <div>
                        <p className="text-sm font-medium text-foreground">Lampiran Tugas</p>
                        <p className="text-xs text-muted-foreground">File pendukung dari pembimbing</p>
                      </div>
                    </div>
                    <Button asChild size="sm" variant="outline">
                      <a href={`/storage/${task.file_path}`} target="_blank" rel="noreferrer">
                        <Download /> Unduh Lampiran
                      </a>
                    </Button>
                  </div>
                )}
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="border-b">
                <CardTitle>{isSubmitted ? 'Submission Anda' : 'Kumpulkan Tugas'}</CardTitle>
              </CardHeader>
              <CardContent className="pt-6">
                {isSubmitted ? (
                  <div className="space-y-4">
                    <FlashBanner type="success">
                      Tugas berhasil dikumpulkan pada{' '}
                      {new Date(submission.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </FlashBanner>

                    {submission.content && (
                      <div>
                        <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                          <FileText className="h-4 w-4 text-muted-foreground" /> Laporan Teks
                        </p>
                        <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                          {submission.content}
                        </div>
                      </div>
                    )}

                    {submission.file_path && (
                      <div className="flex items-center justify-between rounded-lg border border-border p-4">
                        <div>
                          <p className="text-sm font-medium text-foreground">File Submission</p>
                          <p className="text-xs text-muted-foreground">Klik untuk melihat file</p>
                        </div>
                        <Button asChild size="sm" variant="outline">
                          <a href={`/storage/${submission.file_path}`} target="_blank" rel="noreferrer">Lihat File</a>
                        </Button>
                      </div>
                    )}

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                      <div>
                        <p className="mb-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">Nilai</p>
                        <div className="rounded-lg border border-border bg-muted p-4">
                          <p className={cn('text-lg font-bold', submission.grade ? 'text-green-700 dark:text-green-400' : 'text-muted-foreground')}>
                            {submission.grade ?? 'Belum dinilai'}
                          </p>
                        </div>
                      </div>
                      <div>
                        <p className="mb-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">Komentar Pembimbing</p>
                        <div className="rounded-lg border border-border bg-muted p-4">
                          <p className="text-sm text-foreground">
                            {submission.comments ?? 'Belum ada komentar dari pembimbing'}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                ) : (
                  <form onSubmit={handleSubmit} className="space-y-5">
                    <div className="space-y-2">
                      <Label htmlFor="content">Laporan Teks (Opsional)</Label>
                      <Textarea
                        id="content"
                        rows={6}
                        value={data.content}
                        onChange={(e) => setData('content', e.target.value)}
                        placeholder="Tulis laporan atau penjelasan mengenai tugas yang Anda kerjakan..."
                      />
                      {errors.content && <p className="text-sm text-destructive">{errors.content}</p>}
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="file">Upload File (Opsional)</Label>
                      <input
                        id="file"
                        ref={fileInputRef}
                        type="file"
                        onChange={handleFileChange}
                        className="block w-full rounded-md border border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                      />
                      <p className="text-xs text-muted-foreground">PDF, DOCX, ZIP, atau format lainnya hingga 10MB</p>
                      {fileName && <p className="text-xs text-primary">{fileName}</p>}
                      {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
                    </div>

                    <div className="flex justify-end border-t border-border pt-5">
                      <Button type="submit" disabled={processing}>
                        <Send /> Kumpulkan Tugas
                      </Button>
                    </div>
                  </form>
                )}
              </CardContent>
            </Card>
          </div>

          <div className="space-y-6">
            <Card>
              <CardHeader className="border-b">
                <CardTitle>Informasi Tugas</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3 pt-6">
                <InfoTile icon={User} label="Pembimbing">
                  <p className="text-sm font-medium text-foreground">{task.supervisor?.user?.name}</p>
                </InfoTile>
                <InfoTile icon={Tag} label="Tipe Tugas">
                  <p className="text-sm font-medium text-foreground">{task.type === 'daily' ? 'Harian' : 'Laporan Akhir'}</p>
                </InfoTile>
                <InfoTile icon={Clock} label="Deadline">
                  {task.due_date ? (
                    <>
                      <p className="text-sm font-medium tabular-nums text-foreground">
                        {new Date(task.due_date).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>
                      <p className={cn('text-xs font-medium', deadlineToneClass[deadline.color])}>{deadline.text}</p>
                    </>
                  ) : (
                    <p className="text-sm font-medium text-foreground">Tidak ada deadline</p>
                  )}
                </InfoTile>
              </CardContent>
            </Card>

            {isSubmitted && (
              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Progress Tugas</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3 pt-6">
                  <div className="flex items-start gap-3">
                    <div className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-green-500" />
                    <div className="text-sm">
                      <p className="font-medium text-foreground">Tugas dikumpulkan</p>
                      <p className="tabular-nums text-muted-foreground">
                        {new Date(submission.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <div className={cn('mt-1.5 h-2 w-2 shrink-0 rounded-full', submission.grade ? 'bg-blue-500' : 'bg-amber-500')} />
                    <div className="text-sm">
                      <p className="font-medium text-foreground">{submission.grade ? 'Sudah dinilai' : 'Menunggu penilaian'}</p>
                      <p className="text-muted-foreground">{submission.grade ? `Nilai: ${submission.grade}` : 'Pembimbing sedang review'}</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </div>
      </div>
    </StudentLayout>
  )
}
