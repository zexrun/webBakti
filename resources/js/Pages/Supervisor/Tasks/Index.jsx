import { Link, router } from '@inertiajs/react'
import { Users, ClipboardList, Clock, Plus, Printer, Eye, Pencil, Trash2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

function StatCard({ icon: Icon, label, value, accent }) {
  return (
    <Card>
      <CardContent className="flex items-center gap-4 p-5">
        <div className={`flex h-11 w-11 items-center justify-center rounded-lg ${accent}`}>
          <Icon className="h-5 w-5" />
        </div>
        <div>
          <p className="text-sm text-muted-foreground">{label}</p>
          <p className="text-2xl font-bold text-foreground">{value}</p>
        </div>
      </CardContent>
    </Card>
  )
}

export default function Index({ students }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const totalTasks = students.data.reduce((sum, s) => sum + s.tasks.length, 0)
  const activeTasks = students.data.reduce(
    (sum, s) => sum + s.tasks.filter((t) => !t.due_date || new Date(t.due_date) > new Date()).length,
    0,
  )

  function handleDelete(task) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
      router.delete(r('supervisor.tasks.destroy', task.id))
    }
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Daftar Penugasan</h1>
            <p className="text-muted-foreground">Kelola tugas untuk setiap mahasiswa bimbingan</p>
          </div>
          <Link href={r('supervisor.tasks.create')}>
            <Button>
              <Plus className="h-4 w-4" /> Buat Tugas Baru
            </Button>
          </Link>
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} accent="bg-blue-100 text-blue-700" />
          <StatCard icon={ClipboardList} label="Total Tugas (halaman ini)" value={totalTasks} accent="bg-green-100 text-green-700" />
          <StatCard icon={Clock} label="Tugas Aktif (halaman ini)" value={activeTasks} accent="bg-yellow-100 text-yellow-700" />
        </div>

        <div className="space-y-6">
          {students.data.length === 0 ? (
            <Card>
              <CardContent className="p-12 text-center">
                <Users className="mx-auto mb-4 h-16 w-16 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum ada mahasiswa bimbingan</h3>
                <p className="mb-6 text-muted-foreground">Anda belum memiliki mahasiswa yang dibimbing.</p>
              </CardContent>
            </Card>
          ) : (
            students.data.map((student) => (
              <Card key={student.id} className="overflow-hidden">
                <div className="flex flex-col gap-4 border-b border-border p-6 sm:flex-row sm:items-center sm:justify-between">
                  <div className="flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                      <span className="text-lg font-semibold text-blue-700">{student.user?.name?.charAt(0)?.toUpperCase()}</span>
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold text-foreground">{student.user?.name}</h3>
                      <p className="text-sm text-muted-foreground">{student.nim ?? 'NIM belum diisi'}</p>
                      <p className="text-xs text-muted-foreground">{student.user?.email}</p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="text-right">
                      <p className="text-sm font-medium text-foreground">{student.tasks.length} Tugas</p>
                      <p className="text-xs text-muted-foreground">
                        {student.tasks.filter((t) => t.is_submitted).length} Selesai
                      </p>
                    </div>
                    <a href={r('supervisor.pdf.grade', student.id)}>
                      <Button size="sm" variant="secondary">
                        <Printer className="h-4 w-4" /> Cetak Nilai
                      </Button>
                    </a>
                  </div>
                </div>

                <CardContent className="p-0">
                  {student.tasks.length ? (
                    <div className="overflow-x-auto">
                      <table className="w-full">
                        <thead className="bg-muted">
                          <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tugas</th>
                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tipe</th>
                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tenggat</th>
                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                          {student.tasks.map((task) => {
                            const isOverdue = task.due_date && new Date(task.due_date) < new Date()
                            return (
                              <tr key={task.id} className="hover:bg-accent">
                                <td className="px-6 py-4">
                                  <p className="text-sm font-medium text-foreground">{task.title}</p>
                                  <p className="max-w-xs truncate text-sm text-muted-foreground">{task.description}</p>
                                </td>
                                <td className="whitespace-nowrap px-6 py-4">
                                  <Badge variant={task.type === 'harian' ? 'default' : 'secondary'}>
                                    {task.type === 'harian' ? 'Harian' : 'Akhir'}
                                  </Badge>
                                </td>
                                <td className="whitespace-nowrap px-6 py-4">
                                  {task.due_date ? (
                                    <>
                                      <p className="text-sm text-foreground">
                                        {new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                                      </p>
                                      <p className={`text-xs ${isOverdue ? 'text-red-500' : 'text-muted-foreground'}`}>
                                        {new Date(task.due_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                        {isOverdue && !task.is_submitted && <span className="ml-1 font-medium text-red-600">Terlambat</span>}
                                      </p>
                                    </>
                                  ) : (
                                    <span className="text-sm text-muted-foreground">Tidak ada</span>
                                  )}
                                </td>
                                <td className="whitespace-nowrap px-6 py-4">
                                  {task.is_submitted ? (
                                    <Badge variant="success">Selesai</Badge>
                                  ) : isOverdue ? (
                                    <Badge variant="destructive">Terlambat</Badge>
                                  ) : (
                                    <Badge variant="warning">Menunggu</Badge>
                                  )}
                                </td>
                                <td className="whitespace-nowrap px-6 py-4">
                                  <div className="flex items-center gap-3">
                                    <Link href={r('supervisor.tasks.show', task.id)} className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                                      <Eye className="h-3 w-3" /> Lihat
                                    </Link>
                                    <Link href={r('supervisor.tasks.edit', task.id)} className="inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:underline">
                                      <Pencil className="h-3 w-3" /> Edit
                                    </Link>
                                    <button
                                      type="button"
                                      onClick={() => handleDelete(task)}
                                      className="inline-flex items-center gap-1 text-sm font-medium text-destructive hover:underline"
                                    >
                                      <Trash2 className="h-3 w-3" /> Hapus
                                    </button>
                                  </div>
                                </td>
                              </tr>
                            )
                          })}
                        </tbody>
                      </table>
                    </div>
                  ) : (
                    <div className="p-12 text-center">
                      <ClipboardList className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                      <h3 className="mb-1 text-sm font-medium text-foreground">Belum ada tugas</h3>
                      <p className="text-sm text-muted-foreground">Belum ada tugas yang diberikan untuk mahasiswa ini.</p>
                    </div>
                  )}
                </CardContent>
              </Card>
            ))
          )}
        </div>

        <Pagination links={students.links} />
      </div>
    </SupervisorLayout>
  )
}
