import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon, RotateCcw } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

const statusVariant = {
  Complete: 'success',
  Partial: 'warning',
  Pending: 'secondary',
}

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
        <div>
          <h1 className="text-2xl font-bold text-foreground">Pencarian Lanjutan - Tugas</h1>
          <p className="text-muted-foreground">Filter dan cari tugas dengan kriteria spesifik</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Judul Tugas</label>
                  <Input name="task_search" defaultValue={filters.task_search ?? ''} placeholder="Cari judul..." />
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Status</label>
                  <Select value={taskStatusFilter} onValueChange={setTaskStatusFilter}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua</SelectItem>
                      <SelectItem value="pending">Pending</SelectItem>
                      <SelectItem value="completed">Completed</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Deadline Dari</label>
                  <Input type="date" name="due_date_from" defaultValue={filters.due_date_from ?? ''} />
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Deadline Sampai</label>
                  <Input type="date" name="due_date_to" defaultValue={filters.due_date_to ?? ''} />
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit">
                  <SearchIcon className="h-4 w-4" /> Cari
                </Button>
                <Link href={r('search.advanced')}>
                  <Button type="button" variant="secondary">
                    <RotateCcw className="h-4 w-4" /> Reset
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>

        <p className="text-sm text-muted-foreground">
          Menampilkan <strong className="text-foreground">{tasks.data.length}</strong> dari <strong className="text-foreground">{tasks.total}</strong> hasil
        </p>

        <Card>
          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="border-b border-border bg-muted">
                  <tr>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Judul Tugas</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Deadline</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Submitted</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Graded</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Status</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {tasks.data.length ? (
                    tasks.data.map((task) => {
                      const submitted = task.submissions.length
                      const graded = task.submissions.filter((s) => s.grade !== null).length
                      const status = submitted === 0 ? 'Pending' : graded === submitted ? 'Complete' : 'Partial'
                      return (
                        <tr key={task.id} className="hover:bg-accent">
                          <td className="px-6 py-4 text-sm font-medium text-foreground">{task.title}</td>
                          <td className="px-6 py-4 text-sm text-muted-foreground">
                            {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                          </td>
                          <td className="px-6 py-4 text-center text-sm text-foreground">{submitted}</td>
                          <td className="px-6 py-4 text-center text-sm text-foreground">{graded}</td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant={statusVariant[status]}>{status}</Badge>
                          </td>
                          <td className="px-6 py-4 text-center text-sm">
                            <Link href={r('supervisor.tasks.show', task.id)} className="font-medium text-primary hover:underline">
                              Lihat
                            </Link>
                          </td>
                        </tr>
                      )
                    })
                  ) : (
                    <tr>
                      <td colSpan={6} className="px-6 py-8 text-center text-muted-foreground">Tidak ada hasil</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <Pagination links={tasks.links} />
      </div>
    </RoleLayout>
  )
}
