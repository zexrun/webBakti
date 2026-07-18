import { Link, useForm } from '@inertiajs/react'
import { UserPlus, Mail, Users, X, Send } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

const roleDescriptions = {
  admin: {
    title: 'Administrator',
    text: 'Memiliki akses penuh ke sistem, dapat mengelola semua pengguna, konfigurasi, dan data sistem.',
  },
  student: {
    title: 'Mahasiswa',
    text: 'Dapat mengakses fitur mahasiswa, melihat pembimbing yang ditugaskan, dan mengelola profil pribadi.',
  },
  supervisor: {
    title: 'Pembimbing',
    text: 'Dapat membimbing mahasiswa, melihat daftar mahasiswa yang dibimbing, dan mengelola data pembimbingan.',
  },
}

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    role: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('admin.users.store'))
  }

  return (
    <AdminLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <Card>
          <CardContent className="flex items-center gap-4 p-6">
            <div className="rounded-full bg-blue-100 p-3">
              <UserPlus className="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-foreground">Tambah Pengguna Baru</h1>
              <p className="text-muted-foreground">Buat akun pengguna baru untuk sistem</p>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h2 className="text-lg font-semibold text-foreground">Informasi Pengguna</h2>
            <p className="mb-6 text-sm text-muted-foreground">Masukkan data pengguna yang akan dibuat</p>

            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="email" className="flex items-center gap-1">
                  <Mail className="h-4 w-4" /> Alamat Email <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="email"
                  type="email"
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  placeholder="contoh: user@email.com"
                  required
                />
                {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                <p className="text-sm text-muted-foreground">Email akan digunakan untuk login dan aktivasi akun</p>
              </div>

              <div className="space-y-2">
                <Label htmlFor="role" className="flex items-center gap-1">
                  <Users className="h-4 w-4" /> Peran (Role) <span className="text-destructive">*</span>
                </Label>
                <Select id="role" value={data.role} onChange={(e) => setData('role', e.target.value)} required>
                  <option value="">-- Pilih Peran --</option>
                  <option value="admin">👨‍💼 Administrator</option>
                  <option value="student">🎓 Mahasiswa</option>
                  <option value="supervisor">👨‍🏫 Pembimbing</option>
                </Select>
                {errors.role && <p className="text-sm text-destructive">{errors.role}</p>}
              </div>

              {data.role && roleDescriptions[data.role] && (
                <div className="rounded-lg border border-blue-200 bg-blue-50 p-4">
                  <h4 className="text-sm font-medium text-blue-800">{roleDescriptions[data.role].title}</h4>
                  <p className="mt-1 text-sm text-blue-700">{roleDescriptions[data.role].text}</p>
                </div>
              )}

              <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <h4 className="text-sm font-medium text-yellow-800">Informasi Penting</h4>
                <ul className="mt-1 list-inside list-disc space-y-1 text-sm text-yellow-700">
                  <li>Email aktivasi akan dikirim ke alamat email yang dimasukkan</li>
                  <li>Pengguna harus mengaktifkan akun melalui email sebelum dapat login</li>
                  <li>Password akan dibuat otomatis dan dikirim melalui email</li>
                </ul>
              </div>

              <div className="flex flex-col justify-end gap-3 border-t border-border pt-6 sm:flex-row">
                <Link href={r('admin.users.index')}>
                  <Button type="button" variant="secondary" className="w-full sm:w-auto">
                    <X className="h-4 w-4" /> Batal
                  </Button>
                </Link>
                <Button type="submit" disabled={processing} className="w-full sm:w-auto">
                  <Send className="h-4 w-4" /> Buat Pengguna
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
