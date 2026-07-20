import { Link } from '@inertiajs/react'
import { Megaphone, Calendar, Clock, ArrowRight } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  normal: 'default',
  low: 'success',
}

const priorityLabel = {
  urgent: 'Urgent',
  high: 'High',
  normal: 'Normal',
  low: 'Low',
}

export default function Index({ announcements }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader title="Pengumuman" description="Daftar pengumuman untuk Anda" />

        {announcements.data.length === 0 ? (
          <Card>
            <EmptyState
              icon={Megaphone}
              title="Belum ada pengumuman"
              description="Pengumuman baru akan ditampilkan di sini."
            />
          </Card>
        ) : (
          <div className="space-y-4">
            {announcements.data.map((announcement) => (
              <Card key={announcement.id} className="transition-colors duration-150 hover:border-muted-foreground/30">
                <CardContent className="p-5">
                  <div className="flex items-start justify-between gap-4">
                    <Link
                      href={r('announcements.show', announcement.id)}
                      className="font-heading text-base font-semibold text-foreground transition-colors hover:text-primary"
                    >
                      {announcement.title}
                    </Link>
                    <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'}>
                      {priorityLabel[announcement.priority] ?? announcement.priority}
                    </Badge>
                  </div>

                  <p className="mt-2 line-clamp-2 text-sm text-muted-foreground">{announcement.content}</p>

                  <div className="mt-4 flex items-center justify-between border-t border-border pt-3 text-xs text-muted-foreground">
                    <div className="flex items-center gap-4 tabular-nums">
                      <span className="flex items-center gap-1.5">
                        <Calendar className="h-3.5 w-3.5" />
                        {new Date(announcement.published_at).toLocaleDateString('id-ID')}
                      </span>
                      <span className="flex items-center gap-1.5">
                        <Clock className="h-3.5 w-3.5" />
                        {new Date(announcement.published_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </div>
                    <Link href={r('announcements.show', announcement.id)} className="flex items-center gap-1 font-medium text-primary hover:underline">
                      Baca Selengkapnya <ArrowRight className="h-3.5 w-3.5" />
                    </Link>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}

        {announcements.links?.length > 3 && <Pagination links={announcements.links} />}
      </div>
    </RoleLayout>
  )
}
