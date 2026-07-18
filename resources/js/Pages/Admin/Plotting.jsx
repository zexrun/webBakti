import { useMemo, useState } from 'react'
import { router, usePage } from '@inertiajs/react'
import { Users, CheckCircle2, Clock, Search, Pencil, Plus, X } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

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
      (s.jabatan ?? '').toLowerCase().includes(term) ||
      (s.direktorat ?? '').toLowerCase().includes(term)
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
        <Card>
          <CardContent className="p-6">
            <h1 className="text-2xl font-bold text-foreground">Plotting Pembimbing Mahasiswa</h1>
            <p className="text-muted-foreground">Pilih dosen pembimbing untuk setiap mahasiswa yang tersedia.</p>
          </CardContent>
        </Card>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}

        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-full bg-blue-100 p-3">
                <Users className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Total Mahasiswa</p>
                <p className="text-2xl font-bold text-foreground">{students.length}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-full bg-green-100 p-3">
                <CheckCircle2 className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Sudah Ditugaskan</p>
                <p className="text-2xl font-bold text-foreground">{assignedCount}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="rounded-full bg-orange-100 p-3">
                <Clock className="h-6 w-6 text-orange-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Belum Ditugaskan</p>
                <p className="text-2xl font-bold text-foreground">{unassignedCount}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card className="overflow-hidden">
          <div className="border-b border-border bg-muted px-6 py-4">
            <h3 className="text-lg font-semibold text-foreground">Daftar Mahasiswa</h3>
          </div>
          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-muted">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Nama Mahasiswa</th>
                    <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">NIM</th>
                    <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground md:table-cell">Universitas</th>
                    <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Status Pembimbing</th>
                    <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {students.length ? (
                    students.map((student) => (
                      <tr key={student.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                              <span className="text-sm font-medium text-blue-600">{student.user.name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div>
                              <div className="text-sm font-medium text-foreground">{student.user.name}</div>
                              <div className="text-sm text-muted-foreground md:hidden">{student.universitas}</div>
                            </div>
                          </div>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">{student.nim}</td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-muted-foreground md:table-cell">{student.universitas}</td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {student.supervisor ? (
                            <Badge variant="success">
                              <CheckCircle2 className="mr-1 h-3 w-3" /> {student.supervisor.user.name}
                            </Badge>
                          ) : (
                            <Badge variant="warning">Belum Ditugaskan</Badge>
                          )}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <Button type="button" size="sm" onClick={() => openModal(student)}>
                            {student.supervisor ? (
                              <>
                                <Pencil className="h-4 w-4" /> Ubah Pembimbing
                              </>
                            ) : (
                              <>
                                <Plus className="h-4 w-4" /> Pilih Pembimbing
                              </>
                            )}
                          </Button>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={5} className="px-6 py-12 text-center">
                        <Users className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                        <h3 className="mb-1 text-sm font-medium text-foreground">Tidak ada data mahasiswa</h3>
                        <p className="text-sm text-muted-foreground">Belum ada mahasiswa yang perlu ditugaskan pembimbing.</p>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>

      {modalStudent && (
        <div className="fixed inset-0 z-50 bg-black/50" onClick={(e) => e.target === e.currentTarget && closeModal()}>
          <div className="flex min-h-screen items-center justify-center p-4">
            <div className="flex max-h-[90vh] w-full max-w-lg flex-col rounded-lg bg-background shadow-xl">
              <div className="border-b border-border px-6 py-4">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="text-lg font-semibold text-foreground">Pilih Pembimbing</h3>
                    <p className="mt-1 text-sm text-muted-foreground">
                      untuk <span className="font-medium">{modalStudent.user.name}</span>
                    </p>
                  </div>
                  <button type="button" onClick={closeModal} className="text-muted-foreground hover:text-foreground">
                    <X className="h-6 w-6" />
                  </button>
                </div>
              </div>

              <div className="flex flex-1 flex-col overflow-hidden p-6">
                {modalStudent.supervisor && (
                  <div className="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                    <p className="text-sm text-blue-800">
                      <span className="font-medium">Pembimbing saat ini:</span> {modalStudent.supervisor.user.name}
                    </p>
                  </div>
                )}

                <div className="mb-4">
                  <div className="relative">
                    <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      value={search}
                      onChange={(e) => setSearch(e.target.value)}
                      placeholder="Cari nama pembimbing..."
                      className="pl-10"
                      autoFocus
                    />
                  </div>
                </div>

                <div className="flex-1 overflow-y-auto rounded-lg border border-border">
                  {filteredSupervisors.length ? (
                    filteredSupervisors.map((supervisor) => (
                      <div
                        key={supervisor.id}
                        onClick={() => setSelectedSupervisorId(supervisor.id)}
                        className={`flex cursor-pointer items-center border-b border-border p-4 last:border-b-0 hover:bg-accent ${
                          selectedSupervisorId === supervisor.id ? 'bg-blue-100' : ''
                        }`}
                      >
                        <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                          <span className="text-sm font-medium text-green-600">{supervisor.user.name.charAt(0).toUpperCase()}</span>
                        </div>
                        <div className="ml-3 flex-1">
                          <div className="text-sm font-medium text-foreground">{supervisor.user.name}</div>
                          <div className="text-xs text-muted-foreground">{supervisor.jabatan}</div>
                          <div className="text-xs text-muted-foreground/70">{supervisor.direktorat}</div>
                        </div>
                        <div
                          className={`h-4 w-4 flex-shrink-0 rounded-full border-2 ${
                            selectedSupervisorId === supervisor.id ? 'border-blue-600 bg-blue-600' : 'border-border'
                          }`}
                        />
                      </div>
                    ))
                  ) : (
                    <div className="p-8 text-center">
                      <Search className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                      <p className="text-muted-foreground">Tidak ada pembimbing yang ditemukan</p>
                    </div>
                  )}
                </div>
              </div>

              <div className="flex justify-end gap-3 border-t border-border bg-muted px-6 py-4">
                <Button type="button" variant="secondary" onClick={closeModal}>Batal</Button>
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
