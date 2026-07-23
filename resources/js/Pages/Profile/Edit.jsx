import { useState } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { X, Save, Eye, EyeOff } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import ProfilePhotoCard from '@/Components/ProfilePhotoCard'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

const roleLabel = { admin: 'Administrator', supervisor: 'Pembimbing', student: 'Mahasiswa' }

function PasswordField({ id, label, value, onChange, error, placeholder }) {
  const [visible, setVisible] = useState(false)
  return (
    <div className="space-y-2">
      <Label htmlFor={id}>{label}</Label>
      <div className="relative">
        <Input id={id} type={visible ? 'text' : 'password'} value={value} onChange={onChange} placeholder={placeholder} className="pr-10" />
        <button
          type="button"
          onClick={() => setVisible((v) => !v)}
          aria-label={visible ? 'Sembunyikan password' : 'Tampilkan password'}
          className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground transition-colors hover:text-foreground"
        >
          {visible ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
        </button>
      </div>
      {error && <p className="text-sm text-destructive">{error}</p>}
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
        <PageHeader
          title="Edit Profil"
          description="Perbarui informasi profil Anda"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('profile.show')}>
                <X /> Batal
              </Link>
            </Button>
          }
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div className="space-y-6 lg:col-span-1">
              <ProfilePhotoCard profilePhotoUrl={user.profile_photo_url} />

              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Informasi Akun</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3 pt-6 text-sm">
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Role</span>
                    <span className="font-medium text-foreground">{roleLabel[user.role] ?? user.role}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Bergabung</span>
                    <span className="font-medium tabular-nums text-foreground">
                      {new Date(user.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-muted-foreground">Status</span>
                    <Badge variant="success">Aktif</Badge>
                  </div>
                </CardContent>
              </Card>
            </div>

            <div className="space-y-6 lg:col-span-2">
              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Informasi Dasar</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5 pt-6">
                  {user.username === null ? (
                    <div className="space-y-2">
                      <Label htmlFor="username">Buat Username Anda</Label>
                      <Input id="username" value={data.username} onChange={(e) => setData('username', e.target.value)} placeholder="Masukkan username" required />
                      <p className="text-xs text-muted-foreground">Username hanya bisa dibuat satu kali dan tidak bisa diubah.</p>
                      {errors.username && <p className="text-sm text-destructive">{errors.username}</p>}
                    </div>
                  ) : (
                    <div className="space-y-2">
                      <Label htmlFor="username">Username</Label>
                      <Input id="username" value={user.username} readOnly disabled />
                      <p className="text-xs text-muted-foreground">Username tidak dapat diubah setelah dibuat.</p>
                    </div>
                  )}

                  <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div className="space-y-2">
                      <Label htmlFor="name">Nama Lengkap <span className="text-destructive">*</span></Label>
                      <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required />
                      {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="email">Email <span className="text-destructive">*</span></Label>
                      <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required />
                      {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="border-b">
                  <CardTitle>Ubah Password</CardTitle>
                  <CardDescription>Kosongkan jika tidak ingin mengubah password</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4 pt-6">
                  <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
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

                  <FlashBanner type="info" className="font-normal">
                    <p className="font-medium">Syarat Password</p>
                    <ul className="mt-1 list-inside list-disc space-y-0.5 text-xs">
                      <li>Minimal 8 karakter</li>
                      <li>Mengandung huruf besar dan kecil</li>
                      <li>Mengandung angka dan karakter khusus</li>
                    </ul>
                  </FlashBanner>
                </CardContent>
              </Card>

              {user.role === 'supervisor' && user.supervisor && (
                <Card>
                  <CardHeader className="border-b">
                    <CardTitle>Informasi Pembimbing</CardTitle>
                  </CardHeader>
                  <CardContent className="pt-6">
                    <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                      <div className="space-y-2">
                        <Label htmlFor="nip">NIP</Label>
                        <Input id="nip" value={data.nip} onChange={(e) => setData('nip', e.target.value)} />
                        {errors.nip && <p className="text-sm text-destructive">{errors.nip}</p>}
                      </div>
                    </div>
                  </CardContent>
                </Card>
              )}

              <div className="flex justify-end gap-2">
                <Button asChild type="button" variant="outline">
                  <Link href={r('profile.show')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>
                  <Save /> Simpan Perubahan
                </Button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </RoleLayout>
  )
}
