import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon, RotateCcw } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { ResultCount } from './SearchFilters'

function FilterSelect({ label, value, onChange, children }) {
  return (
    <div className="space-y-2">
      <label className="block text-sm font-medium text-foreground">{label}</label>
      <Select value={value} onValueChange={onChange}>
        <SelectTrigger className="w-full">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>{children}</SelectContent>
      </Select>
    </div>
  )
}

export default function AdvancedAdmin({ students, supervisors, directorates, universities, filters }) {
  const [direktoratFilter, setDirektoratFilter] = useState(filters.direktorat ?? 'all')
  const [supervisorIdFilter, setSupervisorIdFilter] = useState(filters.supervisor_id ? String(filters.supervisor_id) : 'all')
  const [universitasFilter, setUniversitasFilter] = useState(filters.universitas ?? 'all')
  const [sortByFilter, setSortByFilter] = useState(filters.sort_by ?? 'created_at')
  const [sortDirFilter, setSortDirFilter] = useState(filters.sort_dir ?? 'desc')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('search.advanced'), {
      search: form.search.value,
      ...(direktoratFilter !== 'all' ? { direktorat: direktoratFilter } : {}),
      ...(supervisorIdFilter !== 'all' ? { supervisor_id: supervisorIdFilter } : {}),
      ...(universitasFilter !== 'all' ? { universitas: universitasFilter } : {}),
      sort_by: sortByFilter,
      sort_dir: sortDirFilter,
    })
  }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <PageHeader title="Pencarian Lanjutan — Mahasiswa" description="Filter dan cari mahasiswa dengan kriteria spesifik" />

        <Card>
          <CardContent className="p-5">
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Nama / NIM</label>
                  <Input name="search" defaultValue={filters.search ?? ''} placeholder="Cari..." />
                </div>
                <FilterSelect label="Direktorat" value={direktoratFilter} onChange={setDirektoratFilter}>
                  <SelectItem value="all">Semua</SelectItem>
                  {directorates.map((dir) => (
                    <SelectItem key={dir.id} value={dir.name}>{dir.name}</SelectItem>
                  ))}
                </FilterSelect>
                <FilterSelect label="Pembimbing" value={supervisorIdFilter} onChange={setSupervisorIdFilter}>
                  <SelectItem value="all">Semua</SelectItem>
                  {supervisors.map((sup) => (
                    <SelectItem key={sup.id} value={String(sup.id)}>{sup.user.name}</SelectItem>
                  ))}
                </FilterSelect>
                <FilterSelect label="Universitas" value={universitasFilter} onChange={setUniversitasFilter}>
                  <SelectItem value="all">Semua</SelectItem>
                  {universities.map((uni) => (
                    <SelectItem key={uni.id} value={uni.name}>{uni.name}</SelectItem>
                  ))}
                </FilterSelect>
              </div>

              <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FilterSelect label="Urutkan berdasarkan" value={sortByFilter} onChange={setSortByFilter}>
                  <SelectItem value="created_at">Terbaru</SelectItem>
                  <SelectItem value="name">Nama</SelectItem>
                  <SelectItem value="nim">NIM</SelectItem>
                </FilterSelect>
                <FilterSelect label="Arah Urutan" value={sortDirFilter} onChange={setSortDirFilter}>
                  <SelectItem value="desc">Menurun</SelectItem>
                  <SelectItem value="asc">Menaik</SelectItem>
                </FilterSelect>
              </div>

              <div className="flex gap-2">
                <Button type="submit">
                  <SearchIcon /> Cari
                </Button>
                <Button asChild type="button" variant="outline">
                  <Link href={r('search.advanced')}>
                    <RotateCcw /> Reset
                  </Link>
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <ResultCount shown={students.data.length} total={students.total} />

        <Card>
          {students.data.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama</TableHead>
                  <TableHead>NIM</TableHead>
                  <TableHead>Universitas</TableHead>
                  <TableHead>Pembimbing</TableHead>
                  <TableHead>Direktorat</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {students.data.map((student) => (
                  <TableRow key={student.id}>
                    <TableCell className="font-medium text-foreground">{student.user.name}</TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">{student.nim}</TableCell>
                    <TableCell className="text-muted-foreground">{student.universitas ?? '-'}</TableCell>
                    <TableCell className="text-muted-foreground">{student.supervisor?.user?.name ?? '-'}</TableCell>
                    <TableCell className="text-muted-foreground">{student.direktorat ?? '-'}</TableCell>
                    <TableCell>
                      <Button asChild size="xs" variant="outline">
                        <Link href={r('admin.monitoring.student.show', student.id)}>Lihat</Link>
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState icon={SearchIcon} title="Tidak ada hasil" description="Coba ubah kriteria pencarian Anda." />
          )}
        </Card>

        {students.links?.length > 3 && <Pagination links={students.links} />}
      </div>
    </RoleLayout>
  )
}
