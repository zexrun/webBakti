import { useState } from 'react'
import { Link, router, useForm, usePage } from '@inertiajs/react'
import { Building2, Briefcase, GraduationCap, Search, Plus, Pencil, Trash2, ExternalLink, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Button } from '@/Components/ui/button'
import Pagination from '@/Components/Pagination'

const tabs = [
  { key: 'directorates', label: 'Direktorat', icon: Building2, color: 'blue' },
  { key: 'positions', label: 'Jabatan', icon: Briefcase, color: 'green' },
  { key: 'universities', label: 'Universitas', icon: GraduationCap, color: 'purple' },
]

const colorClasses = {
  blue: { border: 'border-blue-500', text: 'text-blue-600', bg: 'bg-blue-100', badgeText: 'text-blue-800', ring: 'focus:ring-blue-500 focus:border-blue-500', btn: 'bg-blue-600 hover:bg-blue-700' },
  green: { border: 'border-green-500', text: 'text-green-600', bg: 'bg-green-100', badgeText: 'text-green-800', ring: 'focus:ring-green-500 focus:border-green-500', btn: 'bg-green-600 hover:bg-green-700' },
  purple: { border: 'border-purple-500', text: 'text-purple-600', bg: 'bg-purple-100', badgeText: 'text-purple-800', ring: 'focus:ring-purple-500 focus:border-purple-500', btn: 'bg-purple-600 hover:bg-purple-700' },
}

