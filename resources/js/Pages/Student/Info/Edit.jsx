import { useMemo, useState } from 'react'
import { useForm, usePage } from '@inertiajs/react'
import { Users, GraduationCap, CalendarRange, Award, CheckCircle2, Clock, Search, ChevronDown, Download } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

function UniversityCombobox({ universities, value, onChange }) {
  const [open, setOpen] = useState(false)
  const [search, setSearch] = useState('')

  const filtered = useMemo(() => {
    const term = search.toLowerCase()
    return universities.filter((u) => u.toLowerCase().includes(term))
  }, [search, universities])

  function select(item) {
    onChange(item)
    setOpen(false)
    setSearch('')
  }

  return (
    <div className="relative">
      <button
        type="button"
        onClick={() => setOpen((o) => !o)}
        className="flex w-full items-center justify-between rounded-md border-2 border-border bg-background px-4 py-3 text-left text-sm shadow-sm hover:border-muted-foreground focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10"
      >
        <span className="truncate">{value || 'Pilih Universitas'}</span>
        <ChevronDown className={`ml-2 h-5 w-5 flex-shrink-0 text-muted-foreground transition-transform ${open ? 'rotate-180' : ''}`} />
      </button>

      {open && (
        <>
          <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
          <div className="absolute z-20 mt-2 max-h-64 w-full overflow-hidden rounded-lg border border-border bg-background shadow-lg">
            <div className="border-b border-border bg-muted px-4 py-3">
              <div className="relative">
                <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                  autoFocus
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  placeholder="Cari atau tambah universitas..."
                  className="w-full rounded-md border border-border bg-background py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                />
              </div>
            </div>
            <ul className="max-h-48 overflow-y-auto text-sm">
              {filtered.map((item) => (
                <li key={item}>
                  <button
                    type="button"
                    onClick={() => select(item)}
                    className={`flex w-full items-center px-4 py-3 text-left hover:bg-accent ${value === item ? 'bg-accent text-primary' : ''}`}
                  >
                    {item}
                  </button>
                </li>
              ))}
              {filtered.length === 0 && search && (
                <li>
                  <button type="button" onClick={() => select(search)} className="flex w-full items-center px-4 py-3 text-left hover:bg-accent">
                    Gunakan "{search}"
                  </button>
                </li>
              )}
              {filtered.length === 0 && !search && (
                <li className="px-4 py-6 text-center text-sm text-muted-foreground">Universitas tidak ditemukan</li>
              )}
            </ul>
          </div>
        </>
      )}
    </div>
  )
}

const progressSteps = [
  { key: 'hasProposal', label: 'P', title: 'Proposal (25%)', color: 'bg-orange-500' },
  { key: 'hasLaporanAkhir', label: 'L', title: 'Laporan (50%)', color: 'bg-yellow-500' },
  { key: 'hasAssessment', label: 'N', title: 'Dinilai (75%)', color: 'bg-blue-500' },
  { key: 'hasCertificate', label: 'S', title: 'Sertifikat (100%)', color: 'bg-green-500' },
]

const progressBarColor = {
  0: 'bg-muted-foreground/40',
  25: 'bg-orange-500',
  50: 'bg-yellow-500',
  75: 'bg-blue-500',
  100: 'bg-green-500',
}

