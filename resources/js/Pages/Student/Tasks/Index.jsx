import { useMemo, useState } from 'react'
import { Link } from '@inertiajs/react'
import { ClipboardList, Search, LayoutList, LayoutGrid, User, Clock } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Input } from '@/Components/ui/input'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { formatDeadline, deadlineToneClass } from '@/lib/deadline'
import { cn } from '@/lib/utils'

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

function ViewToggle({ view, onChange }) {
  return (
    <div className="flex items-center gap-1 rounded-md border border-border p-1">
      {[
        { key: 'table', icon: LayoutList },
        { key: 'card', icon: LayoutGrid },
      ].map(({ key, icon: Icon }) => (
        <button
          key={key}
          type="button"
          onClick={() => onChange(key)}
          aria-label={key === 'table' ? 'Tampilan tabel' : 'Tampilan kartu'}
          className={cn(
            'rounded-sm p-1.5 transition-colors duration-150',
            view === key ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground',
          )}
        >
          <Icon className="h-4 w-4" />
        </button>
      ))}
    </div>
  )
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
        <PageHeader
          title="Daftar Tugas"
          description="Kelola dan kerjakan tugas dari pembimbing Anda"
          actions={
            <div className="flex flex-wrap items-center gap-2">
              <Badge variant="secondary">Total: {tasks.total}</Badge>
              <Badge variant="warning">Pending: {pendingCount}</Badge>
              <Badge variant="success">Selesai: {completedCount}</Badge>
            </div>
          }
        />

        <form className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="sm:w-56">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Tugas</SelectItem>
              <SelectItem value="pending">Belum Dikerjakan</SelectItem>
              <SelectItem value="completed">Sudah Selesai</SelectItem>
              <SelectItem value="overdue">Terlambat</SelectItem>
            </SelectContent>
          </Select>
          <div className="flex items-center gap-3">
            <div className="relative flex-1 sm:w-64 sm:flex-none">
              <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari tugas..." className="pl-9" />
            </div>
            <ViewToggle view={view} onChange={setView} />
          </div>
        </form>

        {filtered.length === 0 ? (
          <Card>
            <EmptyState
              icon={ClipboardList}
              title="Belum ada tugas"
              description="Tugas dari pembimbing akan muncul di sini."
            />
          </Card>
        ) : view === 'table' ? (
          <Card>
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Judul Tugas</TableHead>
                  <TableHead>Tipe & Status</TableHead>
                  <TableHead>Pembimbing</TableHead>
                  <TableHead>Deadline</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filtered.map((task) => {
                  const deadline = formatDeadline(task.due_date)
                  const config = typeConfig[task.type] ?? { label: task.type, variant: 'secondary' }
                  return (
                    <TableRow key={task.id}>
                      <TableCell className="whitespace-normal">
                        <p className="text-sm font-medium text-foreground">{task.title}</p>
                        <p className="max-w-xs truncate text-xs text-muted-foreground">{task.description}</p>
                      </TableCell>
                      <TableCell>
                        <div className="flex flex-col items-start gap-1">
                          <Badge variant={config.variant}>{config.label}</Badge>
                          <StatusBadge status={task.status} />
                        </div>
                      </TableCell>
                      <TableCell className="text-muted-foreground">{task.supervisor?.user?.name}</TableCell>
                      <TableCell>
                        {task.due_date ? (
                          <div>
                            <p className="text-sm tabular-nums text-foreground">
                              {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                            </p>
                            <p className={cn('text-xs font-medium', deadlineToneClass[deadline.color])}>{deadline.text}</p>
                          </div>
                        ) : (
                          <span className="text-sm text-muted-foreground">Tidak ada deadline</span>
                        )}
                      </TableCell>
                      <TableCell>
                        <Button asChild size="xs" variant="outline">
                          <Link href={r('student.tasks.show', task.id)}>
                            {task.is_submitted ? 'Lihat Detail' : 'Kerjakan'}
                          </Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </Card>
        ) : (
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            {filtered.map((task) => {
              const deadline = formatDeadline(task.due_date)
              return (
                <Card key={task.id} className="flex flex-col">
                  <CardContent className="flex flex-1 flex-col p-5">
                    <div className="mb-2 flex items-start justify-between gap-2">
                      <h3 className="text-base font-semibold text-foreground">{task.title}</h3>
                      <StatusBadge status={task.status} />
                    </div>
                    <p className="mb-4 line-clamp-2 text-sm text-muted-foreground">{task.description}</p>
                    <div className="mb-4 mt-auto space-y-1.5 text-sm">
                      <p className="flex items-center gap-2 text-muted-foreground">
                        <User className="h-4 w-4" /> {task.supervisor?.user?.name}
                      </p>
                      {task.due_date && (
                        <p className={cn('flex items-center gap-2', deadlineToneClass[deadline.color])}>
                          <Clock className="h-4 w-4" /> {deadline.text}
                        </p>
                      )}
                    </div>
                    <Button asChild variant="outline" className="w-full">
                      <Link href={r('student.tasks.show', task.id)}>
                        {task.is_submitted ? 'Lihat Detail' : 'Kerjakan Tugas'}
                      </Link>
                    </Button>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        )}

        {tasks.links?.length > 3 && <Pagination links={tasks.links} />}
      </div>
    </StudentLayout>
  )
}
