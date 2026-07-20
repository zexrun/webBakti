import { Link } from '@inertiajs/react'
import { ArrowLeft, Calendar, User } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

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

export default function Show({ announcement }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <RoleLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <PageHeader
          title={announcement.title}
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('announcements.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
          <span className="flex items-center gap-1.5 tabular-nums">
            <Calendar className="h-4 w-4" />
            {new Date(announcement.published_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
          </span>
          <span className="flex items-center gap-1.5">
            <User className="h-4 w-4" /> {announcement.admin?.name ?? 'Admin'}
          </span>
          <Badge variant={priorityVariant[announcement.priority] ?? 'secondary'}>
            {priorityLabel[announcement.priority] ?? announcement.priority}
          </Badge>
        </div>

        <Card>
          <CardContent className="whitespace-pre-line p-6 text-sm leading-relaxed text-foreground">
            {announcement.content}
          </CardContent>
        </Card>
      </div>
    </RoleLayout>
  )
}
