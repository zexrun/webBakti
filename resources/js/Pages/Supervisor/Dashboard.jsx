import { Link } from '@inertiajs/react'
import { Users, ClipboardList, Clock, GraduationCap, MessageSquare, ClipboardPlus, History } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

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

function QuickAction({ href, icon: Icon, title, description, accent }) {
  return (
    <Link
      href={href}
      className="group flex items-center gap-3 rounded-lg border border-border p-4 transition-all hover:shadow-md"
    >
      <div className={`flex h-10 w-10 items-center justify-center rounded-lg ${accent}`}>
        <Icon className="h-5 w-5" />
      </div>
      <div>
        <p className="font-semibold text-foreground">{title}</p>
        <p className="text-sm text-muted-foreground">{description}</p>
      </div>
    </Link>
  )
}

export default function Dashboard({ stats, recentStudents, unreadMessages }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Dashboard Pembimbing 👋</h1>
          <p className="text-muted-foreground">
            {new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}
          </p>
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Mahasiswa" value={stats.totalStudents} accent="bg-blue-100 text-blue-700" />
          <StatCard icon={ClipboardList} label="Total Tugas" value={stats.totalTasks} accent="bg-green-100 text-green-700" />
          <StatCard icon={Clock} label="Menunggu Penilaian Akhir" value={stats.pendingAssessments} accent="bg-orange-100 text-orange-700" />
          <StatCard icon={GraduationCap} label="Magang Selesai" value={stats.completedInternships} accent="bg-purple-100 text-purple-700" />
        </div>

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Aksi Cepat</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <QuickAction
                href={r('messages.inbox')}
                icon={MessageSquare}
                title="Pesan"
                description={unreadMessages > 0 ? `${unreadMessages} pesan baru` : 'Tidak ada pesan baru'}
                accent="bg-purple-100 text-purple-700"
              />
              <QuickAction
                href={r('supervisor.tasks.create')}
                icon={ClipboardPlus}
                title="Buat Tugas"
                description="Tambahkan penugasan baru"
                accent="bg-green-100 text-green-700"
              />
              <QuickAction
                href={r('supervisor.students.list.index')}
                icon={Users}
                title="Daftar Mahasiswa"
                description="Lihat semua mahasiswa bimbingan"
                accent="bg-blue-100 text-blue-700"
              />
              <QuickAction
                href={r('supervisor.submissions.index')}
                icon={ClipboardList}
                title="Dashboard Penilaian"
                description="Nilai submission mahasiswa"
                accent="bg-orange-100 text-orange-700"
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-base flex items-center gap-2">
                <History className="h-4 w-4" /> Aktivitas Terbaru
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              {recentStudents?.length ? (
                recentStudents.map((student) => (
                  <div key={student.id} className="flex items-center gap-3 rounded-lg bg-muted p-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                      <span className="text-xs font-semibold text-blue-700">
                        {student.user?.name?.charAt(0)?.toUpperCase()}
                      </span>
                    </div>
                    <div className="min-w-0 flex-1">
                      <p className="truncate text-sm font-medium text-foreground">{student.user?.name}</p>
                      <p className="text-xs text-muted-foreground">{student.universitas ?? 'Universitas belum diisi'}</p>
                    </div>
                  </div>
                ))
              ) : (
                <p className="text-sm text-muted-foreground">Belum ada mahasiswa.</p>
              )}

              {recentStudents?.length > 0 && (
                <Link
                  href={r('supervisor.students.list.index')}
                  className="inline-block pt-2 text-sm font-medium text-primary hover:underline"
                >
                  Lihat semua mahasiswa →
                </Link>
              )}
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Ringkasan Cepat</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="flex items-center justify-between">
              <span className="text-sm text-muted-foreground">Total Tugas</span>
              <span className="text-sm font-semibold text-foreground">{stats.totalTasks}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-sm text-muted-foreground">Menunggu Penilaian Akhir</span>
              <span className="text-sm font-semibold text-orange-600">{stats.pendingAssessments}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-sm text-muted-foreground">Magang Selesai</span>
              <span className="text-sm font-semibold text-green-600">{stats.completedInternships}</span>
            </div>
            {stats.pendingAssessments > 0 && (
              <Badge variant="warning" className="mt-2">
                {stats.pendingAssessments} mahasiswa menunggu penilaian akhir
              </Badge>
            )}
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
