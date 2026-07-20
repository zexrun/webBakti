import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon, ArrowRight } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import SearchFilters, { ResultCount } from './SearchFilters'

const statusVariant = {
  Dinilai: 'success',
  Pending: 'warning',
  'Belum Submit': 'destructive',
}

const statusOptions = [
  { value: 'all', label: 'Semua' },
  { value: 'pending', label: 'Belum Submit' },
  { value: 'submitted', label: 'Sudah Submit' },
  { value: 'graded', label: 'Sudah Dinilai' },
]

export default function AdvancedStudent({ tasks, studentId, filters }) {
  const [statusFilter, setStatusFilter] = useState(filters.status ?? 'all')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('search.advanced'), {
      task_search: form.task_search.value,
      ...(statusFilter !== 'all' ? { status: statusFilter } : {}),
      due_date_from: form.due_date_from.value,
      due_date_to: form.due_date_to.value,
    })
  }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader title="Pencarian Lanjutan — Tugas" description="Filter dan cari tugas Anda dengan kriteria spesifik" />

        <SearchFilters
          filters={filters}
          statusOptions={statusOptions}
          statusValue={statusFilter}
          onStatusChange={setStatusFilter}
          onSubmit={handleSubmit}
          resetHref={r('search.advanced')}
        />

        <ResultCount shown={tasks.data.length} total={tasks.total} />

        {tasks.data.length ? (
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            {tasks.data.map((task) => {
              const submission = task.submissions.find((s) => s.student_id === studentId)
              const status = submission ? (submission.grade !== null ? 'Dinilai' : 'Pending') : 'Belum Submit'

              return (
                <Card key={task.id} className="flex flex-col transition-colors duration-150 hover:border-muted-foreground/30">
                  <CardContent className="flex flex-1 flex-col p-5">
                    <div className="mb-2 flex items-start justify-between gap-2">
                      <h3 className="text-base font-semibold text-foreground">{task.title}</h3>
                      <Badge variant={statusVariant[status]}>{status}</Badge>
                    </div>

                    {task.description && (
                      <p className="mb-3 line-clamp-2 text-sm text-muted-foreground">{task.description}</p>
                    )}

                    <div className="mb-4 mt-auto space-y-1 text-sm">
                      <p className="text-muted-foreground">
                        <span className="font-medium text-foreground">Deadline:</span>{' '}
                        <span className="tabular-nums">
                          {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                        </span>
                      </p>
                      {submission && submission.grade !== null && (
                        <p className="text-muted-foreground">
                          <span className="font-medium text-foreground">Nilai:</span>{' '}
                          <span className="font-bold text-primary">{submission.grade}</span>
                        </p>
                      )}
                    </div>

                    <Button asChild size="xs" variant="outline" className="w-fit">
                      <Link href={r('student.tasks.show', task.id)}>
                        Lihat Detail <ArrowRight />
                      </Link>
                    </Button>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        ) : (
          <Card>
            <EmptyState icon={SearchIcon} title="Tidak ada hasil" description="Coba ubah kriteria pencarian Anda." />
          </Card>
        )}

        {tasks.links?.length > 3 && <Pagination links={tasks.links} />}
      </div>
    </RoleLayout>
  )
}
