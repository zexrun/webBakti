import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { KeyRound, Info, Eye, EyeOff } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

export default function Activate({ token, email }) {
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmation, setShowConfirmation] = useState(false)
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
      <div className="mb-4 text-center">
        <div className="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg">
          <KeyRound className="h-5 w-5 text-white" />
        </div>
        <h1 className="mb-1 text-xl font-bold text-foreground">Aktivasi Akun</h1>
        <p className="text-xs text-muted-foreground">Lengkapi data untuk mengaktifkan akun Anda</p>
      </div>

      <div className="mb-3 flex items-start gap-2 rounded-lg border border-blue-200 bg-blue-50 p-2">
        <Info className="mt-0.5 h-3 w-3 flex-shrink-0 text-blue-400" />
        <p className="text-xs text-blue-700">
          Aktivasi akun email: <strong>{email}</strong>
        </p>
      </div>

      <Card>
        <CardContent className="p-4">
          <form onSubmit={handleSubmit} className="space-y-3">
            <div>
              <Label htmlFor="name" className="text-xs">Nama Lengkap</Label>
              <Input
                id="name"
                type="text"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                placeholder="Masukkan nama lengkap"
                className="mt-1"
                required
                autoFocus
              />
              {errors.name && <p className="mt-1 text-xs text-destructive">{errors.name}</p>}
            </div>

            <div>
              <Label htmlFor="username" className="text-xs">Username</Label>
              <Input
                id="username"
                type="text"
                value={data.username}
                onChange={(e) => setData('username', e.target.value)}
                placeholder="Pilih username unik"
                className="mt-1"
                required
              />
              {errors.username && <p className="mt-1 text-xs text-destructive">{errors.username}</p>}
            </div>

            <div>
              <Label htmlFor="password" className="text-xs">Password Baru</Label>
              <div className="relative mt-1">
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  placeholder="Password yang kuat"
                  className="pr-8"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword((v) => !v)}
                  className="absolute inset-y-0 right-0 flex items-center pr-2 text-muted-foreground hover:text-foreground"
                >
                  {showPassword ? <EyeOff className="h-3 w-3" /> : <Eye className="h-3 w-3" />}
                </button>
              </div>
              {errors.password && <p className="mt-1 text-xs text-destructive">{errors.password}</p>}
            </div>

            <div>
              <Label htmlFor="password_confirmation" className="text-xs">Konfirmasi Password</Label>
              <div className="relative mt-1">
                <Input
                  id="password_confirmation"
                  type={showConfirmation ? 'text' : 'password'}
                  value={data.password_confirmation}
                  onChange={(e) => setData('password_confirmation', e.target.value)}
                  placeholder="Ulangi password"
                  className="pr-8"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmation((v) => !v)}
                  className="absolute inset-y-0 right-0 flex items-center pr-2 text-muted-foreground hover:text-foreground"
                >
                  {showConfirmation ? <EyeOff className="h-3 w-3" /> : <Eye className="h-3 w-3" />}
                </button>
              </div>
              {passwordMismatch && <p className="mt-1 text-xs text-destructive">Password tidak cocok</p>}
              {errors.password_confirmation && <p className="mt-1 text-xs text-destructive">{errors.password_confirmation}</p>}
            </div>

            <div className="pt-2">
              <Button type="submit" disabled={processing} className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700">
                <KeyRound className="h-4 w-4" /> Aktifkan Akun
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <div className="mt-3 text-center">
        <p className="text-xs text-muted-foreground">
          Sudah memiliki akun?{' '}
          <Link href={r('login')} className="font-medium text-primary hover:underline">
            Masuk di sini
          </Link>
        </p>
      </div>
    </GuestLayout>
  )
}
