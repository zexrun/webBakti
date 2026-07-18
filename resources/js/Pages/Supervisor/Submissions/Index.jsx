import { Link, router, usePage } from '@inertiajs/react'
import { CheckCircle2, Clock, FileText } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

export default function Index({ submissions, tasks, students, totalSubmissions, gradedCount, pendingCount, filters }) {
  const { flash } = usePage().props
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('supervisor.submissions.index'), {
      status: form.status.value,
      task_id: form.task_id.value,
      student_id: form.student_id.value,
      sort_by: form.sort_by.value,
    })
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Dashboard Penilaian</h1>
          <p className="text-muted-foreground">Kelola dan nilai semua submission dari mahasiswa bimbingan</p>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}

        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-lg bg-blue-100 p-2">
                <FileText className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Total Submission</p>
                <p className="text-2xl font-bold text-foreground">{totalSubmissions}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-lg bg-green-100 p-2">
                <CheckCircle2 className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Sudah Dinilai</p>
                <p className="text-2xl font-bold text-green-600">{gradedCount}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-lg bg-yellow-100 p-2">
                <Clock className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Menunggu Nilai</p>
                <p className="text-2xl font-bold text-yellow-600">{pendingCount}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleFilter} className="space-y-4">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Status</label>
                  <Select name="status" defaultValue={filters.status ?? ''}>
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Nilai</option>
                    <option value="graded">Sudah Dinilai</option>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Tugas</label>
                  <Select name="task_id" defaultValue={filters.task_id ?? ''}>
                    <option value="">Semua Tugas</option>
                    {tasks.map((task) => (
                      <option key={task.id} value={task.id}>{task.title}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Mahasiswa</label>
                  <Select name="student_id" defaultValue={filters.student_id ?? ''}>
                    <option value="">Semua Mahasiswa</option>
                    {students.map((student) => (
                      <option key={student.id} value={student.id}>{student.user.name}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Urutkan</label>
                  <Select name="sort_by" defaultValue={filters.sort_by ?? 'created_at'}>
                    <option value="created_at">Terbaru</option>
                    <option value="updated_at">Terakhir Diupdate</option>
                    <option value="grade">Nilai</option>
                  </Select>
                </div>
              </div>

              <div className="flex justify-end gap-2">
                <Link href={r('supervisor.submissions.index')}>
                  <Button type="button" variant="secondary">Reset</Button>
                </Link>
                <Button type="submit">Terapkan Filter</Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-0">
            {submissions.data.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Mahasiswa</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Tugas</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Disubmit</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Nilai</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {submissions.data.map((submission) => (
                      <tr key={submission.id} className="hover:bg-accent">
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                              <span className="text-sm font-semibold text-blue-600">
                                {submission.student.user.name.charAt(0).toUpperCase()}
                              </span>
                            </div>
                            <div>
                              <p className="text-sm font-medium text-foreground">{submission.student.user.name}</p>
                              <p className="text-xs text-muted-foreground">{submission.student.user.email}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <p className="text-sm font-medium text-foreground">{submission.task.title}</p>
                          <p className="text-xs text-muted-foreground">{submission.task.type === 'harian' ? 'Harian' : 'Akhir'}</p>
                        </td>
                        <td className="px-6 py-4">
                          <p className="text-sm text-foreground">{new Date(submission.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</p>
                          <p className="text-xs text-muted-foreground">{new Date(submission.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
                        </td>
                        <td className="px-6 py-4">
                          {submission.grade ? (
                            <Badge variant="success">Dinilai</Badge>
                          ) : (
                            <Badge variant="warning">Menunggu</Badge>
                          )}
                        </td>
                        <td className="px-6 py-4">
                          {submission.grade ? (
                            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-600">
                              {submission.grade}
                            </span>
                          ) : (
                            <span className="text-sm text-muted-foreground">-</span>
                          )}
                        </td>
                        <td className="px-6 py-4 text-sm">
                          <div className="flex gap-3">
                            <Link href={r('supervisor.submissions.edit', submission.id)} className="font-medium text-primary hover:underline">
                              Nilai
                            </Link>
                            <Link href={r('supervisor.tasks.show', submission.task.id)} className="font-medium text-muted-foreground hover:underline">
                              Lihat Detail
                            </Link>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <FileText className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Tidak ada submission</h3>
                <p className="text-muted-foreground">Belum ada submission yang sesuai dengan filter yang dipilih.</p>
              </div>
            )}
            <div className="border-t border-border p-4">
              <Pagination links={submissions.links} />
            </div>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
