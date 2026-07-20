import { Link, useForm } from '@inertiajs/react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { AuthHeader, PasswordInput } from './auth-parts'

export default function Register() {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('register'), {
      onFinish: () => reset('password', 'password_confirmation'),
    })
  }

  return (
    <GuestLayout>
      <Card>
        <CardContent className="p-6">
          <AuthHeader title="Buat Akun Baru" description="Daftarkan diri Anda untuk mulai menggunakan sistem" />

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">Nama</Label>
              <Input id="name" type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} required autoFocus autoComplete="name" />
              {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required autoComplete="username" />
              {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <PasswordInput id="password" value={data.password} onChange={(e) => setData('password', e.target.value)} autoComplete="new-password" />
              {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
              <PasswordInput id="password_confirmation" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} autoComplete="new-password" />
              {errors.password_confirmation && <p className="text-sm text-destructive">{errors.password_confirmation}</p>}
            </div>

            <Button type="submit" disabled={processing} className="w-full">Daftar</Button>

            <p className="text-center text-sm text-muted-foreground">
              Sudah punya akun?{' '}
              <Link href={r('login')} className="font-medium text-primary hover:underline">Masuk di sini</Link>
            </p>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
