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
  MessageSquare,
  TestTube2,
} from 'lucide-react'
import AppShell from '@/Layouts/AppShell'

const nav = [
  {
    items: [
      { label: 'Dasbor', icon: LayoutDashboard, route: 'admin.dashboard', match: 'admin.dashboard' },
      { label: 'Penugasan Pembimbing', icon: Users2, route: 'admin.plotting', match: 'admin.plotting' },
      { label: 'Pemantauan Aktivitas', icon: Activity, route: 'admin.monitoring.index', match: 'admin.monitoring.*' },
    ],
  },
  {
    label: 'Presensi',
    items: [
      { label: 'Presensi', icon: CalendarCheck, route: 'admin.attendance.index', match: 'admin.attendance.index' },
      { label: 'Presensi Mencurigakan', icon: AlertTriangle, route: 'admin.attendance.suspicious', match: 'admin.attendance.suspicious' },
      { label: 'Persetujuan Presensi', icon: ClipboardCheck, route: 'admin.attendance.approvals', match: 'admin.attendance.approvals' },
      { label: 'Laporan Presensi', icon: FileText, route: 'admin.attendance.reports', match: 'admin.attendance.reports' },
      { label: 'Pengaturan Presensi', icon: SlidersHorizontal, route: 'admin.attendance.settings', match: 'admin.attendance.settings' },
    ],
  },
  {
    label: 'Manajemen Pengguna',
    items: [
      { label: 'Daftar Pengguna', icon: Users, route: 'admin.users.index', match: 'admin.users.index' },
      { label: 'Tambah Pengguna', icon: UserPlus, route: 'admin.users.create', match: 'admin.users.create' },
      { label: 'Pengaturan Sistem', icon: Settings, route: 'admin.settings.index', match: 'admin.settings.*' },
    ],
  },
  {
    label: 'Komunikasi',
    items: [
      { label: 'Pesan', icon: MessageSquare, route: 'messages.inbox', match: 'messages.*' },
      { label: 'Daftar Pengumuman', icon: Megaphone, route: 'admin.announcements.index', match: 'admin.announcements.index' },
      { label: 'Buat Pengumuman', icon: SquarePen, route: 'admin.announcements.create', match: 'admin.announcements.create' },
    ],
  },
  {
    label: 'Alat Pengembang',
    items: [
      { label: 'Uji Notifikasi', icon: TestTube2, route: 'admin.notification-test.index', match: 'admin.notification-test.*' },
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
