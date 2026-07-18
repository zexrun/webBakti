import { useForm } from '@inertiajs/react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

export default function ResetPassword({ email, token }) {
  const { data, setData, post, processing, errors } = useForm({
    token,
    email: email ?? '',
    password: '',
    password_confirmation: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('password.store'))
  }

  return (
    <GuestLayout>
      <Card>
        <CardContent className="p-6">
          <div className="mb-6 text-center">
            <h1 className="mb-1 text-2xl font-bold text-foreground">Reset Password</h1>
            <p className="text-sm text-muted-foreground">Buat password baru untuk akun Anda</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                className="mt-1"
                required
                autoFocus
                autoComplete="username"
              />
              {errors.email && <p className="mt-1 text-sm text-destructive">{errors.email}</p>}
            </div>

            <div>
              <Label htmlFor="password">Password Baru</Label>
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
              <Label htmlFor="password_confirmation">Konfirmasi Password Baru</Label>
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

            <div className="flex justify-end pt-2">
              <Button type="submit" disabled={processing}>Reset Password</Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
