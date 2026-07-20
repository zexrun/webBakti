import { router } from '@inertiajs/react'
import { MailCheck } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { AuthHeader, AuthBanner } from './auth-parts'

export default function VerifyEmail({ status }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  function resend(e) {
    e.preventDefault()
    router.post(r('verification.send'))
  }

  function logout(e) {
    e.preventDefault()
    router.post(r('logout'))
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-md">
        <Card>
          <CardContent className="p-6">
            <AuthHeader
              icon={MailCheck}
              title="Verifikasi Email"
              description="Terima kasih telah mendaftar! Mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan."
            />

            {status === 'verification-link-sent' && (
              <AuthBanner type="success">
                Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
              </AuthBanner>
            )}

            <div className="space-y-3">
              <Button type="button" onClick={resend} className="w-full">Kirim Ulang Email Verifikasi</Button>
              <Button type="button" variant="ghost" onClick={logout} className="w-full">Keluar</Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
