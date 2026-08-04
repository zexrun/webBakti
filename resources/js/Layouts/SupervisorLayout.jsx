import {
  LayoutDashboard,
  ClipboardPlus,
  ClipboardList,
  Users,
  NotebookPen,
  ClipboardCheck,
  BarChart3,
  Layers,
  Send,
  Upload,
  SlidersHorizontal,
  MessageSquare,
  Megaphone,
  CalendarCheck,
  AlertTriangle,
  FileText,
} from 'lucide-react'
import AppShell from '@/Layouts/AppShell'

const nav = [
  {
    items: [
      { label: 'Dasbor', icon: LayoutDashboard, route: 'supervisor.dashboard', match: 'supervisor.dashboard' },
      { label: 'Buat Tugas', icon: ClipboardPlus, route: 'supervisor.tasks.create', match: 'supervisor.tasks.create' },
    ],
  },
  {
    label: 'Bimbingan',
    items: [
      { label: 'Daftar Tugas', icon: ClipboardList, route: 'supervisor.tasks.index', match: 'supervisor.tasks.index' },
      { label: 'Mahasiswa Bimbingan', icon: Users, route: 'supervisor.students.index', match: 'supervisor.students.*' },
      { label: 'Laporan Kegiatan Harian Mahasiswa', icon: NotebookPen, route: 'supervisor.logbooks.index', match: 'supervisor.logbooks.*' },
    ],
  },
  {
    label: 'Presensi',
    items: [
      { label: 'Presensi', icon: CalendarCheck, route: 'supervisor.attendance.index', match: 'supervisor.attendance.index' },
      { label: 'Persetujuan Presensi', icon: ClipboardCheck, route: 'supervisor.attendance.approvals', match: 'supervisor.attendance.approvals' },
      { label: 'Laporan Presensi', icon: FileText, route: 'supervisor.attendance.reports', match: 'supervisor.attendance.reports' },
      { label: 'Presensi Mencurigakan', icon: AlertTriangle, route: 'supervisor.attendance.suspicious', match: 'supervisor.attendance.suspicious' },
    ],
  },
  {
    label: 'Penilaian',
    items: [
      { label: 'Dasbor Penilaian', icon: ClipboardCheck, route: 'supervisor.submissions.index', match: 'supervisor.submissions.*' },
      { label: 'Analitik Kinerja', icon: BarChart3, route: 'supervisor.analytics.dashboard', match: 'supervisor.analytics.dashboard' },
    ],
  },
  {
    label: 'Operasi Massal',
    items: [
      { label: 'Tugas Massal', icon: Layers, route: 'supervisor.bulk.create-task', match: 'supervisor.bulk.create-task' },
      { label: 'Notifikasi Massal', icon: Send, route: 'supervisor.bulk.send-notification', match: 'supervisor.bulk.send-notification' },
      { label: 'Impor Nilai', icon: Upload, route: 'supervisor.bulk.import-grades', match: 'supervisor.bulk.import-grades' },
      { label: 'Pengaturan Otomasi', icon: SlidersHorizontal, route: 'supervisor.bulk.automation-settings', match: 'supervisor.bulk.automation-settings' },
    ],
  },
  {
    label: 'Komunikasi',
    items: [
      { label: 'Pesan', icon: MessageSquare, route: 'messages.inbox', match: 'messages.*' },
      { label: 'Pengumuman', icon: Megaphone, route: 'announcements.index', match: 'announcements.*' },
    ],
  },
]

export default function SupervisorLayout({ children }) {
  return (
    <AppShell nav={nav} homeRoute="supervisor.dashboard">
      {children}
    </AppShell>
  )
}
