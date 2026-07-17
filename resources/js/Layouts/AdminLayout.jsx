import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import {
  LayoutDashboard,
  Users2,
  Activity,
  CalendarCheck,
  Settings,
  UserCog,
  Megaphone,
  UserCircle,
  LogOut,
  Menu,
  ChevronDown,
} from 'lucide-react'
import { cn } from '@/lib/utils'

function NavLink({ href, icon: Icon, label, active }) {
  return (
    <Link
      href={href}
      className={cn(
        'flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100',
        active && 'bg-blue-50 text-blue-700 border-r-2 border-blue-600',
      )}
    >
      <Icon className={cn('w-5 h-5 flex-shrink-0', active ? 'text-blue-600' : 'text-gray-500')} />
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
          'flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-gray-100',
          active && 'bg-blue-50 text-blue-700',
        )}
      >
        <Icon className={cn('w-5 h-5 flex-shrink-0', active ? 'text-blue-600' : 'text-gray-500')} />
        <span className="flex-1 ms-3 text-left">{label}</span>
        <ChevronDown className={cn('w-4 h-4 transition-transform', open && 'rotate-180')} />
      </button>
      {open && (
        <ul className="py-2 space-y-1 ml-6 border-l border-gray-200">{children}</ul>
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
          'flex items-center w-full p-2 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100',
          active && 'bg-blue-50 text-blue-700 font-medium',
        )}
      >
        {label}
      </Link>
    </li>
  )
}

export default function AdminLayout({ children }) {
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
    <div className="min-h-screen bg-gray-50">
      <nav className="fixed top-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div className="px-3 py-3 lg:px-5 lg:pl-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center">
              <button
                type="button"
                className="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100"
                onClick={() => setSidebarOpen(!sidebarOpen)}
              >
                <Menu className="w-6 h-6" />
              </button>
              <Link href={r('admin.dashboard')} className="flex items-center ms-2 md:me-24">
                <span className="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-gray-900">
                  Magang BAKTI
                </span>
              </Link>
            </div>

            <div className="flex items-center space-x-3">
              <span className="hidden sm:block text-sm text-gray-700">
                Halo, <span className="font-medium text-gray-900">{auth?.user?.name}</span>
              </span>
              <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <Link href={r('profile.show')}>
                  <span className="text-sm font-medium text-white">
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
          'fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 shadow-lg',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0',
        )}
      >
        <div className="h-full px-3 pb-4 overflow-y-auto bg-white">
          <nav className="space-y-1 font-medium">
            <NavLink href={r('admin.dashboard')} icon={LayoutDashboard} label="Dashboard" active={isActive('admin.dashboard')} />
            <NavLink href={r('admin.plotting')} icon={Users2} label="Plotting" active={isActive('admin.plotting')} />
            <NavLink href={r('admin.monitoring.index')} icon={Activity} label="Monitoring" active={isActive('admin.monitoring.*')} />

            <NavGroup icon={CalendarCheck} label="Attendance" active={isActive('admin.attendance.*')}>
              <SubLink href={r('admin.attendance.index')} label="Kehadiran" active={isActive('admin.attendance.index')} />
              <SubLink href={r('admin.attendance.suspicious')} label="⚠️ Mencurigakan" active={isActive('admin.attendance.suspicious')} />
              <SubLink href={r('admin.attendance.approvals')} label="Approval" active={isActive('admin.attendance.approvals')} />
              <SubLink href={r('admin.attendance.reports')} label="Laporan" active={isActive('admin.attendance.reports')} />
              <SubLink href={r('admin.attendance.settings')} label="⚙️ Pengaturan" active={isActive('admin.attendance.settings')} />
            </NavGroup>

            <NavLink href={r('admin.settings.index')} icon={Settings} label="Settings" active={isActive('admin.settings.*')} />

            <NavGroup icon={UserCog} label="User Management" active={isActive('admin.users.*')}>
              <SubLink href={r('admin.users.index')} label="User List" active={isActive('admin.users.index')} />
              <SubLink href={r('admin.users.create')} label="Add New User" active={isActive('admin.users.create')} />
            </NavGroup>

            <NavGroup icon={Megaphone} label="Komunikasi" active={isActive('admin.announcements.*')}>
              <SubLink href={r('admin.announcements.index')} label="Daftar Pengumuman" active={isActive('admin.announcements.index')} />
              <SubLink href={r('admin.announcements.create')} label="Buat Pengumuman" active={isActive('admin.announcements.create')} />
            </NavGroup>

            <div className="border-t border-gray-200 my-4" />

            <NavLink href={r('profile.show')} icon={UserCircle} label="Profile" active={isActive('profile.*')} />

            <button
              type="button"
              onClick={handleLogout}
              className="flex items-center w-full p-3 text-gray-700 rounded-lg transition-all duration-200 hover:bg-red-50 hover:text-red-700"
            >
              <LogOut className="w-5 h-5 text-gray-500 flex-shrink-0" />
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
