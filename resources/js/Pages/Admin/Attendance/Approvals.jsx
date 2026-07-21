import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Clock, FileText, ArrowLeft } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
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
import ApprovalModal from '@/Components/ApprovalModal'

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

function shortDate(value) {
  return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

function RowActions({ onDetail, onApprove, onReject }) {
  return (
    <div className="flex items-center gap-1.5">
      <Button type="button" size="xs" variant="outline" onClick={onDetail}>Detail</Button>
      <Button
        type="button"
        size="xs"
        variant="ghost"
        className="text-green-700 hover:text-green-700 dark:text-green-400 dark:hover:text-green-400"
        onClick={onApprove}
      >
        Setujui
      </Button>
      <Button
        type="button"
        size="xs"
        variant="ghost"
        className="text-destructive hover:text-destructive"
        onClick={onReject}
      >
        Tolak
      </Button>
    </div>
  )
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

  const tabs = [
    { id: 'attendance', label: `Absensi Pending (${pendingAttendances.total})` },
    { id: 'exceptions', label: `Pengajuan Izin (${pendingExceptions.total})` },
  ]

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Persetujuan Absensi"
          description="Kelola persetujuan absensi dan pengajuan izin mahasiswa"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('admin.attendance.index')}>
                <ArrowLeft /> Kembali ke Monitoring
              </Link>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <StatCard icon={Clock} label="Pending Absensi" value={pendingAttendances.total} tone="amber" />
          <StatCard icon={FileText} label="Pending Izin" value={pendingExceptions.total} tone="blue" />
        </div>

        <Card>
          <nav className="flex gap-6 border-b border-border px-4">
            {tabs.map((t) => (
              <button
                key={t.id}
                type="button"
                onClick={() => setTab(t.id)}
                className={cn(
                  'border-b-2 py-3 text-sm font-medium transition-colors duration-150',
                  tab === t.id
                    ? 'border-primary text-primary'
                    : 'border-transparent text-muted-foreground hover:text-foreground',
                )}
              >
                {t.label}
              </button>
            ))}
          </nav>

          {tab === 'attendance' ? (
            pendingAttendances.data.length ? (
              <>
                <Table>
                  <TableHeader>
                    <TableRow className="hover:bg-transparent">
                      <TableHead>Mahasiswa</TableHead>
                      <TableHead>Tanggal</TableHead>
                      <TableHead className="hidden md:table-cell">Check In/Out</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                      <TableHead>Aksi</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {pendingAttendances.data.map((attendance) => (
                      <TableRow key={attendance.id}>
                        <TableCell>
                          <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} />
                        </TableCell>
                        <TableCell>
                          <p className="font-medium tabular-nums text-foreground">{shortDate(attendance.date)}</p>
                          <p className="text-xs text-muted-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </TableCell>
                        <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                          <div>In: {time(attendance.check_in)}</div>
                          <div>Out: {time(attendance.check_out)}</div>
                        </TableCell>
                        <TableCell>
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {statusLabel[attendance.status] ?? attendance.status}
                          </Badge>
                        </TableCell>
                        <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                          {new Date(attendance.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </TableCell>
                        <TableCell>
                          <RowActions
                            onDetail={() => openModal('attendance', attendance)}
                            onApprove={() => quickAction(attendance.id, 'attendance', 'approve')}
                            onReject={() => quickAction(attendance.id, 'attendance', 'reject')}
                          />
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
                {pendingAttendances.links?.length > 3 && (
                  <div className="border-t border-border px-4 py-3">
                    <Pagination links={pendingAttendances.links} />
                  </div>
                )}
              </>
            ) : (
              <EmptyState
                icon={Clock}
                title="Tidak ada absensi pending"
                description="Semua absensi sudah diproses atau belum ada pengajuan baru."
              />
            )
          ) : pendingExceptions.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Tanggal</TableHead>
                    <TableHead className="hidden md:table-cell">Jenis</TableHead>
                    <TableHead>Alasan</TableHead>
                    <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {pendingExceptions.data.map((exception) => (
                    <TableRow key={exception.id}>
                      <TableCell>
                        <UserCell name={exception.user?.name} subtitle={exception.user?.email} />
                      </TableCell>
                      <TableCell>
                        <p className="font-medium tabular-nums text-foreground">{shortDate(exception.date)}</p>
                        <p className="text-xs text-muted-foreground">
                          {new Date(exception.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                        </p>
                      </TableCell>
                      <TableCell className="hidden md:table-cell">
                        <Badge variant="secondary">{exception.type_name ?? 'Izin'}</Badge>
                      </TableCell>
                      <TableCell className="max-w-xs truncate whitespace-normal text-foreground" title={exception.reason}>
                        {exception.reason}
                      </TableCell>
                      <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                        {new Date(exception.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </TableCell>
                      <TableCell>
                        <RowActions
                          onDetail={() => openModal('exception', exception)}
                          onApprove={() => quickAction(exception.id, 'exception', 'approve')}
                          onReject={() => quickAction(exception.id, 'exception', 'reject')}
                        />
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {pendingExceptions.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={pendingExceptions.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={FileText}
              title="Tidak ada pengajuan izin pending"
              description="Semua pengajuan izin sudah diproses atau belum ada pengajuan baru."
            />
          )}
        </Card>
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type={modalType}
        item={modalItem}
        routePrefix="admin"
      />
    </AdminLayout>
  )
}
