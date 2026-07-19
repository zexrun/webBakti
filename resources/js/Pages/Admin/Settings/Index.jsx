import { useState } from 'react'
import { router, useForm, usePage } from '@inertiajs/react'
import { Building2, Briefcase, GraduationCap, Search, Plus, ExternalLink, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import Pagination from '@/Components/Pagination'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

// Semantic entity colors (§7): blue=direktorat, green=jabatan, purple=universitas
const tabDefs = [
  {
    key: 'directorates',
    entity: 'directorate',
    label: 'Direktorat',
    singular: 'Direktorat',
    icon: Building2,
    activeClass: 'border-blue-600 text-blue-700 dark:border-blue-400 dark:text-blue-300',
    searchParam: 'search_directorate',
    storeRoute: 'admin.settings.storeDirectorate',
    updateRoute: 'admin.settings.updateDirectorate',
    fields: [{ name: 'name', label: 'Nama Direktorat', required: true }],
  },
  {
    key: 'positions',
    entity: 'position',
    label: 'Jabatan',
    singular: 'Jabatan',
    icon: Briefcase,
    activeClass: 'border-green-600 text-green-700 dark:border-green-400 dark:text-green-300',
    searchParam: 'search_position',
    storeRoute: 'admin.settings.storePosition',
    updateRoute: 'admin.settings.updatePosition',
    fields: [{ name: 'name', label: 'Nama Jabatan', required: true }],
  },
  {
    key: 'universities',
    entity: 'university',
    label: 'Universitas',
    singular: 'Universitas',
    icon: GraduationCap,
    activeClass: 'border-purple-600 text-purple-700 dark:border-purple-400 dark:text-purple-300',
    searchParam: 'search_university',
    storeRoute: 'admin.settings.storeUniversity',
    updateRoute: 'admin.settings.updateUniversity',
    fields: [
      { name: 'name', label: 'Nama Universitas', required: true },
      { name: 'domain', label: 'Domain' },
      { name: 'website', label: 'Website', type: 'url' },
    ],
  },
]

function EntityModal({ onClose, title, fields, initial, onSubmit, submitLabel }) {
  const { data, setData, processing, errors, reset } = useForm(initial)

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
        <div className="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
          <div className="mb-4 flex items-center justify-between">
            <h3 className="text-lg font-semibold text-foreground">{title}</h3>
            <button
              type="button"
              onClick={onClose}
              aria-label="Tutup"
              className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
            >
              <X className="h-5 w-5" />
            </button>
          </div>
          <form onSubmit={handleSubmit} className="space-y-4">
            {fields.map((field) => (
              <div key={field.name} className="space-y-2">
                <Label htmlFor={field.name}>{field.label}</Label>
                <Input
                  id={field.name}
                  type={field.type ?? 'text'}
                  value={data[field.name] ?? ''}
                  onChange={(e) => setData(field.name, e.target.value)}
                  required={field.required}
                />
                {errors[field.name] && <p className="text-sm text-destructive">{errors[field.name]}</p>}
              </div>
            ))}
            <div className="flex justify-end gap-2 pt-2">
              <Button type="button" variant="outline" onClick={onClose}>Batal</Button>
              <Button type="submit" disabled={processing}>{submitLabel}</Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}

function EntityTable({ tab, paginator, searchValue, onSearch, onAdd, onEdit, onDelete }) {
  const isUniversity = tab.key === 'universities'
  const Icon = tab.icon

  return (
    <div className="space-y-4">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form onSubmit={(e) => onSearch(e, tab)} className="flex flex-1 gap-2 sm:max-w-sm">
          <div className="relative flex-1">
            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input name={tab.searchParam} defaultValue={searchValue ?? ''} placeholder={`Cari ${tab.label.toLowerCase()}...`} className="pl-9" />
          </div>
          <Button type="submit" variant="outline">Cari</Button>
        </form>
        <Button type="button" onClick={() => onAdd(tab)}>
          <Plus /> Tambah {tab.singular}
        </Button>
      </div>

      {paginator.data.length ? (
        <div className="overflow-hidden rounded-lg border border-border">
          <Table>
            <TableHeader>
              <TableRow className="hover:bg-transparent">
                <TableHead className="w-14 text-right">No</TableHead>
                <TableHead>Nama {tab.singular}</TableHead>
                {isUniversity && <TableHead>Domain</TableHead>}
                {isUniversity && <TableHead>Website</TableHead>}
                <TableHead className="w-36">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {paginator.data.map((item, index) => (
                <TableRow key={item.id}>
                  <TableCell className="text-right tabular-nums text-muted-foreground">{paginator.from + index}</TableCell>
                  <TableCell className="font-medium text-foreground">{item.name}</TableCell>
                  {isUniversity && <TableCell className="text-muted-foreground">{item.domain ?? '-'}</TableCell>}
                  {isUniversity && (
                    <TableCell>
                      {item.website ? (
                        <a
                          href={item.website}
                          target="_blank"
                          rel="noreferrer"
                          className="inline-flex items-center gap-1 text-primary hover:underline"
                        >
                          {item.website.length > 25 ? `${item.website.slice(0, 25)}...` : item.website}
                          <ExternalLink className="h-3 w-3" />
                        </a>
                      ) : (
                        <span className="text-muted-foreground">-</span>
                      )}
                    </TableCell>
                  )}
                  <TableCell>
                    <div className="flex items-center gap-1.5">
                      <Button type="button" size="xs" variant="outline" onClick={() => onEdit(tab, item)}>Edit</Button>
                      <Button
                        type="button"
                        size="xs"
                        variant="ghost"
                        className="text-destructive hover:text-destructive"
                        onClick={() => onDelete(tab, item)}
                      >
                        Hapus
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      ) : (
        <div className="rounded-lg border border-border">
          <EmptyState
            icon={Icon}
            title={searchValue ? 'Tidak ada hasil' : `Belum ada ${tab.label.toLowerCase()}`}
            description={searchValue ? 'Coba kata kunci lain.' : `Tambahkan ${tab.label.toLowerCase()} pertama.`}
          />
        </div>
      )}

      {paginator.links?.length > 3 && <Pagination links={paginator.links} />}
    </div>
  )
}

export default function Index({ directorates, positions, universities, activeTab, searchDirectorate, searchPosition, searchUniversity }) {
  const { flash } = usePage().props
  const [modal, setModal] = useState(null) // { type: 'add'|'edit', tab, item }
  const [deleteTarget, setDeleteTarget] = useState(null) // { entity, id, name }

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const paginators = { directorates, positions, universities }
  const searches = {
    directorates: searchDirectorate,
    positions: searchPosition,
    universities: searchUniversity,
  }
  const currentTab = tabDefs.find((t) => t.key === activeTab) ?? tabDefs[0]

  function switchTab(tab) {
    router.get(r('admin.settings.index'), { tab })
  }

  function handleSearch(e, tab) {
    e.preventDefault()
    router.get(r('admin.settings.index'), { tab: tab.key, [tab.searchParam]: e.target[tab.searchParam].value })
  }

  function handleModalSubmit(data, onDone) {
    const { type, tab, item } = modal
    if (type === 'add') {
      router.post(r(tab.storeRoute), data, { preserveScroll: true, onSuccess: onDone })
    } else {
      router.put(r(tab.updateRoute, item.id), data, { preserveScroll: true, onSuccess: onDone })
    }
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
        <PageHeader
          title="Konfigurasi Sistem"
          description="Kelola data direktorat, jabatan, dan universitas untuk sistem"
        />

        {successMessage && <FlashBanner type="success">{successMessage}</FlashBanner>}

        <Card>
          <nav className="flex gap-6 overflow-x-auto border-b border-border px-4">
            {tabDefs.map((tab) => {
              const Icon = tab.icon
              const isActive = currentTab.key === tab.key
              return (
                <button
                  key={tab.key}
                  type="button"
                  onClick={() => switchTab(tab.key)}
                  className={cn(
                    'flex items-center gap-2 whitespace-nowrap border-b-2 py-3 text-sm font-medium transition-colors duration-150',
                    isActive ? tab.activeClass : 'border-transparent text-muted-foreground hover:text-foreground',
                  )}
                >
                  <Icon className="h-4 w-4" />
                  {tab.label}
                  <Badge variant="secondary">{paginators[tab.key].total}</Badge>
                </button>
              )
            })}
          </nav>

          <div className="p-4 sm:p-6">
            <EntityTable
              tab={currentTab}
              paginator={paginators[currentTab.key]}
              searchValue={searches[currentTab.key]}
              onSearch={handleSearch}
              onAdd={(tab) => setModal({ type: 'add', tab })}
              onEdit={(tab, item) => setModal({ type: 'edit', tab, item })}
              onDelete={(tab, item) => setDeleteTarget({ entity: tab.entity, id: item.id, name: item.name })}
            />
          </div>
        </Card>
      </div>

      {modal && (
        <EntityModal
          onClose={() => setModal(null)}
          title={`${modal.type === 'add' ? 'Tambah' : 'Edit'} ${modal.tab.singular}`}
          fields={modal.tab.fields}
          initial={Object.fromEntries(modal.tab.fields.map((f) => [f.name, modal.item?.[f.name] ?? '']))}
          submitLabel={modal.type === 'add' ? 'Simpan' : 'Update'}
          onSubmit={handleModalSubmit}
        />
      )}

      {deleteTarget && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
          onClick={(e) => e.target === e.currentTarget && setDeleteTarget(null)}
        >
          <div className="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
            <h3 className="text-lg font-semibold text-foreground">Hapus Data</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Yakin ingin menghapus <strong className="text-foreground">{deleteTarget.name}</strong>? Tindakan ini tidak dapat dibatalkan.
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
