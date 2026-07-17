import { Link } from '@inertiajs/react'
import { cn } from '@/lib/utils'

export default function MessageTabs({ active }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <div className="mb-6 flex gap-4 border-b border-border">
      <Link
        href={r('messages.inbox')}
        className={cn(
          'border-b-2 px-4 py-2 font-medium',
          active === 'inbox' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground',
        )}
      >
        Inbox
      </Link>
      <Link
        href={r('messages.sent')}
        className={cn(
          'border-b-2 px-4 py-2 font-medium',
          active === 'sent' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground',
        )}
      >
        Terkirim
      </Link>
    </div>
  )
}
