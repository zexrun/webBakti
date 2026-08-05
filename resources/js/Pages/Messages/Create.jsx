import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import RecipientPicker from '@/Components/RecipientPicker'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
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
        <PageHeader
          title="Buat Pesan Baru"
          description="Kirim pesan ke pengguna lain"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('messages.inbox')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label htmlFor="recipient_id">Penerima</Label>
                <RecipientPicker
                  recipients={recipients}
                  value={data.recipient_id}
                  onChange={(id) => setData('recipient_id', id)}
                />
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
                <p className="text-xs text-muted-foreground">Maksimum 5000 karakter</p>
                {errors.body && <p className="text-sm text-destructive">{errors.body}</p>}
              </div>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">Preview</p>
                <div className="rounded-lg border border-border bg-card p-4">
                  <p className="text-sm font-semibold text-foreground">{data.subject || 'Subjek pesan'}</p>
                  <p className="mt-2 whitespace-pre-wrap text-sm text-muted-foreground">
                    {data.body || 'Isi pesan akan muncul di sini...'}
                  </p>
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-5">
                <Button asChild type="button" variant="outline">
                  <Link href={r('messages.inbox')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>Kirim Pesan</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
