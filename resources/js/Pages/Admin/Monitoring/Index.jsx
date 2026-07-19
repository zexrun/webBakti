import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Activity, Users, GraduationCap, Search, Download, ChevronRight, UserCog, ClipboardList } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
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
        <PageHeader
          title="Monitoring Aktivitas"
          description="Pantau aktivitas supervisor dan mahasiswa bimbingan"
          actions={
            <Button asChild variant="outline" size="sm">
              <a href={r('admin.monitoring.export-csv') + (typeof window !== 'undefined' ? window.location.search : '')}>
                <Download /> Export CSV
              </a>
            </Button>
          }
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard icon={UserCog} label="Supervisor" value={stats.total_supervisors} tone="blue" />
          <StatCard icon={GraduationCap} label="Mahasiswa" value={stats.total_students} tone="green" />
          <StatCard icon={Users} label="Rata-rata/Supervisor" value={stats.avg_students_per_supervisor} tone="orange" />
          <StatCard icon={ClipboardList} label="Submission Pending" value={stats.active_submissions} tone="purple" />
        </div>

        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
          <div className="min-w-56 flex-1 space-y-2">
            <label className="block text-sm font-medium text-foreground">Cari</label>
            <div className="relative">
              <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input name="search" defaultValue={search ?? ''} placeholder="Nama supervisor/mahasiswa..." className="pl-9" />
            </div>
          </div>
          <div className="space-y-2">
            <label className="block text-sm font-medium text-foreground">Direktorat</label>
            <Select value={directoratFilter} onValueChange={setDirectoratFilter}>
              <SelectTrigger className="w-44">
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
              <SelectTrigger className="w-40">
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
              <SelectTrigger className="w-44">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="name">Nama</SelectItem>
                <SelectItem value="students">Jumlah Mahasiswa</SelectItem>
                <SelectItem value="created_at">Tanggal Dibuat</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <Button type="submit" variant="outline">Terapkan</Button>
          <Button asChild type="button" variant="ghost">
            <Link href={r('admin.monitoring.index')}>Reset</Link>
          </Button>
          <span className="pb-2.5 text-sm tabular-nums text-muted-foreground">{supervisors.total} hasil</span>
        </form>

        <div className="space-y-6">
          {supervisors.data.length ? (
            supervisors.data.map((supervisor) => (
              <Card key={supervisor.id}>
                <div className="flex flex-col gap-3 border-b border-border px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                  <UserCell
                    name={supervisor.user.name}
                    subtitle={[supervisor.direktorat ?? 'Belum ditentukan', supervisor.jabatan].filter(Boolean).join(' · ')}
                  />
                  <div className="flex items-center gap-2">
                    <Badge variant="secondary">{supervisor.students.length} Mahasiswa</Badge>
                    <Button asChild size="xs" variant="outline">
                      <Link href={r('admin.monitoring.supervisor.show', supervisor.id)}>
                        Lihat Detail <ChevronRight />
                      </Link>
                    </Button>
                  </div>
                </div>

                {supervisor.students.length ? (
                  <div className="divide-y divide-border">
                    {supervisor.students.map((student) => (
                      <div key={student.id} className="flex items-center justify-between gap-3 px-4 py-3 transition-colors duration-150 hover:bg-muted">
                        <UserCell
                          name={student.user.name}
                          subtitle={[student.nim, student.universitas].filter(Boolean).join(' · ')}
                          tone="green"
                        />
                        <Button asChild size="xs" variant="ghost">
                          <Link href={r('admin.monitoring.student.show', student.id)}>Detail</Link>
                        </Button>
                      </div>
                    ))}
                  </div>
                ) : (
                  <EmptyState
                    title="Belum ada mahasiswa bimbingan"
                    description="Supervisor ini belum memiliki mahasiswa yang dibimbing."
                    className="py-8"
                  />
                )}
              </Card>
            ))
          ) : (
            <Card>
              <EmptyState
                icon={Activity}
                title="Belum ada data monitoring"
                description="Belum ada supervisor yang terdaftar dalam sistem."
                action={
                  <Button asChild>
                    <Link href={r('admin.users.create')}>Tambah Supervisor</Link>
                  </Button>
                }
              />
            </Card>
          )}
        </div>

        {supervisors.links?.length > 3 && <Pagination links={supervisors.links} />}
      </div>
    </AdminLayout>
  )
}
