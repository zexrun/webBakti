import { useRef, useState } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, FileText, Paperclip, Download, User, Tag, Clock, Send, CheckCircle2 } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { formatDeadline } from '@/lib/deadline'

export default function Show({ task, submission }) {
  const { flash } = usePage().props
  const fileInputRef = useRef(null)
  const [fileName, setFileName] = useState(null)

  const { data, setData, post, processing, errors } = useForm({
    content: '',
    file: null,
  })

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

  return (
    <StudentLayout>
      <div className="mx-auto max-w-5xl space-y-6">
        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
            {flash.success}
          </div>
        )}
        {flash?.error && (
          <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
            {flash.error}
          </div>
        )}

        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Detail Tugas</h1>
            <p className="text-muted-foreground">Informasi lengkap dan submission tugas 📝</p>
          </div>
          <div className="flex items-center gap-3">
            {isSubmitted ? (
              <Badge variant="success" className="gap-1">
                <CheckCircle2 className="h-3 w-3" /> Sudah Dikumpulkan
              </Badge>
            ) : deadline.isOverdue ? (
              <Badge variant="destructive">Terlambat</Badge>
            ) : (
              <Badge variant="warning">Belum Dikumpulkan</Badge>
            )}
            <Link href={r('student.tasks.index')}>
              <Button variant="secondary">
                <ArrowLeft className="h-4 w-4" /> Kembali
              </Button>
            </Link>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle className="text-base">{task.title}</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                  {task.description}
                </div>
                {task.file_path && (
                  <div className="flex items-center justify-between rounded-lg border border-border bg-muted p-4">
                    <div className="flex items-center gap-3">
                      <Paperclip className="h-5 w-5 text-muted-foreground" />
                      <div>
                        <p className="text-sm font-medium text-foreground">Lampiran Tugas</p>
                        <p className="text-xs text-muted-foreground">File pendukung dari pembimbing</p>
                      </div>
                    </div>
                    <a
                      href={`/storage/${task.file_path}`}
                      target="_blank"
                      rel="noreferrer"
                      className="inline-flex items-center gap-2 rounded-lg bg-foreground px-4 py-2 text-sm font-medium text-background hover:opacity-90"
                    >
                      <Download className="h-4 w-4" /> Unduh Lampiran
                    </a>
                  </div>
                )}
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-base">
                  {isSubmitted ? 'Submission Anda' : 'Kumpulkan Tugas'}
                </CardTitle>
              </CardHeader>
              <CardContent>
                {isSubmitted ? (
                  <div className="space-y-4">
                    <div className="rounded-lg border border-green-200 bg-green-50 p-4">
                      <p className="font-semibold text-green-800">Tugas berhasil dikumpulkan!</p>
                      <p className="text-sm text-green-700">
                        Dikumpulkan pada{' '}
                        {new Date(submission.created_at).toLocaleString('id-ID', {
                          day: '2-digit',
                          month: 'short',
                          year: 'numeric',
                          hour: '2-digit',
                          minute: '2-digit',
                        })}
                      </p>
                    </div>

                    {submission.content && (
                      <div>
                        <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                          <FileText className="h-4 w-4" /> Laporan Teks
                        </p>
                        <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                          {submission.content}
                        </div>
                      </div>
                    )}

                    {submission.file_path && (
                      <div className="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <div>
                          <p className="text-sm font-medium text-foreground">File Submission</p>
                          <p className="text-xs text-muted-foreground">Klik untuk melihat file</p>
                        </div>
                        <a
                          href={`/storage/${submission.file_path}`}
                          target="_blank"
                          rel="noreferrer"
                          className="rounded-lg bg-blue-100 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-200"
                        >
                          Lihat File
                        </a>
                      </div>
                    )}

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                      <div>
                        <p className="mb-2 text-sm font-medium text-foreground">Nilai</p>
                        <div className={`rounded-lg border p-4 ${submission.grade ? 'border-green-200 bg-green-50' : 'border-border bg-muted'}`}>
                          <p className={`text-lg font-bold ${submission.grade ? 'text-green-800' : 'text-muted-foreground'}`}>
                            {submission.grade ?? 'Belum dinilai'}
                          </p>
                        </div>
                      </div>
                      <div>
                        <p className="mb-2 text-sm font-medium text-foreground">Status Review</p>
                        <div className={`rounded-lg border p-4 ${submission.comments ? 'border-blue-200 bg-blue-50' : 'border-border bg-muted'}`}>
                          <p className={`text-sm ${submission.comments ? 'text-blue-800' : 'text-muted-foreground'}`}>
                            {submission.comments ?? 'Belum ada komentar dari pembimbing'}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                ) : (
                  <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="space-y-2">
                      <label className="text-sm font-medium text-foreground">Laporan Teks (Opsional)</label>
                      <Textarea
                        rows={6}
                        value={data.content}
                        onChange={(e) => setData('content', e.target.value)}
                        placeholder="Tulis laporan atau penjelasan mengenai tugas yang Anda kerjakan..."
                      />
                      {errors.content && <p className="text-sm text-destructive">{errors.content}</p>}
                    </div>

                    <div className="space-y-2">
                      <label className="text-sm font-medium text-foreground">Upload File (Opsional)</label>
                      <input
                        ref={fileInputRef}
                        type="file"
                        onChange={handleFileChange}
                        className="block w-full rounded-md border-2 border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                      />
                      <p className="text-xs text-muted-foreground">PDF, DOCX, ZIP, atau format lainnya hingga 10MB</p>
                      {fileName && <p className="text-xs text-primary">{fileName}</p>}
                      {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
                    </div>

                    <div className="flex justify-end border-t border-border pt-6">
                      <Button type="submit" disabled={processing}>
                        <Send className="h-4 w-4" /> Kumpulkan Tugas
                      </Button>
                    </div>
                  </form>
                )}
              </CardContent>
            </Card>
          </div>

          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Informasi Tugas</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <div className="flex items-center gap-3 rounded-lg bg-muted p-3">
                  <User className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="text-xs uppercase tracking-wide text-muted-foreground">Pembimbing</p>
                    <p className="text-sm font-semibold text-foreground">{task.supervisor?.user?.name}</p>
                  </div>
                </div>
                <div className="flex items-center gap-3 rounded-lg bg-muted p-3">
                  <Tag className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="text-xs uppercase tracking-wide text-muted-foreground">Tipe Tugas</p>
                    <p className="text-sm font-semibold text-foreground">{task.type === 'harian' ? 'Harian' : 'Laporan Akhir'}</p>
                  </div>
                </div>
                <div className="flex items-center gap-3 rounded-lg bg-muted p-3">
                  <Clock className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="text-xs uppercase tracking-wide text-muted-foreground">Deadline</p>
                    {task.due_date ? (
                      <>
                        <p className="text-sm font-semibold text-foreground">
                          {new Date(task.due_date).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </p>
                        <p className={`text-xs font-medium text-${deadline.color}-600`}>{deadline.text}</p>
                      </>
                    ) : (
                      <p className="text-sm font-semibold text-foreground">Tidak ada deadline</p>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>

            {isSubmitted && (
              <Card>
                <CardHeader>
                  <CardTitle className="text-base">Progress Tugas</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                  <div className="flex items-center gap-3">
                    <div className="h-2 w-2 rounded-full bg-green-500" />
                    <div className="text-sm">
                      <p className="font-medium text-foreground">Tugas dikumpulkan</p>
                      <p className="text-muted-foreground">
                        {new Date(submission.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className={`h-2 w-2 rounded-full ${submission.grade ? 'bg-blue-500' : 'bg-yellow-500'}`} />
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
