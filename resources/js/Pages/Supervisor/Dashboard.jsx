import { Link } from '@inertiajs/react'
import { Users, ClipboardList, Clock, GraduationCap, MessageSquare, ClipboardPlus, History } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import UserCell from '@/Components/UserCell'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { cn } from '@/lib/utils'

const accents = {
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  orange: 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
  purple: 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-300',
}

function QuickAction({ href, icon: Icon, title, description, tone }) {
  return (
    <Link
      href={href}
      className="group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors duration-150 hover:bg-muted"
    >
      <div className={cn('flex h-10 w-10 shrink-0 items-center justify-center rounded-lg', accents[tone])}>
        <Icon className="h-5 w-5" />
      </div>
      <div className="min-w-0">
        <p className="text-sm font-semibold text-foreground">{title}</p>
        <p className="truncate text-sm text-muted-foreground">{description}</p>
      </div>
    </Link>
  )
}

export default function Dashboard({ stats, recentStudents, unreadMessages }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Dasbor Pembimbing"
          description={new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={Users} label="Total Mahasiswa" value={stats.totalStudents} tone="blue" index={0} />
          <StatCard icon={ClipboardList} label="Total Tugas" value={stats.totalTasks} tone="green" index={1} />
          <StatCard icon={Clock} label="Menunggu Penilaian Akhir" value={stats.pendingAssessments} tone="orange" index={2} />
          <StatCard icon={GraduationCap} label="Magang Selesai" value={stats.completedInternships} tone="purple" index={3} />
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle>Aksi Cepat</CardTitle>
            </CardHeader>
            <CardContent className="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <QuickAction
                href={r('messages.inbox')}
                icon={MessageSquare}
                title="Pesan"
                description={unreadMessages > 0 ? `${unreadMessages} pesan baru` : 'Tidak ada pesan baru'}
                tone="purple"
              />
              <QuickAction
                href={r('supervisor.tasks.create')}
                icon={ClipboardPlus}
                title="Buat Tugas"
                description="Tambahkan penugasan baru"
                tone="green"
              />
              <QuickAction
                href={r('supervisor.students.index')}
                icon={Users}
                title="Mahasiswa Bimbingan"
                description="Lihat semua mahasiswa bimbingan"
                tone="blue"
              />
              <QuickAction
                href={r('supervisor.submissions.index')}
                icon={ClipboardList}
                title="Dasbor Penilaian"
                description="Nilai submission mahasiswa"
                tone="orange"
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <History className="h-4 w-4 text-muted-foreground" /> Aktivitas Terbaru
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {recentStudents?.length ? (
                recentStudents.map((student) => (
                  <div key={student.id} className="rounded-lg bg-muted p-3">
                    <UserCell
                      name={student.user?.name}
                      photoUrl={student.user?.profile_photo_url}
                      subtitle={student.university ?? 'Universitas belum diisi'}
                    />
                  </div>
                ))
              ) : (
                <p className="text-sm text-muted-foreground">Belum ada mahasiswa.</p>
              )}

              {recentStudents?.length > 0 && (
                <Link
                  href={r('supervisor.students.index')}
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
            <CardTitle>Ringkasan Cepat</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2.5">
            <div className="flex items-center justify-between text-sm">
              <span className="text-muted-foreground">Total Tugas</span>
              <span className="font-semibold tabular-nums text-foreground">{stats.totalTasks}</span>
            </div>
            <div className="flex items-center justify-between text-sm">
              <span className="text-muted-foreground">Menunggu Penilaian Akhir</span>
              <span className="font-semibold tabular-nums text-amber-700 dark:text-amber-400">{stats.pendingAssessments}</span>
            </div>
            <div className="flex items-center justify-between text-sm">
              <span className="text-muted-foreground">Magang Selesai</span>
              <span className="font-semibold tabular-nums text-green-700 dark:text-green-400">{stats.completedInternships}</span>
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
