import { Link } from '@inertiajs/react'
import { NotebookPen, Plus, Eye, CheckCircle2, Clock } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

export default function Index({ logbook }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <StudentLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Logbook Harian</h1>
            <p className="text-muted-foreground">Kelola laporan kegiatan magang Anda 📋</p>
          </div>
          <div className="flex items-center gap-3">
            <Badge variant="secondary" className="text-sm">
              Total: {logbook.total}
            </Badge>
            <Link href={r('student.logbooks.create')}>
              <Button>
                <Plus className="h-4 w-4" /> Buat Laporan
              </Button>
            </Link>
          </div>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-base">
              <NotebookPen className="h-5 w-5 text-blue-600" /> Daftar Laporan Harian
            </CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            {logbook.data.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Judul Kegiatan</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {logbook.data.map((entry) => (
                      <tr key={entry.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <p className="text-sm font-medium text-foreground">
                            {new Date(entry.activity_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                          </p>
                          <p className="text-xs text-muted-foreground">
                            {new Date(entry.activity_date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </td>
                        <td className="px-6 py-4">
                          <p className="text-sm font-medium text-foreground">{entry.title}</p>
                          <p className="text-xs text-muted-foreground">{entry.description?.substring(0, 50)}</p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {entry.is_verified ? (
                            <Badge variant="success" className="gap-1">
                              <CheckCircle2 className="h-3 w-3" /> Sudah Dilihat
                            </Badge>
                          ) : (
                            <Badge variant="warning" className="gap-1">
                              <Clock className="h-3 w-3" /> Menunggu
                            </Badge>
                          )}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">
                          <Link
                            href={r('student.logbooks.show', entry.id)}
                            className="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200"
                          >
                            <Eye className="h-3 w-3" /> Lihat Detail
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <NotebookPen className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum ada laporan</h3>
                <p className="mb-4 text-muted-foreground">Mulai buat laporan harian pertama Anda</p>
                <Link href={r('student.logbooks.create')}>
                  <Button>
                    <Plus className="h-4 w-4" /> Buat Laporan Sekarang
                  </Button>
                </Link>
              </div>
            )}
          </CardContent>
        </Card>

        <Pagination links={logbook.links} />
      </div>
    </StudentLayout>
  )
}
