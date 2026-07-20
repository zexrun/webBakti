import { Link } from '@inertiajs/react'
import { Plus, Inbox as InboxIcon } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import MessageTabs from './MessageTabs'
import MessageListItem from './MessageListItem'

export default function Inbox({ messages, unreadCount }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader
          title="Pesan"
          description={unreadCount > 0 ? `${unreadCount} pesan baru` : 'Tidak ada pesan baru'}
          actions={
            <Button asChild>
              <Link href={r('messages.create')}>
                <Plus /> Buat Pesan
              </Link>
            </Button>
          }
        />

        <MessageTabs active="inbox" />

        <div className="space-y-2">
          {messages.data.length ? (
            messages.data.map((message) => (
              <MessageListItem
                key={message.id}
                href={r('messages.show', message.id)}
                heading={message.sender?.name}
                subject={message.subject}
                body={message.body}
                date={new Date(message.created_at).toLocaleDateString('id-ID')}
                unread={!message.is_read}
              />
            ))
          ) : (
            <Card>
              <EmptyState icon={InboxIcon} title="Inbox kosong" description="Pesan masuk akan muncul di sini." />
            </Card>
          )}
        </div>

        {messages.links?.length > 3 && <Pagination links={messages.links} />}
      </div>
    </RoleLayout>
  )
}
