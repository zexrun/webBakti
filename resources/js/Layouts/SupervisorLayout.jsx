import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import {
  LayoutDashboard,
  ClipboardPlus,
  Users,
  NotebookPen,
  ClipboardCheck,
  BarChart3,
  Layers,
  MessageSquare,
  Megaphone,
  UserCircle,
  LogOut,
  Menu,
  ChevronDown,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import ThemeToggle from '@/Components/ThemeToggle'

function NavLink({ href, icon: Icon, label, active }) {
  return (
    <Link
      href={href}
      className={cn(
        'flex items-center w-full p-3 rounded-lg text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground',
        active && 'bg-sidebar-accent text-sidebar-primary font-medium',
      )}
    >
      <Icon className={cn('w-5 h-5 flex-shrink-0', active ? 'text-sidebar-primary' : 'text-sidebar-muted')} />
      <span className="ms-3">{label}</span>
    </Link>
  )
}

function NavGroup({ icon: Icon, label, active, children, defaultOpen }) {
  const [open, setOpen] = useState(defaultOpen ?? active)

  return (
    <div>
      <button
        type="button"
        onClick={() => setOpen(!open)}
        className={cn(
          'flex items-center w-full p-3 rounded-lg text-sm text-sidebar-muted transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground',
          active && 'text-sidebar-primary font-medium',
        )}
      >
        <Icon className={cn('w-5 h-5 flex-shrink-0', active ? 'text-sidebar-primary' : 'text-sidebar-muted')} />
        <span className="flex-1 ms-3 text-left">{label}</span>
        <ChevronDown className={cn('w-4 h-4 transition-transform', open && 'rotate-180')} />
      </button>
      {open && (
        <ul className="py-2 space-y-1 ml-6 border-l border-sidebar-border">{children}</ul>
      )}
    </div>
  )
}

function SubLink({ href, label, active }) {
  return (
    <li>
      <Link
        href={href}
        className={cn(
          'flex items-center w-full p-2 pl-3 text-sm text-sidebar-muted rounded-lg transition-colors duration-150 hover:bg-sidebar-accent hover:text-sidebar-foreground',
          active && 'bg-sidebar-accent text-sidebar-primary font-medium',
        )}
      >
        {label}
      </Link>
    </li>
  )
}

export default function SupervisorLayout({ children }) {
  const { auth } = usePage().props
  const [sidebarOpen, setSidebarOpen] = useState(false)

  function isActive(pattern) {
    if (typeof window === 'undefined' || !window.route) return false
    return window.route().current(pattern)
  }

  function handleLogout() {
    router.post(window.route('logout'))
  }

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

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
              <Link href={r('supervisor.dashboard')} className="flex items-center ms-2 md:me-24">
                <span className="self-center font-heading text-xl font-semibold sm:text-2xl whitespace-nowrap text-foreground">
                  Magang <span className="text-primary">BAKTI</span>
                </span>
              </Link>
            </div>

            <div className="flex items-center space-x-3">
              <ThemeToggle />
              <span className="hidden sm:block text-sm text-muted-foreground">
                Halo, <span className="font-medium text-foreground">{auth?.user?.name}</span>
              </span>
              <div className="w-8 h-8 bg-accent rounded-full flex items-center justify-center">
                <Link href={r('profile.show')}>
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
            <NavLink href={r('supervisor.dashboard')} icon={LayoutDashboard} label="Dashboard" active={isActive('supervisor.dashboard')} />
            <NavLink href={r('supervisor.tasks.create')} icon={ClipboardPlus} label="Buat Tugas" active={isActive('supervisor.tasks.create')} />

            <NavGroup
              icon={Users}
              label="Bimbingan Magang"
              active={isActive('supervisor.tasks.index') || isActive('supervisor.students.list.*') || isActive('supervisor.logbooks.*')}
            >
              <SubLink href={r('supervisor.tasks.index')} label="Tugas Mahasiswa" active={isActive('supervisor.tasks.index')} />
              <SubLink href={r('supervisor.students.list.index')} label="Daftar Mahasiswa" active={isActive('supervisor.students.list.*')} />
              <SubLink href={r('supervisor.logbooks.index')} label="Logbook Mahasiswa" active={isActive('supervisor.logbooks.*')} />
            </NavGroup>

            <NavGroup
              icon={ClipboardCheck}
              label="Penilaian & Analitik"
              active={isActive('supervisor.analytics.*') || isActive('supervisor.submissions.*')}
            >
              <SubLink href={r('supervisor.submissions.index')} label="Dashboard Penilaian" active={isActive('supervisor.submissions.*')} />
              <SubLink href={r('supervisor.analytics.dashboard')} label="📊 Analitik Kinerja" active={isActive('supervisor.analytics.dashboard')} />
            </NavGroup>

            <NavGroup
              icon={Layers}
              label="Operasi Massal"
              active={isActive('supervisor.bulk.*')}
            >
              <SubLink href={r('supervisor.bulk.create-task')} label="Buat Tugas Massal" active={isActive('supervisor.bulk.create-task')} />
              <SubLink href={r('supervisor.bulk.send-notification')} label="Kirim Notifikasi Massal" active={isActive('supervisor.bulk.send-notification')} />
              <SubLink href={r('supervisor.bulk.import-grades')} label="Import Nilai" active={isActive('supervisor.bulk.import-grades')} />
              <SubLink href={r('supervisor.bulk.automation-settings')} label="Pengaturan Automasi" active={isActive('supervisor.bulk.automation-settings')} />
            </NavGroup>

            <div className="border-t border-sidebar-border my-4" />

            <NavLink href={r('messages.inbox')} icon={MessageSquare} label="Pesan" active={isActive('messages.*')} />
            <NavLink href={r('announcements.index')} icon={Megaphone} label="📢 Pengumuman" active={isActive('announcements.*')} />

            <div className="border-t border-sidebar-border my-4" />

            <NavLink href={r('profile.show')} icon={UserCircle} label="Profile" active={isActive('profile.*')} />

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
