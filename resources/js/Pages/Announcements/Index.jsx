import { Link } from '@inertiajs/react'
import { Megaphone, Calendar, Clock } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import Pagination from '@/Components/Pagination'

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  normal: 'default',
  low: 'success',
}

export default function Index({ announcements }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <RoleLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Pengumuman</h1>
          <p className="text-muted-foreground">Daftar pengumuman untuk Anda</p>
        </div>

        {announcements.data.length === 0 ? (
          <Card className="border-blue-200 bg-blue-50">
            <CardContent className="p-8 text-center">
              <p className="text-lg text-blue-800">
                <Megaphone className="mr-2 inline h-5 w-5" /> Belum ada pengumuman
              </p>
              <p className="mt-2 text-sm text-blue-600">Pengumuman baru akan ditampilkan di sini</p>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {announcements.data.map((announcement) => (
              <Card key={announcement.id} className="transition-shadow hover:shadow-md">
                <CardContent className="p-6">
                  <div className="mb-3 flex items-start justify-between">
                    <Link
                      href={r('announcements.show', announcement.id)}
                      className="text-lg font-semibold text-foreground hover:text-primary"
                    >
                      {announcement.title}
                    </Link>
                    <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'} className="ml-4 flex-shrink-0">
                      {announcement.priority.charAt(0).toUpperCase() + announcement.priority.slice(1)}
                    </Badge>
                  </div>

                  <p className="mb-4 line-clamp-2 text-sm text-muted-foreground">{announcement.content}</p>

                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <div className="flex items-center gap-4">
                      <span className="flex items-center gap-1">
                        <Calendar className="h-3 w-3" />
                        {new Date(announcement.published_at).toLocaleDateString('id-ID')}
                      </span>
                      <span className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        {new Date(announcement.published_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </div>
                    <Link href={r('announcements.show', announcement.id)} className="font-medium text-primary hover:underline">
                      Baca Selengkapnya →
                    </Link>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}

        <Pagination links={announcements.links} />
      </div>
    </RoleLayout>
  )
}
