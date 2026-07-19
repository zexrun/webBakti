import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { ArrowLeft, AlertTriangle, CheckCircle2, MapPin } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import ApprovalModal from './ApprovalModal'

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

export default function Suspicious({ suspiciousAttendances }) {
  const [modalItem, setModalItem] = useState(null)
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Kehadiran Mencurigakan"
          description="Kehadiran yang memerlukan review manual karena anomali terdeteksi"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('admin.attendance.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        {suspiciousAttendances.data.length === 0 ? (
          <Card>
            <EmptyState
              icon={CheckCircle2}
              title="Tidak ada kehadiran mencurigakan"
              description="Semua data kehadiran terlihat normal."
            />
          </Card>
        ) : (
          <Card>
            <div className="flex items-center gap-2.5 border-b border-border px-4 py-3.5">
              <AlertTriangle className="h-4 w-4 text-amber-600 dark:text-amber-400" />
              <h3 className="text-base font-semibold text-foreground">Perlu Review</h3>
              <span className="text-sm tabular-nums text-muted-foreground">({suspiciousAttendances.total})</span>
            </div>
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check-in</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Alasan Anomali</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {suspiciousAttendances.data.map((attendance) => (
                  <TableRow key={attendance.id}>
                    <TableCell>
                      <UserCell name={attendance.user?.name} subtitle={attendance.user?.email} tone="red" />
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">
                      {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">
                      {attendance.check_in_time ? new Date(attendance.check_in_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                    </TableCell>
                    <TableCell>
                      <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                        {statusLabel[attendance.status] ?? attendance.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="whitespace-normal">
                      <div className="space-y-1">
                        {attendance.location_notes && (
                          <p className="flex items-start gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <MapPin className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            {attendance.location_notes}
                          </p>
                        )}
                        {attendance.latitude && attendance.longitude && (
                          <p className="text-xs tabular-nums text-muted-foreground">
                            Koordinat: {Number(attendance.latitude).toFixed(4)}, {Number(attendance.longitude).toFixed(4)}
                          </p>
                        )}
                      </div>
                    </TableCell>
                    <TableCell>
                      <Button
                        type="button"
                        size="xs"
                        variant="outline"
                        onClick={() => setModalItem({
                          id: attendance.id,
                          userName: attendance.user?.name,
                          date: new Date(attendance.date).toLocaleDateString('id-ID'),
                        })}
                      >
                        Review
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
            {suspiciousAttendances.links?.length > 3 && (
              <div className="border-t border-border px-4 py-3">
                <Pagination links={suspiciousAttendances.links} />
              </div>
            )}
          </Card>
        )}
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
      />
    </AdminLayout>
  )
}