function EntityModal({ open, onClose, title, color, fields, initial, onSubmit, submitLabel }) {
  const { data, setData, processing, errors, reset } = useForm(initial)

  if (!open) return null
  const classes = colorClasses[color]

  function handleSubmit(e) {
    e.preventDefault()
    onSubmit(data, () => {
      reset()
      onClose()
    })
  }

  return (
    <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-md rounded-lg bg-background p-6 shadow-xl">
          <div className="mb-4 flex items-center justify-between">
            <h3 className="text-lg font-medium text-foreground">{title}</h3>
            <button type="button" onClick={onClose} className="text-muted-foreground hover:text-foreground">
              <X className="h-6 w-6" />
            </button>
          </div>
          <form onSubmit={handleSubmit} className="space-y-4">
            {fields.map((field) => (
              <div key={field.name}>
                <Label htmlFor={field.name}>{field.label}</Label>
                <Input
                  id={field.name}
                  type={field.type ?? 'text'}
                  value={data[field.name] ?? ''}
                  onChange={(e) => setData(field.name, e.target.value)}
                  required={field.required}
                  className={`mt-2 ${classes.ring}`}
                />
                {errors[field.name] && <p className="mt-1 text-sm text-destructive">{errors[field.name]}</p>}
              </div>
            ))}
            <div className="flex justify-end gap-3 pt-2">
              <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
              <Button type="submit" disabled={processing} className={classes.btn}>{submitLabel}</Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}

export default function Index({ directorates, positions, universities, activeTab, searchDirectorate, searchPosition, searchUniversity }) {
  const { flash } = usePage().props
  const [modal, setModal] = useState(null) // { type: 'add'|'edit', entity: 'directorate'|'position'|'university', item }
  const [deleteTarget, setDeleteTarget] = useState(null) // { entity, id, name }

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function switchTab(tab) {
    router.get(r('admin.settings.index'), { tab })
  }

  function handleSearch(e, tab, param) {
    e.preventDefault()
    router.get(r('admin.settings.index'), { tab, [param]: e.target[param].value })
  }

  function submitCreate(entity, routeName, data, onDone) {
    router.post(r(routeName), data, { preserveScroll: true, onSuccess: onDone })
  }

  function submitUpdate(routeName, id, data, onDone) {
    router.put(r(routeName, id), data, { preserveScroll: true, onSuccess: onDone })
  }

  function confirmDelete() {
    const routes = {
      directorate: 'admin.settings.deleteDirectorate',
      position: 'admin.settings.deletePosition',
      university: 'admin.settings.deleteUniversity',
    }
    router.delete(r(routes[deleteTarget.entity], deleteTarget.id), {
      preserveScroll: true,
      onFinish: () => setDeleteTarget(null),
    })
  }

  const successMessage = flash?.success ?? flash?.success_position ?? flash?.success_university

  return (
    <AdminLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Konfigurasi Sistem</h1>
              <p className="text-muted-foreground">Kelola data direktorat, jabatan, dan universitas untuk sistem</p>
            </div>
            <div className="flex gap-2">
              <div className="rounded-lg bg-blue-50 px-3 py-2">
                <span className="text-sm font-medium text-blue-800">{directorates.total} Direktorat</span>
              </div>
              <div className="rounded-lg bg-green-50 px-3 py-2">
                <span className="text-sm font-medium text-green-800">{positions.total} Jabatan</span>
              </div>
              <div className="rounded-lg bg-purple-50 px-3 py-2">
                <span className="text-sm font-medium text-purple-800">{universities.total} Universitas</span>
              </div>
            </div>
          </CardContent>
        </Card>

        {successMessage && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{successMessage}</div>
        )}

        <Card className="overflow-hidden">
          <div className="border-b border-border">
            <nav className="-mb-px flex space-x-8 px-6">
              {tabs.map((tab) => {
                const Icon = tab.icon
                const isActive = activeTab === tab.key
                const classes = colorClasses[tab.color]
                const total = { directorates, positions, universities }[tab.key].total
                return (
                  <button
                    key={tab.key}
                    type="button"
                    onClick={() => switchTab(tab.key)}
                    className={`flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium ${
                      isActive ? `${classes.border} ${classes.text}` : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground'
                    }`}
                  >
                    <Icon className="h-5 w-5" />
                    {tab.label}
                    <Badge variant="secondary">{total}</Badge>
                  </button>
                )
              })}
            </nav>
          </div>

          <CardContent className="p-6">
            {activeTab === 'directorates' && (
              <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <h3 className="text-lg font-medium text-foreground">Kelola Direktorat</h3>
                    <p className="mt-1 text-sm text-muted-foreground">Tambah, edit, atau hapus direktorat</p>
                  </div>
                  <div className="flex gap-3">
                    <form onSubmit={(e) => handleSearch(e, 'directorates', 'search_directorate')} className="flex">
                      <div className="relative">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input name="search_directorate" defaultValue={searchDirectorate ?? ''} placeholder="Cari direktorat..." className="pl-9" />
                      </div>
                      <Button type="submit" variant="secondary" className="ml-2">Cari</Button>
                    </form>
                    <Button type="button" className="bg-blue-600 hover:bg-blue-700" onClick={() => setModal({ type: 'add', entity: 'directorate' })}>
                      <Plus className="h-4 w-4" /> Tambah Direktorat
                    </Button>
                  </div>
                </div>

                <div className="overflow-hidden rounded-lg ring-1 ring-border">
                  <table className="min-w-full divide-y divide-border">
                    <thead className="bg-muted">
                      <tr>
                        <th className="w-16 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">No</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Nama Direktorat</th>
                        <th className="w-32 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {directorates.data.length ? (
                        directorates.data.map((dir, index) => (
                          <tr key={dir.id} className="hover:bg-accent">
                            <td className="px-6 py-4 text-sm text-muted-foreground">{directorates.from + index}</td>
                            <td className="px-6 py-4 text-sm font-medium text-foreground">{dir.name}</td>
                            <td className="px-6 py-4 text-sm font-medium">
                              <div className="flex gap-3">
                                <button type="button" onClick={() => setModal({ type: 'edit', entity: 'directorate', item: dir })} className="text-blue-600 hover:text-blue-900">Edit</button>
                                <button type="button" onClick={() => setDeleteTarget({ entity: 'directorate', id: dir.id, name: dir.name })} className="text-destructive hover:opacity-80">Hapus</button>
                              </div>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan={3} className="px-6 py-12 text-center">
                            <Building2 className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <h3 className="mb-1 text-sm font-medium text-foreground">{searchDirectorate ? 'Tidak ada hasil' : 'Belum ada direktorat'}</h3>
                            <p className="text-sm text-muted-foreground">{searchDirectorate ? 'Coba kata kunci lain' : 'Tambahkan direktorat pertama'}</p>
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>

                <Pagination links={directorates.links} />
              </div>
            )}

            {activeTab === 'positions' && (
              <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <h3 className="text-lg font-medium text-foreground">Kelola Jabatan</h3>
                    <p className="mt-1 text-sm text-muted-foreground">Tambah, edit, atau hapus jabatan</p>
                  </div>
                  <div className="flex gap-3">
                    <form onSubmit={(e) => handleSearch(e, 'positions', 'search_position')} className="flex">
                      <div className="relative">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input name="search_position" defaultValue={searchPosition ?? ''} placeholder="Cari jabatan..." className="pl-9" />
                      </div>
                      <Button type="submit" variant="secondary" className="ml-2">Cari</Button>
                    </form>
                    <Button type="button" className="bg-green-600 hover:bg-green-700" onClick={() => setModal({ type: 'add', entity: 'position' })}>
                      <Plus className="h-4 w-4" /> Tambah Jabatan
                    </Button>
                  </div>
                </div>

                <div className="overflow-hidden rounded-lg ring-1 ring-border">
                  <table className="min-w-full divide-y divide-border">
                    <thead className="bg-muted">
                      <tr>
                        <th className="w-16 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">No</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Nama Jabatan</th>
                        <th className="w-32 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {positions.data.length ? (
                        positions.data.map((pos, index) => (
                          <tr key={pos.id} className="hover:bg-accent">
                            <td className="px-6 py-4 text-sm text-muted-foreground">{positions.from + index}</td>
                            <td className="px-6 py-4 text-sm font-medium text-foreground">{pos.name}</td>
                            <td className="px-6 py-4 text-sm font-medium">
                              <div className="flex gap-3">
                                <button type="button" onClick={() => setModal({ type: 'edit', entity: 'position', item: pos })} className="text-blue-600 hover:text-blue-900">Edit</button>
                                <button type="button" onClick={() => setDeleteTarget({ entity: 'position', id: pos.id, name: pos.name })} className="text-destructive hover:opacity-80">Hapus</button>
                              </div>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan={3} className="px-6 py-12 text-center">
                            <Briefcase className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <h3 className="mb-1 text-sm font-medium text-foreground">{searchPosition ? 'Tidak ada hasil' : 'Belum ada jabatan'}</h3>
                            <p className="text-sm text-muted-foreground">{searchPosition ? 'Coba kata kunci lain' : 'Tambahkan jabatan pertama'}</p>
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>

                <Pagination links={positions.links} />
              </div>
            )}

            {activeTab === 'universities' && (
              <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <h3 className="text-lg font-medium text-foreground">Kelola Universitas</h3>
                    <p className="mt-1 text-sm text-muted-foreground">Tambah, edit, atau hapus universitas</p>
                  </div>
                  <div className="flex gap-3">
                    <form onSubmit={(e) => handleSearch(e, 'universities', 'search_university')} className="flex">
                      <div className="relative">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input name="search_university" defaultValue={searchUniversity ?? ''} placeholder="Cari universitas..." className="pl-9" />
                      </div>
                      <Button type="submit" variant="secondary" className="ml-2">Cari</Button>
                    </form>
                    <Button type="button" className="bg-purple-600 hover:bg-purple-700" onClick={() => setModal({ type: 'add', entity: 'university' })}>
                      <Plus className="h-4 w-4" /> Tambah Universitas
                    </Button>
                  </div>
                </div>

                <div className="overflow-hidden rounded-lg ring-1 ring-border">
                  <table className="min-w-full divide-y divide-border">
                    <thead className="bg-muted">
                      <tr>
                        <th className="w-16 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">No</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Nama Universitas</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Domain</th>
                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Website</th>
                        <th className="w-32 px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {universities.data.length ? (
                        universities.data.map((uni, index) => (
                          <tr key={uni.id} className="hover:bg-accent">
                            <td className="px-6 py-4 text-sm text-muted-foreground">{universities.from + index}</td>
                            <td className="px-6 py-4 text-sm font-medium text-foreground">{uni.name}</td>
                            <td className="px-6 py-4 text-sm text-muted-foreground">{uni.domain ?? '-'}</td>
                            <td className="px-6 py-4 text-sm">
                              {uni.website ? (
                                <a href={uni.website} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
                                  {uni.website.length > 25 ? `${uni.website.slice(0, 25)}...` : uni.website}
                                  <ExternalLink className="h-3 w-3" />
                                </a>
                              ) : (
                                <span className="text-muted-foreground">-</span>
                              )}
                            </td>
                            <td className="px-6 py-4 text-sm font-medium">
                              <div className="flex gap-3">
                                <button type="button" onClick={() => setModal({ type: 'edit', entity: 'university', item: uni })} className="text-blue-600 hover:text-blue-900">Edit</button>
                                <button type="button" onClick={() => setDeleteTarget({ entity: 'university', id: uni.id, name: uni.name })} className="text-destructive hover:opacity-80">Hapus</button>
                              </div>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan={5} className="px-6 py-12 text-center">
                            <GraduationCap className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <h3 className="mb-1 text-sm font-medium text-foreground">{searchUniversity ? 'Tidak ada hasil' : 'Belum ada universitas'}</h3>
                            <p className="text-sm text-muted-foreground">{searchUniversity ? 'Coba kata kunci lain' : 'Tambahkan universitas pertama'}</p>
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>

                <Pagination links={universities.links} />
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      {modal?.entity === 'directorate' && (
        <EntityModal
          open
          onClose={() => setModal(null)}
          title={modal.type === 'add' ? 'Tambah Direktorat' : 'Edit Direktorat'}
          color="blue"
          fields={[{ name: 'name', label: 'Nama Direktorat', required: true }]}
          initial={{ name: modal.item?.name ?? '' }}
          submitLabel={modal.type === 'add' ? 'Simpan' : 'Update'}
          onSubmit={(data, onDone) =>
            modal.type === 'add'
              ? submitCreate('directorate', 'admin.settings.storeDirectorate', data, onDone)
              : submitUpdate('admin.settings.updateDirectorate', modal.item.id, data, onDone)
          }
        />
      )}

      {modal?.entity === 'position' && (
        <EntityModal
          open
          onClose={() => setModal(null)}
          title={modal.type === 'add' ? 'Tambah Jabatan' : 'Edit Jabatan'}
          color="green"
          fields={[{ name: 'name', label: 'Nama Jabatan', required: true }]}
          initial={{ name: modal.item?.name ?? '' }}
          submitLabel={modal.type === 'add' ? 'Simpan' : 'Update'}
          onSubmit={(data, onDone) =>
            modal.type === 'add'
              ? submitCreate('position', 'admin.settings.storePosition', data, onDone)
              : submitUpdate('admin.settings.updatePosition', modal.item.id, data, onDone)
          }
        />
      )}

      {modal?.entity === 'university' && (
        <EntityModal
          open
          onClose={() => setModal(null)}
          title={modal.type === 'add' ? 'Tambah Universitas' : 'Edit Universitas'}
          color="purple"
          fields={[
            { name: 'name', label: 'Nama Universitas', required: true },
            { name: 'domain', label: 'Domain' },
            { name: 'website', label: 'Website', type: 'url' },
          ]}
          initial={{ name: modal.item?.name ?? '', domain: modal.item?.domain ?? '', website: modal.item?.website ?? '' }}
          submitLabel={modal.type === 'add' ? 'Simpan' : 'Update'}
          onSubmit={(data, onDone) =>
            modal.type === 'add'
              ? submitCreate('university', 'admin.settings.storeUniversity', data, onDone)
              : submitUpdate('admin.settings.updateUniversity', modal.item.id, data, onDone)
          }
        />
      )}

      {deleteTarget && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onClick={(e) => e.target === e.currentTarget && setDeleteTarget(null)}>
          <div className="w-full max-w-md rounded-lg bg-background p-6 shadow-xl">
            <h3 className="text-lg font-semibold text-foreground">Hapus Data</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Yakin ingin menghapus <strong>{deleteTarget.name}</strong>? Tindakan ini tidak dapat dibatalkan.
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
