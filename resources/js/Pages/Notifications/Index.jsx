import { useState } from 'react'
import { router } from '@inertiajs/react'
import { ClipboardList, CheckCircle2, Clock, ShieldCheck, ShieldX, Bell, X, BellOff } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

function NotificationIcon({ type }) {
  if (type === 'new_task') {
    return (
      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
        <ClipboardList className="h-6 w-6 text-blue-600" />
      </div>
    )
  }
  if (type === 'submission_graded') {
    return (
      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
        <CheckCircle2 className="h-6 w-6 text-green-600" />
      </div>
    )
  }
  if (type?.startsWith('task_deadline')) {
    return (
      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">
        <Clock className="h-6 w-6 text-yellow-600" />
      </div>
    )
  }
  if (type?.startsWith('attendance_') || type?.startsWith('exception_')) {
    const approved = type.endsWith('approved')
    return (
      <div className={`flex h-10 w-10 items-center justify-center rounded-full ${approved ? 'bg-green-100' : 'bg-red-100'}`}>
        {approved ? <ShieldCheck className="h-6 w-6 text-green-600" /> : <ShieldX className="h-6 w-6 text-red-600" />}
      </div>
    )
  }
  return (
    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-muted">
      <Bell className="h-6 w-6 text-muted-foreground" />
    </div>
  )
}

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  medium: 'warning',
}

const priorityLabel = {
  urgent: 'Urgent',
  high: 'Penting',
  medium: 'Normal',
}

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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Notifikasi</h1>
            <p className="text-muted-foreground">Kelola semua notifikasi Anda</p>
          </div>
          {items.length > 0 && (
            <Button type="button" variant="outline" onClick={deleteAll} className="border-destructive text-destructive hover:bg-destructive/10">
              Hapus Semua
            </Button>
          )}
        </div>

        <div className="space-y-4">
          {items.length ? (
            items.map((notification) => (
              <Card
                key={notification.id}
                className={`transition-shadow hover:shadow-md ${!notification.read_at ? 'cursor-pointer' : ''}`}
                onClick={() => !notification.read_at && markAsRead(notification.id)}
              >
                <CardContent className="flex items-start gap-4 p-6">
                  <NotificationIcon type={notification.data.type} />

                  <div className="flex-1">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <p className="text-base font-semibold text-foreground">{notification.data.message ?? 'Notifikasi'}</p>
                        <p className="mt-2 text-sm text-muted-foreground">
                          {new Date(notification.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </p>

                        {notification.data.action_url && (
                          <a
                            href={notification.data.action_url}
                            onClick={(e) => e.stopPropagation()}
                            className="mt-3 inline-block rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 hover:bg-blue-100"
                          >
                            Lihat Detail
                          </a>
                        )}
                      </div>

                      {!notification.read_at && (
                        <Badge className="ml-4">Belum dibaca</Badge>
                      )}
                    </div>

                    {notification.data.priority && priorityLabel[notification.data.priority] && (
                      <div className="mt-3">
                        <Badge variant={priorityVariant[notification.data.priority] ?? 'secondary'}>
                          {priorityLabel[notification.data.priority]}
                        </Badge>
                      </div>
                    )}
                  </div>

                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation()
                      deleteNotification(notification.id)
                    }}
                    title="Hapus notifikasi"
                    className="ml-4 rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-foreground"
                  >
                    <X className="h-5 w-5" />
                  </button>
                </CardContent>
              </Card>
            ))
          ) : (
            <Card>
              <CardContent className="p-12 text-center">
                <BellOff className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Tidak ada notifikasi</h3>
                <p className="text-muted-foreground">Anda sudah membaca semua notifikasi Anda.</p>
              </CardContent>
            </Card>
          )}

          <Pagination links={notifications.links} />
        </div>
      </div>
    </RoleLayout>
  )
}
