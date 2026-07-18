import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, CheckCircle2, XCircle } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

const roleBadge = {
  admin: { label: 'Administrator', className: 'bg-red-100 text-red-800' },
  supervisor: { label: 'Pembimbing', className: 'bg-blue-100 text-blue-800' },
  student: { label: 'Mahasiswa', className: 'bg-green-100 text-green-800' },
}

function Field({ label, value, muted }) {
  return (
    <div>
      <label className="mb-1 block text-sm font-medium text-muted-foreground">{label}</label>
      <div className={`rounded-md border border-border bg-muted px-3 py-2 text-sm ${muted ? 'text-muted-foreground' : 'text-foreground'}`}>
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
        <Card>
          <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Profil Pengguna</h1>
              <p className="text-muted-foreground">Informasi lengkap akun Anda</p>
            </div>
            <div className="flex items-center gap-3">
              <Button type="button" variant="secondary" onClick={() => window.history.back()}>
                <ArrowLeft className="h-4 w-4" /> Kembali
              </Button>
              <Link href={r('profile.edit')}>
                <Button type="button">
                  <Pencil className="h-4 w-4" /> Edit Profil
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-1">
            <Card>
              <CardContent className="p-6 text-center">
                <div className="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 text-3xl font-bold text-white shadow-lg">
                  {user.name.charAt(0).toUpperCase()}
                </div>
                <h2 className="mb-2 text-xl font-bold text-foreground">{user.name}</h2>
                <span className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ${role.className}`}>
                  {role.label}
                </span>
                {user.username && <p className="mt-2 text-muted-foreground">@{user.username}</p>}
              </CardContent>
            </Card>

            <Card>
              <CardContent className="p-6">
                <h3 className="mb-4 text-lg font-semibold text-foreground">Status Akun</h3>
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Status</span>
                    <Badge variant="success">
                      <CheckCircle2 className="mr-1 h-3 w-3" /> Aktif
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Email</span>
                    {user.email_verified_at ? (
                      <Badge variant="success">
                        <CheckCircle2 className="mr-1 h-3 w-3" /> Terverifikasi
                      </Badge>
                    ) : (
                      <Badge variant="destructive">
                        <XCircle className="mr-1 h-3 w-3" /> Belum Terverifikasi
                      </Badge>
                    )}
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Bergabung</span>
                    <span className="text-sm font-medium text-foreground">
                      {new Date(user.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="lg:col-span-2">
            <Card>
              <div className="border-b border-border px-6 py-4">
                <h3 className="text-lg font-semibold text-foreground">Informasi Detail</h3>
              </div>
              <CardContent className="space-y-8 p-6">
                <div>
                  <h4 className="mb-4 text-sm font-medium text-foreground">Informasi Dasar</h4>
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Field label="Nama Lengkap" value={user.name} />
                    <Field label="Email" value={user.email} />
                    <div className="md:col-span-2">
                      <Field label="Username" value={user.username ?? 'Belum diisi'} muted={!user.username} />
                    </div>
                  </div>
                </div>

                {user.role === 'student' && user.student && (
                  <div className="space-y-6 border-t border-border pt-8">
                    <h4 className="text-sm font-medium text-foreground">Informasi Mahasiswa</h4>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                      <Field label="NIM" value={user.student.nim ?? 'Belum diisi'} muted={!user.student.nim} />
                      <Field
                        label="Semester"
                        value={user.student.semester ? `Semester ${user.student.semester}` : 'Belum diisi'}
                        muted={!user.student.semester}
                      />
                      <Field label="Universitas" value={user.student.universitas ?? 'Belum diisi'} muted={!user.student.universitas} />
                      <Field label="Program Studi" value={user.student.program_studi ?? 'Belum diisi'} muted={!user.student.program_studi} />
                      <Field label="Direktorat" value={user.student.direktorat ?? 'Belum ditentukan'} muted={!user.student.direktorat} />
                      <Field
                        label="Pembimbing"
                        value={user.student.supervisor?.user?.name ?? 'Belum ditugaskan'}
                        muted={!user.student.supervisor?.user?.name}
                      />
                    </div>
                    <Field
                      label="Periode Magang"
                      value={
                        user.student.periode_mulai && user.student.periode_selesai
                          ? `${new Date(user.student.periode_mulai).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })} - ${new Date(user.student.periode_selesai).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`
                          : 'Periode belum ditentukan'
                      }
                      muted={!(user.student.periode_mulai && user.student.periode_selesai)}
                    />
                  </div>
                )}

                {user.role === 'supervisor' && user.supervisor && (
                  <div className="space-y-6 border-t border-border pt-8">
                    <h4 className="text-sm font-medium text-foreground">Informasi Pembimbing</h4>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                      <Field label="NIP" value={user.supervisor.nip ?? 'Belum diisi'} muted={!user.supervisor.nip} />
                      <Field label="Jabatan" value={user.supervisor.jabatan ?? 'Belum diisi'} muted={!user.supervisor.jabatan} />
                      <div className="md:col-span-2">
                        <Field label="Direktorat" value={user.supervisor.direktorat ?? '-'} />
                      </div>
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </RoleLayout>
  )
}
