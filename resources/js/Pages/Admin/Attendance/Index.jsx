import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { CheckCircle2, Clock, XCircle, Users, Filter, ClipboardCheck, BarChart3, Settings as SettingsIcon } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'
import ApprovalModal from './ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'destructive',
  absent: 'secondary',
}

const approvalVariant = {
  pending: 'warning',
  approved: 'success',
  rejected: 'destructive',
}

export default function Index({ attendances, stats, date, status }) {
  const [modalItem, setModalItem] = useState(null)
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('admin.attendance.index'), { date: form.date.value, status: form.status.value })
  }

  function quickApprove(attendance) {
    if (confirm('Apakah Anda yakin ingin menyetujui absensi ini?')) {
      router.post(r('admin.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="p-6">
            <h1 className="text-2xl font-bold text-foreground">Monitoring Absensi</h1>
            <p className="text-muted-foreground">Pantau absensi mahasiswa secara real-time dan kelola persetujuan.</p>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-4">
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                <Users className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Total Hadir</p>
                <p className="text-2xl font-bold text-foreground">{stats.total}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <CheckCircle2 className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Tepat Waktu</p>
                <p className="text-2xl font-bold text-foreground">{stats.present}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                <Clock className="h-6 w-6 text-orange-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Terlambat</p>
                <p className="text-2xl font-bold text-foreground">{stats.late}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <XCircle className="h-6 w-6 text-red-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Tidak Hadir</p>
                <p className="text-2xl font-bold text-foreground">{stats.absent}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardContent className="flex flex-col gap-4 p-6 lg:flex-row lg:items-end lg:justify-between">
            <form onSubmit={handleFilter} className="flex flex-col items-start gap-4 sm:flex-row sm:items-end">
              <div className="space-y-2">
                <label htmlFor="date" className="block text-sm font-medium text-foreground">Tanggal</label>
                <Input id="date" name="date" type="date" defaultValue={date} />
              </div>
              <div className="space-y-2">
                <label htmlFor="status" className="block text-sm font-medium text-foreground">Status</label>
                <Select id="status" name="status" defaultValue={status ?? ''}>
                  <option value="">Semua Status</option>
                  <option value="present">Hadir</option>
                  <option value="late">Terlambat</option>
                  <option value="absent">Tidak Hadir</option>
                  <option value="pending">Pending</option>
                </Select>
              </div>
              <Button type="submit">
                <Filter className="h-4 w-4" /> Filter
              </Button>
            </form>

            <div className="flex flex-wrap gap-3">
              <Link href={r('admin.attendance.approvals')}>
                <Button className="bg-yellow-600 hover:bg-yellow-700">
                  <ClipboardCheck className="h-4 w-4" /> Persetujuan
                </Button>
              </Link>
              <Link href={r('admin.attendance.reports')}>
                <Button className="bg-green-600 hover:bg-green-700">
                  <BarChart3 className="h-4 w-4" /> Laporan
                </Button>
              </Link>
              <Link href={r('admin.attendance.settings')}>
                <Button variant="secondary">
                  <SettingsIcon className="h-4 w-4" /> Pengaturan
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>

        <Card>
          <div className="border-b border-border p-6">
            <h3 className="text-lg font-semibold text-foreground">
              Data Absensi - {new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}
            </h3>
          </div>
          <CardContent className="p-0">
            {attendances.data.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Mahasiswa</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Check In</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Check Out</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground lg:table-cell">Durasi</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground sm:table-cell">Approval</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {attendances.data.map((attendance) => (
                      <tr key={attendance.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                              <span className="text-sm font-medium text-blue-600">
                                {attendance.user?.name?.charAt(0)?.toUpperCase()}
                              </span>
                            </div>
                            <div>
                              <p className="text-sm font-medium text-foreground">{attendance.user?.name}</p>
                              <p className="text-sm text-muted-foreground">{attendance.user?.email}</p>
                            </div>
                          </div>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {attendance.check_in ? (
                            <>
                              <p className="text-sm font-medium text-foreground">
                                {new Date(attendance.check_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                              </p>
                              {attendance.is_late && <p className="text-xs text-destructive">Terlambat</p>}
                            </>
                          ) : (
                            <span className="text-muted-foreground">-</span>
                          )}
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-foreground md:table-cell">
                          {attendance.check_out ? new Date(attendance.check_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-foreground lg:table-cell">
                          {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                          </Badge>
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 sm:table-cell">
                          <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                            {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                          </Badge>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <div className="flex items-center gap-3">
                            <button
                              type="button"
                              onClick={() => setModalItem({
                                id: attendance.id,
                                approvalType: 'attendance',
                                userName: attendance.user?.name,
                                date: new Date(attendance.date).toLocaleDateString('id-ID'),
                              })}
                              className="text-sm font-medium text-primary hover:underline"
                            >
                              Detail
                            </button>
                            {attendance.supervisor_approval === 'pending' && (
                              <button
                                type="button"
                                onClick={() => quickApprove(attendance)}
                                className="text-sm font-medium text-green-600 hover:underline"
                              >
                                Setujui
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <Users className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada data absensi</h3>
                <p className="text-sm text-muted-foreground">Belum ada data absensi untuk tanggal yang dipilih.</p>
              </div>
            )}
          </CardContent>
          <div className="border-t border-border p-4">
            <Pagination links={attendances.links} />
          </div>
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
      />
    </AdminLayout>
  )
}
