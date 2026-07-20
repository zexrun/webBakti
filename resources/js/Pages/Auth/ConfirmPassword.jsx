import { useForm } from '@inertiajs/react'
import { ShieldCheck } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Button } from '@/Components/ui/button'
import { AuthHeader, PasswordInput } from './auth-parts'

export default function ConfirmPassword() {
  const { data, setData, post, processing, errors } = useForm({ password: '' })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('password.confirm'))
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-md">
        <Card>
          <CardContent className="p-6">
            <AuthHeader
              icon={ShieldCheck}
              title="Konfirmasi Password"
              description="Ini adalah area aman aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan."
            />

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <PasswordInput id="password" value={data.password} onChange={(e) => setData('password', e.target.value)} autoFocus autoComplete="current-password" />
                {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
              </div>

              <Button type="submit" disabled={processing} className="w-full">Konfirmasi</Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
