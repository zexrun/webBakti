import { Link, router, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, Trash2, Check } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'

export default function Show({ message, conversation }) {
  const { auth } = usePage().props
  const { data, setData, post, processing, errors, reset } = useForm({ body: '' })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleReply(e) {
    e.preventDefault()
    post(r('messages.reply', message.id), {
      onSuccess: () => reset(),
    })
  }

  function handleDelete() {
    if (confirm('Hapus pesan ini?')) {
      router.delete(r('messages.delete', message.id))
    }
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <div className="flex items-center justify-between">
          <Link href={r('messages.inbox')} className="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
            <ArrowLeft className="h-4 w-4" /> Kembali ke Inbox
          </Link>
          <button
            type="button"
            onClick={handleDelete}
            className="inline-flex items-center gap-1 text-sm font-medium text-destructive hover:text-destructive/80"
          >
            <Trash2 className="h-4 w-4" /> Hapus
          </button>
        </div>

        <div className="space-y-4">
          {conversation.map((msg) => (
            <Card key={msg.id}>
              <CardContent className="p-6">
                <div className="mb-3 flex items-start justify-between">
                  <div>
                    <p className="font-semibold text-foreground">{msg.sender?.name}</p>
                    <p className="text-sm text-muted-foreground">
                      {new Date(msg.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                      })}
                    </p>
                  </div>
                  {msg.recipient_id === auth.user.id && msg.is_read && (
                    <span className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Check className="h-3 w-3" /> Terbaca
                    </span>
                  )}
                </div>
                <p className="mb-2 text-sm font-medium text-foreground">{msg.subject}</p>
                <p className="whitespace-pre-wrap text-foreground">{msg.body}</p>
              </CardContent>
            </Card>
          ))}
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Balas Pesan</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleReply} className="space-y-4">
              <div className="space-y-2">
                <Textarea
                  rows={5}
                  value={data.body}
                  onChange={(e) => setData('body', e.target.value)}
                  placeholder="Tulis balasan Anda..."
                />
                {errors.body && <p className="text-sm text-destructive">{errors.body}</p>}
              </div>
              <Button type="submit" disabled={processing}>
                Kirim Balasan
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
