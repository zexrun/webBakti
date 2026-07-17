import { Link } from '@inertiajs/react'
import { Plus } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'
import MessageTabs from './MessageTabs'

export default function Inbox({ messages, unreadCount }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <RoleLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Inbox</h1>
            <p className="text-muted-foreground">{unreadCount} pesan baru</p>
          </div>
          <Link href={r('messages.create')}>
            <Button>
              <Plus className="h-4 w-4" /> Buat Pesan
            </Button>
          </Link>
        </div>

        <MessageTabs active="inbox" />

        <div className="space-y-2">
          {messages.data.length ? (
            messages.data.map((message) => (
              <Link key={message.id} href={r('messages.show', message.id)}>
                <Card className="transition-colors hover:bg-accent">
                  <CardContent className="p-4">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="mb-1 flex items-center gap-2">
                          <h3 className={message.is_read ? 'font-semibold text-foreground' : 'font-bold text-foreground'}>
                            {message.sender?.name}
                          </h3>
                          {!message.is_read && <Badge>Baru</Badge>}
                        </div>
                        <p className="mb-1 text-sm font-medium text-foreground">{message.subject}</p>
                        <p className="line-clamp-2 text-sm text-muted-foreground">
                          {message.body.substring(0, 100)}...
                        </p>
                      </div>
                      <p className="ml-4 flex-shrink-0 text-xs text-muted-foreground">
                        {new Date(message.created_at).toLocaleDateString('id-ID')}
                      </p>
                    </div>
                  </CardContent>
                </Card>
              </Link>
            ))
          ) : (
            <Card>
              <CardContent className="p-12 text-center">
                <p className="text-muted-foreground">Inbox kosong</p>
              </CardContent>
            </Card>
          )}
        </div>

        <Pagination links={messages.links} />
      </div>
    </RoleLayout>
  )
}
