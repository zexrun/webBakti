import { Link } from '@inertiajs/react'
import { ArrowLeft, Calendar, User } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

const priorityVariant = {
  urgent: 'destructive',
  high: 'warning',
  normal: 'default',
  low: 'success',
}

export default function Show({ announcement }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <RoleLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <div>
          <Link href={r('announcements.index')} className="mb-4 inline-flex items-center gap-2 text-sm text-primary hover:underline">
            <ArrowLeft className="h-4 w-4" /> Kembali ke Pengumuman
          </Link>
          <h1 className="text-2xl font-bold text-foreground">{announcement.title}</h1>
          <div className="mt-2 flex items-center gap-4 text-sm text-muted-foreground">
            <span className="flex items-center gap-1">
              <Calendar className="h-4 w-4" />
              {new Date(announcement.published_at).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })}
            </span>
            <span className="flex items-center gap-1">
              <User className="h-4 w-4" /> {announcement.admin?.name ?? 'Admin'}
            </span>
            <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'}>
              {announcement.priority.charAt(0).toUpperCase() + announcement.priority.slice(1)}
            </Badge>
          </div>
        </div>

        <Card>
          <CardContent className="whitespace-pre-line p-8 text-foreground">{announcement.content}</CardContent>
        </Card>

        <div className="text-center">
          <Link href={r('announcements.index')}>
            <Button>Kembali ke Daftar Pengumuman</Button>
          </Link>
        </div>
      </div>
    </RoleLayout>
  )
}
