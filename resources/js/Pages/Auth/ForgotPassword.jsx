import { Link, useForm } from '@inertiajs/react'
import { Mail, CheckCircle2, AlertCircle } from 'lucide-react'
import GuestLayout from '@/Layouts/GuestLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

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
          <div className="mb-6 text-center">
            <h1 className="mb-1 text-2xl font-bold text-foreground">Lupa Password?</h1>
            <p className="text-sm text-muted-foreground">
              Masukkan alamat email Anda dan kami akan mengirimkan link untuk reset password.
            </p>
          </div>

          {status && (
            <div className="mb-4 flex items-center gap-2 rounded border-l-4 border-green-400 bg-green-50 p-3">
              <CheckCircle2 className="h-4 w-4 flex-shrink-0 text-green-500" />
              <p className="text-sm text-green-700">{status}</p>
            </div>
          )}

          {errors.email && (
            <div className="mb-4 flex items-start gap-2 rounded border-l-4 border-red-400 bg-red-50 p-3">
              <AlertCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-red-400" />
              <p className="text-sm text-red-600">{errors.email}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label htmlFor="email" className="mb-1 block text-sm font-medium text-foreground">
                Alamat Email
              </label>
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

            <div className="pt-2">
              <Button type="submit" disabled={processing} className="w-full">
                Kirim Link Reset Password
              </Button>
            </div>
          </form>

          <div className="mt-4 text-center">
            <Link href={r('login')} className="text-sm text-primary hover:underline">
              Kembali ke halaman login
            </Link>
          </div>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
