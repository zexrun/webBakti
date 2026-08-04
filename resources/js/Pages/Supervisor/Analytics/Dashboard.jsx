import { Link } from '@inertiajs/react'
import { Users, ClipboardList, FileText, CheckCircle2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

// Sequential magnitude → single indigo hue, thin rounded bar on a recessive track.
function Meter({ value, className }) {
  return (
    <div className={className}>
      <div className="h-1.5 w-full overflow-hidden rounded-full bg-muted">
        <div className="h-full rounded-full bg-primary" style={{ width: `${Math.min(value, 100)}%` }} />
      </div>
    </div>
  )
}

function GradeStat({ label, value }) {
  return (
    <div className="flex items-center justify-between rounded-lg bg-muted p-3">
      <span className="text-sm text-muted-foreground">{label}</span>
      <span className="text-xl font-semibold tabular-nums text-foreground">{value}</span>
    </div>
  )
}

// Highlight list rows (top / at-risk) share one shape, differing only in tone.
function HighlightRow({ name, meta, value, metaTone, href }) {
  return (
    <div className="flex items-center justify-between gap-3 rounded-lg border border-border p-3">
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{name}</p>
        <p className={`truncate text-xs ${metaTone}`}>{meta}</p>
      </div>
      <div className="shrink-0 text-right">
        <p className="text-lg font-semibold tabular-nums text-foreground">{value}</p>
        <Link href={href} className="text-xs text-primary hover:underline">Lihat laporan</Link>
      </div>
    </div>
  )
}

export default function Dashboard({
  totalStudents,
  totalTasks,
  totalSubmissions,
  gradedSubmissions,
  gradeStats,
  gradeDistribution,
  taskPerformance,
  topStudents,
  atRiskStudents,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const maxDistribution = Math.max(...Object.values(gradeDistribution), 1)
  const completePercent = totalSubmissions > 0 ? Math.round((gradedSubmissions / totalSubmissions) * 1000) / 10 : 0

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader title="Analitik Kinerja" description="Analisis performa mahasiswa dan tugas secara menyeluruh" />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Mahasiswa" value={totalStudents} tone="blue" index={0} />
          <StatCard icon={ClipboardList} label="Total Tugas" value={totalTasks} tone="purple" index={1} />
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="green" index={2} />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedSubmissions} hint={`${completePercent}% selesai`} tone="orange" index={3} />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader className="border-b">
              <CardTitle>Statistik Nilai</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-2 gap-3 pt-6">
              <GradeStat label="Rata-rata" value={gradeStats.average} />
              <GradeStat label="Median" value={gradeStats.median} />
              <GradeStat label="Tertinggi" value={gradeStats.highest} />
              <GradeStat label="Terendah" value={gradeStats.lowest} />
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
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Performa Tugas</h3>
          </div>
          {taskPerformance.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Judul Tugas</TableHead>
                  <TableHead className="text-right">Dikumpul</TableHead>
                  <TableHead className="text-right">Dinilai</TableHead>
                  <TableHead className="w-48">Completion Rate</TableHead>
                  <TableHead className="text-right">Nilai Rata-rata</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {taskPerformance.map((task) => (
                  <TableRow key={task.id}>
                    <TableCell className="font-medium text-foreground">{task.title}</TableCell>
                    <TableCell className="text-right tabular-nums text-muted-foreground">{task.submitted}</TableCell>
                    <TableCell className="text-right tabular-nums text-muted-foreground">{task.graded}</TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Meter value={task.completion_rate} className="flex-1" />
                        <span className="w-9 text-right text-xs tabular-nums text-muted-foreground">{task.completion_rate}%</span>
                      </div>
                    </TableCell>
                    <TableCell className="text-right">
                      <Badge variant="secondary">{task.average_grade}</Badge>
                    </TableCell>
                    <TableCell>
                      <Button asChild size="xs" variant="outline">
                        <Link href={r('supervisor.analytics.task', task.id)}>Detail</Link>
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState icon={ClipboardList} title="Belum ada data tugas" />
          )}
        </Card>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader className="border-b">
              <CardTitle>Mahasiswa Terbaik</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3 pt-6">
              {topStudents.length ? (
                topStudents.map((student) => (
                  <HighlightRow
                    key={student.student_id}
                    name={student.student_name}
                    meta={`${student.submission_count} submission`}
                    metaTone="text-muted-foreground"
                    value={student.average_grade}
                    href={r('supervisor.analytics.student', student.student_id)}
                  />
                ))
              ) : (
                <p className="py-4 text-center text-sm text-muted-foreground">Belum ada data.</p>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="border-b">
              <CardTitle>Mahasiswa Perlu Perhatian</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3 pt-6">
              {atRiskStudents.length ? (
                atRiskStudents.map((student) => (
                  <HighlightRow
                    key={student.student_id}
                    name={student.student_name}
                    meta={student.reason}
                    metaTone="text-destructive"
                    value={student.average_grade}
                    href={r('supervisor.analytics.student', student.student_id)}
                  />
                ))
              ) : (
                <div className="flex items-center justify-center gap-2 py-4 text-sm text-green-700 dark:text-green-400">
                  <CheckCircle2 className="h-4 w-4" /> Semua mahasiswa baik-baik saja.
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </SupervisorLayout>
  )
}
