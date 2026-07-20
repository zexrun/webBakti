import { Link } from '@inertiajs/react'
import { ClipboardList, CheckCircle2, Clock, GraduationCap, CalendarCheck, NotebookPen } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Progress } from '@/Components/ui/progress'

function gradeVariant(grade) {
  const numeric = parseFloat(grade)
  if (Number.isNaN(numeric)) return 'secondary'
  if (numeric >= 80) return 'success'
  if (numeric >= 60) return 'warning'
  return 'destructive'
}

// One row shape for the upcoming / grades / pending lists.
function ItemRow({ title, subtitle, trailing }) {
  return (
    <div className="flex items-center justify-between gap-3 rounded-lg border border-border p-3">
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{title}</p>
        {subtitle && <p className="truncate text-xs text-muted-foreground">{subtitle}</p>}
      </div>
      <div className="shrink-0">{trailing}</div>
    </div>
  )
}

export default function Dashboard({
  student,
  upcomingTasks,
  taskStats,
  pendingSubmissions,
  recentGrades,
  attendanceStats,
  supervisor,
  logbooksThisMonth,
  completionPercentage,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <StudentLayout>
      <div className="space-y-6">
        <PageHeader
          title={`Selamat datang, ${student?.user?.name ?? 'Mahasiswa'}`}
          description={`Pembimbing Anda: ${supervisor?.name ?? '-'}`}
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskStats?.total ?? 0} tone="blue" />
          <StatCard icon={CheckCircle2} label="Tugas Selesai" value={taskStats?.completed ?? 0} tone="green" />
          <StatCard icon={Clock} label="Tugas Pending" value={taskStats?.pending ?? 0} tone="amber" />
          <StatCard icon={GraduationCap} label="Sudah Dinilai" value={taskStats?.graded ?? 0} tone="purple" />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Progress Penyelesaian Tugas</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="mb-2 flex items-baseline justify-between text-sm">
                <span className="text-muted-foreground">Selesai</span>
                <span className="text-xl font-semibold tabular-nums text-foreground">{completionPercentage}%</span>
              </div>
              <Progress value={completionPercentage} />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-base">
                <CalendarCheck className="h-4 w-4 text-muted-foreground" /> Presensi Bulan Ini
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="mb-2 flex items-baseline justify-between text-sm">
                <span className="text-muted-foreground">Tingkat kehadiran</span>
                <span className="text-xl font-semibold tabular-nums text-foreground">{attendanceStats?.rate ?? 0}%</span>
              </div>
              <Progress value={attendanceStats?.rate ?? 0} />
              <div className="mt-3 flex gap-4 text-xs tabular-nums text-muted-foreground">
                <span>Hadir: {attendanceStats?.present ?? 0}</span>
                <span>Absen: {attendanceStats?.absent ?? 0}</span>
                <span>Pending: {attendanceStats?.pending ?? 0}</span>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-base">
                <NotebookPen className="h-4 w-4 text-muted-foreground" /> Logbook Bulan Ini
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-3xl font-bold tabular-nums text-foreground">{logbooksThisMonth ?? 0}</p>
              <p className="text-sm text-muted-foreground">entri tercatat</p>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Tugas Mendatang (7 hari)</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2.5">
              {upcomingTasks?.length ? (
                upcomingTasks.map((task) => (
                  <ItemRow
                    key={task.id}
                    title={task.title}
                    subtitle={`Deadline ${new Date(task.due_date).toLocaleDateString('id-ID')}`}
                    trailing={
                      <Button asChild size="xs" variant="outline">
                        <Link href={r('student.tasks.show', task.id)}>Lihat</Link>
                      </Button>
                    }
                  />
                ))
              ) : (
                <p className="text-sm text-muted-foreground">Tidak ada tugas mendatang.</p>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Nilai Terbaru</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2.5">
              {recentGrades?.length ? (
                recentGrades.map((submission) => (
                  <ItemRow
                    key={submission.id}
                    title={submission.task?.title}
                    subtitle={`Oleh ${submission.task?.supervisor?.user?.name ?? '-'}`}
                    trailing={<Badge variant={gradeVariant(submission.grade)}>{submission.grade}</Badge>}
                  />
                ))
              ) : (
                <p className="text-sm text-muted-foreground">Belum ada nilai.</p>
              )}
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Submission Menunggu Penilaian</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2.5">
            {pendingSubmissions?.length ? (
              pendingSubmissions.map((submission) => (
                <ItemRow
                  key={submission.id}
                  title={submission.task?.title}
                  subtitle={`Dikumpulkan ${new Date(submission.created_at).toLocaleDateString('id-ID')}`}
                  trailing={<Badge variant="secondary">Menunggu</Badge>}
                />
              ))
            ) : (
              <p className="text-sm text-muted-foreground">Tidak ada submission yang menunggu penilaian.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
