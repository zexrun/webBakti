import { Link } from '@inertiajs/react'
import { Plus, Send } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import MessageTabs from './MessageTabs'
import MessageListItem from './MessageListItem'

export default function Sent({ messages }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader
          title="Pesan"
          description="Daftar pesan yang sudah Anda kirim"
          actions={
            <Button asChild>
              <Link href={r('messages.create')}>
                <Plus /> Buat Pesan
              </Link>
            </Button>
          }
        />

        <MessageTabs active="sent" />

        <div className="space-y-2">
          {messages.data.length ? (
            messages.data.map((message) => (
              <MessageListItem
                key={message.id}
                href={r('messages.show', message.id)}
                heading={`Ke: ${message.recipient?.name}`}
                subject={message.subject}
                body={message.body}
                date={new Date(message.created_at).toLocaleDateString('id-ID')}
              />
            ))
          ) : (
            <Card>
              <EmptyState icon={Send} title="Belum ada pesan terkirim" description="Pesan yang Anda kirim akan muncul di sini." />
            </Card>
          )}
        </div>

        {messages.links?.length > 3 && <Pagination links={messages.links} />}
      </div>
    </RoleLayout>
  )
}
