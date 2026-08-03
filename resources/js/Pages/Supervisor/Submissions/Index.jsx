import { useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { CheckCircle2, Clock, FileText } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default function Index({ submissions, tasks, students, totalSubmissions, gradedCount, pendingCount, filters }) {
  const { flash } = usePage().props
  const [statusFilter, setStatusFilter] = useState(filters.status ?? 'all')
  const [taskIdFilter, setTaskIdFilter] = useState(filters.task_id ? String(filters.task_id) : 'all')
  const [studentIdFilter, setStudentIdFilter] = useState(filters.student_id ? String(filters.student_id) : 'all')
  const [sortByFilter, setSortByFilter] = useState(filters.sort_by ?? 'created_at')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFilter(e) {
    e.preventDefault()
    router.get(r('supervisor.submissions.index'), {
      ...(statusFilter !== 'all' ? { status: statusFilter } : {}),
      ...(taskIdFilter !== 'all' ? { task_id: taskIdFilter } : {}),
      ...(studentIdFilter !== 'all' ? { student_id: studentIdFilter } : {}),
      sort_by: sortByFilter,
    })
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Dashboard Penilaian"
          description="Kelola dan nilai semua submission dari mahasiswa bimbingan"
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <StatCard icon={FileText} label="Total Submission" value={totalSubmissions} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Sudah Dinilai" value={gradedCount} tone="green" index={1} />
          <StatCard icon={Clock} label="Menunggu Nilai" value={pendingCount} tone="amber" index={2} />
        </div>

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Status</label>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-40">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Status</SelectItem>
                <SelectItem value="pending">Menunggu Nilai</SelectItem>
                <SelectItem value="graded">Sudah Dinilai</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Tugas</label>
            <Select value={taskIdFilter} onValueChange={setTaskIdFilter}>
              <SelectTrigger className="w-48">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Tugas</SelectItem>
                {tasks.map((task) => (
                  <SelectItem key={task.id} value={String(task.id)}>{task.title}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Mahasiswa</label>
            <Select value={studentIdFilter} onValueChange={setStudentIdFilter}>
              <SelectTrigger className="w-48">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Mahasiswa</SelectItem>
                {students.map((student) => (
                  <SelectItem key={student.id} value={String(student.id)}>{student.user.name}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Urutkan</label>
            <Select value={sortByFilter} onValueChange={setSortByFilter}>
              <SelectTrigger className="w-44">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="created_at">Terbaru</SelectItem>
                <SelectItem value="updated_at">Terakhir Diupdate</SelectItem>
                <SelectItem value="grade">Nilai</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button asChild type="button" variant="ghost">
            <Link href={r('supervisor.submissions.index')}>Reset</Link>
          </Button>
        </form>

        <Card>
          {submissions.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Tugas</TableHead>
                    <TableHead>Disubmit</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Nilai</TableHead>
                    <TableHead className="w-44">Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {submissions.data.map((submission) => (
                    <TableRow key={submission.id}>
                      <TableCell>
                        <UserCell name={submission.student.user.name} subtitle={submission.student.user.email} />
                      </TableCell>
                      <TableCell className="whitespace-normal">
                        <p className="text-sm font-medium text-foreground">{submission.task.title}</p>
                        <p className="text-xs text-muted-foreground">{submission.task.type === 'daily' ? 'Harian' : 'Akhir'}</p>
                      </TableCell>
                      <TableCell>
                        <p className="tabular-nums text-foreground">
                          {new Date(submission.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                        </p>
                        <p className="text-xs tabular-nums text-muted-foreground">
                          {new Date(submission.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                        </p>
                      </TableCell>
                      <TableCell>
                        {submission.grade ? (
                          <Badge variant="success">Dinilai</Badge>
                        ) : (
                          <Badge variant="warning">Menunggu</Badge>
                        )}
                      </TableCell>
                      <TableCell>
                        {submission.grade ? (
                          <span className="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                            {submission.grade}
                          </span>
                        ) : (
                          <span className="text-muted-foreground">-</span>
                        )}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
                          <Button asChild size="xs" variant="outline">
                            <Link href={r('supervisor.submissions.edit', submission.id)}>Nilai</Link>
                          </Button>
                          <Button asChild size="xs" variant="ghost">
                            <Link href={r('supervisor.tasks.show', submission.task.id)}>Lihat Detail</Link>
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {submissions.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={submissions.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={FileText}
              title="Tidak ada submission"
              description="Belum ada submission yang sesuai dengan filter yang dipilih."
            />
          )}
        </Card>
      </div>
    </SupervisorLayout>
  )
}
