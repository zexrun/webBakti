import { Link, useForm } from '@inertiajs/react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Create({ recipients }) {
  const { data, setData, post, processing, errors } = useForm({
    recipient_id: '',
    subject: '',
    body: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('messages.store'))
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Buat Pesan Baru</h1>
          <p className="text-muted-foreground">Kirim pesan ke pengguna lain</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="recipient_id">Penerima</Label>
                <Select
                  value={data.recipient_id}
                  onValueChange={(v) => setData('recipient_id', v)}
                >
                  <SelectTrigger id="recipient_id" className="w-full">
                    <SelectValue placeholder="Pilih penerima..." />
                  </SelectTrigger>
                  <SelectContent>
                    {recipients.map((user) => (
                      <SelectItem key={user.id} value={String(user.id)}>
                        {user.name} ({user.role.charAt(0).toUpperCase() + user.role.slice(1)})
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.recipient_id && <p className="text-sm text-destructive">{errors.recipient_id}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="subject">Subjek</Label>
                <Input
                  id="subject"
                  value={data.subject}
                  onChange={(e) => setData('subject', e.target.value)}
                  placeholder="Masukkan subjek..."
                />
                {errors.subject && <p className="text-sm text-destructive">{errors.subject}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="body">Pesan</Label>
                <Textarea
                  id="body"
                  rows={8}
                  value={data.body}
                  onChange={(e) => setData('body', e.target.value)}
                  placeholder="Tulis pesan Anda..."
                />
                <p className="text-xs text-muted-foreground">Maximum 5000 karakter</p>
                {errors.body && <p className="text-sm text-destructive">{errors.body}</p>}
              </div>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-2 text-sm font-medium text-foreground">Preview:</p>
                <div className="rounded border border-border bg-background p-3">
                  <p className="text-sm font-semibold text-foreground">{data.subject || 'Subjek pesan'}</p>
                  <p className="mt-3 whitespace-pre-wrap text-sm text-muted-foreground">
                    {data.body || 'Isi pesan akan muncul di sini...'}
                  </p>
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                  Kirim Pesan
                </Button>
                <Link href={r('messages.inbox')}>
                  <Button type="button" variant="secondary">
                    Batal
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
