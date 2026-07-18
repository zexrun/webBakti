import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Clock, FileText, ArrowLeft } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'
import ApprovalModal from './ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'destructive',
  absent: 'secondary',
}

export default function Approvals({ pendingAttendances, pendingExceptions }) {
  const [tab, setTab] = useState('attendance')
  const [modalItem, setModalItem] = useState(null)
  const [modalType, setModalType] = useState('attendance')

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function quickAction(id, approvalType, action) {
    const label = action === 'approve' ? 'menyetujui' : 'menolak'
    if (confirm(`Yakin ingin ${label} item ini?`)) {
      router.post(r('admin.attendance.approve', [approvalType, id]), { action }, { preserveScroll: true })
    }
  }

  function openModal(approvalType, item) {
    setModalType(approvalType)
    setModalItem({
      id: item.id,
      approvalType,
      userName: item.user?.name,
      date: new Date(item.date).toLocaleDateString('id-ID'),
    })
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="p-6">
            <h1 className="text-2xl font-bold text-foreground">Persetujuan Absensi</h1>
            <p className="text-muted-foreground">Kelola persetujuan absensi dan pengajuan izin mahasiswa.</p>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                <Clock className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Pending Absensi</p>
                <p className="text-2xl font-bold text-foreground">{pendingAttendances.total}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <FileText className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Pending Izin</p>
                <p className="text-2xl font-bold text-foreground">{pendingExceptions.total}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <div className="flex items-center justify-between border-b border-border px-6 py-4">
            <nav className="flex gap-8">
              <button
                type="button"
                onClick={() => setTab('attendance')}
                className={`border-b-2 py-2 text-sm font-medium ${tab === 'attendance' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'}`}
              >
                Absensi Pending ({pendingAttendances.total})
              </button>
              <button
                type="button"
                onClick={() => setTab('exceptions')}
                className={`border-b-2 py-2 text-sm font-medium ${tab === 'exceptions' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'}`}
              >
                Pengajuan Izin ({pendingExceptions.total})
              </button>
            </nav>
            <Link href={r('admin.attendance.index')}>
              <Button variant="secondary" size="sm">
                <ArrowLeft className="h-4 w-4" /> Kembali ke Monitoring
              </Button>
            </Link>
          </div>

          {tab === 'attendance' ? (
            <CardContent className="p-0">
              {pendingAttendances.data.length ? (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-muted">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Mahasiswa</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                        <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Check In/Out</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                        <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground sm:table-cell">Diajukan</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {pendingAttendances.data.map((attendance) => (
                        <tr key={attendance.id} className="hover:bg-accent">
                          <td className="whitespace-nowrap px-6 py-4">
                            <div className="flex items-center gap-3">
                              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                <span className="text-sm font-medium text-blue-600">{attendance.user?.name?.charAt(0)?.toUpperCase()}</span>
                              </div>
                              <div>
                                <p className="text-sm font-medium text-foreground">{attendance.user?.name}</p>
                                <p className="text-sm text-muted-foreground">{attendance.user?.email}</p>
                              </div>
                            </div>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <p className="text-sm font-medium text-foreground">
                              {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                            </p>
                            <p className="text-sm text-muted-foreground">
                              {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                            </p>
                          </td>
                          <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-foreground md:table-cell">
                            <div>In: {attendance.check_in ? new Date(attendance.check_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}</div>
                            <div>Out: {attendance.check_out ? new Date(attendance.check_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}</div>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                              {attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                            </Badge>
                          </td>
                          <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-muted-foreground sm:table-cell">
                            {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <div className="flex items-center gap-3">
                              <button type="button" onClick={() => openModal('attendance', attendance)} className="text-sm font-medium text-primary hover:underline">Detail</button>
                              <button type="button" onClick={() => quickAction(attendance.id, 'attendance', 'approve')} className="text-sm font-medium text-green-600 hover:underline">Setujui</button>
                              <button type="button" onClick={() => quickAction(attendance.id, 'attendance', 'reject')} className="text-sm font-medium text-destructive hover:underline">Tolak</button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="p-12 text-center">
                  <Clock className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                  <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada absensi pending</h3>
                  <p className="text-sm text-muted-foreground">Semua absensi sudah diproses atau belum ada pengajuan baru.</p>
                </div>
              )}
              <div className="border-t border-border p-4">
                <Pagination links={pendingAttendances.links} />
              </div>
            </CardContent>
          ) : (
            <CardContent className="p-0">
              {pendingExceptions.data.length ? (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-muted">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Mahasiswa</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                        <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Jenis</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Alasan</th>
                        <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground sm:table-cell">Diajukan</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {pendingExceptions.data.map((exception) => (
                        <tr key={exception.id} className="hover:bg-accent">
                          <td className="whitespace-nowrap px-6 py-4">
                            <div className="flex items-center gap-3">
                              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                <span className="text-sm font-medium text-blue-600">{exception.user?.name?.charAt(0)?.toUpperCase()}</span>
                              </div>
                              <div>
                                <p className="text-sm font-medium text-foreground">{exception.user?.name}</p>
                                <p className="text-sm text-muted-foreground">{exception.user?.email}</p>
                              </div>
                            </div>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <p className="text-sm font-medium text-foreground">
                              {new Date(exception.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                            </p>
                            <p className="text-sm text-muted-foreground">
                              {new Date(exception.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                            </p>
                          </td>
                          <td className="hidden whitespace-nowrap px-6 py-4 md:table-cell">
                            <Badge variant="default">{exception.type_name ?? 'Izin'}</Badge>
                          </td>
                          <td className="max-w-xs truncate px-6 py-4 text-sm text-foreground" title={exception.reason}>
                            {exception.reason}
                          </td>
                          <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-muted-foreground sm:table-cell">
                            {new Date(exception.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <div className="flex items-center gap-3">
                              <button type="button" onClick={() => openModal('exception', exception)} className="text-sm font-medium text-primary hover:underline">Detail</button>
                              <button type="button" onClick={() => quickAction(exception.id, 'exception', 'approve')} className="text-sm font-medium text-green-600 hover:underline">Setujui</button>
                              <button type="button" onClick={() => quickAction(exception.id, 'exception', 'reject')} className="text-sm font-medium text-destructive hover:underline">Tolak</button>
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
                  <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada pengajuan izin pending</h3>
                  <p className="text-sm text-muted-foreground">Semua pengajuan izin sudah diproses atau belum ada pengajuan baru.</p>
                </div>
              )}
              <div className="border-t border-border p-4">
                <Pagination links={pendingExceptions.links} />
              </div>
            </CardContent>
          )}
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
      />
    </AdminLayout>
  )
}
