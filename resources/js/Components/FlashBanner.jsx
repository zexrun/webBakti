import { cn } from '@/lib/utils'

const types = {
  success:
    'border-green-200 bg-green-50 text-green-800 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-200',
  danger:
    'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200',
  warning:
    'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200',
  info:
    'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200',
}

/** Standard flash/notice banner, dark-safe. type: success | danger | warning | info */
export default function FlashBanner({ type = 'info', className, children }) {
  return (
    <div className={cn('rounded-lg border p-4 text-sm font-medium', types[type] ?? types.info, className)}>
      {children}
    </div>
  )
}
