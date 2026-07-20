import { Link } from '@inertiajs/react'
import { cn } from '@/lib/utils'

const tabs = [
  { key: 'inbox', label: 'Inbox', route: 'messages.inbox' },
  { key: 'sent', label: 'Terkirim', route: 'messages.sent' },
]

export default function MessageTabs({ active }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <nav className="flex gap-6 border-b border-border">
      {tabs.map((tab) => (
        <Link
          key={tab.key}
          href={r(tab.route)}
          className={cn(
            'border-b-2 py-3 text-sm font-medium transition-colors duration-150',
            active === tab.key
              ? 'border-primary text-primary'
              : 'border-transparent text-muted-foreground hover:text-foreground',
          )}
        >
          {tab.label}
        </Link>
      ))}
    </nav>
  )
}
