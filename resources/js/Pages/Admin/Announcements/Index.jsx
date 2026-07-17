import { Link, router, usePage } from '@inertiajs/react'
import { Plus } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  normal: 'default',
  low: 'secondary',
}

export default function Index({ announcements }) {
  const { flash } = usePage().props
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handlePublish(announcement) {
    router.post(r('admin.announcements.publish', announcement.id))
  }

  function handleDelete(announcement) {
    if (confirm('Hapus pengumuman ini?')) {
      router.delete(r('admin.announcements.destroy', announcement.id))
    }
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Pengumuman</h1>
            <p className="text-muted-foreground">Kelola pengumuman sistem</p>
          </div>
          <Link href={r('admin.announcements.create')}>
            <Button>
              <Plus className="h-4 w-4" /> Buat Pengumuman
            </Button>
          </Link>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4">
            <p className="font-medium text-green-800">{flash.success}</p>
          </div>
        )}

        <div className="space-y-4">
          {announcements.data.length ? (
            announcements.data.map((announcement) => (
              <Card key={announcement.id}>
                <CardContent className="p-6">
                  <div className="mb-3 flex items-start justify-between">
                    <div>
                      <div className="mb-1 flex items-center gap-2">
                        <h3 className="text-lg font-semibold text-foreground">{announcement.title}</h3>
                        <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'}>
                          {announcement.priority.charAt(0).toUpperCase() + announcement.priority.slice(1)}
                        </Badge>
                        <Badge variant={announcement.published_at ? 'success' : 'warning'}>
                          {announcement.published_at ? 'Dipublikasikan' : 'Draft'}
                        </Badge>
                      </div>
                      <p className="text-sm text-muted-foreground">Oleh {announcement.admin?.name}</p>
                    </div>
                    <p className="text-sm text-muted-foreground">
                      {new Date(announcement.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                      })}
                    </p>
                  </div>

                  <p className="mb-4 line-clamp-3 text-foreground">{announcement.content}</p>

                  <div className="flex gap-3">
                    <Link
                      href={r('admin.announcements.edit', announcement.id)}
                      className="text-sm font-medium text-primary hover:underline"
                    >
                      Edit
                    </Link>
                    {!announcement.published_at && (
                      <button
                        type="button"
                        onClick={() => handlePublish(announcement)}
                        className="text-sm font-medium text-green-600 hover:text-green-800"
                      >
                        Publikasikan
                      </button>
                    )}
                    <button
                      type="button"
                      onClick={() => handleDelete(announcement)}
                      className="text-sm font-medium text-destructive hover:text-destructive/80"
                    >
                      Hapus
                    </button>
                  </div>
                </CardContent>
              </Card>
            ))
          ) : (
            <Card>
              <CardContent className="p-12 text-center">
                <p className="text-muted-foreground">Belum ada pengumuman</p>
              </CardContent>
            </Card>
          )}
        </div>

        <Pagination links={announcements.links} />
      </div>
    </AdminLayout>
  )
}
