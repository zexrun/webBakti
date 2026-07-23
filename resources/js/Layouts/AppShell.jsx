import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import { AnimatePresence, motion } from 'motion/react'
import { LogOut, Menu } from 'lucide-react'
import { cn } from '@/lib/utils'
import ThemeToggle from '@/Components/ThemeToggle'
import NotificationBell from '@/Components/NotificationBell'

const roleLabels = {
  admin: 'Administrator',
  supervisor: 'Pembimbing',
  student: 'Mahasiswa',
}

function Brand({ href }) {
  return (
    <Link href={href} className="flex h-16 shrink-0 items-center border-b border-sidebar-border px-5">
      <span className="font-heading text-xl font-semibold text-sidebar-foreground">
        Magang <span className="text-sidebar-primary">BAKTI</span>
      </span>
    </Link>
  )
}

function NavItem({ href, icon: Icon, label, active }) {
  return (
    <Link
      href={href}
      className={cn(
        'group relative flex items-center gap-3 rounded-md px-3 py-2 text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground',
        active && 'bg-sidebar-accent font-medium text-sidebar-primary',
      )}
    >
      {active && <span className="absolute -left-3 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-full bg-sidebar-primary" aria-hidden="true" />}
      <Icon className={cn('h-5 w-5 shrink-0 transition-colors duration-150', active ? 'text-sidebar-primary' : 'text-sidebar-muted group-hover:text-sidebar-foreground')} />
      <span className="truncate">{label}</span>
    </Link>
  )
}

/**
 * Shared application shell for all authenticated roles: full-height navy
 * sidebar (brand → sectioned flat nav → user block) plus a slim sticky
 * top bar. Role layouts pass a `nav` config:
 *   [{ label?: 'SECTION', items: [{ label, icon, route, match, params? }] }]
 */
export default function AppShell({ nav, homeRoute, children }) {
  const { props: { auth }, url } = usePage()
  const [sidebarOpen, setSidebarOpen] = useState(false)

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function isActive(pattern) {
    if (typeof window === 'undefined' || !window.route) return false
    return window.route().current(pattern)
  }

  function handleLogout() {
    router.post(r('logout'))
  }

  const initial = auth?.user?.name?.charAt(0)?.toUpperCase() ?? '?'
  const profilePhotoUrl = auth?.user?.profile_photo_url

  return (
    <div className="min-h-screen bg-background">
      <aside
        className={cn(
          'fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-sidebar transition-transform duration-200',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0',
        )}
      >
        <Brand href={r(homeRoute)} />

        <nav className="flex-1 space-y-6 overflow-y-auto px-3 py-4">
          {nav.map((section, i) => (
            <div key={section.label ?? i}>
              {section.label && (
                <p className="mb-2 px-3 text-[11px] font-medium uppercase tracking-wider text-sidebar-muted">
                  {section.label}
                </p>
              )}
              <div className="space-y-1">
                {section.items.map((item) => (
                  <NavItem
                    key={item.route}
                    href={r(item.route, item.params)}
                    icon={item.icon}
                    label={item.label}
                    active={isActive(item.match)}
                  />
                ))}
              </div>
            </div>
          ))}
        </nav>

        <div className="shrink-0 border-t border-sidebar-border p-3">
          <div className="flex items-center gap-3 rounded-md p-2">
            <Link
              href={r('profile.show')}
              className="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sidebar-primary text-sm font-semibold text-sidebar-primary-foreground"
            >
              {profilePhotoUrl ? (
                <img src={profilePhotoUrl} alt={auth?.user?.name} className="h-full w-full object-cover" />
              ) : (
                initial
              )}
            </Link>
            <Link href={r('profile.show')} className="min-w-0 flex-1">
              <p className="truncate text-sm font-medium text-sidebar-foreground">{auth?.user?.name}</p>
              <p className="truncate text-xs text-sidebar-muted">{roleLabels[auth?.user?.role] ?? auth?.user?.role}</p>
            </Link>
            <button
              type="button"
              onClick={handleLogout}
              title="Logout"
              aria-label="Logout"
              className="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-red-300"
            >
              <LogOut className="h-4 w-4" />
            </button>
          </div>
        </div>
      </aside>

      {sidebarOpen && (
        <div className="fixed inset-0 z-30 bg-black/50 sm:hidden" onClick={() => setSidebarOpen(false)} />
      )}

      <div className="flex min-h-screen flex-col sm:pl-64">
        <header className="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-border bg-card px-4 sm:px-6">
          <button
            type="button"
            aria-label="Buka menu"
            className="inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground sm:hidden"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            <Menu className="h-5 w-5" />
          </button>

          <span className="font-heading text-lg font-semibold text-foreground sm:hidden">
            Magang <span className="text-primary">BAKTI</span>
          </span>

          <div className="flex-1" />

          <NotificationBell />
          <ThemeToggle />
          <span className="hidden text-sm text-muted-foreground md:block">
            Halo, <span className="font-medium text-foreground">{auth?.user?.name}</span>
          </span>
          <Link
            href={r('profile.show')}
            className="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-secondary text-sm font-medium text-secondary-foreground"
          >
            {profilePhotoUrl ? (
              <img src={profilePhotoUrl} alt={auth?.user?.name} className="h-full w-full object-cover" />
            ) : (
              initial
            )}
          </Link>
        </header>

        <main className="flex-1 p-4 sm:p-6 lg:p-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={url}
              initial={{ opacity: 0, y: 8 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -8 }}
              transition={{ duration: 0.15 }}
            >
              {children}
            </motion.div>
          </AnimatePresence>
        </main>
      </div>
    </div>
  )
}
