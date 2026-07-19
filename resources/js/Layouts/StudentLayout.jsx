import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import {
  LayoutDashboard,
  ClipboardList,
  NotebookPen,
  Info,
  CalendarCheck,
  FileText,
  MessageSquare,
  Megaphone,
  UserCircle,
  LogOut,
  Menu,
} from 'lucide-react'
import { cn } from '@/lib/utils'

const navItems = [
  { label: 'Dashboard', href: 'student.dashboard', icon: LayoutDashboard, match: 'student.dashboard' },
  { label: 'Tugas', href: 'student.tasks.index', icon: ClipboardList, match: 'student.tasks.*' },
  { label: 'Logbook', href: 'student.logbooks.index', icon: NotebookPen, match: 'student.logbooks.*' },
  { label: 'Informasi Magang', href: 'student.info.edit', icon: Info, match: 'student.info.*' },
  { label: 'Presensi', href: 'student.attendance.index', icon: CalendarCheck, match: 'student.attendance.*' },
  { label: 'Dokumen', href: 'student.documents.index', icon: FileText, match: 'student.documents.*' },
  { label: 'Pesan', href: 'messages.inbox', icon: MessageSquare, match: 'messages.*' },
  { label: 'Pengumuman', href: 'announcements.index', icon: Megaphone, match: 'announcements.*' },
]

export default function StudentLayout({ children }) {
  const { auth } = usePage().props
  const [sidebarOpen, setSidebarOpen] = useState(false)

  function isActive(pattern) {
    if (typeof window === 'undefined' || !window.route) return false
    return window.route().current(pattern)
  }

  function handleLogout() {
    router.post(window.route('logout'))
  }

  return (
    <div className="min-h-screen bg-background">
      <nav className="fixed top-0 z-50 w-full border-b border-border bg-card">
        <div className="px-3 py-3 lg:px-5 lg:pl-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center">
              <button
                type="button"
                className="inline-flex items-center p-2 text-sm text-muted-foreground rounded-lg sm:hidden hover:bg-accent"
                onClick={() => setSidebarOpen(!sidebarOpen)}
              >
                <Menu className="w-6 h-6" />
              </button>
              <Link href={window.route ? window.route('student.dashboard') : '#'} className="flex items-center ms-2 md:me-24">
                <span className="self-center font-heading text-xl font-semibold sm:text-2xl whitespace-nowrap text-foreground">
                  Magang <span className="text-primary">BAKTI</span>
                </span>
              </Link>
            </div>

            <div className="flex items-center space-x-3">
              <span className="hidden sm:block text-sm text-muted-foreground">
                Halo, <span className="font-medium text-foreground">{auth?.user?.name}</span>
              </span>
              <div className="w-8 h-8 bg-accent rounded-full flex items-center justify-center">
                <Link href={window.route ? window.route('profile.show') : '#'}>
                  <span className="text-sm font-medium text-accent-foreground">
                    {auth?.user?.name?.charAt(0)?.toUpperCase()}
                  </span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <aside
        className={cn(
          'fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-sidebar border-r border-sidebar-border',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0',
        )}
      >
        <div className="h-full px-3 pb-4 overflow-y-auto">
          <nav className="space-y-1">
            {navItems.map((item) => {
              const Icon = item.icon
              const active = isActive(item.match)
              return (
                <Link
                  key={item.label}
                  href={window.route ? window.route(item.href) : '#'}
                  className={cn(
                    'flex items-center w-full p-3 rounded-lg text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground',
                    active && 'bg-sidebar-accent text-sidebar-primary font-medium',
                  )}
                >
                  <Icon className={cn('w-5 h-5 flex-shrink-0', active ? 'text-sidebar-primary' : 'text-sidebar-muted')} />
                  <span className="ms-3">{item.label}</span>
                </Link>
              )
            })}

            <div className="border-t border-sidebar-border my-4" />

            <Link
              href={window.route ? window.route('profile.show') : '#'}
              className="flex items-center w-full p-3 rounded-lg text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground"
            >
              <UserCircle className="w-5 h-5 flex-shrink-0" />
              <span className="ms-3">Profile</span>
            </Link>

            <button
              type="button"
              onClick={handleLogout}
              className="flex items-center w-full p-3 rounded-lg text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-destructive"
            >
              <LogOut className="w-5 h-5 flex-shrink-0" />
              <span className="ms-3">Logout</span>
            </button>
          </nav>
        </div>
      </aside>

      {sidebarOpen && (
        <div
          className="fixed inset-0 z-30 bg-black/50 sm:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <main className="pt-20 sm:ml-64">
        <div className="p-4">{children}</div>
      </main>
    </div>
  )
}
