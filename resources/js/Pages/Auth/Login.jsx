import { Link, useForm } from '@inertiajs/react'
import { User, Lock } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'
import { AuthHeader, AuthBanner, PasswordInput } from './auth-parts'

export default function Login({ status }) {
  const { data, setData, post, processing, errors } = useForm({
    login: '',
    password: '',
    remember: false,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('login'))
  }

  return (
    <GuestLayout>
      <Card>
        <CardContent className="p-6">
          <AuthHeader title="Selamat Datang di MABA!" description="Silakan masuk dengan akun Anda" />

          {status && <AuthBanner type="success">{status}</AuthBanner>}

          {(errors.login || errors.password) && (
            <AuthBanner type="error">
              <p className="font-medium">Terdapat kesalahan:</p>
              <ul className="mt-1 list-inside list-disc">
                {errors.login && <li>{errors.login}</li>}
                {errors.password && <li>{errors.password}</li>}
              </ul>
            </AuthBanner>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="login">Username atau Email</Label>
              <div className="relative">
                <User className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  id="login"
                  type="text"
                  value={data.login}
                  onChange={(e) => setData('login', e.target.value)}
                  placeholder="Masukkan username atau email"
                  className="pl-10"
                  required
                  autoFocus
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <PasswordInput
                id="password"
                icon={Lock}
                value={data.password}
                onChange={(e) => setData('password', e.target.value)}
                placeholder="Masukkan password"
                autoComplete="current-password"
              />
            </div>

            <div className="flex items-center justify-between">
              <label htmlFor="remember_me" className="flex cursor-pointer items-center gap-2">
                <Checkbox
                  id="remember_me"
                  checked={data.remember}
                  onCheckedChange={(checked) => setData('remember', checked)}
                />
                <span className="text-sm text-muted-foreground">Ingat saya</span>
              </label>

              <Link href={r('password.request')} className="text-sm text-primary hover:underline">
                Lupa password?
              </Link>
            </div>

            <Button type="submit" disabled={processing} className="w-full">
              Masuk ke Akun
            </Button>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
