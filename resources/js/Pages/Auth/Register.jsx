import { Link, useForm } from '@inertiajs/react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

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
          <div className="mb-6 text-center">
            <h1 className="mb-1 text-2xl font-bold text-foreground">Buat Akun Baru</h1>
            <p className="text-sm text-muted-foreground">Daftarkan diri Anda untuk mulai menggunakan sistem</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <Label htmlFor="name">Nama</Label>
              <Input
                id="name"
                type="text"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                className="mt-1"
                required
                autoFocus
                autoComplete="name"
              />
              {errors.name && <p className="mt-1 text-sm text-destructive">{errors.name}</p>}
            </div>

            <div>
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                className="mt-1"
                required
                autoComplete="username"
              />
              {errors.email && <p className="mt-1 text-sm text-destructive">{errors.email}</p>}
            </div>

            <div>
              <Label htmlFor="password">Password</Label>
              <Input
                id="password"
                type="password"
                value={data.password}
                onChange={(e) => setData('password', e.target.value)}
                className="mt-1"
                required
                autoComplete="new-password"
              />
              {errors.password && <p className="mt-1 text-sm text-destructive">{errors.password}</p>}
            </div>

            <div>
              <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
              <Input
                id="password_confirmation"
                type="password"
                value={data.password_confirmation}
                onChange={(e) => setData('password_confirmation', e.target.value)}
                className="mt-1"
                required
                autoComplete="new-password"
              />
              {errors.password_confirmation && <p className="mt-1 text-sm text-destructive">{errors.password_confirmation}</p>}
            </div>

            <div className="flex items-center justify-between pt-2">
              <Link href={r('login')} className="text-sm text-muted-foreground underline hover:text-foreground">
                Sudah punya akun?
              </Link>
              <Button type="submit" disabled={processing}>Daftar</Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
