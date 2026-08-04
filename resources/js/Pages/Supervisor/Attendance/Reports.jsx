import { useState } from 'react'
import { router } from '@inertiajs/react'
import { Download, FileSpreadsheet, RotateCcw } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import { Card } from '@/Components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

function average(items, key) {
  if (!items.length) return 0
  return Math.round((items.reduce((sum, item) => sum + item[key], 0) / items.length) * 10) / 10
}

function CountCell({ value, toneWhenPositive }) {
  return (
    <TableCell
      className={cn(
        'text-right tabular-nums',
        value > 0 ? toneWhenPositive : 'text-muted-foreground',
      )}
    >
      {value}
    </TableCell>
  )
}

export default function Reports({ summary, users, month, year, userId }) {
  const [monthFilter, setMonthFilter] = useState(String(month))
  const [yearFilter, setYearFilter] = useState(String(year))
  const [userIdFilter, setUserIdFilter] = useState(userId ? String(userId) : 'all')
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleFilter(e) {
    e.preventDefault()
    router.get(r('supervisor.attendance.reports'), {
      month: monthFilter,
      year: yearFilter,
      ...(userIdFilter !== 'all' ? { user_id: userIdFilter } : {}),
    })
  }

  function exportUrl(type) {
    const params = new URLSearchParams({ month, year, type })
    if (userId) params.set('user_id', userId)
    return `${r('supervisor.attendance.export-csv')}?${params.toString()}`
  }

  const currentYear = new Date().getFullYear()

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Laporan Presensi"
          description="Export dan analisis data kehadiran mahasiswa bimbingan Anda"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <a href={exportUrl('detail')}>
                  <Download /> Export Detail
                </a>
              </Button>
              <Button asChild variant="outline" size="sm">
                <a href={exportUrl('summary')}>
                  <FileSpreadsheet /> Export Ringkasan
                </a>
              </Button>
            </>
          }
        />

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Bulan</label>
            <Select value={monthFilter} onValueChange={setMonthFilter}>
              <SelectTrigger className="w-40">
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
            <label className="block text-sm font-medium text-foreground">Tahun</label>
            <Select value={yearFilter} onValueChange={setYearFilter}>
              <SelectTrigger className="w-28">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {[0, 1, 2].map((offset) => (
                  <SelectItem key={offset} value={String(currentYear - offset)}>{currentYear - offset}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Mahasiswa</label>
            <Select value={userIdFilter} onValueChange={setUserIdFilter}>
              <SelectTrigger className="w-56">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Mahasiswa Bimbingan</SelectItem>
                {users.map((user) => (
                  <SelectItem key={user.id} value={String(user.id)}>
                    {user.name} ({user.student?.nim ?? '-'})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button type="button" variant="ghost" onClick={() => router.get(r('supervisor.attendance.reports'))}>
            <RotateCcw /> Reset
          </Button>
        </form>

        {summary.length > 0 ? (
          <>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <StatCard label="Total Mahasiswa" value={summary.length} tone="neutral" index={0} />
              <StatCard label="Rata-rata Hadir" value={average(summary, 'present')} tone="green" index={1} />
              <StatCard label="Rata-rata Terlambat" value={average(summary, 'late')} tone="amber" index={2} />
              <StatCard label="Rata-rata Tidak Hadir" value={average(summary, 'absent')} tone="red" index={3} />
            </div>

            <Card>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Nama Mahasiswa</TableHead>
                    <TableHead>NIM</TableHead>
                    <TableHead className="text-right">Total Hari</TableHead>
                    <TableHead className="text-right">Hadir</TableHead>
                    <TableHead className="text-right">Terlambat</TableHead>
                    <TableHead className="text-right">Tidak Hadir</TableHead>
                    <TableHead className="text-right">Rata-rata Jam</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {summary.map((item) => (
                    <TableRow key={item.user.id}>
                      <TableCell className="font-medium text-foreground">{item.user.name}</TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">{item.user.student?.nim ?? '-'}</TableCell>
                      <TableCell className="text-right font-medium tabular-nums text-foreground">{item.total_days}</TableCell>
                      <CountCell value={item.present} toneWhenPositive="font-medium text-green-700 dark:text-green-400" />
                      <CountCell value={item.late} toneWhenPositive="font-medium text-amber-700 dark:text-amber-400" />
                      <CountCell value={item.absent} toneWhenPositive="font-medium text-red-700 dark:text-red-400" />
                      <TableCell className="text-right tabular-nums text-foreground">
                        {item.avg_hours ? Number(item.avg_hours).toFixed(1) : 0} jam
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </Card>
          </>
        ) : (
          <Card>
            <EmptyState
              icon={FileSpreadsheet}
              title="Tidak ada data kehadiran"
              description="Tidak ada data kehadiran bimbingan Anda untuk periode yang dipilih."
            />
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
