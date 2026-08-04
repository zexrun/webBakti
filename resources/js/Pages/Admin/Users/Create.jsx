import { Link, useForm } from '@inertiajs/react'
import { Info, Send, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
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
        <PageHeader title="Tambah Pengguna" description="Buat akun pengguna baru untuk sistem" />

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Informasi Pengguna</CardTitle>
            <CardDescription>Masukkan data pengguna yang akan dibuat</CardDescription>
          </CardHeader>
          <CardContent className="pt-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label htmlFor="email">
                  Alamat Email <span className="text-destructive">*</span>
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
                <Label htmlFor="role">
                  Peran (Role) <span className="text-destructive">*</span>
                </Label>
                <Select value={data.role} onValueChange={(v) => setData('role', v)}>
                  <SelectTrigger id="role" className="w-full">
                    <SelectValue placeholder="Pilih peran" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="admin">Administrator</SelectItem>
                    <SelectItem value="student">Mahasiswa</SelectItem>
                    <SelectItem value="supervisor">Pembimbing</SelectItem>
                  </SelectContent>
                </Select>
                {errors.role && <p className="text-sm text-destructive">{errors.role}</p>}
              </div>

              {data.role && roleDescriptions[data.role] && (
                <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/10">
                  <h4 className="text-sm font-medium text-blue-800 dark:text-blue-200">{roleDescriptions[data.role].title}</h4>
                  <p className="mt-1 text-sm text-blue-700 dark:text-blue-300">{roleDescriptions[data.role].text}</p>
                </div>
              )}

              <div className="flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                <Info className="mt-0.5 h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400" />
                <div>
                  <h4 className="text-sm font-medium text-amber-800 dark:text-amber-200">Informasi Penting</h4>
                  <ul className="mt-1 list-inside list-disc space-y-1 text-sm text-amber-700 dark:text-amber-300">
                    <li>Email aktivasi akan dikirim ke alamat email yang dimasukkan</li>
                    <li>Pengguna harus mengaktifkan akun melalui email sebelum dapat login</li>
                    <li>Password akan dibuat otomatis dan dikirim melalui email</li>
                  </ul>
                </div>
              </div>

              <div className="flex flex-col justify-end gap-2 border-t border-border pt-5 sm:flex-row">
                <Button asChild type="button" variant="outline" className="w-full sm:w-auto">
                  <Link href={r('admin.users.index')}>
                    <X className="h-4 w-4" /> Batal
                  </Link>
                </Button>
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
