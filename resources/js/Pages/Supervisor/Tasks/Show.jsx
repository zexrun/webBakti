import { Link, router } from '@inertiajs/react'
import { ArrowLeft, Pencil, Trash2, Download, Tag, Calendar, User } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import UserCell from '@/Components/UserCell'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Progress } from '@/Components/ui/progress'
import GradingForm from './GradingForm'

export default function Show({ task, assignedStudents, submissions }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const totalStudents = assignedStudents.length
  const submittedCount = Object.keys(submissions).length
  const gradedCount = Object.values(submissions).filter((s) => s.grade).length
  const pendingCount = submittedCount - gradedCount
  const progressPct = totalStudents > 0 ? Math.round((submittedCount / totalStudents) * 100) : 0

  function handleDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-5xl space-y-6">
        <PageHeader
          title="Detail Tugas"
          description="Kelola dan nilai submission mahasiswa"
          actions={
            <>
              <Button asChild variant="outline">
                <Link href={r('supervisor.tasks.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
              <Button asChild variant="outline">
                <Link href={r('supervisor.tasks.edit', task.id)}>
                  <Pencil /> Edit
                </Link>
              </Button>
              <Button
                variant="outline"
                className="text-destructive hover:text-destructive"
                onClick={handleDelete}
              >
                <Trash2 /> Hapus
              </Button>
            </>
          }
        />

        <Card>
          <CardContent className="flex flex-col gap-6 p-6 lg:flex-row lg:justify-between">
            <div className="flex-1">
              <h2 className="mb-3 text-2xl font-bold text-foreground">{task.title}</h2>

              <div className="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                  <Tag className="h-4 w-4" />
                  <span className="font-medium">Tipe:</span>
                  <Badge variant="secondary">{task.type === 'harian' ? 'Harian' : 'Akhir'}</Badge>
                </div>
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                  <Calendar className="h-4 w-4" />
                  <span className="font-medium">Tenggat:</span>
                  <span>
                    {task.due_date
                      ? new Date(task.due_date).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                      : 'Tidak ada'}
                  </span>
                </div>
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                  <User className="h-4 w-4" />
                  <span className="font-medium">Pembimbing:</span>
                  <span>{task.supervisor?.user?.name}</span>
                </div>
              </div>

              <div className="mb-4">
                <h3 className="mb-2 text-sm font-medium text-foreground">Deskripsi Tugas</h3>
                <p className="leading-relaxed text-foreground">{task.description}</p>
              </div>

              {task.file_path && (
                <div>
                  <h3 className="mb-2 text-sm font-medium text-foreground">Lampiran</h3>
                  <Button asChild size="sm" variant="outline">
                    <a href={`/storage/${task.file_path}`} target="_blank" rel="noreferrer">
                      <Download /> Unduh Lampiran
                    </a>
                  </Button>
                </div>
              )}
            </div>

            <div className="lg:w-80">
              <div className="rounded-lg bg-muted p-4">
                <h3 className="mb-3 text-sm font-medium text-foreground">Statistik Submission</h3>
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Total Mahasiswa</span>
                    <span className="text-sm font-medium text-foreground">{totalStudents}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Sudah Submit</span>
                    <span className="text-sm font-medium tabular-nums text-green-700 dark:text-green-400">{submittedCount}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Sudah Dinilai</span>
                    <span className="text-sm font-medium tabular-nums text-blue-700 dark:text-blue-400">{gradedCount}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Menunggu Nilai</span>
                    <span className="text-sm font-medium tabular-nums text-amber-700 dark:text-amber-400">{pendingCount}</span>
                  </div>
                  <div className="mt-4">
                    <div className="mb-1 flex justify-between text-xs text-muted-foreground">
                      <span>Progress Submission</span>
                      <span>{progressPct}%</span>
                    </div>
                    <Progress value={progressPct} />
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <div>
          <h3 className="mb-4 text-lg font-semibold text-foreground">Status Submission Mahasiswa</h3>

          <div className="space-y-6">
            {assignedStudents.map((student) => {
              const submission = submissions[student.id]
              const isSubmitted = Boolean(submission)
              const isGraded = isSubmitted && submission.grade
              const isPending = isSubmitted && !submission.grade

              return (
                <Card key={student.id}>
                  <div className="flex flex-col gap-3 border-b border-border px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <UserCell
                      name={student.user?.name}
                      subtitle={[student.nim ?? 'NIM belum diisi', student.user?.email].filter(Boolean).join(' · ')}
                    />
                    {isGraded ? (
                      <Badge variant="success">Sudah Dinilai</Badge>
                    ) : isPending ? (
                      <Badge variant="warning">Menunggu Penilaian</Badge>
                    ) : (
                      <Badge variant="destructive">Belum Submit</Badge>
                    )}
                  </div>

                  {submission && (
                    <CardContent className="space-y-4 p-6">
                      <div>
                        <h5 className="mb-2 text-sm font-medium text-foreground">Laporan Teks</h5>
                        <div className="rounded-lg bg-muted p-4 text-foreground">
                          {submission.content ?? 'Tidak ada laporan teks yang diberikan.'}
                        </div>
                      </div>

                      {submission.file_path && (
                        <div>
                          <h5 className="mb-2 text-sm font-medium text-foreground">File Submission</h5>
                          <Button asChild size="sm" variant="outline">
                            <a href={`/storage/${submission.file_path}`} target="_blank" rel="noreferrer">
                              <Download /> Unduh File Submission
                            </a>
                          </Button>
                        </div>
                      )}

                      <GradingForm submission={submission} />
                    </CardContent>
                  )}
                </Card>
              )
            })}
          </div>
        </div>
      </div>
    </SupervisorLayout>
  )
}
