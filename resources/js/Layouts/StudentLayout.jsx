import {
  LayoutDashboard,
  ClipboardList,
  NotebookPen,
  Info,
  CalendarCheck,
  FileText,
  MessageSquare,
  Megaphone,
} from 'lucide-react'
import AppShell from '@/Layouts/AppShell'

const nav = [
  {
    items: [
      { label: 'Dashboard', icon: LayoutDashboard, route: 'student.dashboard', match: 'student.dashboard' },
      { label: 'Tugas', icon: ClipboardList, route: 'student.tasks.index', match: 'student.tasks.*' },
      { label: 'Logbook', icon: NotebookPen, route: 'student.logbooks.index', match: 'student.logbooks.*' },
      { label: 'Informasi Magang', icon: Info, route: 'student.info.edit', match: 'student.info.*' },
      { label: 'Presensi', icon: CalendarCheck, route: 'student.attendance.index', match: 'student.attendance.*' },
      { label: 'Dokumen', icon: FileText, route: 'student.documents.index', match: 'student.documents.*' },
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

export default function StudentLayout({ children }) {
  return (
    <AppShell nav={nav} homeRoute="student.dashboard">
      {children}
    </AppShell>
  )
}
