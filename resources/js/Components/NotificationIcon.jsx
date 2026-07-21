import { ClipboardList, CheckCircle2, Clock, ShieldCheck, ShieldX, Bell } from 'lucide-react'
import { cn } from '@/lib/utils'

const iconTones = {
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
  amber: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
  neutral: 'bg-muted text-muted-foreground',
}

// Notification type → semantic icon + tone (§7 category colors).
function iconFor(type) {
  if (type === 'new_task') return { Icon: ClipboardList, tone: 'blue' }
  if (type === 'submission_graded') return { Icon: CheckCircle2, tone: 'green' }
  if (type?.startsWith('task_deadline')) return { Icon: Clock, tone: 'amber' }
  if (type?.startsWith('attendance_') || type?.startsWith('exception_')) {
    return type.endsWith('approved')
      ? { Icon: ShieldCheck, tone: 'green' }
      : { Icon: ShieldX, tone: 'red' }
  }
  return { Icon: Bell, tone: 'neutral' }
}

/**
 * Shared notification-type → icon/color mapping, used by both the full
 * Notifications/Index.jsx page and the NotificationBell dropdown so
 * they render identically for the same notification types.
 */
export default function NotificationIcon({ type, className }) {
  const { Icon, tone } = iconFor(type)
  return (
    <div className={cn('flex h-10 w-10 shrink-0 items-center justify-center rounded-full', iconTones[tone], className)}>
      <Icon className="h-5 w-5" />
    </div>
  )
}
