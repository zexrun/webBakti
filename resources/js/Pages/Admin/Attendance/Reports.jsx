import { router } from '@inertiajs/react'
import { Download, FileSpreadsheet, RotateCcw } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

function average(items, key) {
  if (!items.length) return 0
  return Math.round((items.reduce((sum, item) => sum + item[key], 0) / items.length) * 10) / 10
}

export default function Reports({ summary, users, month, year, userId }) {
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('admin.attendance.reports'), {
      month: form.month.value,
      year: form.year.value,
      user_id: form.user_id.value,
    })
  }

  function exportUrl(type) {
    const params = new URLSearchParams({ month, year, type })
    if (userId) params.set('user_id', userId)
    return `${r('admin.attendance.export-csv')}?${params.toString()}`
  }

  const currentYear = new Date().getFullYear()

  return (
    <AdminLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Laporan Kehadiran</h1>
          <p className="text-muted-foreground">Export dan analisis data kehadiran mahasiswa</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Filter & Export</h2>

            <form onSubmit={handleFilter} className="space-y-4">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Bulan</label>
                  <Select name="month" defaultValue={month}>
                    {monthNames.map((name, index) => (
                      <option key={name} value={index + 1}>{name}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Tahun</label>
                  <Select name="year" defaultValue={year}>
                    {[0, 1, 2].map((offset) => (
                      <option key={offset} value={currentYear - offset}>{currentYear - offset}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Mahasiswa</label>
                  <Select name="user_id" defaultValue={userId ?? ''}>
                    <option value="">Semua Mahasiswa</option>
                    {users.map((user) => (
                      <option key={user.id} value={user.id}>
                        {user.name} ({user.student?.nim ?? '-'})
                      </option>
                    ))}
                  </Select>
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit">Terapkan Filter</Button>
                <Button type="button" variant="secondary" onClick={() => router.get(r('admin.attendance.reports'))}>
                  <RotateCcw className="h-4 w-4" /> Reset
                </Button>
              </div>
            </form>

            <div className="mt-6 border-t border-border pt-6">
              <h3 className="mb-3 text-sm font-semibold text-foreground">Export Data</h3>
              <div className="flex flex-wrap gap-3">
                <a href={exportUrl('detail')}>
                  <Button className="bg-green-600 hover:bg-green-700">
                    <Download className="h-4 w-4" /> Export Detail CSV
                  </Button>
                </a>
                <a href={exportUrl('summary')}>
                  <Button className="bg-green-600 hover:bg-green-700">
                    <FileSpreadsheet className="h-4 w-4" /> Export Ringkasan CSV
                  </Button>
                </a>
              </div>
            </div>
          </CardContent>
        </Card>

        {summary.length > 0 ? (
          <>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
              <Card>
                <CardContent className="p-4">
                  <p className="text-sm text-muted-foreground">Total Mahasiswa</p>
                  <p className="text-2xl font-bold text-foreground">{summary.length}</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4">
                  <p className="text-sm text-muted-foreground">Rata-rata Hadir</p>
                  <p className="text-2xl font-bold text-green-600">{average(summary, 'present')}</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4">
                  <p className="text-sm text-muted-foreground">Rata-rata Terlambat</p>
                  <p className="text-2xl font-bold text-orange-600">{average(summary, 'late')}</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4">
                  <p className="text-sm text-muted-foreground">Rata-rata Tidak Hadir</p>
                  <p className="text-2xl font-bold text-red-600">{average(summary, 'absent')}</p>
                </CardContent>
              </Card>
            </div>

            <Card>
              <CardContent className="p-0">
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-muted">
                      <tr>
                        <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Nama Mahasiswa</th>
                        <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">NIM</th>
                        <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Pembimbing</th>
                        <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Total Hari</th>
                        <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Hadir</th>
                        <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Terlambat</th>
                        <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Tidak Hadir</th>
                        <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Rata-rata Jam</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {summary.map((item) => (
                        <tr key={item.user.id} className="hover:bg-accent">
                          <td className="px-6 py-4 text-sm font-medium text-foreground">{item.user.name}</td>
                          <td className="px-6 py-4 text-sm text-muted-foreground">{item.user.student?.nim ?? '-'}</td>
                          <td className="px-6 py-4 text-sm text-muted-foreground">{item.user.student?.supervisor?.user?.name ?? '-'}</td>
                          <td className="px-6 py-4 text-center text-sm font-medium text-foreground">{item.total_days}</td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant="success">{item.present}</Badge>
                          </td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant="warning">{item.late}</Badge>
                          </td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant="destructive">{item.absent}</Badge>
                          </td>
                          <td className="px-6 py-4 text-center text-sm font-medium text-foreground">
                            {item.avg_hours ? Number(item.avg_hours).toFixed(1) : 0} jam
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>
          </>
        ) : (
          <Card>
            <CardContent className="p-12 text-center">
              <FileSpreadsheet className="mx-auto mb-4 h-16 w-16 text-muted-foreground" />
              <p className="text-muted-foreground">Tidak ada data kehadiran untuk periode yang dipilih</p>
            </CardContent>
          </Card>
        )}
      </div>
    </AdminLayout>
  )
}
