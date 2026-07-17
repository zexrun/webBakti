import { Link } from '@inertiajs/react'
import { ClipboardList, CheckCircle2, Clock, GraduationCap, CalendarCheck, NotebookPen } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Progress } from '@/Components/ui/progress'

function StatCard({ icon: Icon, label, value, accent }) {
  return (
    <Card>
      <CardContent className="flex items-center gap-4 p-5">
        <div className={`flex h-11 w-11 items-center justify-center rounded-lg ${accent}`}>
          <Icon className="h-5 w-5" />
        </div>
        <div>
          <p className="text-sm text-muted-foreground">{label}</p>
          <p className="text-2xl font-bold text-foreground">{value}</p>
        </div>
      </CardContent>
    </Card>
  )
}

function gradeVariant(grade) {
  const numeric = parseFloat(grade)
  if (Number.isNaN(numeric)) return 'secondary'
  if (numeric >= 80) return 'success'
  if (numeric >= 60) return 'warning'
  return 'destructive'
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
  return (
    <StudentLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">
            Selamat datang, {student?.user?.name ?? 'Mahasiswa'}!
          </h1>
          <p className="text-muted-foreground">
            Pembimbing Anda: {supervisor?.name ?? '-'}
          </p>
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard
            icon={ClipboardList}
            label="Total Tugas"
            value={taskStats?.total ?? 0}
            accent="bg-blue-100 text-blue-700"
          />
          <StatCard
            icon={CheckCircle2}
            label="Tugas Selesai"
            value={taskStats?.completed ?? 0}
            accent="bg-green-100 text-green-700"
          />
          <StatCard
            icon={Clock}
            label="Tugas Pending"
            value={taskStats?.pending ?? 0}
            accent="bg-yellow-100 text-yellow-700"
          />
          <StatCard
            icon={GraduationCap}
            label="Sudah Dinilai"
            value={taskStats?.graded ?? 0}
            accent="bg-purple-100 text-purple-700"
          />
        </div>

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Progress Penyelesaian Tugas</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="mb-2 flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Selesai</span>
                <span className="font-semibold">{completionPercentage}%</span>
              </div>
              <Progress value={completionPercentage} />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base flex items-center gap-2">
                <CalendarCheck className="h-4 w-4" /> Presensi Bulan Ini
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="mb-2 flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Tingkat kehadiran</span>
                <span className="font-semibold">{attendanceStats?.rate ?? 0}%</span>
              </div>
              <Progress value={attendanceStats?.rate ?? 0} />
              <div className="mt-3 flex gap-4 text-xs text-muted-foreground">
                <span>Hadir: {attendanceStats?.present ?? 0}</span>
                <span>Absen: {attendanceStats?.absent ?? 0}</span>
                <span>Pending: {attendanceStats?.pending ?? 0}</span>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base flex items-center gap-2">
                <NotebookPen className="h-4 w-4" /> Logbook Bulan Ini
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-3xl font-bold text-foreground">{logbooksThisMonth ?? 0}</p>
              <p className="text-sm text-muted-foreground">entri tercatat</p>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Tugas Mendatang (7 hari)</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              {upcomingTasks?.length ? (
                upcomingTasks.map((task) => (
                  <div key={task.id} className="flex items-center justify-between rounded-md border border-border p-3">
                    <div>
                      <p className="text-sm font-medium text-foreground">{task.title}</p>
                      <p className="text-xs text-muted-foreground">
                        Deadline: {new Date(task.due_date).toLocaleDateString('id-ID')}
                      </p>
                    </div>
                    <Link
                      href={window.route ? window.route('student.tasks.show', task.id) : '#'}
                      className="text-xs font-medium text-primary hover:underline"
                    >
                      Lihat
                    </Link>
                  </div>
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
            <CardContent className="space-y-3">
              {recentGrades?.length ? (
                recentGrades.map((submission) => (
                  <div key={submission.id} className="flex items-center justify-between rounded-md border border-border p-3">
                    <div>
                      <p className="text-sm font-medium text-foreground">{submission.task?.title}</p>
                      <p className="text-xs text-muted-foreground">
                        Oleh: {submission.task?.supervisor?.user?.name ?? '-'}
                      </p>
                    </div>
                    <Badge variant={gradeVariant(submission.grade)}>{submission.grade}</Badge>
                  </div>
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
          <CardContent className="space-y-3">
            {pendingSubmissions?.length ? (
              pendingSubmissions.map((submission) => (
                <div key={submission.id} className="flex items-center justify-between rounded-md border border-border p-3">
                  <div>
                    <p className="text-sm font-medium text-foreground">{submission.task?.title}</p>
                    <p className="text-xs text-muted-foreground">
                      Dikumpulkan: {new Date(submission.created_at).toLocaleDateString('id-ID')}
                    </p>
                  </div>
                  <Badge variant="secondary">Menunggu</Badge>
                </div>
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
