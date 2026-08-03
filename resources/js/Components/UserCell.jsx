import { cn } from '@/lib/utils'

const tones = {
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
  neutral: 'bg-muted text-muted-foreground',
}

/**
 * Avatar (photo, falling back to initial) + name + subtitle cell, the
 * standard person identity block inside tables and lists.
 */
export default function UserCell({ name, subtitle, tone = 'blue', photoUrl }) {
  return (
    <div className="flex items-center gap-3">
      <div className={cn('flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full', tones[tone] ?? tones.blue)}>
        {photoUrl ? (
          <img src={photoUrl} alt={name} className="h-full w-full object-cover" />
        ) : (
          <span className="text-sm font-medium">{name?.charAt(0)?.toUpperCase() ?? '?'}</span>
        )}
      </div>
      <div className="min-w-0">
        <p className="truncate text-sm font-medium text-foreground">{name}</p>
        {subtitle && <p className="truncate text-xs text-muted-foreground">{subtitle}</p>}
      </div>
    </div>
  )
}
