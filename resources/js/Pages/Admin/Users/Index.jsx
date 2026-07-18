import { useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { Plus, Search, Send, Pencil, Trash2, Users as UsersIcon } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

function UserSection({ title, dotColor, badgeVariant, users, onDelete }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <Card className="overflow-hidden">
      <div className="flex items-center gap-3 border-b border-border bg-muted px-6 py-4">
        <div className={`h-2 w-2 rounded-full ${dotColor}`} />
        <h3 className="text-lg font-semibold text-foreground">{title}</h3>
        <Badge variant={badgeVariant}>{users.total}</Badge>
      </div>
      <CardContent className="p-0">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-muted text-xs uppercase text-muted-foreground">
              <tr>
                <th className="px-6 py-3 text-left">No</th>
                <th className="px-6 py-3 text-left">Nama</th>
                <th className="px-6 py-3 text-left">Username</th>
                <th className="px-6 py-3 text-left">Email</th>
                <th className="px-6 py-3 text-left">Direktorat</th>
                <th className="px-6 py-3 text-left">Jabatan</th>
                <th className="w-48 px-6 py-3 text-left">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {users.data.length ? (
                users.data.map((user, index) => (
                  <tr key={user.id} className="hover:bg-accent">
                    <td className="px-6 py-4">{index + users.from}</td>
                    <td className="px-6 py-4 font-medium text-foreground">{user.name}</td>
                    <td className="px-6 py-4 text-muted-foreground">{user.username}</td>
                    <td className="px-6 py-4 text-muted-foreground">{user.email}</td>
                    <td className="px-6 py-4 text-muted-foreground">{user.supervisor?.direktorat ?? user.student?.direktorat ?? '-'}</td>
                    <td className="px-6 py-4 text-muted-foreground">{user.supervisor?.jabatan ?? '-'}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        {user.email_verified_at === null ? (
                          <button
                            type="button"
                            onClick={() => router.post(r('admin.users.resend_activation', user.id), {}, { preserveScroll: true })}
                            className="inline-flex items-center gap-1 rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                          >
                            <Send className="h-3 w-3" /> Kirim Ulang
                          </button>
                        ) : (
                          <Link
                            href={r('admin.users.edit', user.id)}
                            className="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                          >
                            <Pencil className="h-3 w-3" /> Edit
                          </Link>
                        )}
                        <button
                          type="button"
                          onClick={() => onDelete(user)}
                          className="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                        >
                          <Trash2 className="h-3 w-3" /> Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center">
                    <UsersIcon className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                    <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada data pengguna</h3>
                    <p className="text-sm text-muted-foreground">Belum ada pengguna yang terdaftar dalam sistem.</p>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
        {users.data.length > 0 && (
          <div className="border-t border-border bg-muted px-6 py-4">
            <Pagination links={users.links} />
          </div>
        )}
      </CardContent>
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
        <Card>
          <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Manajemen Pengguna</h1>
              <p className="text-muted-foreground">Kelola data mahasiswa dan pembimbing</p>
            </div>
            <Link href={r('admin.users.create')}>
              <Button>
                <Plus className="h-4 w-4" /> Tambah User
              </Button>
            </Link>
          </CardContent>
        </Card>

        {flash?.danger && (
          <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">{flash.danger}</div>
        )}
        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}
        {flash?.error && (
          <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">{flash.error}</div>
        )}

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSearch} className="flex flex-col gap-4 sm:flex-row">
              <div className="relative flex-1">
                <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <Input name="search" defaultValue={search ?? ''} placeholder="Cari nama pengguna..." className="pl-10" />
              </div>
              <Button type="submit">
                <Search className="h-4 w-4" /> Cari
              </Button>
            </form>
          </CardContent>
        </Card>

        <UserSection title="Administrator" dotColor="bg-blue-600" badgeVariant="default" users={admins} onDelete={setDeleteTarget} />
        <UserSection title="Pembimbing" dotColor="bg-green-600" badgeVariant="success" users={supervisors} onDelete={setDeleteTarget} />
        <UserSection title="Mahasiswa" dotColor="bg-purple-600" badgeVariant="secondary" users={students} onDelete={setDeleteTarget} />
      </div>

      {deleteTarget && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onClick={(e) => e.target === e.currentTarget && setDeleteTarget(null)}>
          <div className="w-full max-w-md rounded-lg bg-background p-6 shadow-xl">
            <h3 className="text-lg font-semibold text-foreground">Hapus Pengguna</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Yakin ingin menghapus pengguna <strong>{deleteTarget.name}</strong>?
              <br />
              <span className="text-destructive">Semua data terkait pengguna ini akan ikut terhapus dan tidak dapat dikembalikan.</span>
            </p>
            <div className="mt-6 flex justify-end gap-3">
              <Button type="button" variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
              <Button type="button" variant="destructive" onClick={confirmDelete}>Hapus</Button>
            </div>
          </div>
        </div>
      )}
    </AdminLayout>
  )
}
