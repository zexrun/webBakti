import { useEffect, useRef, useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { Bell } from 'lucide-react'
import NotificationIcon from '@/Components/NotificationIcon'
import { cn } from '@/lib/utils'

const POLL_INTERVAL_MS = 30000

function relativeTime(value) {
  const diffMs = Date.now() - new Date(value).getTime()
  const diffMinutes = Math.round(diffMs / 60000)
  if (diffMinutes < 1) return 'Baru saja'
  if (diffMinutes < 60) return `${diffMinutes} menit lalu`
  const diffHours = Math.round(diffMinutes / 60)
  if (diffHours < 24) return `${diffHours} jam lalu`
  const diffDays = Math.round(diffHours / 24)
  return `${diffDays} hari lalu`
}

/**
 * Top-bar bell icon + unread badge + dropdown of the 5 most recent
 * notifications. Badge count starts from the unreadNotificationsCount
 * Inertia shared prop (so it's correct on first paint / after any page
 * navigation with zero extra requests) and is kept fresh afterwards by
 * a 30-second background poll of /notifications/recent, which also
 * supplies the dropdown's item list when opened.
 */
export default function NotificationBell() {
  const { unreadNotificationsCount } = usePage().props
  const [open, setOpen] = useState(false)
  const [count, setCount] = useState(unreadNotificationsCount ?? 0)
  const [items, setItems] = useState([])
  const [loaded, setLoaded] = useState(false)
  const containerRef = useRef(null)

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  useEffect(() => {
    setCount(unreadNotificationsCount ?? 0)
  }, [unreadNotificationsCount])

  function fetchRecent() {
    window.axios
      .get(r('notifications.recent'))
      .then(({ data }) => {
        setItems(data.notifications ?? [])
        setCount(data.unread_count ?? 0)
        setLoaded(true)
      })
      .catch(() => {
        // Background refresh - fail silently, badge just stops updating
        // until the next successful poll.
      })
  }

  useEffect(() => {
    const interval = setInterval(fetchRecent, POLL_INTERVAL_MS)
    return () => clearInterval(interval)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    function handleClickOutside(e) {
      if (containerRef.current && !containerRef.current.contains(e.target)) {
        setOpen(false)
      }
    }
    if (open) {
      document.addEventListener('mousedown', handleClickOutside)
      return () => document.removeEventListener('mousedown', handleClickOutside)
    }
  }, [open])

  function handleToggle() {
    const next = !open
    setOpen(next)
    if (next) fetchRecent()
  }

  function handleItemClick(notification) {
    window.axios.post(r('notifications.mark-as-read', notification.id))
    setOpen(false)
    router.visit(notification.data.action_url ?? r('notifications.index'))
  }

  const badgeLabel = count > 9 ? '9+' : String(count)

  return (
    <div className="relative" ref={containerRef}>
      <button
        type="button"
        onClick={handleToggle}
        aria-label={count > 0 ? `Notifikasi (${count} belum dibaca)` : 'Notifikasi'}
        title="Notifikasi"
        className="relative inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
      >
        <Bell className="h-4 w-4" />
        {count > 0 && (
          <span className="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-destructive px-1 text-[10px] font-medium leading-none text-destructive-foreground">
            {badgeLabel}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 z-30 mt-2 w-80 rounded-lg border border-border bg-card shadow-lg">
          <div className="border-b border-border px-4 py-3">
            <h3 className="text-sm font-semibold text-foreground">Notifikasi</h3>
          </div>

          <div className="max-h-80 overflow-y-auto">
            {!loaded ? (
              <p className="px-4 py-6 text-center text-sm text-muted-foreground">Memuat...</p>
            ) : items.length === 0 ? (
              <p className="px-4 py-6 text-center text-sm text-muted-foreground">Tidak ada notifikasi</p>
            ) : (
              items.map((notification) => {
                const unread = !notification.read_at
                return (
                  <button
                    key={notification.id}
                    type="button"
                    onClick={() => handleItemClick(notification)}
                    className={cn(
                      'flex w-full items-start gap-3 border-b border-border px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-muted',
                      unread && 'bg-indigo-50/40 dark:bg-indigo-500/[0.06]',
                    )}
                  >
                    <NotificationIcon type={notification.data.type} className="h-8 w-8" />
                    <div className="min-w-0 flex-1">
                      <p className="line-clamp-2 text-sm text-foreground">{notification.data.message ?? 'Notifikasi'}</p>
                      <p className="mt-1 text-xs tabular-nums text-muted-foreground">{relativeTime(notification.created_at)}</p>
                    </div>
                  </button>
                )
              })
            )}
          </div>

          <div className="border-t border-border px-4 py-2.5">
            <Link
              href={r('notifications.index')}
              onClick={() => setOpen(false)}
              className="block text-center text-sm font-medium text-primary hover:underline"
            >
              Lihat Semua
            </Link>
          </div>
        </div>
      )}
    </div>
  )
}
