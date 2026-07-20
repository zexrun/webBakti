import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, CheckCircle2, XCircle } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

const roleBadge = {
  admin: { label: 'Administrator', className: 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300' },
  supervisor: { label: 'Pembimbing', className: 'bg-blue-100 text-blue-800 dark:bg-blue-500/15 dark:text-blue-300' },
  student: { label: 'Mahasiswa', className: 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-300' },
}

function Field({ label, value, muted, className }) {
  return (
    <div className={className}>
      <p className="mb-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">{label}</p>
      <div className={cn('rounded-md border border-border bg-muted px-3 py-2 text-sm', muted ? 'text-muted-foreground' : 'text-foreground')}>
        {value}
      </div>
    </div>
  )
}

export default function Show({ user }) {
  const r = (name) => (window.route ? window.route(name) : '#')
  const role = roleBadge[user.role] ?? { label: user.role, className: 'bg-secondary text-secondary-foreground' }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader
          title="Profil Pengguna"
          description="Informasi lengkap akun Anda"
          actions={
            <Button asChild>
              <Link href={r('profile.edit')}>
                <Pencil /> Edit Profil
              </Link>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-1">
            <Card>
              <CardContent className="flex flex-col items-center p-6 text-center">
                <div className="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-sidebar text-3xl font-bold text-sidebar-primary-foreground">
                  {user.name.charAt(0).toUpperCase()}
                </div>
                <h2 className="text-lg font-bold text-foreground">{user.name}</h2>
                <span className={cn('mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium', role.className)}>
                  {role.label}
                </span>
                {user.username && <p className="mt-2 text-sm text-muted-foreground">@{user.username}</p>}
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="border-b">
                <CardTitle>Status Akun</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3 pt-6">
                <div className="flex items-center justify-between">
                  <span className="text-sm text-muted-foreground">Status</span>
                  <Badge variant="success"><CheckCircle2 /> Aktif</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-muted-foreground">Email</span>
                  {user.email_verified_at ? (
                    <Badge variant="success"><CheckCircle2 /> Terverifikasi</Badge>
                  ) : (
                    <Badge variant="destructive"><XCircle /> Belum Terverifikasi</Badge>
                  )}
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-muted-foreground">Bergabung</span>
                  <span className="text-sm font-medium tabular-nums text-foreground">
                    {new Date(user.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                  </span>
                </div>
              </CardContent>
            </Card>
          </div>

          <Card className="self-start lg:col-span-2">
            <CardHeader className="border-b">
              <CardTitle>Informasi Detail</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6 pt-6">
              <div>
                <h4 className="mb-3 text-sm font-medium text-foreground">Informasi Dasar</h4>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <Field label="Nama Lengkap" value={user.name} />
                  <Field label="Email" value={user.email} />
                  <Field label="Username" value={user.username ?? 'Belum diisi'} muted={!user.username} className="md:col-span-2" />
                </div>
              </div>

              {user.role === 'student' && user.student && (
                <div className="space-y-4 border-t border-border pt-6">
                  <h4 className="text-sm font-medium text-foreground">Informasi Mahasiswa</h4>
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <Field label="NIM" value={user.student.nim ?? 'Belum diisi'} muted={!user.student.nim} />
                    <Field label="Semester" value={user.student.semester ? `Semester ${user.student.semester}` : 'Belum diisi'} muted={!user.student.semester} />
                    <Field label="Universitas" value={user.student.universitas ?? 'Belum diisi'} muted={!user.student.universitas} />
                    <Field label="Program Studi" value={user.student.program_studi ?? 'Belum diisi'} muted={!user.student.program_studi} />
                    <Field label="Direktorat" value={user.student.direktorat ?? 'Belum ditentukan'} muted={!user.student.direktorat} />
                    <Field label="Pembimbing" value={user.student.supervisor?.user?.name ?? 'Belum ditugaskan'} muted={!user.student.supervisor?.user?.name} />
                  </div>
                  <Field
                    label="Periode Magang"
                    value={
                      user.student.periode_mulai && user.student.periode_selesai
                        ? `${new Date(user.student.periode_mulai).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })} – ${new Date(user.student.periode_selesai).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`
                        : 'Periode belum ditentukan'
                    }
                    muted={!(user.student.periode_mulai && user.student.periode_selesai)}
                  />
                </div>
              )}

              {user.role === 'supervisor' && user.supervisor && (
                <div className="space-y-4 border-t border-border pt-6">
                  <h4 className="text-sm font-medium text-foreground">Informasi Pembimbing</h4>
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <Field label="NIP" value={user.supervisor.nip ?? 'Belum diisi'} muted={!user.supervisor.nip} />
                    <Field label="Jabatan" value={user.supervisor.jabatan ?? 'Belum diisi'} muted={!user.supervisor.jabatan} />
                    <Field label="Direktorat" value={user.supervisor.direktorat ?? '-'} className="md:col-span-2" />
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </RoleLayout>
  )
}
