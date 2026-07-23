import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { CheckCircle2, Clock, XCircle, Users, ClipboardCheck, BarChart3, Settings as SettingsIcon, AlertTriangle } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import ApprovalModal from '@/Components/ApprovalModal'
import AttendancePhotoThumb from '@/Components/AttendancePhotoThumb'
import PhotoLightbox from '@/Components/PhotoLightbox'
import { useConfirm } from '@/hooks/useConfirm'

const statusVariant = {
  present: 'success',
  late: 'warning',
  absent: 'destructive',
}

const statusLabel = {
  present: 'Hadir',
  late: 'Terlambat',
  absent: 'Tidak Hadir',
}

const approvalVariant = {
  pending: 'warning',
  approved: 'success',
  rejected: 'destructive',
}

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

export default function Index({ attendances, stats, date, status }) {
  const [modalItem, setModalItem] = useState(null)
  const [lightbox, setLightbox] = useState(null)
  const [statusFilter, setStatusFilter] = useState(status ?? 'all')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const confirm = useConfirm()

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('admin.attendance.index'), {
      date: form.date.value,
      ...(statusFilter !== 'all' ? { status: statusFilter } : {}),
    })
  }

  async function quickApprove(attendance) {
    const confirmed = await confirm({ title: 'Setujui absensi ini?' })
    if (confirmed) {
      router.post(r('admin.attendance.approve', ['attendance', attendance.id]), { action: 'approve' }, { preserveScroll: true })
    }
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Monitoring Absensi"
          description="Pantau absensi mahasiswa secara real-time dan kelola persetujuan"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <Link href={r('admin.attendance.approvals')}>
                  <ClipboardCheck /> Persetujuan
                </Link>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link href={r('admin.attendance.reports')}>
                  <BarChart3 /> Laporan
                </Link>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link href={r('admin.attendance.settings')}>
                  <SettingsIcon /> Pengaturan
                </Link>
              </Button>
            </>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <StatCard icon={Users} label="Total Hadir" value={stats.total} tone="green" />
          <StatCard icon={CheckCircle2} label="Tepat Waktu" value={stats.present} tone="blue" />
          <StatCard icon={Clock} label="Terlambat" value={stats.late} tone="orange" />
          <StatCard icon={XCircle} label="Tidak Hadir" value={stats.absent} tone="red" />
          <StatCard icon={AlertTriangle} label="Mencurigakan" value={stats.suspicious} tone="amber" />
        </div>

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label htmlFor="date" className="block text-sm font-medium text-foreground">Tanggal</label>
            <Input id="date" name="date" type="date" defaultValue={date} className="w-44" />
          </div>
          <div className="space-y-2">
            <label htmlFor="status" className="block text-sm font-medium text-foreground">Status</label>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger id="status" className="w-44">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Status</SelectItem>
                <SelectItem value="present">Hadir</SelectItem>
                <SelectItem value="late">Terlambat</SelectItem>
                <SelectItem value="absent">Tidak Hadir</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
        </form>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">
              Data Absensi — {new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}
            </h3>
          </div>
          {attendances.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                    <TableHead className="hidden lg:table-cell">Foto</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {attendances.data.map((attendance) => (
                    <TableRow key={attendance.id}>
                      <TableCell>
                        <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} />
                      </TableCell>
                      <TableCell>
                        <span className="font-medium tabular-nums text-foreground">{time(attendance.check_in)}</span>
                        {attendance.is_late && <p className="text-xs text-amber-600 dark:text-amber-400">Terlambat</p>}
                      </TableCell>
                      <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                        {time(attendance.check_out)}
                      </TableCell>
                      <TableCell className="hidden text-right tabular-nums text-muted-foreground lg:table-cell">
                        {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                      </TableCell>
                      <TableCell>
                        <div className="flex flex-wrap items-center gap-1">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {statusLabel[attendance.status] ?? attendance.status}
                          </Badge>
                          {attendance.requires_manual_review && (
                            <Badge variant="warning">Mencurigakan</Badge>
                          )}
                        </div>
                      </TableCell>
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
                      </TableCell>
                      <TableCell className="hidden lg:table-cell">
                        <div className="flex items-center gap-1.5">
                          <AttendancePhotoThumb
                            url={attendance.check_in_photo_url}
                            label="Check-in"
                            onClick={() => setLightbox({ url: attendance.check_in_photo_url, caption: `Check-in — ${attendance.user?.name}` })}
                          />
                          <AttendancePhotoThumb
                            url={attendance.check_out_photo_url}
                            label="Check-out"
                            onClick={() => setLightbox({ url: attendance.check_out_photo_url, caption: `Check-out — ${attendance.user?.name}` })}
                          />
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
                          <Button
                            type="button"
                            size="xs"
                            variant="outline"
                            onClick={() => setModalItem({
                              id: attendance.id,
                              approvalType: 'attendance',
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                              checkInPhotoUrl: attendance.check_in_photo_url,
                              checkOutPhotoUrl: attendance.check_out_photo_url,
                            })}
                          >
                            Detail
                          </Button>
                          {attendance.supervisor_approval === 'pending' && (
                            <Button
                              type="button"
                              size="xs"
                              variant="ghost"
                              className="text-green-700 hover:text-green-700 dark:text-green-400 dark:hover:text-green-400"
                              onClick={() => quickApprove(attendance)}
                            >
                              Setujui
                            </Button>
                          )}
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {attendances.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={attendances.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={Users}
              title="Tidak ada data absensi"
              description="Belum ada data absensi untuk tanggal yang dipilih."
            />
          )}
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="attendance"
        item={modalItem}
        routePrefix="admin"
      />

      <PhotoLightbox
        open={Boolean(lightbox)}
        onClose={() => setLightbox(null)}
        url={lightbox?.url}
        caption={lightbox?.caption}
      />
    </AdminLayout>
  )
}
