import { Link, router, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, Trash2, Check } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import UserCell from '@/Components/UserCell'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { useConfirm } from '@/hooks/useConfirm'

export default function Show({ message, conversation }) {
  const { auth } = usePage().props
  const { data, setData, post, processing, errors, reset } = useForm({ body: '' })
  const confirm = useConfirm()

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleReply(e) {
    e.preventDefault()
    post(r('messages.reply', message.id), { onSuccess: () => reset() })
  }

  async function handleDelete() {
    const confirmed = await confirm({ title: 'Hapus pesan ini?', variant: 'destructive' })
    if (confirmed) {
      router.delete(r('messages.delete', message.id))
    }
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <PageHeader
          title={message.subject}
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <Link href={r('messages.inbox')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
              <Button variant="outline" size="sm" className="text-destructive hover:text-destructive" onClick={handleDelete}>
                <Trash2 /> Hapus
              </Button>
            </>
          }
        />

        <div className="space-y-3">
          {conversation.map((msg) => (
            <Card key={msg.id}>
              <CardContent className="p-5">
                <div className="mb-3 flex items-start justify-between gap-3">
                  <UserCell
                    name={msg.sender?.name}
                    photoUrl={msg.sender?.profile_photo_url}
                    subtitle={new Date(msg.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  />
                  {msg.recipient_id === auth.user.id && msg.is_read && (
                    <span className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Check className="h-3.5 w-3.5" /> Terbaca
                    </span>
                  )}
                </div>
                <p className="whitespace-pre-wrap text-sm text-foreground">{msg.body}</p>
              </CardContent>
            </Card>
          ))}
        </div>

        <Card>
          <CardHeader className="border-b">
            <CardTitle>Balas Pesan</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <form onSubmit={handleReply} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="body" className="sr-only">Balasan</Label>
                <Textarea
                  id="body"
                  rows={5}
                  value={data.body}
                  onChange={(e) => setData('body', e.target.value)}
                  placeholder="Tulis balasan Anda..."
                />
                {errors.body && <p className="text-sm text-destructive">{errors.body}</p>}
              </div>
              <div className="flex justify-end">
                <Button type="submit" disabled={processing}>Kirim Balasan</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
