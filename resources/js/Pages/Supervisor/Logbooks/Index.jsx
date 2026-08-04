import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { NotebookPen, CheckCircle2, Clock, CalendarDays, Printer } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
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

export default function Index({ logbooks, students, filters }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const [studentFilter, setStudentFilter] = useState(filters?.student_id ? String(filters.student_id) : 'all')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('supervisor.logbooks.index'), {
      ...(studentFilter !== 'all' ? { student_id: studentFilter } : {}),
      ...(form.date_from.value ? { date_from: form.date_from.value } : {}),
      ...(form.date_to.value ? { date_to: form.date_to.value } : {}),
    })
  }

  function exportRecapUrl() {
    const params = new URLSearchParams()
    if (studentFilter !== 'all') params.set('student_id', studentFilter)
    if (filters?.date_from) params.set('date_from', filters.date_from)
    if (filters?.date_to) params.set('date_to', filters.date_to)
    const query = params.toString()
    return r('supervisor.logbooks.export-recap-pdf') + (query ? `?${query}` : '')
  }

  const verifiedCount = logbooks.data.filter((l) => l.is_verified).length
  const unverifiedCount = logbooks.data.filter((l) => !l.is_verified).length
  const todayCount = logbooks.data.filter(
    (l) => new Date(l.activity_date).toDateString() === new Date().toDateString(),
  ).length

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader title="Laporan Kegiatan Harian Mahasiswa" description="Pantau aktivitas harian mahasiswa bimbingan" />

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="space-y-2">
            <label htmlFor="student_id" className="block text-sm font-medium text-foreground">Mahasiswa</label>
            <Select value={studentFilter} onValueChange={setStudentFilter}>
              <SelectTrigger id="student_id" className="w-52">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Mahasiswa</SelectItem>
                {students.map((student) => (
                  <SelectItem key={student.id} value={String(student.id)}>
                    {student.user?.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <label htmlFor="date_from" className="block text-sm font-medium text-foreground">Dari Tanggal</label>
            <Input id="date_from" name="date_from" type="date" defaultValue={filters?.date_from ?? ''} className="w-44" />
          </div>
          <div className="space-y-2">
            <label htmlFor="date_to" className="block text-sm font-medium text-foreground">Sampai Tanggal</label>
            <Input id="date_to" name="date_to" type="date" defaultValue={filters?.date_to ?? ''} className="w-44" />
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button asChild variant="outline">
            <a href={exportRecapUrl()} target="_blank" rel="noreferrer">
              <Printer /> Cetak Hasil Filter
            </a>
          </Button>
        </form>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={NotebookPen} label="Total Laporan" value={logbooks.total} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Telah Dilihat" value={verifiedCount} hint="halaman ini" tone="green" index={1} />
          <StatCard icon={Clock} label="Belum Dilihat" value={unverifiedCount} hint="halaman ini" tone="amber" index={2} />
          <StatCard icon={CalendarDays} label="Hari Ini" value={todayCount} hint="halaman ini" tone="purple" index={3} />
        </div>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Daftar Laporan Kegiatan Harian</h3>
          </div>
          {logbooks.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Tanggal</TableHead>
                    <TableHead>Mahasiswa</TableHead>
                    <TableHead>Kegiatan</TableHead>
                    <TableHead>Waktu</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {logbooks.data.map((entry) => (
                    <TableRow key={entry.id}>
                      <TableCell>
                        <p className="tabular-nums text-foreground">
                          {new Date(entry.activity_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                        </p>
                        <p className="text-xs text-muted-foreground">
                          {new Date(entry.activity_date).toLocaleDateString('id-ID', { weekday: 'long' })}
                        </p>
                      </TableCell>
                      <TableCell>
                        <UserCell name={entry.student?.user?.name} photoUrl={entry.student?.user?.profile_photo_url} subtitle={entry.student?.nim ?? 'NIM belum diisi'} />
                      </TableCell>
                      <TableCell className="whitespace-normal">
                        <p className="text-sm font-medium text-foreground">{entry.title}</p>
                        <p className="max-w-xs truncate text-xs text-muted-foreground">{entry.description}</p>
                      </TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">
                        {entry.start_time.slice(0, 5)}–{entry.end_time.slice(0, 5)}
                      </TableCell>
                      <TableCell>
                        <Badge variant={entry.is_verified ? 'success' : 'warning'}>
                          {entry.is_verified ? 'Telah Dilihat' : 'Belum Dilihat'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <Button asChild size="xs" variant="outline">
                          <Link href={r('supervisor.logbooks.show', entry.id)}>Lihat</Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {logbooks.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={logbooks.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={NotebookPen}
              title="Belum ada logbook"
              description="Belum ada logbook dari mahasiswa bimbingan Anda."
            />
          )}
        </Card>
      </div>
    </SupervisorLayout>
  )
}
