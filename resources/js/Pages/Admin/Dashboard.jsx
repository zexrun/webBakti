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
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Progress } from '@/Components/ui/progress'

function StatCard({ icon: Icon, label, value, accent }) {
  return (
    <Card>
      <CardContent className="flex items-center gap-4 p-5">
        <div className={`flex h-12 w-12 items-center justify-center rounded-lg ${accent}`}>
          <Icon className="h-6 w-6" />
        </div>
        <div>
          <p className="text-sm text-muted-foreground">{label}</p>
          <p className="text-2xl font-bold text-foreground">{value}</p>
        </div>
      </CardContent>
    </Card>
  )
}

function RateCard({ icon: Icon, title, rate, detail }) {
  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-base flex items-center gap-2">
          <Icon className="h-4 w-4" /> {title}
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div className="mb-2 flex items-center justify-between text-sm">
          <span className="text-muted-foreground">Rate</span>
          <span className="font-semibold">{rate}%</span>
        </div>
        <Progress value={rate} />
        <p className="mt-3 text-xs text-muted-foreground">{detail}</p>
      </CardContent>
    </Card>
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
        <div>
          <h1 className="text-2xl font-bold text-foreground">Dashboard Admin</h1>
          <p className="text-muted-foreground">Statistik sistem secara real-time</p>
        </div>

        {studentWithoutSupervisor > 0 && (
          <div className="flex items-center gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4">
            <AlertTriangle className="h-5 w-5 flex-shrink-0 text-orange-600" />
            <p className="text-sm text-orange-800">
              <span className="font-semibold">{studentWithoutSupervisor} mahasiswa</span> belum memiliki pembimbing.
            </p>
          </div>
        )}

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Users" value={userCount} accent="bg-blue-100 text-blue-700" />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={studentCount} accent="bg-green-100 text-green-700" />
          <StatCard icon={UserCog} label="Pembimbing" value={supervisorCount} accent="bg-purple-100 text-purple-700" />
          <StatCard icon={Building2} label="Direktorat" value={directorateCount} accent="bg-orange-100 text-orange-700" />
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={ClipboardList} label="Total Tugas" value={taskCount} accent="bg-indigo-100 text-indigo-700" />
          <StatCard icon={CheckCircle2} label="Submission Dinilai" value={submissionsWithGrades} accent="bg-emerald-100 text-emerald-700" />
          <StatCard icon={CalendarCheck} label="Total Presensi" value={totalAttendance} accent="bg-cyan-100 text-cyan-700" />
          <StatCard icon={UserCog} label="Admin" value={adminCount} accent="bg-slate-100 text-slate-700" />
        </div>

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
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

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <Card>
            <CardHeader>
              <CardTitle className="text-base flex items-center gap-2">
                <TrendingUp className="h-4 w-4" /> Aktivitas Hari Ini
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Presensi</span>
                <span className="font-semibold text-foreground">{todayAttendance}</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Submission</span>
                <span className="font-semibold text-foreground">{todaySubmissions}</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Approval</span>
                <span className="font-semibold text-foreground">{todayApprovals}</span>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Bulan Ini</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Mahasiswa Baru</span>
                <span className="font-semibold text-foreground">{thisMonthStudents}</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Tugas Baru</span>
                <span className="font-semibold text-foreground">{thisMonthTasks}</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Presensi</span>
                <span className="font-semibold text-foreground">{thisMonthAttendance}</span>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Rata-rata per Pembimbing</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Mahasiswa</span>
                <span className="font-semibold text-foreground">{averageStudentsPerSupervisor}</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Tugas</span>
                <span className="font-semibold text-foreground">{averageTasksPerSupervisor}</span>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Performa Direktorat</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            {departmentStats?.length ? (
              departmentStats.map((dept) => (
                <div key={dept.name} className="flex items-center justify-between rounded-md border border-border p-3">
                  <div>
                    <p className="text-sm font-medium text-foreground">{dept.name}</p>
                    <p className="text-xs text-muted-foreground">{dept.supervisors} pembimbing</p>
                  </div>
                  <Badge variant="secondary">{dept.students} mahasiswa</Badge>
                </div>
              ))
            ) : (
              <p className="text-sm text-muted-foreground">Belum ada data direktorat.</p>
            )}
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
