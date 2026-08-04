import { useMemo, useState } from 'react'
import { router, usePage } from '@inertiajs/react'
import { Users, CheckCircle2, Clock, Search, Pencil, Plus, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import { Card } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

export default function Plotting({ students, supervisors }) {
  const { flash } = usePage().props
  const [modalStudent, setModalStudent] = useState(null)
  const [search, setSearch] = useState('')
  const [selectedSupervisorId, setSelectedSupervisorId] = useState(null)
  const [saving, setSaving] = useState(false)

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const assignedCount = students.filter((s) => s.supervisor_id).length
  const unassignedCount = students.length - assignedCount

  const filteredSupervisors = useMemo(() => {
    const term = search.toLowerCase()
    if (!term) return supervisors
    return supervisors.filter((s) =>
      s.user.name.toLowerCase().includes(term) ||
      (s.position ?? '').toLowerCase().includes(term) ||
      (s.directorate ?? '').toLowerCase().includes(term)
    )
  }, [search, supervisors])

  function openModal(student) {
    setModalStudent(student)
    setSelectedSupervisorId(student.supervisor_id)
    setSearch('')
  }

  function closeModal() {
    setModalStudent(null)
    setSelectedSupervisorId(null)
    setSearch('')
  }

  function assign() {
    if (!selectedSupervisorId) return
    setSaving(true)
    router.post(
      r('admin.plotting.assign'),
      { student_id: modalStudent.id, supervisor_id: selectedSupervisorId },
      {
        preserveScroll: true,
        onFinish: () => {
          setSaving(false)
          closeModal()
        },
      }
    )
  }

  return (
    <AdminLayout>
      <div className="space-y-6">
        <PageHeader
          title="Penugasan Pembimbing"
          description="Pilih dosen pembimbing untuk setiap mahasiswa yang tersedia"
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <StatCard icon={Users} label="Total Mahasiswa" value={students.length} tone="blue" index={0} />
          <StatCard icon={CheckCircle2} label="Sudah Ditugaskan" value={assignedCount} tone="green" index={1} />
          <StatCard icon={Clock} label="Belum Ditugaskan" value={unassignedCount} tone="orange" index={2} />
        </div>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Daftar Mahasiswa</h3>
          </div>
          {students.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama Mahasiswa</TableHead>
                  <TableHead>NIM</TableHead>
                  <TableHead className="hidden md:table-cell">Universitas</TableHead>
                  <TableHead>Status Pembimbing</TableHead>
                  <TableHead className="w-48">Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {students.map((student) => (
                  <TableRow key={student.id}>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 dark:bg-blue-500/15">
                          {student.user.profile_photo_url ? (
                            <img src={student.user.profile_photo_url} alt={student.user.name} className="h-full w-full object-cover" />
                          ) : (
                            <span className="text-sm font-medium text-blue-700 dark:text-blue-300">
                              {student.user.name.charAt(0).toUpperCase()}
                            </span>
                          )}
                        </div>
                        <div>
                          <div className="font-medium text-foreground">{student.user.name}</div>
                          <div className="text-xs text-muted-foreground md:hidden">{student.university}</div>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell className="tabular-nums text-muted-foreground">{student.nim}</TableCell>
                    <TableCell className="hidden text-muted-foreground md:table-cell">{student.university}</TableCell>
                    <TableCell>
                      {student.supervisor ? (
                        <Badge variant="success">
                          <CheckCircle2 /> {student.supervisor.user.name}
                        </Badge>
                      ) : (
                        <Badge variant="warning">Belum Ditugaskan</Badge>
                      )}
                    </TableCell>
                    <TableCell>
                      <Button type="button" size="xs" variant="outline" onClick={() => openModal(student)}>
                        {student.supervisor ? (
                          <>
                            <Pencil /> Ubah Pembimbing
                          </>
                        ) : (
                          <>
                            <Plus /> Pilih Pembimbing
                          </>
                        )}
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState
              icon={Users}
              title="Tidak ada data mahasiswa"
              description="Belum ada mahasiswa yang perlu ditugaskan pembimbing."
            />
          )}
        </Card>
      </div>

      {modalStudent && (
        <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && closeModal()}>
          <div className="flex min-h-screen items-center justify-center p-4">
            <div className="flex max-h-[90vh] w-full max-w-lg flex-col rounded-xl border border-border bg-card shadow-lg">
              <div className="border-b border-border px-6 py-4">
                <div className="flex items-start justify-between gap-4">
                  <div>
                    <h3 className="text-lg font-semibold text-foreground">Pilih Pembimbing</h3>
                    <p className="mt-1 text-sm text-muted-foreground">
                      untuk <span className="font-medium text-foreground">{modalStudent.user.name}</span>
                    </p>
                  </div>
                  <button
                    type="button"
                    onClick={closeModal}
                    aria-label="Tutup"
                    className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
                  >
                    <X className="h-5 w-5" />
                  </button>
                </div>
              </div>

              <div className="flex flex-1 flex-col overflow-hidden p-6">
                {modalStudent.supervisor && (
                  <div className="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-500/30 dark:bg-blue-500/10">
                    <p className="text-sm text-blue-800 dark:text-blue-200">
                      <span className="font-medium">Pembimbing saat ini:</span> {modalStudent.supervisor.user.name}
                    </p>
                  </div>
                )}

                <div className="relative mb-4">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                  <Input
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Cari nama pembimbing..."
                    className="pl-9"
                    autoFocus
                  />
                </div>

                <div className="flex-1 overflow-y-auto rounded-lg border border-border">
                  {filteredSupervisors.length ? (
                    filteredSupervisors.map((supervisor) => {
                      const selected = selectedSupervisorId === supervisor.id
                      return (
                        <button
                          type="button"
                          key={supervisor.id}
                          onClick={() => setSelectedSupervisorId(supervisor.id)}
                          className={cn(
                            'flex w-full items-center gap-3 border-b border-border p-3.5 text-left transition-colors duration-150 last:border-b-0',
                            selected ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'hover:bg-muted',
                          )}
                        >
                          <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/15">
                            <span className="text-sm font-medium text-green-700 dark:text-green-300">
                              {supervisor.user.name.charAt(0).toUpperCase()}
                            </span>
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="truncate text-sm font-medium text-foreground">{supervisor.user.name}</div>
                            <div className="truncate text-xs text-muted-foreground">
                              {[supervisor.position, supervisor.directorate].filter(Boolean).join(' · ')}
                            </div>
                          </div>
                          <div
                            className={cn(
                              'h-4 w-4 shrink-0 rounded-full border-2 transition-colors duration-150',
                              selected ? 'border-primary bg-primary' : 'border-border',
                            )}
                          />
                        </button>
                      )
                    })
                  ) : (
                    <EmptyState icon={Search} title="Tidak ada pembimbing yang ditemukan" className="py-8" />
                  )}
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border px-6 py-4">
                <Button type="button" variant="outline" onClick={closeModal}>Batal</Button>
                <Button type="button" onClick={assign} disabled={!selectedSupervisorId || saving}>
                  {saving ? 'Menyimpan...' : 'Tugaskan Pembimbing'}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </AdminLayout>
  )
}
