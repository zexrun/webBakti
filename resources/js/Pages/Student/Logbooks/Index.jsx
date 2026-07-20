import { Link } from '@inertiajs/react'
import { NotebookPen, Plus, CheckCircle2, Clock } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default function Index({ logbook }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <StudentLayout>
      <div className="space-y-6">
        <PageHeader
          title="Logbook Harian"
          description="Kelola laporan kegiatan magang Anda"
          actions={
            <>
              <Badge variant="secondary">Total: {logbook.total}</Badge>
              <Button asChild>
                <Link href={r('student.logbooks.create')}>
                  <Plus /> Buat Laporan
                </Link>
              </Button>
            </>
          }
        />

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Daftar Laporan Harian</h3>
          </div>
          {logbook.data.length ? (
            <>
              <Table>
                <TableHeader>
                  <TableRow className="hover:bg-transparent">
                    <TableHead>Tanggal</TableHead>
                    <TableHead>Judul Kegiatan</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Aksi</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {logbook.data.map((entry) => (
                    <TableRow key={entry.id}>
                      <TableCell>
                        <p className="tabular-nums text-foreground">
                          {new Date(entry.activity_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                        </p>
                        <p className="text-xs text-muted-foreground">
                          {new Date(entry.activity_date).toLocaleDateString('id-ID', { weekday: 'long' })}
                        </p>
                      </TableCell>
                      <TableCell className="whitespace-normal">
                        <p className="text-sm font-medium text-foreground">{entry.title}</p>
                        <p className="max-w-xs truncate text-xs text-muted-foreground">{entry.description}</p>
                      </TableCell>
                      <TableCell>
                        {entry.is_verified ? (
                          <Badge variant="success"><CheckCircle2 /> Sudah Dilihat</Badge>
                        ) : (
                          <Badge variant="warning"><Clock /> Menunggu</Badge>
                        )}
                      </TableCell>
                      <TableCell>
                        <Button asChild size="xs" variant="outline">
                          <Link href={r('student.logbooks.show', entry.id)}>Lihat Detail</Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
              {logbook.links?.length > 3 && (
                <div className="border-t border-border px-4 py-3">
                  <Pagination links={logbook.links} />
                </div>
              )}
            </>
          ) : (
            <EmptyState
              icon={NotebookPen}
              title="Belum ada laporan"
              description="Mulai buat laporan harian pertama Anda."
              action={
                <Button asChild>
                  <Link href={r('student.logbooks.create')}>
                    <Plus /> Buat Laporan Sekarang
                  </Link>
                </Button>
              }
            />
          )}
        </Card>
      </div>
    </StudentLayout>
  )
}
