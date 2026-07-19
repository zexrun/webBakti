import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, Users, CheckCircle2, UserX, Plus } from 'lucide-react'
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

export default function StudentDetail({ student }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title={student.user.name}
          description="Detail mahasiswa & informasi bimbingan"
          actions={
            <>
              <Button asChild variant="outline">
                <Link href={r('admin.monitoring.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
              {student.supervisor && (
                <Button asChild variant="outline">
                  <Link href={r('admin.monitoring.supervisor.show', student.supervisor.id)}>
                    <Users /> Lihat Supervisor
                  </Link>
                </Button>
              )}
              <Button asChild>
                <Link href={r('admin.users.edit', student.user.id)}>
                  <Pencil /> Edit Mahasiswa
                </Link>
              </Button>
            </>
          }
        />

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card className="self-start">
            <CardHeader className="border-b">
              <CardTitle>Informasi Mahasiswa</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4 pt-6">
              <InfoRow label="Nama Lengkap" value={student.user.name} />
              <InfoRow label="Email" value={student.user.email} />
              <InfoRow label="NIM" value={student.nim} />
              <InfoRow label="Universitas" value={student.universitas} />
              <InfoRow label="Program Studi" value={student.program_studi} />
              <InfoRow label="Semester" value={student.semester} />
              <InfoRow label="Direktorat" value={student.direktorat} />
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</p>
                <Badge variant="success" className="mt-1">
                  <CheckCircle2 /> Aktif
                </Badge>
              </div>
            </CardContent>
          </Card>

          <Card className="self-start lg:col-span-2">
            <CardHeader className="border-b">
              <CardTitle>Pembimbing</CardTitle>
            </CardHeader>
            <CardContent className="pt-6">
              {student.supervisor ? (
                <div className="flex flex-col gap-3 rounded-lg border border-border bg-muted p-4 sm:flex-row sm:items-center sm:justify-between">
                  <UserCell
                    name={student.supervisor.user.name}
                    subtitle={[
                      student.supervisor.user.email,
                      student.supervisor.nip && `NIP: ${student.supervisor.nip}`,
                      student.supervisor.jabatan,
                    ].filter(Boolean).join(' · ')}
                  />
                  <Button asChild size="xs" variant="outline">
                    <Link href={r('admin.monitoring.supervisor.show', student.supervisor.id)}>Detail</Link>
                  </Button>
                </div>
              ) : (
                <EmptyState
                  icon={UserX}
                  title="Belum ada pembimbing"
                  description="Mahasiswa ini belum memiliki pembimbing yang ditugaskan."
                  action={
                    <Button asChild>
                      <Link href={r('admin.plotting')}>
                        <Plus /> Assign Pembimbing
                      </Link>
                    </Button>
                  }
                  className="py-8"
                />
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </AdminLayout>
  )
}
