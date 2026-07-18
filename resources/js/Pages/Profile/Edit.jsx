import { useState } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { X, Save, Eye, EyeOff, CheckCircle2 } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

const roleLabel = {
  admin: 'Administrator',
  supervisor: 'Pembimbing',
  student: 'Mahasiswa',
}

function PasswordField({ id, label, value, onChange, error, placeholder }) {
  const [visible, setVisible] = useState(false)

  return (
    <div>
      <Label htmlFor={id}>{label}</Label>
      <div className="relative mt-2">
        <Input
          id={id}
          type={visible ? 'text' : 'password'}
          value={value}
          onChange={onChange}
          placeholder={placeholder}
          className="pr-10"
        />
        <button
          type="button"
          onClick={() => setVisible((v) => !v)}
          className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground"
        >
          {visible ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
        </button>
      </div>
      {error && <p className="mt-1 text-sm text-destructive">{error}</p>}
    </div>
  )
}

export default function Edit({ user }) {
  const { flash } = usePage().props
  const { data, setData, patch, processing, errors } = useForm({
    username: user.username ?? '',
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    nip: user.supervisor?.nip ?? '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    patch(r('profile.update'))
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-5xl space-y-6">
        <Card>
          <CardContent className="flex items-center justify-between p-6">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Edit Profil</h1>
              <p className="text-muted-foreground">Perbarui informasi profil Anda</p>
            </div>
            <Link href={r('profile.show')}>
              <Button type="button" variant="secondary">
                <X className="h-4 w-4" /> Batal
              </Button>
            </Link>
          </CardContent>
        </Card>

        {flash?.success && (
          <div className="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
            <CheckCircle2 className="h-5 w-5" /> {flash.success}
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div className="space-y-6 lg:col-span-1">
              <Card>
                <CardContent className="p-6 text-center">
                  <h3 className="mb-4 text-lg font-semibold text-foreground">Foto Profil</h3>
                  <div className="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 text-3xl font-bold text-white shadow-lg">
                    {user.name.charAt(0).toUpperCase()}
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardContent className="p-6">
                  <h3 className="mb-4 text-lg font-semibold text-foreground">Informasi Akun</h3>
                  <div className="space-y-3 text-sm">
                    <div className="flex justify-between">
                      <span className="text-muted-foreground">Role</span>
                      <span className="font-medium text-foreground">{roleLabel[user.role] ?? user.role}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-muted-foreground">Bergabung</span>
                      <span className="font-medium text-foreground">
                        {new Date(user.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                      </span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Status</span>
                      <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>

            <div className="space-y-6 lg:col-span-2">
              <Card>
                <div className="border-b border-border px-6 py-4">
                  <h3 className="text-lg font-semibold text-foreground">Informasi Dasar</h3>
                </div>
                <CardContent className="space-y-6 p-6">
                  {user.username === null ? (
                    <div>
                      <Label htmlFor="username">Buat Username Anda</Label>
                      <Input
                        id="username"
                        value={data.username}
                        onChange={(e) => setData('username', e.target.value)}
                        placeholder="Masukkan username"
                        className="mt-2"
                        required
                      />
                      <p className="mt-1 text-xs text-muted-foreground">Username hanya bisa dibuat satu kali dan tidak bisa diubah.</p>
                      {errors.username && <p className="mt-1 text-sm text-destructive">{errors.username}</p>}
                    </div>
                  ) : (
                    <div>
                      <Label htmlFor="username">Username</Label>
                      <Input id="username" value={user.username} readOnly disabled className="mt-2 cursor-not-allowed bg-muted" />
                      <p className="mt-1 text-xs text-muted-foreground">Username tidak dapat diubah setelah dibuat.</p>
                    </div>
                  )}

                  <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                      <Label htmlFor="name">Nama Lengkap <span className="text-destructive">*</span></Label>
                      <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} className="mt-2" required />
                      {errors.name && <p className="mt-1 text-sm text-destructive">{errors.name}</p>}
                    </div>
                    <div>
                      <Label htmlFor="email">Email <span className="text-destructive">*</span></Label>
                      <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className="mt-2" required />
                      {errors.email && <p className="mt-1 text-sm text-destructive">{errors.email}</p>}
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <div className="border-b border-border px-6 py-4">
                  <h3 className="text-lg font-semibold text-foreground">Ubah Password</h3>
                  <p className="mt-1 text-sm text-muted-foreground">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <CardContent className="p-6">
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <PasswordField
                      id="password"
                      label="Password Baru"
                      value={data.password}
                      onChange={(e) => setData('password', e.target.value)}
                      error={errors.password}
                      placeholder="Masukkan password baru"
                    />
                    <PasswordField
                      id="password_confirmation"
                      label="Konfirmasi Password Baru"
                      value={data.password_confirmation}
                      onChange={(e) => setData('password_confirmation', e.target.value)}
                      placeholder="Konfirmasi password baru"
                    />
                  </div>

                  <div className="mt-4 rounded-md bg-blue-50 p-3">
                    <p className="mb-2 text-sm font-medium text-blue-900">Syarat Password:</p>
                    <ul className="space-y-1 text-xs text-blue-800">
                      <li>Minimal 8 karakter</li>
                      <li>Mengandung huruf besar dan kecil</li>
                      <li>Mengandung angka dan karakter khusus</li>
                    </ul>
                  </div>
                </CardContent>
              </Card>

              {user.role === 'supervisor' && user.supervisor && (
                <Card>
                  <div className="border-b border-border px-6 py-4">
                    <h3 className="text-lg font-semibold text-foreground">Informasi Pembimbing</h3>
                  </div>
                  <CardContent className="p-6">
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                      <div>
                        <Label htmlFor="nip">NIP</Label>
                        <Input id="nip" value={data.nip} onChange={(e) => setData('nip', e.target.value)} className="mt-2" />
                        {errors.nip && <p className="mt-1 text-sm text-destructive">{errors.nip}</p>}
                      </div>
                    </div>
                  </CardContent>
                </Card>
              )}

              <Card>
                <CardContent className="flex justify-end gap-3 p-6">
                  <Link href={r('profile.show')}>
                    <Button type="button" variant="secondary">
                      <X className="h-4 w-4" /> Batal
                    </Button>
                  </Link>
                  <Button type="submit" disabled={processing}>
                    <Save className="h-4 w-4" /> Simpan Perubahan
                  </Button>
                </CardContent>
              </Card>
            </div>
          </div>
        </form>
      </div>
    </RoleLayout>
  )
}
