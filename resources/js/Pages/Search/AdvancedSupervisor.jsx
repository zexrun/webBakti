import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import SearchFilters, { ResultCount } from './SearchFilters'

const statusVariant = {
  Complete: 'success',
  Partial: 'warning',
  Pending: 'secondary',
}

const statusOptions = [
  { value: 'all', label: 'Semua' },
  { value: 'pending', label: 'Pending' },
  { value: 'completed', label: 'Completed' },
]

export default function AdvancedSupervisor({ tasks, filters }) {
  const [taskStatusFilter, setTaskStatusFilter] = useState(filters.task_status ?? 'all')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('search.advanced'), {
      task_search: form.task_search.value,
      ...(taskStatusFilter !== 'all' ? { task_status: taskStatusFilter } : {}),
      due_date_from: form.due_date_from.value,
      due_date_to: form.due_date_to.value,
    })
  }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader title="Pencarian Lanjutan — Tugas" description="Filter dan cari tugas dengan kriteria spesifik" />

        <SearchFilters
          filters={filters}
          statusOptions={statusOptions}
          statusValue={taskStatusFilter}
          onStatusChange={setTaskStatusFilter}
          onSubmit={handleSubmit}
          resetHref={r('search.advanced')}
        />

        <ResultCount shown={tasks.data.length} total={tasks.total} />

        <Card>
          {tasks.data.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Judul Tugas</TableHead>
                  <TableHead>Deadline</TableHead>
                  <TableHead className="text-right">Submitted</TableHead>
                  <TableHead className="text-right">Graded</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {tasks.data.map((task) => {
                  const submitted = task.submissions.length
                  const graded = task.submissions.filter((s) => s.grade !== null).length
                  const status = submitted === 0 ? 'Pending' : graded === submitted ? 'Complete' : 'Partial'
                  return (
                    <TableRow key={task.id}>
                      <TableCell className="font-medium text-foreground">{task.title}</TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">
                        {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                      </TableCell>
                      <TableCell className="text-right tabular-nums text-muted-foreground">{submitted}</TableCell>
                      <TableCell className="text-right tabular-nums text-muted-foreground">{graded}</TableCell>
                      <TableCell>
                        <Badge variant={statusVariant[status]}>{status}</Badge>
                      </TableCell>
                      <TableCell>
                        <Button asChild size="xs" variant="outline">
                          <Link href={r('supervisor.tasks.show', task.id)}>Lihat</Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          ) : (
            <EmptyState icon={SearchIcon} title="Tidak ada hasil" description="Coba ubah kriteria pencarian Anda." />
          )}
        </Card>

        {tasks.links?.length > 3 && <Pagination links={tasks.links} />}
      </div>
    </RoleLayout>
  )
}
