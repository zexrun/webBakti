import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, GraduationCap, CheckCircle2 } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

function InfoRow({ label, value }) {
  return (
    <div>
      <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">{label}</p>
      <p className="mt-1 text-sm text-foreground">{value ?? '-'}</p>
    </div>
  )
}

export default function SupervisorDetail({ supervisor }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title={supervisor.user.name}
          description="Detail supervisor & mahasiswa bimbingan"
          actions={
            <>
              <Button asChild variant="outline">
                <Link href={r('admin.monitoring.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
              <Button asChild>
                <Link href={r('admin.users.edit', supervisor.user.id)}>
                  <Pencil /> Edit Supervisor
                </Link>
              </Button>
            </>
          }
        />

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-1">
            <Card>
              <CardHeader className="border-b">
                <CardTitle>Informasi Supervisor</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4 pt-6">
                <InfoRow label="Nama Lengkap" value={supervisor.user.name} />
                <InfoRow label="Email" value={supervisor.user.email} />
                <InfoRow label="NIP" value={supervisor.employee_id} />
                <InfoRow label="Direktorat" value={supervisor.directorate} />
                <InfoRow label="Jabatan" value={supervisor.position} />
                <div>
                  <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</p>
                  <Badge variant="success" className="mt-1">
                    <CheckCircle2 /> Aktif
                  </Badge>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="border-b">
                <CardTitle>Statistik</CardTitle>
              </CardHeader>
              <CardContent className="pt-6">
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm font-medium text-foreground">Total Mahasiswa</span>
                  <span className="text-xl font-semibold tabular-nums text-primary">{supervisor.students.length}</span>
                </div>
              </CardContent>
            </Card>
          </div>

          <Card className="self-start lg:col-span-2">
            <div className="border-b border-border px-4 py-3.5">
              <h2 className="text-base font-semibold text-foreground">
                Mahasiswa Bimbingan <span className="font-normal tabular-nums text-muted-foreground">({supervisor.students.length})</span>
              </h2>
            </div>
            {supervisor.students.length ? (
              <div className="divide-y divide-border">
                {supervisor.students.map((student) => (
                  <div key={student.id} className="flex flex-col gap-3 px-4 py-3.5 transition-colors duration-150 hover:bg-muted sm:flex-row sm:items-center sm:justify-between">
                    <UserCell
                      name={student.user.name}
                      photoUrl={student.user.profile_photo_url}
                      subtitle={[student.user.email, student.nim && `NIM: ${student.nim}`, student.study_program].filter(Boolean).join(' · ')}
                      tone="green"
                    />
                    <div className="flex items-center gap-1.5">
                      <Button asChild size="xs" variant="outline">
                        <Link href={r('admin.monitoring.student.show', student.id)}>Lihat Detail</Link>
                      </Button>
                      <Button asChild size="xs" variant="ghost">
                        <Link href={r('admin.users.edit', student.user.id)}>Edit</Link>
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <EmptyState
                icon={GraduationCap}
                title="Belum ada mahasiswa bimbingan"
                description="Supervisor ini belum memiliki mahasiswa yang dibimbing."
              />
            )}
          </Card>
        </div>
      </div>
    </AdminLayout>
  )
}
