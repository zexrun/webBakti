import { Link, useForm } from '@inertiajs/react'
import { Pencil, Save, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Edit({ user, directorates, positions }) {
  const currentDirectorateName = user.supervisor?.direktorat ?? user.student?.direktorat ?? ''
  const { data, setData, put, processing, errors } = useForm({
    name: user.name,
    email: user.email,
    role: user.role,
    nim: user.student?.nim ?? '',
    universitas: user.student?.universitas ?? '',
    program_studi: user.student?.program_studi ?? '',
    semester: user.student?.semester ?? '',
    nip: user.supervisor?.nip ?? '',
    direktorat: directorates.find((d) => d.name === currentDirectorateName)?.id ?? '',
    jabatan: positions.find((p) => p.name === user.supervisor?.jabatan)?.id ?? '',
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
      <div className="mx-auto max-w-3xl space-y-6">
        <Card>
          <CardContent className="flex items-center gap-4 p-6">
            <div className="rounded-full bg-blue-100 p-3">
              <Pencil className="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-foreground">Edit Pengguna</h1>
              <p className="text-muted-foreground">{user.name} ({user.email})</p>
            </div>
          </CardContent>
        </Card>

        <form onSubmit={handleSubmit} className="space-y-6">
          <Card>
            <div className="border-b border-border bg-muted px-6 py-4">
              <h2 className="text-lg font-semibold text-foreground">Informasi Dasar</h2>
            </div>
            <CardContent className="space-y-6 p-6">
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

              <div className="space-y-2">
                <Label htmlFor="role">Peran (Role) <span className="text-destructive">*</span></Label>
                <Select value={data.role} onValueChange={(v) => setData('role', v)}>
                  <SelectTrigger id="role" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="admin">👨‍💼 Administrator</SelectItem>
                    <SelectItem value="supervisor">👨‍🏫 Pembimbing</SelectItem>
                    <SelectItem value="student">🎓 Mahasiswa</SelectItem>
                  </SelectContent>
                </Select>
                {errors.role && <p className="text-sm text-destructive">{errors.role}</p>}
              </div>
            </CardContent>
          </Card>

          {data.role === 'student' && (
            <Card>
              <div className="border-b border-blue-200 bg-blue-50 px-6 py-4">
                <h2 className="text-lg font-semibold text-foreground">Data Mahasiswa</h2>
              </div>
              <CardContent className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="nim">NIM</Label>
                  <Input id="nim" value={data.nim} onChange={(e) => setData('nim', e.target.value)} />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="universitas">Universitas</Label>
                  <Input id="universitas" value={data.universitas} onChange={(e) => setData('universitas', e.target.value)} />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="program_studi">Program Studi</Label>
                  <Input id="program_studi" value={data.program_studi} onChange={(e) => setData('program_studi', e.target.value)} />
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
              <div className="border-b border-green-200 bg-green-50 px-6 py-4">
                <h2 className="text-lg font-semibold text-foreground">Data Pembimbing</h2>
              </div>
              <CardContent className="p-6">
                <div className="space-y-2">
                  <Label htmlFor="nip">NIP</Label>
                  <Input id="nip" value={data.nip} onChange={(e) => setData('nip', e.target.value)} />
                </div>
              </CardContent>
            </Card>
          )}

          {(data.role === 'student' || data.role === 'supervisor') && (
            <Card>
              <div className="border-b border-purple-200 bg-purple-50 px-6 py-4">
                <h2 className="text-lg font-semibold text-foreground">Direktorat & Jabatan</h2>
              </div>
              <CardContent className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="direktorat">Direktorat</Label>
                  <Select
                    value={data.direktorat === '' ? 'none' : String(data.direktorat)}
                    onValueChange={(v) => setData('direktorat', v === 'none' ? '' : v)}
                  >
                    <SelectTrigger id="direktorat" className="w-full">
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
                    <Label htmlFor="jabatan">Jabatan</Label>
                    <Select
                      value={data.jabatan === '' ? 'none' : String(data.jabatan)}
                      onValueChange={(v) => setData('jabatan', v === 'none' ? '' : v)}
                    >
                      <SelectTrigger id="jabatan" className="w-full">
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

          <Card>
            <div className="border-b border-yellow-200 bg-yellow-50 px-6 py-4">
              <h2 className="text-lg font-semibold text-foreground">Ubah Password</h2>
              <p className="mt-1 text-sm text-yellow-700">Kosongkan jika tidak ingin mengubah password</p>
            </div>
            <CardContent className="space-y-6 p-6">
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

          <div className="flex flex-col justify-end gap-3 pt-2 sm:flex-row">
            <Link href={r('admin.users.index')}>
              <Button type="button" variant="secondary" className="w-full sm:w-auto">
                <X className="h-4 w-4" /> Batal
              </Button>
            </Link>
            <Button type="submit" disabled={processing} className="w-full sm:w-auto">
              <Save className="h-4 w-4" /> Simpan Perubahan
            </Button>
          </div>
        </form>
      </div>
    </AdminLayout>
  )
}
