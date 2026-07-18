import { useState } from 'react'
import { router } from '@inertiajs/react'
import { CheckCircle2, AlertTriangle, XCircle, FileText, Filter, RotateCcw, Eye, Calendar } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

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

const exceptionStatusVariant = {
  approved: 'success',
  rejected: 'destructive',
  pending: 'warning',
}

export default function History({ attendances, exceptions, month, year }) {
  const [monthFilter, setMonthFilter] = useState(String(month))
  const [yearFilter, setYearFilter] = useState(String(year))
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleFilter(e) {
    e.preventDefault()
    router.get(r('student.attendance.history'), {
      month: monthFilter,
      year: yearFilter,
    })
  }

  function handleReset() {
    router.get(r('student.attendance.history'))
  }

  function viewDetail(id) {
    alert(`Detail absensi ID: ${id}\n\nFitur ini akan segera tersedia.`)
  }

  const presentCount = attendances.data.filter((a) => a.status === 'present').length
  const lateCount = attendances.data.filter((a) => a.status === 'late').length
  const absentCount = attendances.data.filter((a) => a.status === 'absent').length

  const currentYear = new Date().getFullYear()

  return (
    <StudentLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="flex items-center justify-between p-6">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Riwayat Absensi</h1>
              <p className="text-muted-foreground">Lihat riwayat absensi dan pengajuan Anda</p>
            </div>
            <div className="hidden items-center gap-4 md:flex">
              <div className="text-right">
                <p className="text-sm text-muted-foreground">Periode</p>
                <p className="text-lg font-semibold text-foreground">{monthNames[month - 1]} {year}</p>
              </div>
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <Calendar className="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        {attendances.data.length > 0 && (
          <div className="grid grid-cols-1 gap-6 md:grid-cols-4">
            <Card>
              <CardContent className="flex items-center gap-4 p-6">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                  <CheckCircle2 className="h-6 w-6 text-green-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Total Hadir</p>
                  <p className="text-2xl font-bold text-foreground">{presentCount}</p>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="flex items-center gap-4 p-6">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                  <AlertTriangle className="h-6 w-6 text-orange-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Terlambat</p>
                  <p className="text-2xl font-bold text-foreground">{lateCount}</p>
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
                  <p className="text-2xl font-bold text-foreground">{absentCount}</p>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="flex items-center gap-4 p-6">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                  <FileText className="h-6 w-6 text-blue-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Pengajuan Izin</p>
                  <p className="text-2xl font-bold text-foreground">{exceptions.length}</p>
                </div>
              </CardContent>
            </Card>
          </div>
        )}

        <Card>
          <CardContent className="p-6">
            <div className="mb-4 flex items-center justify-between">
              <h3 className="text-lg font-semibold text-foreground">Filter Data</h3>
              <Filter className="h-5 w-5 text-muted-foreground" />
            </div>
            <form onSubmit={handleFilter} className="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
              <div className="space-y-2">
                <label htmlFor="month" className="block text-sm font-medium text-foreground">Bulan</label>
                <Select value={monthFilter} onValueChange={setMonthFilter}>
                  <SelectTrigger id="month" className="w-full">
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
                  <SelectTrigger id="year" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {[0, 1, 2].map((offset) => (
                      <SelectItem key={offset} value={String(currentYear - offset)}>{currentYear - offset}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="flex gap-3">
                <Button type="submit" className="flex-1">
                  <Filter className="h-4 w-4" /> Filter
                </Button>
                <Button type="button" variant="secondary" onClick={handleReset}>
                  <RotateCcw className="h-4 w-4" /> Reset
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <div className="flex items-center justify-between border-b border-border p-6">
            <h3 className="text-lg font-semibold text-foreground">Data Absensi</h3>
            <span className="text-sm text-muted-foreground">{attendances.total} total data</span>
          </div>
          <CardContent className="p-0">
            {attendances.data.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
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
                          <p className="text-sm font-medium text-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                          </p>
                          <p className="text-sm text-muted-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {attendance.check_in ? (
                            <>
                              <p className="text-sm font-medium text-foreground">
                                {new Date(attendance.check_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                              </p>
                              {attendance.is_late && <p className="mt-1 text-xs text-destructive">Terlambat</p>}
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
                          <button
                            type="button"
                            onClick={() => viewDetail(attendance.id)}
                            className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline"
                          >
                            <Eye className="h-4 w-4" /> Detail
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <FileText className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada data absensi</h3>
                <p className="text-sm text-muted-foreground">Tidak ada data absensi untuk periode yang dipilih.</p>
              </div>
            )}
          </CardContent>
          {attendances.data.length > 0 && (
            <div className="border-t border-border p-4">
              <Pagination links={attendances.links} />
            </div>
          )}
        </Card>

        <Card>
          <div className="flex items-center justify-between border-b border-border p-6">
            <h3 className="text-lg font-semibold text-foreground">Pengajuan Izin/Sakit</h3>
            <span className="text-sm text-muted-foreground">{exceptions.length} pengajuan</span>
          </div>
          <CardContent className="p-0">
            {exceptions.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Jenis</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Alasan</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground sm:table-cell">Diajukan</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {exceptions.map((exception) => (
                      <tr key={exception.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <p className="text-sm font-medium text-foreground">
                            {new Date(exception.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                          </p>
                          <p className="text-sm text-muted-foreground">
                            {new Date(exception.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <Badge variant="default">{exception.type_name ?? 'Izin'}</Badge>
                        </td>
                        <td className="max-w-xs truncate px-6 py-4 text-sm text-foreground" title={exception.reason}>
                          {exception.reason}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <Badge variant={exceptionStatusVariant[exception.status] ?? 'warning'}>
                            {exception.status.charAt(0).toUpperCase() + exception.status.slice(1)}
                          </Badge>
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-muted-foreground sm:table-cell">
                          {new Date(exception.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <FileText className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada pengajuan</h3>
                <p className="text-sm text-muted-foreground">Tidak ada pengajuan izin/sakit untuk periode yang dipilih.</p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
