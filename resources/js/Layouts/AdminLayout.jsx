import {
  LayoutDashboard,
  Users2,
  Activity,
  CalendarCheck,
  AlertTriangle,
  ClipboardCheck,
  FileText,
  SlidersHorizontal,
  Settings,
  Users,
  UserPlus,
  Megaphone,
  SquarePen,
} from 'lucide-react'
import AppShell from '@/Layouts/AppShell'

const nav = [
  {
    items: [
      { label: 'Dashboard', icon: LayoutDashboard, route: 'admin.dashboard', match: 'admin.dashboard' },
      { label: 'Plotting', icon: Users2, route: 'admin.plotting', match: 'admin.plotting' },
      { label: 'Monitoring', icon: Activity, route: 'admin.monitoring.index', match: 'admin.monitoring.*' },
    ],
  },
  {
    label: 'Attendance',
    items: [
      { label: 'Kehadiran', icon: CalendarCheck, route: 'admin.attendance.index', match: 'admin.attendance.index' },
      { label: 'Mencurigakan', icon: AlertTriangle, route: 'admin.attendance.suspicious', match: 'admin.attendance.suspicious' },
      { label: 'Approval', icon: ClipboardCheck, route: 'admin.attendance.approvals', match: 'admin.attendance.approvals' },
      { label: 'Laporan', icon: FileText, route: 'admin.attendance.reports', match: 'admin.attendance.reports' },
      { label: 'Pengaturan Absensi', icon: SlidersHorizontal, route: 'admin.attendance.settings', match: 'admin.attendance.settings' },
    ],
  },
  {
    label: 'Manajemen',
    items: [
      { label: 'Daftar User', icon: Users, route: 'admin.users.index', match: 'admin.users.index' },
      { label: 'Tambah User', icon: UserPlus, route: 'admin.users.create', match: 'admin.users.create' },
      { label: 'Settings', icon: Settings, route: 'admin.settings.index', match: 'admin.settings.*' },
    ],
  },
  {
    label: 'Komunikasi',
    items: [
      { label: 'Daftar Pengumuman', icon: Megaphone, route: 'admin.announcements.index', match: 'admin.announcements.index' },
      { label: 'Buat Pengumuman', icon: SquarePen, route: 'admin.announcements.create', match: 'admin.announcements.create' },
    ],
  },
]

export default function AdminLayout({ children }) {
  return (
    <AppShell nav={nav} homeRoute="admin.dashboard">
      {children}
    </AppShell>
  )
}
