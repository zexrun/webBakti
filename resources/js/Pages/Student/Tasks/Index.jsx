import { useMemo, useState } from 'react'
import { Link } from '@inertiajs/react'
import { ClipboardList, Search, LayoutList, LayoutGrid, Eye, Clock, User } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Input } from '@/Components/ui/input'
import Pagination from '@/Components/Pagination'
import { formatDeadline } from '@/lib/deadline'

const typeConfig = {
  harian: { label: 'Harian', variant: 'default' },
  akhir: { label: 'Laporan Akhir', variant: 'secondary' },
}

function taskStatus(task) {
  if (task.is_submitted) return 'completed'
  if (task.due_date && new Date(task.due_date) < new Date()) return 'overdue'
  return 'pending'
}

function StatusBadge({ status }) {
  if (status === 'completed') return <Badge variant="success">Selesai</Badge>
  if (status === 'overdue') return <Badge variant="destructive">Terlambat</Badge>
  return <Badge variant="warning">Pending</Badge>
}

export default function Index({ tasks }) {
  const [view, setView] = useState('table')
  const [statusFilter, setStatusFilter] = useState('all')
  const [search, setSearch] = useState('')

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const enriched = useMemo(
    () => tasks.data.map((task) => ({ ...task, status: taskStatus(task) })),
    [tasks.data],
  )

  const pendingCount = enriched.filter((t) => t.status === 'pending' || t.status === 'overdue').length
  const completedCount = enriched.filter((t) => t.status === 'completed').length

  const filtered = enriched.filter((task) => {
    const matchesStatus = statusFilter === 'all' || task.status === statusFilter
    const matchesSearch = task.title.toLowerCase().includes(search.toLowerCase())
    return matchesStatus && matchesSearch
  })

  return (
    <StudentLayout>
      <div className="space-y-6">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Daftar Tugas</h1>
            <p className="text-muted-foreground">Kelola dan kerjakan tugas dari pembimbing Anda 📋</p>
          </div>
          <div className="flex flex-wrap items-center gap-2">
            <Badge variant="secondary">Total: {tasks.total}</Badge>
            <Badge variant="warning">Pending: {pendingCount}</Badge>
            <Badge variant="success">Selesai: {completedCount}</Badge>
          </div>
        </div>

        <Card>
          <CardContent className="flex flex-col gap-4 p-4 md:flex-row md:items-center md:justify-between">
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="md:w-56">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Tugas</SelectItem>
                <SelectItem value="pending">Belum Dikerjakan</SelectItem>
                <SelectItem value="completed">Sudah Selesai</SelectItem>
                <SelectItem value="overdue">Terlambat</SelectItem>
              </SelectContent>
            </Select>
            <div className="relative md:w-64">
              <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Cari tugas..."
                className="pl-9"
              />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex-row items-center justify-between space-y-0">
            <CardTitle className="flex items-center gap-2 text-base">
              <ClipboardList className="h-5 w-5 text-blue-600" /> Daftar Tugas Aktif
            </CardTitle>
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => setView('table')}
                className={`rounded-lg p-2 ${view === 'table' ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'}`}
              >
                <LayoutList className="h-4 w-4" />
              </button>
              <button
                type="button"
                onClick={() => setView('card')}
                className={`rounded-lg p-2 ${view === 'card' ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'}`}
              >
                <LayoutGrid className="h-4 w-4" />
              </button>
            </div>
          </CardHeader>
          <CardContent className={view === 'table' ? 'p-0' : undefined}>
            {filtered.length === 0 ? (
              <div className="p-12 text-center">
                <ClipboardList className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum ada tugas</h3>
                <p className="text-muted-foreground">Tugas dari pembimbing akan muncul di sini</p>
              </div>
            ) : view === 'table' ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Judul Tugas</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tipe & Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Pembimbing</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Deadline</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {filtered.map((task) => {
                      const deadline = formatDeadline(task.due_date)
                      const config = typeConfig[task.type] ?? { label: task.type, variant: 'secondary' }
                      return (
                        <tr key={task.id} className="hover:bg-accent">
                          <td className="px-6 py-4">
                            <p className="text-sm font-semibold text-foreground">{task.title}</p>
                            <p className="text-xs text-muted-foreground">{task.description?.substring(0, 50)}</p>
                          </td>
                          <td className="px-6 py-4">
                            <div className="flex flex-col gap-1">
                              <Badge variant={config.variant} className="w-fit">{config.label}</Badge>
                              <StatusBadge status={task.status} />
                            </div>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4 text-sm text-foreground">
                            {task.supervisor?.user?.name}
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            {task.due_date ? (
                              <div>
                                <p className="text-sm text-foreground">
                                  {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                                </p>
                                <p className={`text-xs font-medium text-${deadline.color}-600`}>{deadline.text}</p>
                              </div>
                            ) : (
                              <span className="text-sm text-muted-foreground">Tidak ada deadline</span>
                            )}
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <Link
                              href={r('student.tasks.show', task.id)}
                              className="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200"
                            >
                              <Eye className="h-3 w-3" /> {task.is_submitted ? 'Lihat Detail' : 'Kerjakan'}
                            </Link>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2 lg:grid-cols-3">
                {filtered.map((task) => {
                  const deadline = formatDeadline(task.due_date)
                  return (
                    <Card key={task.id}>
                      <CardContent className="p-6">
                        <div className="mb-4 flex items-start justify-between">
                          <h3 className="text-lg font-semibold text-foreground">{task.title}</h3>
                          <StatusBadge status={task.status} />
                        </div>
                        <p className="mb-4 text-sm text-muted-foreground">{task.description?.substring(0, 100)}</p>
                        <div className="mb-4 space-y-2">
                          <p className="flex items-center gap-2 text-sm text-muted-foreground">
                            <User className="h-4 w-4" /> {task.supervisor?.user?.name}
                          </p>
                          {task.due_date && (
                            <p className={`flex items-center gap-2 text-sm text-${deadline.color}-600`}>
                              <Clock className="h-4 w-4" /> {deadline.text}
                            </p>
                          )}
                        </div>
                        <Link
                          href={r('student.tasks.show', task.id)}
                          className="block w-full rounded-lg bg-blue-100 px-4 py-2 text-center text-sm font-medium text-blue-700 hover:bg-blue-200"
                        >
                          {task.is_submitted ? 'Lihat Detail' : 'Kerjakan Tugas'}
                        </Link>
                      </CardContent>
                    </Card>
                  )
                })}
              </div>
            )}
          </CardContent>
        </Card>

        <Pagination links={tasks.links} />
      </div>
    </StudentLayout>
  )
}
