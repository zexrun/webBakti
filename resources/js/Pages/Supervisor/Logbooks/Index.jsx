import { Link } from '@inertiajs/react'
import { NotebookPen, CheckCircle2, Clock, CalendarDays } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default function Index({ logbooks }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const verifiedCount = logbooks.data.filter((l) => l.is_verified).length
  const unverifiedCount = logbooks.data.filter((l) => !l.is_verified).length
  const todayCount = logbooks.data.filter(
    (l) => new Date(l.activity_date).toDateString() === new Date().toDateString(),
  ).length

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader title="Logbook Mahasiswa" description="Pantau aktivitas harian mahasiswa bimbingan" />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={NotebookPen} label="Total Logbook" value={logbooks.total} tone="blue" />
          <StatCard icon={CheckCircle2} label="Telah Dilihat" value={verifiedCount} hint="halaman ini" tone="green" />
          <StatCard icon={Clock} label="Belum Dilihat" value={unverifiedCount} hint="halaman ini" tone="amber" />
          <StatCard icon={CalendarDays} label="Hari Ini" value={todayCount} hint="halaman ini" tone="purple" />
        </div>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Daftar Logbook</h3>
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
                        <UserCell name={entry.student?.user?.name} subtitle={entry.student?.nim ?? 'NIM belum diisi'} />
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
