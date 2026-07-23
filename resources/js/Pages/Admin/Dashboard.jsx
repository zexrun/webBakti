import {
  Users,
  GraduationCap,
  UserCog,
  ClipboardList,
  Building2,
  CheckCircle2,
  CalendarCheck,
  TrendingUp,
  AlertTriangle,
} from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Progress } from '@/Components/ui/progress'

function RateCard({ icon: Icon, title, rate, detail }) {
  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2 text-base">
          <Icon className="h-4 w-4 text-muted-foreground" /> {title}
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div className="mb-2 flex items-baseline justify-between">
          <span className="text-sm text-muted-foreground">Rate</span>
          <span className="text-xl font-semibold tabular-nums text-foreground">{rate}%</span>
        </div>
        <Progress value={rate} />
        <p className="mt-3 text-xs text-muted-foreground">{detail}</p>
      </CardContent>
    </Card>
  )
}

function MetricRow({ label, value }) {
  return (
    <div className="flex items-center justify-between text-sm">
      <span className="text-muted-foreground">{label}</span>
      <span className="font-semibold tabular-nums text-foreground">{value}</span>
    </div>
  )
}

export default function Dashboard({
  userCount,
  directorateCount,
  adminCount,
  studentCount,
  supervisorCount,
  taskCount,
  totalSubmissions,
  submissionsWithGrades,
  pendingSubmissions,
  submissionGradeRate,
  totalAttendance,
  approvedAttendance,
  pendingAttendance,
  attendanceApprovalRate,
  todayAttendance,
  todaySubmissions,
  todayApprovals,
  thisMonthStudents,
  thisMonthTasks,
  thisMonthAttendance,
  averageStudentsPerSupervisor,
  averageTasksPerSupervisor,
  studentWithoutSupervisor,
  departmentStats,
}) {
  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader title="Dashboard Admin" description="Statistik sistem secara real-time" />

        {studentWithoutSupervisor > 0 && (
          <div className="flex items-center gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-500/30 dark:bg-orange-500/10">
            <AlertTriangle className="h-5 w-5 shrink-0 text-orange-600 dark:text-orange-400" />
            <p className="text-sm text-orange-800 dark:text-orange-200">
              <span className="font-semibold">{studentWithoutSupervisor} mahasiswa</span> belum memiliki pembimbing.
            </p>
          </div>
        )}

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Users" value={userCount} tone="blue" index={0} />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={studentCount} tone="green" index={1} />
          <StatCard icon={UserCog} label="Pembimbing" value={supervisorCount} tone="purple" index={2} />
          <StatCard icon={Building2} label="Direktorat" value={directorateCount} tone="orange" index={3} />
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskCount} tone="indigo" index={0} />
          <StatCard icon={CheckCircle2} label="Submission Dinilai" value={submissionsWithGrades} tone="emerald" index={1} />
          <StatCard icon={CalendarCheck} label="Total Presensi" value={totalAttendance} tone="cyan" index={2} />
          <StatCard icon={UserCog} label="Admin" value={adminCount} tone="neutral" index={3} />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <RateCard
            icon={ClipboardList}
            title="Tingkat Penilaian Submission"
            rate={submissionGradeRate}
            detail={`${submissionsWithGrades} dari ${totalSubmissions} submission telah dinilai (${pendingSubmissions} menunggu)`}
          />
          <RateCard
            icon={CalendarCheck}
            title="Tingkat Persetujuan Presensi"
            rate={attendanceApprovalRate}
            detail={`${approvedAttendance} dari ${totalAttendance} presensi disetujui (${pendingAttendance} menunggu)`}
          />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-base">
                <TrendingUp className="h-4 w-4 text-muted-foreground" /> Aktivitas Hari Ini
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2.5">
              <MetricRow label="Presensi" value={todayAttendance} />
              <MetricRow label="Submission" value={todaySubmissions} />
              <MetricRow label="Approval" value={todayApprovals} />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Bulan Ini</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2.5">
              <MetricRow label="Mahasiswa Baru" value={thisMonthStudents} />
              <MetricRow label="Tugas Baru" value={thisMonthTasks} />
              <MetricRow label="Presensi" value={thisMonthAttendance} />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Rata-rata per Pembimbing</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2.5">
              <MetricRow label="Mahasiswa" value={averageStudentsPerSupervisor} />
              <MetricRow label="Tugas" value={averageTasksPerSupervisor} />
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Performa Direktorat</CardTitle>
          </CardHeader>
          <CardContent>
            {departmentStats?.length ? (
              <div className="divide-y divide-border">
                {departmentStats.map((dept) => (
                  <div key={dept.name} className="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                    <div>
                      <p className="text-sm font-medium text-foreground">{dept.name}</p>
                      <p className="text-xs text-muted-foreground">{dept.supervisors} pembimbing</p>
                    </div>
                    <Badge variant="secondary">{dept.students} mahasiswa</Badge>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-muted-foreground">Belum ada data direktorat.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
