import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon, RotateCcw, ArrowRight } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

const statusVariant = {
  Dinilai: 'success',
  Pending: 'warning',
  'Belum Submit': 'destructive',
}

export default function AdvancedStudent({ tasks, studentId, filters }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('search.advanced'), {
      task_search: form.task_search.value,
      status: form.status.value,
      due_date_from: form.due_date_from.value,
      due_date_to: form.due_date_to.value,
    })
  }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Pencarian Lanjutan - Tugas</h1>
          <p className="text-muted-foreground">Filter dan cari tugas anda dengan kriteria spesifik</p>
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
                  <Select name="status" defaultValue={filters.status ?? ''}>
                    <option value="">Semua</option>
                    <option value="pending">Belum Submit</option>
                    <option value="submitted">Sudah Submit</option>
                    <option value="graded">Sudah Dinilai</option>
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

        {tasks.data.length ? (
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {tasks.data.map((task) => {
              const submission = task.submissions.find((s) => s.student_id === studentId)
              const status = submission ? (submission.grade !== null ? 'Dinilai' : 'Pending') : 'Belum Submit'

              return (
                <Card key={task.id} className="transition hover:shadow-md">
                  <CardContent className="p-6">
                    <div className="mb-3 flex items-start justify-between gap-2">
                      <h3 className="flex-1 text-lg font-semibold text-foreground">{task.title}</h3>
                      <Badge variant={statusVariant[status]}>{status}</Badge>
                    </div>

                    {task.description && (
                      <p className="mb-3 text-sm text-muted-foreground">
                        {task.description.length > 100 ? `${task.description.slice(0, 100)}...` : task.description}
                      </p>
                    )}

                    <div className="mb-4 space-y-2 text-sm">
                      <p className="text-muted-foreground">
                        <strong className="text-foreground">Deadline:</strong>{' '}
                        {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                      </p>
                      {submission && submission.grade !== null && (
                        <p className="text-muted-foreground">
                          <strong className="text-foreground">Nilai:</strong>{' '}
                          <span className="text-lg font-bold text-blue-600">{submission.grade}</span>
                        </p>
                      )}
                    </div>

                    <Link href={r('student.tasks.show', task.id)} className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                      Lihat Detail <ArrowRight className="h-3 w-3" />
                    </Link>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        ) : (
          <Card>
            <CardContent className="p-12 text-center text-muted-foreground">Tidak ada hasil</CardContent>
          </Card>
        )}

        <Pagination links={tasks.links} />
      </div>
    </RoleLayout>
  )
}
