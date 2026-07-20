import { Link, useForm } from '@inertiajs/react'
import { KeyRound } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { AuthHeader, AuthBanner, PasswordInput } from './auth-parts'

export default function Activate({ token, email }) {
  const { data, setData, post, processing, errors } = useForm({
    token,
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')
  const passwordMismatch = data.password_confirmation.length > 0 && data.password !== data.password_confirmation

  function handleSubmit(e) {
    e.preventDefault()
    post(r('activation.activate'))
  }

  return (
    <GuestLayout>
      <Card>
        <CardContent className="p-6">
          <AuthHeader icon={KeyRound} title="Aktivasi Akun" description="Lengkapi data untuk mengaktifkan akun Anda" />

          <AuthBanner type="info">
            Aktivasi akun email: <strong>{email}</strong>
          </AuthBanner>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">Nama Lengkap</Label>
              <Input id="name" type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} placeholder="Masukkan nama lengkap" required autoFocus />
              {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="username">Username</Label>
              <Input id="username" type="text" value={data.username} onChange={(e) => setData('username', e.target.value)} placeholder="Pilih username unik" required />
              {errors.username && <p className="text-sm text-destructive">{errors.username}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password">Password Baru</Label>
              <PasswordInput id="password" value={data.password} onChange={(e) => setData('password', e.target.value)} placeholder="Password yang kuat" autoComplete="new-password" />
              {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
              <PasswordInput id="password_confirmation" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} placeholder="Ulangi password" autoComplete="new-password" />
              {passwordMismatch && <p className="text-sm text-destructive">Password tidak cocok</p>}
              {errors.password_confirmation && <p className="text-sm text-destructive">{errors.password_confirmation}</p>}
            </div>

            <Button type="submit" disabled={processing} className="w-full">
              <KeyRound /> Aktifkan Akun
            </Button>

            <p className="text-center text-sm text-muted-foreground">
              Sudah memiliki akun?{' '}
              <Link href={r('login')} className="font-medium text-primary hover:underline">Masuk di sini</Link>
            </p>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
