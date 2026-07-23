import { Link, router } from '@inertiajs/react'
import { Users, ClipboardList, Clock, Plus, Printer, Eye, Pencil, Trash2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'
import { useConfirm } from '@/hooks/useConfirm'

export default function Index({ students }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const confirm = useConfirm()

  const totalTasks = students.data.reduce((sum, s) => sum + s.tasks.length, 0)
  const activeTasks = students.data.reduce(
    (sum, s) => sum + s.tasks.filter((t) => !t.due_date || new Date(t.due_date) > new Date()).length,
    0,
  )

  async function handleDelete(task) {
    const confirmed = await confirm({
      title: 'Hapus tugas ini?',
      description: 'Tindakan ini tidak dapat dibatalkan.',
      variant: 'destructive',
    })
    if (confirmed) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Daftar Penugasan"
          description="Kelola tugas untuk setiap mahasiswa bimbingan"
          actions={
            <Button asChild>
              <Link href={r('supervisor.tasks.create')}>
                <Plus /> Buat Tugas Baru
              </Link>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" />
          <StatCard icon={ClipboardList} label="Total Tugas (halaman ini)" value={totalTasks} tone="green" />
          <StatCard icon={Clock} label="Tugas Aktif (halaman ini)" value={activeTasks} tone="amber" />
        </div>

        <div className="space-y-6">
          {students.data.length === 0 ? (
            <Card>
              <EmptyState
                icon={Users}
                title="Belum ada mahasiswa bimbingan"
                description="Anda belum memiliki mahasiswa yang dibimbing."
              />
            </Card>
          ) : (
            students.data.map((student) => (
              <Card key={student.id}>
                <div className="flex flex-col gap-3 border-b border-border px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                  <UserCell
                    name={student.user?.name}
                    subtitle={[student.nim ?? 'NIM belum diisi', student.user?.email].filter(Boolean).join(' · ')}
                  />
                  <div className="flex items-center gap-3">
                    <p className="text-sm tabular-nums text-muted-foreground">
                      <span className="font-medium text-foreground">{student.tasks.length}</span> tugas ·{' '}
                      <span className="font-medium text-foreground">{student.tasks.filter((t) => t.is_submitted).length}</span> selesai
                    </p>
                    <Button asChild size="xs" variant="outline">
                      <a href={r('supervisor.pdf.grade', student.id)}>
                        <Printer /> Cetak Nilai
                      </a>
                    </Button>
                  </div>
                </div>

                {student.tasks.length ? (
                  <Table>
                    <TableHeader>
                      <TableRow className="hover:bg-transparent">
                        <TableHead>Tugas</TableHead>
                        <TableHead>Tipe</TableHead>
                        <TableHead>Tenggat</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className="w-52">Aksi</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {student.tasks.map((task) => {
                        const isOverdue = task.due_date && new Date(task.due_date) < new Date()
                        return (
                          <TableRow key={task.id}>
                            <TableCell className="whitespace-normal">
                              <p className="text-sm font-medium text-foreground">{task.title}</p>
                              <p className="max-w-xs truncate text-sm text-muted-foreground">{task.description}</p>
                            </TableCell>
                            <TableCell>
                              <Badge variant={task.type === 'harian' ? 'default' : 'secondary'}>
                                {task.type === 'harian' ? 'Harian' : 'Akhir'}
                              </Badge>
                            </TableCell>
                            <TableCell>
                              {task.due_date ? (
                                <>
                                  <p className="tabular-nums text-foreground">
                                    {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                                  </p>
                                  <p className={cn('text-xs tabular-nums', isOverdue ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground')}>
                                    {new Date(task.due_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                  </p>
                                </>
                              ) : (
                                <span className="text-muted-foreground">Tidak ada</span>
                              )}
                            </TableCell>
                            <TableCell>
                              {task.is_submitted ? (
                                <Badge variant="success">Selesai</Badge>
                              ) : isOverdue ? (
                                <Badge variant="destructive">Terlambat</Badge>
                              ) : (
                                <Badge variant="warning">Menunggu</Badge>
                              )}
                            </TableCell>
                            <TableCell>
                              <div className="flex items-center gap-1.5">
                                <Button asChild size="xs" variant="outline">
                                  <Link href={r('supervisor.tasks.show', task.id)}>
                                    <Eye /> Lihat
                                  </Link>
                                </Button>
                                <Button asChild size="xs" variant="ghost">
                                  <Link href={r('supervisor.tasks.edit', task.id)}>
                                    <Pencil /> Edit
                                  </Link>
                                </Button>
                                <Button
                                  type="button"
                                  size="xs"
                                  variant="ghost"
                                  className="text-destructive hover:text-destructive"
                                  onClick={() => handleDelete(task)}
                                >
                                  <Trash2 /> Hapus
                                </Button>
                              </div>
                            </TableCell>
                          </TableRow>
                        )
                      })}
                    </TableBody>
                  </Table>
                ) : (
                  <EmptyState
                    icon={ClipboardList}
                    title="Belum ada tugas"
                    description="Belum ada tugas yang diberikan untuk mahasiswa ini."
                    className="py-8"
                  />
                )}
              </Card>
            ))
          )}
        </div>

        {students.links?.length > 3 && <Pagination links={students.links} />}
      </div>
    </SupervisorLayout>
  )
}