export default function Edit({ student, universities, certificateProgress }) {
  const { flash } = usePage().props
  const { data, setData, patch, processing, errors } = useForm({
    nim: student.nim ?? '',
    universitas: student.universitas ?? '',
    program_studi: student.program_studi ?? '',
    semester: student.semester ?? '',
    periode_mulai: student.periode_mulai ?? '',
    periode_selesai: student.periode_selesai ?? '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    patch(r('student.info.update'))
  }

  return (
    <StudentLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <Card className="border-blue-100">
          <CardContent className="flex items-center justify-between p-6">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Informasi Magang Saya</h1>
              <p className="mt-1 text-muted-foreground">Kelola informasi dan data magang Anda 🎓</p>
              <p className="mt-1 text-sm text-muted-foreground">
                {new Date().toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}
              </p>
            </div>
            <div className="rounded-lg bg-blue-50 px-4 py-2">
              <span className="text-sm font-medium text-blue-700">Status: <strong>Aktif</strong></span>
            </div>
          </CardContent>
        </Card>

        {flash?.success && (
          <div className="rounded-lg border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-emerald-50 p-4 text-sm font-medium text-green-700">
            {flash.success}
          </div>
        )}

        <Card className="border-blue-100">
          <CardContent className="p-8">
            <div className="mb-6 flex items-center gap-4">
              <div className="rounded-full bg-blue-100 p-3">
                <GraduationCap className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <h3 className="text-lg font-semibold text-foreground">Data Mahasiswa</h3>
                <p className="text-muted-foreground">Informasi pribadi dan akademik</p>
              </div>
            </div>

            <form onSubmit={handleSubmit} className="space-y-8">
              <div className="rounded-lg border border-border bg-gradient-to-r from-muted to-blue-50 p-6">
                <div className="mb-4 flex items-center gap-3">
                  <div className="rounded-lg bg-muted-foreground/80 p-2">
                    <Users className="h-5 w-5 text-white" />
                  </div>
                  <div>
                    <h4 className="text-sm font-semibold text-foreground">Dosen Pembimbing</h4>
                    <p className="text-sm text-muted-foreground">Pembimbing yang ditugaskan</p>
                  </div>
                </div>
                <Input value={student.supervisor?.user?.name ?? 'Belum Ditugaskan'} disabled readOnly className="bg-muted font-medium" />
              </div>

              <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                  <Label htmlFor="nim">NIM</Label>
                  <Input
                    id="nim"
                    value={data.nim}
                    onChange={(e) => setData('nim', e.target.value)}
                    placeholder="Masukkan NIM"
                    className="mt-2"
                    required
                  />
                  {errors.nim && <p className="mt-1 text-sm text-destructive">{errors.nim}</p>}
                </div>
                <div>
                  <Label htmlFor="semester">Semester</Label>
                  <Input
                    id="semester"
                    type="number"
                    min={1}
                    max={14}
                    value={data.semester}
                    onChange={(e) => setData('semester', e.target.value)}
                    placeholder="Semester saat ini"
                    className="mt-2"
                    required
                  />
                  {errors.semester && <p className="mt-1 text-sm text-destructive">{errors.semester}</p>}
                </div>
              </div>

              <div>
                <Label htmlFor="universitas">Universitas</Label>
                <div className="mt-2">
                  <UniversityCombobox universities={universities} value={data.universitas} onChange={(v) => setData('universitas', v)} />
                </div>
                {errors.universitas && <p className="mt-1 text-sm text-destructive">{errors.universitas}</p>}
              </div>

              <div>
                <Label htmlFor="program_studi">Program Studi</Label>
                <Input
                  id="program_studi"
                  value={data.program_studi}
                  onChange={(e) => setData('program_studi', e.target.value)}
                  placeholder="Contoh: Teknik Informatika"
                  className="mt-2"
                  required
                />
                {errors.program_studi && <p className="mt-1 text-sm text-destructive">{errors.program_studi}</p>}
              </div>

              <div className="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
                <div className="mb-4 flex items-center gap-3">
                  <div className="rounded-lg bg-blue-500 p-2">
                    <CalendarRange className="h-5 w-5 text-white" />
                  </div>
                  <div>
                    <h4 className="text-sm font-semibold text-foreground">Periode Magang</h4>
                    <p className="text-sm text-muted-foreground">Tentukan waktu pelaksanaan magang</p>
                  </div>
                </div>
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                    <Label htmlFor="periode_mulai">Tanggal Mulai</Label>
                    <Input
                      id="periode_mulai"
                      type="date"
                      value={data.periode_mulai}
                      onChange={(e) => setData('periode_mulai', e.target.value)}
                      className="mt-2"
                      required
                    />
                    {errors.periode_mulai && <p className="mt-1 text-sm text-destructive">{errors.periode_mulai}</p>}
                  </div>
                  <div>
                    <Label htmlFor="periode_selesai">Tanggal Selesai</Label>
                    <Input
                      id="periode_selesai"
                      type="date"
                      value={data.periode_selesai}
                      onChange={(e) => setData('periode_selesai', e.target.value)}
                      className="mt-2"
                      required
                    />
                    {errors.periode_selesai && <p className="mt-1 text-sm text-destructive">{errors.periode_selesai}</p>}
                  </div>
                </div>
              </div>

              <div className="flex justify-end pt-4">
                <Button type="submit" disabled={processing}>Simpan Perubahan</Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card className="border-blue-100">
          <CardContent className="p-8">
            <div className="mb-6 flex items-center gap-4">
              <div className="rounded-full bg-yellow-100 p-3">
                <Award className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <h3 className="text-lg font-semibold text-foreground">Sertifikat Magang</h3>
                <p className="text-muted-foreground">Status dan unduhan sertifikat kelulusan</p>
              </div>
            </div>

            {certificateProgress.hasCertificate ? (
              <div className="rounded-lg border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-6">
                <div className="mb-4 flex items-center gap-3">
                  <div className="rounded-full bg-green-500 p-2">
                    <CheckCircle2 className="h-5 w-5 text-white" />
                  </div>
                  <div>
                    <h4 className="text-md font-semibold text-green-900">Sertifikat Tersedia!</h4>
                    <p className="text-sm text-green-700">Selamat! Sertifikat kelulusan magang Anda sudah siap diunduh</p>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <p className="text-sm text-green-600">
                    {student.final_assessment?.certificate_generated_at &&
                      `Dibuat pada: ${new Date(student.final_assessment.certificate_generated_at).toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`}
                  </p>
                  <a
                    href={r('student.pdf.certificate.download')}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center gap-2 rounded-md bg-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:bg-green-700 hover:shadow-xl"
                  >
                    <Download className="h-4 w-4" /> Download Sertifikat
                  </a>
                </div>
              </div>
            ) : (
              <div className="rounded-lg border border-border bg-gradient-to-r from-muted to-blue-50 p-6">
                <div className="mb-4 flex items-center gap-3">
                  <div className="rounded-full bg-muted-foreground/60 p-2">
                    <Clock className="h-5 w-5 text-white" />
                  </div>
                  <div>
                    <h4 className="text-md font-semibold text-foreground">Sertifikat Belum Tersedia</h4>
                    <p className="text-sm text-muted-foreground">Menunggu proses penilaian akhir dari pembimbing</p>
                  </div>
                </div>

                <div className="flex items-center gap-4">
                  <div className="flex-1">
                    <div className="h-2 rounded-full bg-muted">
                      <div
                        className={`h-2 rounded-full transition-all duration-500 ease-out ${progressBarColor[certificateProgress.percent]}`}
                        style={{ width: `${certificateProgress.percent}%` }}
                      />
                    </div>
                    <div className="mt-2 flex items-center justify-between">
                      <p className="text-xs text-muted-foreground">Progress: {certificateProgress.text}</p>
                      <div className="flex items-center gap-1">
                        {progressSteps.map((step) => (
                          <div key={step.key} className="flex items-center">
                            <CheckCircle2 className={`h-3 w-3 ${certificateProgress[step.key] ? 'text-green-500' : 'text-muted-foreground/30'}`} />
                            <span className="ml-1 text-xs text-muted-foreground">{step.label}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                  <span className="text-sm font-medium text-muted-foreground">{certificateProgress.percent}%</span>
                </div>

                <div className="mt-4 rounded-lg bg-muted p-3">
                  <p className="mb-2 text-xs font-medium text-foreground">Keterangan Progress:</p>
                  <div className="grid grid-cols-2 gap-2 text-xs text-muted-foreground md:grid-cols-4">
                    {progressSteps.map((step) => (
                      <div key={step.key} className="flex items-center">
                        <span className={`mr-2 h-2 w-2 rounded-full ${step.color}`} />
                        <span>{step.title}</span>
                      </div>
                    ))}
                  </div>
                </div>

                <p className="mt-4 text-sm text-muted-foreground">
                  Sertifikat akan tersedia setelah pembimbing menyelesaikan penilaian akhir dan men-generate sertifikat kelulusan Anda.
                </p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
