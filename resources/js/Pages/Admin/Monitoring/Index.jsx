import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Activity, Users, GraduationCap, Search, Download, ChevronRight } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

export default function Index({ supervisors, directorates, positions, search, directorat, position, sortBy, sortOrder, stats }) {
  const [directoratFilter, setDirectoratFilter] = useState(directorat ?? 'all')
  const [positionFilter, setPositionFilter] = useState(position ?? 'all')
  const [sortByFilter, setSortByFilter] = useState(sortBy ?? 'name')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFilter(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('admin.monitoring.index'), {
      search: form.search.value,
      ...(directoratFilter !== 'all' ? { directorat: directoratFilter } : {}),
      ...(positionFilter !== 'all' ? { position: positionFilter } : {}),
      sort_by: sortByFilter,
    })
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="flex flex-col gap-6 p-6 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center gap-4">
              <div className="rounded-full bg-green-100 p-3">
                <Activity className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-foreground">Monitoring Aktivitas</h1>
                <p className="text-muted-foreground">Pantau aktivitas supervisor dan mahasiswa bimbingan</p>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-6 sm:grid-cols-4">
              <div className="text-center">
                <p className="text-2xl font-bold text-blue-600">{stats.total_supervisors}</p>
                <p className="text-sm text-muted-foreground">Supervisor</p>
              </div>
              <div className="text-center">
                <p className="text-2xl font-bold text-green-600">{stats.total_students}</p>
                <p className="text-sm text-muted-foreground">Mahasiswa</p>
              </div>
              <div className="text-center">
                <p className="text-2xl font-bold text-orange-600">{stats.avg_students_per_supervisor}</p>
                <p className="text-sm text-muted-foreground">Rata-rata/Supervisor</p>
              </div>
              <div className="text-center">
                <p className="text-2xl font-bold text-purple-600">{stats.active_submissions}</p>
                <p className="text-sm text-muted-foreground">Submission Pending</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleFilter} className="space-y-4">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Cari</label>
                  <div className="relative">
                    <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input name="search" defaultValue={search ?? ''} placeholder="Nama supervisor/mahasiswa..." className="pl-9" />
                  </div>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Direktorat</label>
                  <Select value={directoratFilter} onValueChange={setDirectoratFilter}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua Direktorat</SelectItem>
                      {directorates.map((dir) => (
                        <SelectItem key={dir} value={dir}>{dir}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Jabatan</label>
                  <Select value={positionFilter} onValueChange={setPositionFilter}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua Jabatan</SelectItem>
                      {positions.map((pos) => (
                        <SelectItem key={pos} value={pos}>{pos}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Urutkan</label>
                  <Select value={sortByFilter} onValueChange={setSortByFilter}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="name">Nama</SelectItem>
                      <SelectItem value="students">Jumlah Mahasiswa</SelectItem>
                      <SelectItem value="created_at">Tanggal Dibuat</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div className="flex gap-2">
                  <Button type="submit">Terapkan Filter</Button>
                  <Link href={r('admin.monitoring.index')}>
                    <Button type="button" variant="secondary">Reset</Button>
                  </Link>
                </div>
                <div className="flex items-center gap-4">
                  <span className="text-sm text-muted-foreground">{supervisors.total} hasil ditemukan</span>
                  <a
                    href={r('admin.monitoring.export-csv') + (window.location.search || '')}
                    className="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                  >
                    <Download className="h-4 w-4" /> Export CSV
                  </a>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>

        <div className="space-y-6">
          {supervisors.data.length ? (
            supervisors.data.map((supervisor) => (
              <Card key={supervisor.id} className="overflow-hidden">
                <div className="border-b border-border bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                      <div className="rounded-full bg-blue-100 p-3">
                        <Users className="h-6 w-6 text-blue-600" />
                      </div>
                      <div>
                        <h3 className="text-xl font-bold text-gray-900">{supervisor.user.name}</h3>
                        <div className="mt-1 flex items-center gap-4 text-sm text-gray-600">
                          <span>{supervisor.direktorat ?? 'Belum ditentukan'}</span>
                          {supervisor.jabatan && <span>{supervisor.jabatan}</span>}
                        </div>
                      </div>
                    </div>
                    <div className="flex items-center gap-3">
                      <Badge variant="secondary">{supervisor.students.length} Mahasiswa</Badge>
                      <Link
                        href={r('admin.monitoring.supervisor.show', supervisor.id)}
                        className="inline-flex items-center gap-1 rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100"
                      >
                        Lihat Detail <ChevronRight className="h-4 w-4" />
                      </Link>
                    </div>
                  </div>
                </div>

                <div className="divide-y divide-border">
                  {supervisor.students.length ? (
                    supervisor.students.map((student) => (
                      <div key={student.id} className="flex items-center justify-between p-4 hover:bg-accent">
                        <div className="flex items-center gap-3">
                          <div className="rounded-full bg-green-100 p-2">
                            <GraduationCap className="h-4 w-4 text-green-600" />
                          </div>
                          <div>
                            <h4 className="font-medium text-foreground">{student.user.name}</h4>
                            <div className="mt-1 flex items-center gap-3 text-sm text-muted-foreground">
                              {student.nim && <span>{student.nim}</span>}
                              {student.universitas && <span>{student.universitas}</span>}
                            </div>
                          </div>
                        </div>
                        <div className="flex items-center gap-2">
                          <Badge variant="success">Aktif</Badge>
                          <Link
                            href={r('admin.monitoring.student.show', student.id)}
                            className="inline-flex items-center gap-1 rounded-md border border-blue-300 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100"
                          >
                            Lihat Detail
                          </Link>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="p-8 text-center">
                      <p className="text-sm text-muted-foreground">Belum ada mahasiswa bimbingan</p>
                      <p className="mt-1 text-xs text-muted-foreground">Supervisor ini belum memiliki mahasiswa yang dibimbing</p>
                    </div>
                  )}
                </div>
              </Card>
            ))
          ) : (
            <Card>
              <CardContent className="p-12 text-center">
                <Activity className="mx-auto mb-4 h-16 w-16 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum Ada Data Monitoring</h3>
                <p className="mb-4 text-muted-foreground">Belum ada supervisor yang terdaftar dalam sistem</p>
                <Link href={r('admin.users.create')} className="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                  Tambah Supervisor
                </Link>
              </CardContent>
            </Card>
          )}
        </div>

        <Pagination links={supervisors.links} />
      </div>
    </AdminLayout>
  )
}
