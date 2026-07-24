import { Link, useForm } from '@inertiajs/react'
import { Save, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Edit({ user, directorates, positions }) {
  const currentDirectorateName = user.supervisor?.directorate ?? user.student?.directorate ?? ''
  const { data, setData, put, processing, errors } = useForm({
    name: user.name,
    email: user.email,
    role: user.role,
    nim: user.student?.nim ?? '',
    university: user.student?.university ?? '',
    study_program: user.student?.study_program ?? '',
    semester: user.student?.semester ?? '',
    employee_id: user.supervisor?.employee_id ?? '',
    directorate: directorates.find((d) => d.name === currentDirectorateName)?.id ?? '',
    position: positions.find((p) => p.name === user.supervisor?.position)?.id ?? '',
    password: '',
    password_confirmation: '',
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    put(r('admin.users.update', user.id))
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader title="Edit Pengguna" description={`${user.name} (${user.email})`} />

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div className="space-y-6 lg:col-span-2">
              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5 pt-6">
                  <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div className="space-y-2">
                      <Label htmlFor="name">Nama Lengkap <span className="text-destructive">*</span></Label>
                      <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required />
                      {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="email">Alamat Email <span className="text-destructive">*</span></Label>
                      <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required />
                      {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="role">Peran (Role) <span className="text-destructive">*</span></Label>
                    <Select value={data.role} onValueChange={(v) => setData('role', v)}>
                      <SelectTrigger id="role" className="w-full sm:w-64">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="admin">Administrator</SelectItem>
                        <SelectItem value="supervisor">Pembimbing</SelectItem>
                        <SelectItem value="student">Mahasiswa</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.role && <p className="text-sm text-destructive">{errors.role}</p>}
                  </div>
                </CardContent>
              </Card>

              {data.role === 'student' && (
                <Card>
                  <CardHeader className="border-b">
                    <CardTitle>Data Mahasiswa</CardTitle>
                  </CardHeader>
                  <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-2">
                    <div className="space-y-2">
                      <Label htmlFor="nim">NIM</Label>
                      <Input id="nim" value={data.nim} onChange={(e) => setData('nim', e.target.value)} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="university">Universitas</Label>
                      <Input id="university" value={data.university} onChange={(e) => setData('university', e.target.value)} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="study_program">Program Studi</Label>
                      <Input id="study_program" value={data.study_program} onChange={(e) => setData('study_program', e.target.value)} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="semester">Semester</Label>
                      <Input id="semester" value={data.semester} onChange={(e) => setData('semester', e.target.value)} />
                    </div>
                  </CardContent>
                </Card>
              )}

              {data.role === 'supervisor' && (
                <Card>
                  <CardHeader className="border-b">
                    <CardTitle>Data Pembimbing</CardTitle>
                  </CardHeader>
                  <CardContent className="pt-6">
                    <div className="space-y-2 sm:w-64">
                      <Label htmlFor="employee_id">NIP</Label>
                      <Input id="employee_id" value={data.employee_id} onChange={(e) => setData('employee_id', e.target.value)} />
                    </div>
                  </CardContent>
                </Card>
              )}

              {(data.role === 'student' || data.role === 'supervisor') && (
                <Card>
                  <CardHeader className="border-b">
                    <CardTitle>Direktorat & Jabatan</CardTitle>
                  </CardHeader>
                  <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-2">
                    <div className="space-y-2">
                      <Label htmlFor="directorate">Direktorat</Label>
                      <Select
                        value={data.directorate === '' ? 'none' : String(data.directorate)}
                        onValueChange={(v) => setData('directorate', v === 'none' ? '' : v)}
                      >
                        <SelectTrigger id="directorate" className="w-full">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="none">Pilih Direktorat</SelectItem>
                          {directorates.map((dir) => (
                            <SelectItem key={dir.id} value={String(dir.id)}>{dir.name}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>

                    {data.role === 'supervisor' && (
                      <div className="space-y-2">
                        <Label htmlFor="position">Jabatan</Label>
                        <Select
                          value={data.position === '' ? 'none' : String(data.position)}
                          onValueChange={(v) => setData('position', v === 'none' ? '' : v)}
                        >
                          <SelectTrigger id="position" className="w-full">
                            <SelectValue />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="none">Pilih Jabatan</SelectItem>
                            {positions.map((pos) => (
                              <SelectItem key={pos.id} value={String(pos.id)}>{pos.name}</SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      </div>
                    )}
                  </CardContent>
                </Card>
              )}
            </div>

            <Card className="self-start">
              <CardHeader className="border-b">
                <CardTitle>Ubah Password</CardTitle>
                <CardDescription>Kosongkan jika tidak ingin mengubah password</CardDescription>
              </CardHeader>
              <CardContent className="space-y-5 pt-6">
                <div className="space-y-2">
                  <Label htmlFor="password">Password Baru</Label>
                  <Input id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
                  {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="password_confirmation">Konfirmasi Password Baru</Label>
                  <Input id="password_confirmation" type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} />
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="mt-6 flex flex-col justify-end gap-2 sm:flex-row">
            <Button asChild type="button" variant="outline" className="w-full sm:w-auto">
              <Link href={r('admin.users.index')}>
                <X /> Batal
              </Link>
            </Button>
            <Button type="submit" disabled={processing} className="w-full sm:w-auto">
              <Save /> Simpan Perubahan
            </Button>
          </div>
        </form>
      </div>
    </AdminLayout>
  )
}
