import { Link } from '@inertiajs/react'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { cn } from '@/lib/utils'

/**
 * One row in the inbox / sent lists. `heading` is the person line
 * (sender name, or "Ke: recipient"); `unread` bolds it and shows a badge.
 */
export default function MessageListItem({ href, heading, subject, body, date, unread }) {
  return (
    <Link href={href}>
      <Card className={cn('transition-colors duration-150 hover:border-muted-foreground/30 hover:bg-muted/40', unread && 'border-primary/30 bg-indigo-50/40 dark:bg-indigo-500/[0.06]')}>
        <CardContent className="p-4">
          <div className="flex items-start justify-between gap-3">
            <div className="min-w-0 flex-1">
              <div className="mb-0.5 flex items-center gap-2">
                <h3 className={cn('truncate text-sm text-foreground', unread ? 'font-bold' : 'font-semibold')}>{heading}</h3>
                {unread && <Badge>Baru</Badge>}
              </div>
              <p className="truncate text-sm font-medium text-foreground">{subject}</p>
              <p className="mt-0.5 line-clamp-1 text-sm text-muted-foreground">{body}</p>
            </div>
            <p className="shrink-0 text-xs tabular-nums text-muted-foreground">{date}</p>
          </div>
        </CardContent>
      </Card>
    </Link>
  )
}
