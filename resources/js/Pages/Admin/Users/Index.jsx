import { useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { Plus, Search, Send, Pencil, Trash2, Users as UsersIcon } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

function UserSection({ title, dotColor, users, onDelete }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <Card className="gap-0 overflow-hidden py-0">
      <div className="flex items-center gap-2.5 border-b border-border px-4 py-3.5">
        <span className={cn('h-2 w-2 rounded-full', dotColor)} />
        <h3 className="text-base font-semibold text-foreground">{title}</h3>
        <span className="text-sm tabular-nums text-muted-foreground">({users.total})</span>
      </div>

      {users.data.length ? (
        <>
          <Table>
            <TableHeader>
              <TableRow className="hover:bg-transparent">
                <TableHead className="w-12 text-right">No</TableHead>
                <TableHead>Nama</TableHead>
                <TableHead>Username</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Direktorat</TableHead>
                <TableHead>Jabatan</TableHead>
                <TableHead className="w-44">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {users.data.map((user, index) => (
                <TableRow key={user.id}>
                  <TableCell className="text-right tabular-nums text-muted-foreground">{index + users.from}</TableCell>
                  <TableCell className="font-medium text-foreground">{user.name}</TableCell>
                  <TableCell className="text-muted-foreground">{user.username}</TableCell>
                  <TableCell className="text-muted-foreground">{user.email}</TableCell>
                  <TableCell className="text-muted-foreground">{user.supervisor?.directorate ?? user.student?.directorate ?? '-'}</TableCell>
                  <TableCell className="text-muted-foreground">{user.supervisor?.position ?? '-'}</TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1.5">
                      {user.email_verified_at === null ? (
                        <Button
                          type="button"
                          size="xs"
                          variant="outline"
                          onClick={() => router.post(r('admin.users.resend-activation', user.id), {}, { preserveScroll: true })}
                        >
                          <Send className="h-3.5 w-3.5" /> Kirim Ulang
                        </Button>
                      ) : (
                        <Button asChild size="xs" variant="outline">
                          <Link href={r('admin.users.edit', user.id)}>
                            <Pencil className="h-3.5 w-3.5" /> Edit
                          </Link>
                        </Button>
                      )}
                      <Button
                        type="button"
                        size="xs"
                        variant="ghost"
                        className="text-destructive hover:text-destructive"
                        onClick={() => onDelete(user)}
                      >
                        <Trash2 className="h-3.5 w-3.5" /> Hapus
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
          {users.links?.length > 3 && (
            <div className="border-t border-border px-4 py-3">
              <Pagination links={users.links} />
            </div>
          )}
        </>
      ) : (
        <EmptyState
          icon={UsersIcon}
          title="Tidak ada data pengguna"
          description="Belum ada pengguna yang terdaftar dalam kategori ini."
        />
      )}
    </Card>
  )
}

export default function Index({ admins, supervisors, students, search }) {
  const { flash } = usePage().props
  const [deleteTarget, setDeleteTarget] = useState(null)
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleSearch(e) {
    e.preventDefault()
    router.get(r('admin.users.index'), { search: e.target.search.value })
  }

  function confirmDelete() {
    router.delete(r('admin.users.destroy', deleteTarget.id), {
      onFinish: () => setDeleteTarget(null),
    })
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Manajemen Pengguna"
          description="Kelola data mahasiswa dan pembimbing"
          actions={
            <Button asChild>
              <Link href={r('admin.users.create')}>
                <Plus className="h-4 w-4" /> Tambah User
              </Link>
            </Button>
          }
        />

        {flash?.danger && <FlashBanner type="danger">{flash.danger}</FlashBanner>}
        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}
        {flash?.error && <FlashBanner type="danger">{flash.error}</FlashBanner>}

        <form onSubmit={handleSearch} className="flex flex-col gap-3 sm:flex-row">
          <div className="relative flex-1">
            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input name="search" defaultValue={search ?? ''} placeholder="Cari nama pengguna..." className="pl-9" />
          </div>
          <Button type="submit" variant="outline">
            Cari
          </Button>
        </form>

        <UserSection title="Administrator" dotColor="bg-blue-600 dark:bg-blue-400" users={admins} onDelete={setDeleteTarget} />
        <UserSection title="Pembimbing" dotColor="bg-green-600 dark:bg-green-400" users={supervisors} onDelete={setDeleteTarget} />
        <UserSection title="Mahasiswa" dotColor="bg-purple-600 dark:bg-purple-400" users={students} onDelete={setDeleteTarget} />
      </div>

      {deleteTarget && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
          onClick={(e) => e.target === e.currentTarget && setDeleteTarget(null)}
        >
          <div className="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
            <h3 className="text-lg font-semibold text-foreground">Hapus Pengguna</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Yakin ingin menghapus pengguna <strong className="text-foreground">{deleteTarget.name}</strong>?
            </p>
            <p className="mt-2 text-sm text-destructive">
              Semua data terkait pengguna ini akan ikut terhapus dan tidak dapat dikembalikan.
            </p>
            <div className="mt-6 flex justify-end gap-2">
              <Button type="button" variant="outline" onClick={() => setDeleteTarget(null)}>Batal</Button>
              <Button type="button" variant="destructive" onClick={confirmDelete}>Hapus</Button>
            </div>
          </div>
        </div>
      )}
    </AdminLayout>
  )
}
