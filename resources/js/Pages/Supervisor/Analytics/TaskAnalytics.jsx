import { Link } from '@inertiajs/react'
import { ArrowLeft, Users, FileCheck, UserX, TrendingUp } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { Progress } from '@/Components/ui/progress'
import { cn } from '@/lib/utils'

function InfoRow({ label, value }) {
  return (
    <div>
      <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">{label}</p>
      <p className="mt-1 text-sm text-foreground">{value ?? '-'}</p>
    </div>
  )
}

function MetricRow({ label, value, tone }) {
  return (
    <div className="flex items-center justify-between rounded-lg bg-muted p-3">
      <span className="text-sm text-muted-foreground">{label}</span>
      <span className={cn('text-xl font-semibold tabular-nums', tone ?? 'text-foreground')}>{value}</span>
    </div>
  )
}

// Sequential magnitude → single indigo hue, thin rounded bar on a recessive track.
function Meter({ value }) {
  return (
    <div className="h-1.5 w-full overflow-hidden rounded-full bg-muted">
      <div className="h-full rounded-full bg-primary" style={{ width: `${Math.min(value, 100)}%` }} />
    </div>
  )
}

export default function TaskAnalytics({
  taskInfo,
  submissionStats,
  gradeDistribution,
  gradeStats,
  studentPerformance,
  notSubmittedStudents,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const maxDistribution = Math.max(...Object.values(gradeDistribution), 1)
  const totalSubmitted = submissionStats.graded + submissionStats.pending_grade
  const gradedPercentage = totalSubmitted > 0 ? Math.round((submissionStats.graded / totalSubmitted) * 1000) / 10 : 0

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Analisis Tugas"
          description={taskInfo.title}
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.analytics.dashboard')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Informasi Tugas</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4 pt-6">
            <div className="grid grid-cols-1 gap-5 md:grid-cols-3">
              <InfoRow label="Judul" value={taskInfo.title} />
              <InfoRow label="Dibuat" value={new Date(taskInfo.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} />
              <InfoRow label="Deadline" value={new Date(taskInfo.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} />
            </div>
            {taskInfo.description && (
              <div className="border-t border-border pt-4">
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Deskripsi</p>
                <p className="mt-1 text-sm text-foreground">{taskInfo.description}</p>
              </div>
            )}
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Assigned" value={submissionStats.total_assigned} tone="blue" />
          <StatCard icon={FileCheck} label="Submitted" value={submissionStats.submitted} tone="green" />
          <StatCard icon={UserX} label="Not Submitted" value={submissionStats.not_submitted} tone="red" />
          <StatCard icon={TrendingUp} label="Submission Rate" value={`${submissionStats.submission_rate}%`} tone="orange" />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader className="border-b">
              <CardTitle>Statistik Nilai</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-2 gap-3 pt-6">
              <MetricRow label="Rata-rata" value={gradeStats.average} />
              <MetricRow label="Median" value={gradeStats.median} />
              <MetricRow label="Tertinggi" value={gradeStats.highest} tone="text-green-700 dark:text-green-400" />
              <MetricRow label="Terendah" value={gradeStats.lowest} tone="text-red-700 dark:text-red-400" />
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="border-b">
              <CardTitle>Distribusi Nilai</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3 pt-6">
              {Object.entries(gradeDistribution).map(([range, count]) => (
                <div key={range}>
                  <div className="mb-1 flex items-center justify-between text-sm">
                    <span className="text-foreground">{range}</span>
                    <span className="tabular-nums text-muted-foreground">{count}</span>
                  </div>
                  <Meter value={(count / maxDistribution) * 100} />
                </div>
              ))}
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Status Penilaian</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3 pt-6">
            <div className="grid grid-cols-2 gap-3">
              <MetricRow label="Sudah Dinilai" value={submissionStats.graded} tone="text-green-700 dark:text-green-400" />
              <MetricRow label="Menunggu Penilaian" value={submissionStats.pending_grade} tone="text-amber-700 dark:text-amber-400" />
            </div>
            <div>
              <Progress value={gradedPercentage} />
              <p className="mt-2 text-center text-xs tabular-nums text-muted-foreground">{gradedPercentage}% selesai</p>
            </div>
          </CardContent>
        </Card>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Performa Mahasiswa</h3>
          </div>
          {studentPerformance.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama Mahasiswa</TableHead>
                  <TableHead>Waktu Submit</TableHead>
                  <TableHead className="text-right">Nilai</TableHead>
                  <TableHead>Feedback</TableHead>
                  <TableHead>Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {studentPerformance.map((submission, index) => (
                  <TableRow key={index}>
                    <TableCell>
                      <Link href={r('supervisor.analytics.student', submission.student_id)} className="font-medium text-primary hover:underline">
                        {submission.student_name}
                      </Link>
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">
                      {new Date(submission.submitted_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </TableCell>
                    <TableCell className="text-right">
                      {submission.grade !== null ? <Badge variant="secondary">{submission.grade}</Badge> : <span className="text-muted-foreground">-</span>}
                    </TableCell>
                    <TableCell className="max-w-xs truncate whitespace-normal text-muted-foreground">
                      {submission.feedback ?? '-'}
                    </TableCell>
                    <TableCell>
                      <Badge variant={submission.status === 'Graded' ? 'success' : 'warning'}>
                        {submission.status === 'Graded' ? 'Dinilai' : 'Pending'}
                      </Badge>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState icon={Users} title="Belum ada submission" />
          )}
        </Card>

        {notSubmittedStudents.length > 0 && (
          <Card>
            <div className="flex items-center gap-2.5 border-b border-border px-4 py-3.5">
              <UserX className="h-4 w-4 text-red-600 dark:text-red-400" />
              <h3 className="text-base font-semibold text-foreground">Mahasiswa Belum Submit</h3>
              <span className="text-sm tabular-nums text-muted-foreground">({notSubmittedStudents.length})</span>
            </div>
            <CardContent className="grid grid-cols-1 gap-3 pt-6 md:grid-cols-2">
              {notSubmittedStudents.map((student, index) => (
                <div key={index} className="rounded-lg border border-border p-3">
                  <p className="text-sm font-medium text-foreground">{student.name}</p>
                  <p className="text-xs text-muted-foreground">{student.nim}</p>
                </div>
              ))}
            </CardContent>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
