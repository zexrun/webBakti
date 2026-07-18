import { Link, router } from '@inertiajs/react'
import { Search as SearchIcon, RotateCcw } from 'lucide-react'
import RoleLayout from '@/Layouts/RoleLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

export default function AdvancedAdmin({ students, supervisors, directorates, universities, filters }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    const form = e.target
    router.get(r('search.advanced'), {
      search: form.search.value,
      direktorat: form.direktorat.value,
      supervisor_id: form.supervisor_id.value,
      universitas: form.universitas.value,
      sort_by: form.sort_by.value,
      sort_dir: form.sort_dir.value,
    })
  }

  return (
    <RoleLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Pencarian Lanjutan - Mahasiswa</h1>
          <p className="text-muted-foreground">Filter dan cari mahasiswa dengan kriteria spesifik</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Nama / NIM</label>
                  <Input name="search" defaultValue={filters.search ?? ''} placeholder="Cari..." />
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Direktorat</label>
                  <Select name="direktorat" defaultValue={filters.direktorat ?? ''}>
                    <option value="">Semua</option>
                    {directorates.map((dir) => (
                      <option key={dir.id} value={dir.name}>{dir.name}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Pembimbing</label>
                  <Select name="supervisor_id" defaultValue={filters.supervisor_id ?? ''}>
                    <option value="">Semua</option>
                    {supervisors.map((sup) => (
                      <option key={sup.id} value={sup.id}>{sup.user.name}</option>
                    ))}
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Universitas</label>
                  <Select name="universitas" defaultValue={filters.universitas ?? ''}>
                    <option value="">Semua</option>
                    {universities.map((uni) => (
                      <option key={uni.id} value={uni.name}>{uni.name}</option>
                    ))}
                  </Select>
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Sort By</label>
                  <Select name="sort_by" defaultValue={filters.sort_by ?? 'created_at'}>
                    <option value="created_at">Terbaru</option>
                    <option value="name">Nama</option>
                    <option value="nim">NIM</option>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-foreground">Urutan</label>
                  <Select name="sort_dir" defaultValue={filters.sort_dir ?? 'desc'}>
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                  </Select>
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit">
                  <SearchIcon className="h-4 w-4" /> Cari
                </Button>
                <Link href={r('search.advanced')}>
                  <Button type="button" variant="secondary">
                    <RotateCcw className="h-4 w-4" /> Reset
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>

        <p className="text-sm text-muted-foreground">
          Menampilkan <strong className="text-foreground">{students.data.length}</strong> dari <strong className="text-foreground">{students.total}</strong> hasil
        </p>

        <Card>
          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="border-b border-border bg-muted">
                  <tr>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Nama</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">NIM</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Universitas</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Pembimbing</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Direktorat</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {students.data.length ? (
                    students.data.map((student) => (
                      <tr key={student.id} className="hover:bg-accent">
                        <td className="px-6 py-4 text-sm font-medium text-foreground">{student.user.name}</td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">{student.nim}</td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">{student.universitas ?? '-'}</td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">{student.supervisor?.user?.name ?? '-'}</td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">{student.direktorat ?? '-'}</td>
                        <td className="px-6 py-4 text-center text-sm">
                          <Link href={r('admin.monitoring.student.show', student.id)} className="font-medium text-primary hover:underline">
                            Lihat
                          </Link>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={6} className="px-6 py-8 text-center text-muted-foreground">Tidak ada hasil</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <Pagination links={students.links} />
      </div>
    </RoleLayout>
  )
}
