import { useState } from 'react'
import { router } from '@inertiajs/react'
import { X, BellOff, Trash2 } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import NotificationIcon from '@/Components/NotificationIcon'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

const priorityVariant = { urgent: 'destructive', high: 'warning', medium: 'warning' }
const priorityLabel = { urgent: 'Urgent', high: 'Penting', medium: 'Normal' }

export default function Index({ notifications }) {
  const [items, setItems] = useState(notifications.data)
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function markAsRead(id) {
    window.axios.post(r('notifications.mark-as-read', id)).then(() => {
      setItems((prev) => prev.map((n) => (n.id === id ? { ...n, read_at: new Date().toISOString() } : n)))
    })
  }

  function deleteNotification(id) {
    router.delete(r('notifications.delete', id), {
      preserveScroll: true,
      onSuccess: () => setItems((prev) => prev.filter((n) => n.id !== id)),
    })
  }

  function deleteAll() {
    if (!confirm('Hapus semua notifikasi?')) return
    router.delete(r('notifications.delete-all'), { preserveScroll: true })
  }

  return (
    <RoleLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <PageHeader
          title="Notifikasi"
          description="Kelola semua notifikasi Anda"
          actions={
            items.length > 0 && (
              <Button type="button" variant="outline" size="sm" className="text-destructive hover:text-destructive" onClick={deleteAll}>
                <Trash2 /> Hapus Semua
              </Button>
            )
          }
        />

        <div className="space-y-3">
          {items.length ? (
            items.map((notification) => {
              const unread = !notification.read_at
              return (
                <Card
                  key={notification.id}
                  className={cn('transition-colors duration-150', unread && 'cursor-pointer border-primary/30 bg-indigo-50/40 dark:bg-indigo-500/[0.06]')}
                  onClick={() => unread && markAsRead(notification.id)}
                >
                  <CardContent className="flex items-start gap-4 p-4">
                    <NotificationIcon type={notification.data.type} />

                    <div className="min-w-0 flex-1">
                      <div className="flex items-start justify-between gap-3">
                        <p className="text-sm font-medium text-foreground">{notification.data.message ?? 'Notifikasi'}</p>
                        {unread && <Badge>Baru</Badge>}
                      </div>
                      <p className="mt-1 text-xs tabular-nums text-muted-foreground">
                        {new Date(notification.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>

                      <div className="mt-2 flex flex-wrap items-center gap-2">
                        {notification.data.priority && priorityLabel[notification.data.priority] && (
                          <Badge variant={priorityVariant[notification.data.priority] ?? 'secondary'}>
                            {priorityLabel[notification.data.priority]}
                          </Badge>
                        )}
                        {notification.data.action_url && (
                          <Button asChild size="xs" variant="outline" onClick={(e) => e.stopPropagation()}>
                            <a href={notification.data.action_url}>Lihat Detail</a>
                          </Button>
                        )}
                      </div>
                    </div>

                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation()
                        deleteNotification(notification.id)
                      }}
                      title="Hapus notifikasi"
                      aria-label="Hapus notifikasi"
                      className="shrink-0 rounded-md p-1.5 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  </CardContent>
                </Card>
              )
            })
          ) : (
            <Card>
              <EmptyState
                icon={BellOff}
                title="Tidak ada notifikasi"
                description="Anda sudah membaca semua notifikasi Anda."
              />
            </Card>
          )}

          {notifications.links?.length > 3 && <Pagination links={notifications.links} />}
        </div>
      </div>
    </RoleLayout>
  )
}
