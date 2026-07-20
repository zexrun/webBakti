import { Link, useForm } from '@inertiajs/react'
import { Mail } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { AuthHeader, AuthBanner } from './auth-parts'

export default function ForgotPassword({ status }) {
  const { data, setData, post, processing, errors } = useForm({ email: '' })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('password.email'))
  }

  return (
    <GuestLayout>
      <Card>
        <CardContent className="p-6">
          <AuthHeader
            title="Lupa Password?"
            description="Masukkan alamat email Anda dan kami akan mengirimkan link untuk reset password."
          />

          {status && <AuthBanner type="success">{status}</AuthBanner>}
          {errors.email && <AuthBanner type="error">{errors.email}</AuthBanner>}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="email">Alamat Email</Label>
              <div className="relative">
                <Mail className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  id="email"
                  type="email"
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  placeholder="Masukkan email Anda"
                  className="pl-10"
                  required
                  autoFocus
                />
              </div>
            </div>

            <Button type="submit" disabled={processing} className="w-full">
              Kirim Link Reset Password
            </Button>
          </form>

          <p className="mt-4 text-center">
            <Link href={r('login')} className="text-sm text-primary hover:underline">
              Kembali ke halaman login
            </Link>
          </p>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
