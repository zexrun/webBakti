import { useState } from 'react'
import { router } from '@inertiajs/react'
import { CheckCircle2, AlertTriangle, XCircle, FileText, RotateCcw } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

const statusVariant = { present: 'success', late: 'destructive', absent: 'secondary' }
const statusLabel = { present: 'Hadir', late: 'Terlambat', absent: 'Tidak Hadir', pending: 'Pending' }
const approvalVariant = { pending: 'warning', approved: 'success', rejected: 'destructive' }
const exceptionStatusVariant = { approved: 'success', rejected: 'destructive', pending: 'warning' }

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

function DateCell({ date }) {
  return (
    <>
      <p className="tabular-nums text-foreground">
        {new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
      </p>
      <p className="text-xs text-muted-foreground">{new Date(date).toLocaleDateString('id-ID', { weekday: 'long' })}</p>
    </>
  )
}

export default function History({ attendances, exceptions, month, year }) {
  const [monthFilter, setMonthFilter] = useState(String(month))
  const [yearFilter, setYearFilter] = useState(String(year))
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleFilter(e) {
    e.preventDefault()
    router.get(r('student.attendance.history'), { month: monthFilter, year: yearFilter })
  }

  const presentCount = attendances.data.filter((a) => a.status === 'present').length
  const lateCount = attendances.data.filter((a) => a.status === 'late').length
  const absentCount = attendances.data.filter((a) => a.status === 'absent').length
  const currentYear = new Date().getFullYear()

  return (
    <StudentLayout>
      <div className="space-y-6">
        <PageHeader
          title="Riwayat Absensi"
          description={`Periode ${monthNames[month - 1]} ${year}`}
        />

        {attendances.data.length > 0 && (
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard icon={CheckCircle2} label="Total Hadir" value={presentCount} tone="green" index={0} />
            <StatCard icon={AlertTriangle} label="Terlambat" value={lateCount} tone="orange" index={1} />
            <StatCard icon={XCircle} label="Tidak Hadir" value={absentCount} tone="red" index={2} />
            <StatCard icon={FileText} label="Pengajuan Izin" value={exceptions.length} tone="blue" index={3} />
          </div>
        )}

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label htmlFor="month" className="block text-sm font-medium text-foreground">Bulan</label>
            <Select value={monthFilter} onValueChange={setMonthFilter}>
              <SelectTrigger id="month" className="w-40">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {monthNames.map((name, index) => (
                  <SelectItem key={name} value={String(index + 1)}>{name}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label htmlFor="year" className="block text-sm font-medium text-foreground">Tahun</label>
            <Select value={yearFilter} onValueChange={setYearFilter}>
              <SelectTrigger id="year" className="w-28">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {[0, 1, 2].map((offset) => (
                  <SelectItem key={offset} value={String(currentYear - offset)}>{currentYear - offset}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button type="button" variant="ghost" onClick={() => router.get(r('student.attendance.history'))}>
            <RotateCcw /> Reset
          </Button>
        </form>

        <Card>
          <div className="flex items-center justify-between border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Data Absensi</h3>
            <span className="text-sm tabular-nums text-muted-foreground">{attendances.total} total</span>
          </div>
          {attendances.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Tanggal</TableHead>
                    <TableHead>Check In</TableHead>
                    <TableHead className="hidden md:table-cell">Check Out</TableHead>
                    <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="hidden sm:table-cell">Approval</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {attendances.data.map((attendance) => (
                    <TableRow key={attendance.id}>
                      <TableCell><DateCell date={attendance.date} /></TableCell>
                      <TableCell>
                        <span className="font-medium tabular-nums text-foreground">{time(attendance.check_in)}</span>
                        {attendance.is_late && <p className="text-xs text-red-600 dark:text-red-400">Terlambat</p>}
                      </TableCell>
                      <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">{time(attendance.check_out)}</TableCell>
                      <TableCell className="hidden text-right tabular-nums text-muted-foreground lg:table-cell">
                        {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                      </TableCell>
                      <TableCell>
                        <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                          {statusLabel[attendance.status] ?? attendance.status}
                        </Badge>
                      </TableCell>
                      <TableCell className="hidden sm:table-cell">
                        <Badge variant={approvalVariant[attendance.supervisor_approval] ?? 'warning'}>
                          {attendance.supervisor_approval === 'approved' ? 'Disetujui' : attendance.supervisor_approval === 'rejected' ? 'Ditolak' : 'Pending'}
                        </Badge>
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
              icon={FileText}
              title="Tidak ada data absensi"
              description="Tidak ada data absensi untuk periode yang dipilih."
            />
          )}
        </Card>

        <Card>
          <div className="flex items-center justify-between border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Pengajuan Izin/Sakit</h3>
            <span className="text-sm tabular-nums text-muted-foreground">{exceptions.length} pengajuan</span>
          </div>
          {exceptions.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Jenis</TableHead>
                  <TableHead>Alasan</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="hidden sm:table-cell">Diajukan</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {exceptions.map((exception) => (
                  <TableRow key={exception.id}>
                    <TableCell><DateCell date={exception.date} /></TableCell>
                    <TableCell><Badge variant="secondary">{exception.type_name ?? 'Izin'}</Badge></TableCell>
                    <TableCell className="max-w-xs truncate whitespace-normal text-foreground" title={exception.reason}>
                      {exception.reason}
                    </TableCell>
                    <TableCell>
                      <Badge variant={exceptionStatusVariant[exception.status] ?? 'warning'}>
                        {statusLabel[exception.status] ?? exception.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="hidden tabular-nums text-muted-foreground sm:table-cell">
                      {new Date(exception.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState
              icon={FileText}
              title="Tidak ada pengajuan"
              description="Tidak ada pengajuan izin/sakit untuk periode yang dipilih."
            />
          )}
        </Card>
      </div>
    </StudentLayout>
  )
}
