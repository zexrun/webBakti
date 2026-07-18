import { router } from '@inertiajs/react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'

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
            <p className="mb-4 text-sm text-muted-foreground">
              Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik
              tautan yang baru saja kami kirimkan. Jika Anda belum menerima email tersebut, kami akan dengan senang
              hati mengirimkan yang lain.
            </p>

            {status === 'verification-link-sent' && (
              <p className="mb-4 text-sm font-medium text-green-600">
                Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
              </p>
            )}

            <div className="flex items-center justify-between">
              <Button type="button" onClick={resend}>Kirim Ulang Email Verifikasi</Button>
              <button type="button" onClick={logout} className="text-sm text-muted-foreground underline hover:text-foreground">
                Keluar
              </button>
            </div>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
