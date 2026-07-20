import { Link } from '@inertiajs/react'
import { ArrowLeft, ClipboardList, FileCheck, Award, TrendingUp } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
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

export default function StudentReport({
  studentInfo,
  taskStats,
  gradePerformance,
  submissionTimeline,
  attendanceStats,
  finalAssessment,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Laporan Performa Mahasiswa"
          description={`${studentInfo.name} · ${studentInfo.nim}`}
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
            <CardTitle>Informasi Mahasiswa</CardTitle>
          </CardHeader>
          <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-3">
            <InfoRow label="Nama" value={studentInfo.name} />
            <InfoRow label="NIM" value={studentInfo.nim} />
            <InfoRow label="Universitas" value={studentInfo.universitas} />
            <InfoRow label="Program Studi" value={studentInfo.program_studi} />
            <InfoRow label="Pembimbing" value={studentInfo.supervisor} />
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={ClipboardList} label="Tugas Diberikan" value={taskStats.assigned} tone="blue" />
          <StatCard icon={FileCheck} label="Dikumpulkan" value={taskStats.submitted} tone="green" />
          <StatCard icon={Award} label="Dinilai" value={taskStats.graded} tone="purple" />
          <StatCard icon={TrendingUp} label="Completion Rate" value={`${taskStats.completion_rate}%`} tone="orange" />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader className="border-b">
              <CardTitle>Statistik Nilai</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-2 gap-3 pt-6">
              <MetricRow label="Rata-rata" value={gradePerformance.average_grade} />
              <MetricRow label="Total Dinilai" value={gradePerformance.total_graded} />
              <MetricRow label="Tertinggi" value={gradePerformance.highest_grade} tone="text-green-700 dark:text-green-400" />
              <MetricRow label="Terendah" value={gradePerformance.lowest_grade} tone="text-red-700 dark:text-red-400" />
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="border-b">
              <CardTitle>Statistik Kehadiran (Bulan Ini)</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-2 gap-3 pt-6">
              <MetricRow label="Hadir" value={attendanceStats.present} tone="text-green-700 dark:text-green-400" />
              <MetricRow label="Terlambat" value={attendanceStats.late} tone="text-amber-700 dark:text-amber-400" />
              <MetricRow label="Tidak Hadir" value={attendanceStats.absent} tone="text-red-700 dark:text-red-400" />
              <MetricRow label="Kehadiran Rate" value={`${attendanceStats.rate}%`} tone="text-blue-700 dark:text-blue-400" />
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Timeline Submission</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            {submissionTimeline.length ? (
              <div className="space-y-3">
                {submissionTimeline.map((submission, index) => (
                  <div key={index} className="flex items-start justify-between gap-4 rounded-lg border border-border p-4">
                    <div className="min-w-0 flex-1">
                      <p className="text-sm font-medium text-foreground">{submission.task_title}</p>
                      <p className="mt-0.5 text-xs tabular-nums text-muted-foreground">
                        Submitted {new Date(submission.submitted_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>
                      {submission.feedback && (
                        <p className="mt-2 text-sm italic text-muted-foreground">Feedback: {submission.feedback}</p>
                      )}
                    </div>
                    <Badge variant={submission.status === 'Graded' ? 'success' : 'warning'}>
                      {submission.status === 'Graded' ? submission.grade : 'Pending'}
                    </Badge>
                  </div>
                ))}
              </div>
            ) : (
              <EmptyState icon={ClipboardList} title="Belum ada submission" />
            )}
          </CardContent>
        </Card>

        {finalAssessment && (
          <Card>
            <CardHeader className="border-b">
              <CardTitle>Penilaian Akhir</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-2">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Nilai Akhir</p>
                <p className="mt-1 text-3xl font-bold tabular-nums text-primary">{finalAssessment.final_grade}</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Grade</p>
                <p className="mt-1 text-2xl font-semibold text-foreground">{finalAssessment.grade}</p>
              </div>
              <div className="md:col-span-2">
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Catatan</p>
                <p className="mt-1 text-sm text-foreground">{finalAssessment.notes ?? '-'}</p>
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
