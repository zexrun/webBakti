import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { User, Lock, Eye, EyeOff, CheckCircle2, AlertCircle } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

export default function Login({ status }) {
  const [showPassword, setShowPassword] = useState(false)
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
          <div className="mb-6 text-center">
            <h1 className="mb-1 text-2xl font-bold text-foreground">Selamat Datang di MABA!</h1>
            <p className="text-sm text-muted-foreground">Silakan masuk dengan akun Anda</p>
          </div>

          {status && (
            <div className="mb-4 flex items-center gap-2 rounded border-l-4 border-green-400 bg-green-50 p-3">
              <CheckCircle2 className="h-4 w-4 flex-shrink-0 text-green-500" />
              <p className="text-sm text-green-700">{status}</p>
            </div>
          )}

          {(errors.login || errors.password) && (
            <div className="mb-4 flex items-start gap-2 rounded border-l-4 border-red-400 bg-red-50 p-3">
              <AlertCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-red-400" />
              <div>
                <p className="mb-1 text-sm font-medium text-red-700">Terdapat kesalahan:</p>
                <ul className="list-inside list-disc text-sm text-red-600">
                  {errors.login && <li>{errors.login}</li>}
                  {errors.password && <li>{errors.password}</li>}
                </ul>
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label htmlFor="login" className="mb-1 block text-sm font-medium text-foreground">
                Username atau Email
              </label>
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

            <div>
              <label htmlFor="password" className="mb-1 block text-sm font-medium text-foreground">
                Password
              </label>
              <div className="relative">
                <Lock className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  placeholder="Masukkan password"
                  className="pl-10 pr-10"
                  required
                  autoComplete="current-password"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword((v) => !v)}
                  className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground"
                >
                  {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>

            <div className="flex items-center justify-between">
              <label htmlFor="remember_me" className="flex cursor-pointer items-center">
                <input
                  id="remember_me"
                  type="checkbox"
                  checked={data.remember}
                  onChange={(e) => setData('remember', e.target.checked)}
                  className="h-4 w-4 rounded border-input text-primary focus:ring-primary"
                />
                <span className="ml-2 text-sm text-muted-foreground">Ingat saya</span>
              </label>

              <Link href={r('password.request')} className="text-sm text-primary hover:underline">
                Lupa password?
              </Link>
            </div>

            <div className="pt-2">
              <Button type="submit" disabled={processing} className="w-full">
                Masuk ke Akun
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
