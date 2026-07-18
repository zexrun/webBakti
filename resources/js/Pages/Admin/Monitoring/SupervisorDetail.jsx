import { Link } from '@inertiajs/react'
import { ArrowLeft, Pencil, Users, GraduationCap, CheckCircle2 } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

export default function SupervisorDetail({ supervisor }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
              <div className="rounded-full bg-blue-100 p-3">
                <Users className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-foreground">{supervisor.user.name}</h1>
                <p className="text-muted-foreground">Detail Supervisor & Mahasiswa Bimbingan</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <Link href={r('admin.monitoring.index')}>
                <Button type="button" variant="secondary">
                  <ArrowLeft className="h-4 w-4" /> Kembali
                </Button>
              </Link>
              <Link href={r('admin.users.edit', supervisor.user.id)}>
                <Button type="button">
                  <Pencil className="h-4 w-4" /> Edit Supervisor
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="space-y-6 lg:col-span-1">
            <Card>
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Informasi Supervisor</h2>
                <div className="space-y-4">
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Nama Lengkap</p>
                    <p className="mt-1 text-foreground">{supervisor.user.name}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Email</p>
                    <p className="mt-1 text-foreground">{supervisor.user.email}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">NIP</p>
                    <p className="mt-1 text-foreground">{supervisor.nip ?? '-'}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Direktorat</p>
                    <p className="mt-1 text-foreground">{supervisor.direktorat ?? '-'}</p>
                  </div>
                  <div>
                    <p className="text-sm font-medium text-muted-foreground">Jabatan</p>
                    <p className="mt-1 text-foreground">{supervisor.jabatan ?? '-'}</p>
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

            <Card>
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Statistik</h2>
                <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3">
                  <span className="text-sm font-medium text-blue-900">Total Mahasiswa</span>
                  <span className="text-xl font-bold text-blue-600">{supervisor.students.length}</span>
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="lg:col-span-2">
            <Card>
              <div className="border-b border-border px-6 py-4">
                <h2 className="text-lg font-semibold text-foreground">Mahasiswa Bimbingan ({supervisor.students.length})</h2>
              </div>
              <CardContent className="p-0">
                <div className="divide-y divide-border">
                  {supervisor.students.length ? (
                    supervisor.students.map((student) => (
                      <div key={student.id} className="flex items-center justify-between p-6 hover:bg-accent">
                        <div className="flex items-center gap-4">
                          <div className="rounded-full bg-green-100 p-3">
                            <GraduationCap className="h-5 w-5 text-green-600" />
                          </div>
                          <div>
                            <h3 className="text-lg font-medium text-foreground">{student.user.name}</h3>
                            <p className="text-sm text-muted-foreground">{student.user.email}</p>
                            <div className="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                              {student.nim && <span>NIM: {student.nim}</span>}
                              {student.universitas && <span>{student.universitas}</span>}
                              {student.program_studi && <span>{student.program_studi}</span>}
                            </div>
                          </div>
                        </div>
                        <div className="flex items-center gap-3">
                          <Badge variant="success">Aktif</Badge>
                          <Link href={r('admin.monitoring.student.show', student.id)}>
                            <Button type="button" size="sm" variant="secondary">Lihat Detail</Button>
                          </Link>
                          <Link href={r('admin.users.edit', student.user.id)}>
                            <Button type="button" size="sm" variant="outline">Edit</Button>
                          </Link>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="p-12 text-center">
                      <GraduationCap className="mx-auto mb-4 h-16 w-16 text-muted-foreground" />
                      <h3 className="mb-2 text-lg font-medium text-foreground">Belum Ada Mahasiswa Bimbingan</h3>
                      <p className="text-muted-foreground">Supervisor ini belum memiliki mahasiswa yang dibimbing</p>
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
