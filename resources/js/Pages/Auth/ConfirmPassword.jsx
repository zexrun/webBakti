import { useForm } from '@inertiajs/react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

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
            <p className="mb-4 text-sm text-muted-foreground">
              Ini adalah area aman aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.
            </p>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  className="mt-1"
                  required
                  autoFocus
                  autoComplete="current-password"
                />
                {errors.password && <p className="mt-1 text-sm text-destructive">{errors.password}</p>}
              </div>

              <div className="flex justify-end">
                <Button type="submit" disabled={processing}>Konfirmasi</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
