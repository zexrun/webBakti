import { useMemo, useState } from 'react'
import { useForm, usePage } from '@inertiajs/react'
import { Users, GraduationCap, CalendarRange, Award, CheckCircle2, Clock, Search, ChevronDown, Download } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Progress } from '@/Components/ui/progress'
import { cn } from '@/lib/utils'

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
        className="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 text-left text-sm outline-none transition-colors hover:bg-muted focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring dark:bg-input"
      >
        <span className="truncate">{value || <span className="text-muted-foreground">Pilih Universitas</span>}</span>
        <ChevronDown className={cn('ml-2 h-4 w-4 shrink-0 text-muted-foreground transition-transform', open && 'rotate-180')} />
      </button>

      {open && (
        <>
          <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
          <div className="absolute z-20 mt-2 w-full overflow-hidden rounded-lg border border-border bg-popover shadow-md">
            <div className="border-b border-border p-2">
              <div className="relative">
                <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input autoFocus value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari atau tambah universitas..." className="pl-9" />
              </div>
            </div>
            <ul className="max-h-56 overflow-y-auto p-1 text-sm">
              {filtered.map((item) => (
                <li key={item}>
                  <button
                    type="button"
                    onClick={() => select(item)}
                    className={cn(
                      'flex w-full items-center rounded-sm px-2.5 py-2 text-left transition-colors duration-150 hover:bg-muted',
                      value === item && 'bg-muted font-medium text-primary',
                    )}
                  >
                    {item}
                  </button>
                </li>
              ))}
              {filtered.length === 0 && search && (
                <li>
                  <button type="button" onClick={() => select(search)} className="flex w-full items-center rounded-sm px-2.5 py-2 text-left transition-colors duration-150 hover:bg-muted">
                    Gunakan "{search}"
                  </button>
                </li>
              )}
              {filtered.length === 0 && !search && (
                <li className="px-2.5 py-6 text-center text-sm text-muted-foreground">Universitas tidak ditemukan</li>
              )}
            </ul>
          </div>
        </>
      )}
    </div>
  )
}

