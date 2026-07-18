import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, GraduationCap, Users, CheckCircle2, UserX, Plus } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

export default function StudentDetail({ student }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
              <div className="rounded-full bg-green-100 p-3">
                <GraduationCap className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-foreground">{student.user.name}</h1>
                <p className="text-muted-foreground">Detail Mahasiswa & Informasi Bimbingan</p>
              </div>
            </div>
            <div className="flex flex-wrap items-center gap-3">
              <Link href={r('admin.monitoring.index')}>
                <Button type="button" variant="secondary">
                  <ArrowLeft className="h-4 w-4" /> Kembali
                </Button>
              </Link>
              {student.supervisor && (
                <Link href={r('admin.monitoring.supervisor.show', student.supervisor.id)}>
                  <Button type="button" variant="outline">
                    <Users className="h-4 w-4" /> Lihat Supervisor
                  </Button>
                </Link>
              )}
              <Link href={r('admin.users.edit', student.user.id)}>
                <Button type="button">
                  <Pencil className="h-4 w-4" /> Edit Mahasiswa
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Informasi Mahasiswa</h2>
              <div className="space-y-4">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Nama Lengkap</p>
                  <p className="mt-1 text-foreground">{student.user.name}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Email</p>
                  <p className="mt-1 text-foreground">{student.user.email}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">NIM</p>
                  <p className="mt-1 text-foreground">{student.nim ?? '-'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Universitas</p>
                  <p className="mt-1 text-foreground">{student.universitas ?? '-'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Program Studi</p>
                  <p className="mt-1 text-foreground">{student.program_studi ?? '-'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Semester</p>
                  <p className="mt-1 text-foreground">{student.semester ?? '-'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Direktorat</p>
                  <p className="mt-1 text-foreground">{student.direktorat ?? '-'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Status</p>
                  <div className="mt-1">
                    <Badge variant="success">
                      <CheckCircle2 className="mr-1 h-3 w-3" /> Aktif
                    </Badge>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="lg:col-span-2">
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Pembimbing</h2>
              {student.supervisor ? (
                <div className="flex items-center gap-3 rounded-lg bg-blue-50 p-4">
                  <div className="rounded-full bg-blue-100 p-2">
                    <Users className="h-5 w-5 text-blue-600" />
                  </div>
                  <div className="flex-1">
                    <h3 className="font-medium text-blue-900">{student.supervisor.user.name}</h3>
                    <p className="text-sm text-blue-700">{student.supervisor.user.email}</p>
                    <div className="mt-1 flex items-center gap-3 text-xs text-blue-600">
                      {student.supervisor.nip && <span>NIP: {student.supervisor.nip}</span>}
                      {student.supervisor.jabatan && <span>{student.supervisor.jabatan}</span>}
                    </div>
                  </div>
                  <Link href={r('admin.monitoring.supervisor.show', student.supervisor.id)}>
                    <Button type="button" size="sm" variant="outline">Detail</Button>
                  </Link>
                </div>
              ) : (
                <div className="py-8 text-center">
                  <UserX className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                  <h3 className="mb-2 text-sm font-medium text-foreground">Belum Ada Pembimbing</h3>
                  <p className="mb-4 text-sm text-muted-foreground">Mahasiswa ini belum memiliki pembimbing yang ditugaskan</p>
                  <Link href={r('admin.plotting')}>
                    <Button type="button">
                      <Plus className="h-4 w-4" /> Assign Pembimbing
                    </Button>
                  </Link>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </AdminLayout>
  )
}
