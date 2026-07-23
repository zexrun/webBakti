import { Link, router, usePage } from '@inertiajs/react'
import { Plus, Megaphone, Pencil, Send, Trash2 } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { useConfirm } from '@/hooks/useConfirm'

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  normal: 'default',
  low: 'secondary',
}

const priorityLabel = {
  urgent: 'Urgent',
  high: 'High',
  normal: 'Normal',
  low: 'Low',
}

export default function Index({ announcements }) {
  const { flash } = usePage().props
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const confirm = useConfirm()

  function handlePublish(announcement) {
    router.post(r('admin.announcements.publish', announcement.id))
  }

  async function handleDelete(announcement) {
    const confirmed = await confirm({ title: 'Hapus pengumuman ini?', variant: 'destructive' })
    if (confirmed) {
      router.delete(r('admin.announcements.destroy', announcement.id))
    }
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Pengumuman"
          description="Kelola pengumuman sistem"
          actions={
            <Button asChild>
              <Link href={r('admin.announcements.create')}>
                <Plus /> Buat Pengumuman
              </Link>
            </Button>
          }
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <div className="space-y-4">
          {announcements.data.length ? (
            announcements.data.map((announcement) => (
              <Card key={announcement.id}>
                <CardContent className="p-5">
                  <div className="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div className="flex flex-wrap items-center gap-2">
                      <h3 className="text-base font-semibold text-foreground">{announcement.title}</h3>
                      <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'}>
                        {priorityLabel[announcement.priority] ?? announcement.priority}
                      </Badge>
                      <Badge variant={announcement.published_at ? 'success' : 'warning'}>
                        {announcement.published_at ? 'Dipublikasikan' : 'Draft'}
                      </Badge>
                    </div>
                    <p className="shrink-0 text-sm tabular-nums text-muted-foreground">
                      {new Date(announcement.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                      })}
                    </p>
                  </div>
                  <p className="mt-1 text-sm text-muted-foreground">Oleh {announcement.admin?.name}</p>

                  <p className="mt-3 line-clamp-3 text-sm text-foreground">{announcement.content}</p>

                  <div className="mt-4 flex items-center gap-1.5 border-t border-border pt-3">
                    <Button asChild size="xs" variant="outline">
                      <Link href={r('admin.announcements.edit', announcement.id)}>
                        <Pencil /> Edit
                      </Link>
                    </Button>
                    {!announcement.published_at && (
                      <Button
                        type="button"
                        size="xs"
                        variant="ghost"
                        className="text-green-700 hover:text-green-700 dark:text-green-400 dark:hover:text-green-400"
                        onClick={() => handlePublish(announcement)}
                      >
                        <Send /> Publikasikan
                      </Button>
                    )}
                    <Button
                      type="button"
                      size="xs"
                      variant="ghost"
                      className="text-destructive hover:text-destructive"
                      onClick={() => handleDelete(announcement)}
                    >
                      <Trash2 /> Hapus
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))
          ) : (
            <Card>
              <EmptyState
                icon={Megaphone}
                title="Belum ada pengumuman"
                description="Buat pengumuman pertama untuk pengguna sistem."
                action={
                  <Button asChild>
                    <Link href={r('admin.announcements.create')}>
                      <Plus /> Buat Pengumuman
                    </Link>
                  </Button>
                }
              />
            </Card>
          )}
        </div>

        {announcements.links?.length > 3 && <Pagination links={announcements.links} />}
      </div>
    </AdminLayout>
  )
}