const progressSteps = [
  { key: 'hasProposal', title: 'Proposal (25%)', dot: 'bg-orange-500' },
  { key: 'hasLaporanAkhir', title: 'Laporan (50%)', dot: 'bg-amber-500' },
  { key: 'hasAssessment', title: 'Dinilai (75%)', dot: 'bg-blue-500' },
  { key: 'hasCertificate', title: 'Sertifikat (100%)', dot: 'bg-green-500' },
]

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
      <div className="space-y-6">
        <PageHeader
          title="Informasi Magang Saya"
          description="Kelola informasi dan data magang Anda"
          actions={<Badge variant="success">Status: Aktif</Badge>}
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <Card className="lg:col-span-2">
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <GraduationCap className="h-4 w-4 text-muted-foreground" /> Data Mahasiswa
              </CardTitle>
              <CardDescription>Informasi pribadi dan akademik</CardDescription>
            </CardHeader>
            <CardContent className="pt-6">
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="space-y-2">
                  <Label className="flex items-center gap-2">
                    <Users className="h-4 w-4 text-muted-foreground" /> Dosen Pembimbing
                  </Label>
                  <Input value={student.supervisor?.user?.name ?? 'Belum Ditugaskan'} disabled readOnly />
                </div>

                <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="nim">NIM</Label>
                    <Input id="nim" value={data.nim} onChange={(e) => setData('nim', e.target.value)} placeholder="Masukkan NIM" required />
                    {errors.nim && <p className="text-sm text-destructive">{errors.nim}</p>}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="semester">Semester</Label>
                    <Input id="semester" type="number" min={1} max={14} value={data.semester} onChange={(e) => setData('semester', e.target.value)} placeholder="Semester saat ini" required />
                    {errors.semester && <p className="text-sm text-destructive">{errors.semester}</p>}
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="universitas">Universitas</Label>
                    <UniversityCombobox universities={universities} value={data.universitas} onChange={(v) => setData('universitas', v)} />
                    {errors.universitas && <p className="text-sm text-destructive">{errors.universitas}</p>}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="program_studi">Program Studi</Label>
                    <Input id="program_studi" value={data.program_studi} onChange={(e) => setData('program_studi', e.target.value)} placeholder="Contoh: Teknik Informatika" required />
                    {errors.program_studi && <p className="text-sm text-destructive">{errors.program_studi}</p>}
                  </div>
                </div>

                <div className="space-y-3 rounded-lg border border-border p-4">
                  <p className="flex items-center gap-2 text-sm font-medium text-foreground">
                    <CalendarRange className="h-4 w-4 text-muted-foreground" /> Periode Magang
                  </p>
                  <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div className="space-y-2">
                      <Label htmlFor="periode_mulai">Tanggal Mulai</Label>
                      <Input id="periode_mulai" type="date" value={data.periode_mulai} onChange={(e) => setData('periode_mulai', e.target.value)} required />
                      {errors.periode_mulai && <p className="text-sm text-destructive">{errors.periode_mulai}</p>}
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="periode_selesai">Tanggal Selesai</Label>
                      <Input id="periode_selesai" type="date" value={data.periode_selesai} onChange={(e) => setData('periode_selesai', e.target.value)} required />
                      {errors.periode_selesai && <p className="text-sm text-destructive">{errors.periode_selesai}</p>}
                    </div>
                  </div>
                </div>

                <div className="flex justify-end border-t border-border pt-5">
                  <Button type="submit" disabled={processing}>Simpan Perubahan</Button>
                </div>
              </form>
            </CardContent>
          </Card>

          <Card className="self-start">
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Award className="h-4 w-4 text-muted-foreground" /> Sertifikat Magang
              </CardTitle>
              <CardDescription>Status dan unduhan sertifikat kelulusan</CardDescription>
            </CardHeader>
            <CardContent className="pt-6">
              {certificateProgress.hasCertificate ? (
                <div className="rounded-lg border border-green-200 bg-green-50 p-5 dark:border-green-500/30 dark:bg-green-500/10">
                  <div className="flex items-start gap-3">
                    <CheckCircle2 className="mt-0.5 h-5 w-5 shrink-0 text-green-600 dark:text-green-400" />
                    <div>
                      <h4 className="font-semibold text-green-900 dark:text-green-200">Sertifikat Tersedia!</h4>
                      <p className="text-sm text-green-700 dark:text-green-300">Selamat! Sertifikat kelulusan magang Anda sudah siap diunduh.</p>
                    </div>
                  </div>
                  <div className="mt-4 flex flex-col gap-3">
                    <p className="text-xs tabular-nums text-green-700 dark:text-green-300">
                      {student.final_assessment?.certificate_generated_at &&
                        `Dibuat pada ${new Date(student.final_assessment.certificate_generated_at).toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`}
                    </p>
                    <Button asChild className="w-full">
                      <a href={r('student.pdf.certificate.download')} target="_blank" rel="noreferrer">
                        <Download /> Download Sertifikat
                      </a>
                    </Button>
                  </div>
                </div>
              ) : (
                <div className="space-y-4 rounded-lg border border-border bg-muted/50 p-5">
                  <div className="flex items-start gap-3">
                    <Clock className="mt-0.5 h-5 w-5 shrink-0 text-muted-foreground" />
                    <div>
                      <h4 className="font-semibold text-foreground">Sertifikat Belum Tersedia</h4>
                      <p className="text-sm text-muted-foreground">Menunggu proses penilaian akhir dari pembimbing.</p>
                    </div>
                  </div>

                  <div>
                    <div className="mb-1.5 flex items-center justify-between text-sm">
                      <span className="text-muted-foreground">Progress: {certificateProgress.text}</span>
                      <span className="font-semibold tabular-nums text-foreground">{certificateProgress.percent}%</span>
                    </div>
                    <Progress value={certificateProgress.percent} />
                  </div>

                  <div className="grid grid-cols-2 gap-2 text-xs">
                    {progressSteps.map((step) => (
                      <div key={step.key} className="flex items-center gap-2">
                        <span className={cn('h-2 w-2 shrink-0 rounded-full', certificateProgress[step.key] ? step.dot : 'bg-muted-foreground/30')} />
                        <span className={certificateProgress[step.key] ? 'text-foreground' : 'text-muted-foreground'}>{step.title}</span>
                      </div>
                    ))}
                  </div>

                  <p className="text-sm text-muted-foreground">
                    Sertifikat akan tersedia setelah pembimbing menyelesaikan penilaian akhir dan men-generate sertifikat kelulusan Anda.
                  </p>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>
    </StudentLayout>
  )
}
