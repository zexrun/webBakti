import { Link } from '@inertiajs/react'
import { NotebookPen, CheckCircle2, Clock, CalendarDays, Eye } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import Pagination from '@/Components/Pagination'

function StatCard({ icon: Icon, label, value, accent }) {
  return (
    <Card>
      <CardContent className="flex items-center gap-4 p-5">
        <div className={`flex h-11 w-11 items-center justify-center rounded-lg ${accent}`}>
          <Icon className="h-5 w-5" />
        </div>
        <div>
          <p className="text-sm text-muted-foreground">{label}</p>
          <p className="text-2xl font-bold text-foreground">{value}</p>
        </div>
      </CardContent>
    </Card>
  )
}

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
        <div>
          <h1 className="text-2xl font-bold text-foreground">Logbook Mahasiswa</h1>
          <p className="text-muted-foreground">Pantau aktivitas harian mahasiswa bimbingan</p>
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={NotebookPen} label="Total Logbook" value={logbooks.total} accent="bg-blue-100 text-blue-700" />
          <StatCard icon={CheckCircle2} label="Telah Dilihat (halaman ini)" value={verifiedCount} accent="bg-green-100 text-green-700" />
          <StatCard icon={Clock} label="Belum Dilihat (halaman ini)" value={unverifiedCount} accent="bg-yellow-100 text-yellow-700" />
          <StatCard icon={CalendarDays} label="Hari Ini (halaman ini)" value={todayCount} accent="bg-purple-100 text-purple-700" />
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Daftar Logbook</CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            {logbooks.data.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Mahasiswa</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Kegiatan</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Waktu</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {logbooks.data.map((entry) => (
                      <tr key={entry.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <p className="text-sm text-foreground">
                            {new Date(entry.activity_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                          </p>
                          <p className="text-xs text-muted-foreground">
                            {new Date(entry.activity_date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100">
                              <span className="text-sm font-semibold text-blue-700">
                                {entry.student?.user?.name?.charAt(0)?.toUpperCase()}
                              </span>
                            </div>
                            <div>
                              <p className="text-sm font-medium text-foreground">{entry.student?.user?.name}</p>
                              <p className="text-xs text-muted-foreground">{entry.student?.nim ?? 'NIM belum diisi'}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <p className="text-sm font-medium text-foreground">{entry.title?.substring(0, 40)}</p>
                          <p className="text-xs text-muted-foreground">{entry.description?.substring(0, 60)}</p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4 text-sm text-foreground">
                          {entry.start_time.slice(0, 5)} - {entry.end_time.slice(0, 5)}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {entry.is_verified ? (
                            <Badge variant="success">Telah Dilihat</Badge>
                          ) : (
                            <Badge variant="warning">Belum Dilihat</Badge>
                          )}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">
                          <Link
                            href={r('supervisor.logbooks.show', entry.id)}
                            className="inline-flex items-center gap-1 rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground hover:bg-primary/90"
                          >
                            <Eye className="h-3 w-3" /> Lihat
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
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum ada logbook</h3>
                <p className="text-muted-foreground">Belum ada logbook dari mahasiswa bimbingan Anda.</p>
              </div>
            )}
          </CardContent>
        </Card>

        <Pagination links={logbooks.links} />
      </div>
    </SupervisorLayout>
  )
}
