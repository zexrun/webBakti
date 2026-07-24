import { useEffect, useRef, useState } from 'react'
import { createPortal } from 'react-dom'
import { Link } from '@inertiajs/react'
import { CheckCircle2, ChevronDown, FileText, Award, Download, Users, FileCheck } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import UserCell from '@/Components/UserCell'
import Pagination from '@/Components/Pagination'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Progress } from '@/Components/ui/progress'
import { cn } from '@/lib/utils'

function ActionsMenu({ student, documentsComplete, hasFinalAssessment, certificateGenerated }) {
  const [open, setOpen] = useState(false)
  const [menuPos, setMenuPos] = useState(null)
  const buttonRef = useRef(null)
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  const itemClass = 'flex items-center gap-3 px-4 py-2 text-sm text-foreground transition-colors duration-150 hover:bg-muted'

  useEffect(() => {
    if (!open || !buttonRef.current) return

    const updatePosition = () => {
      const rect = buttonRef.current.getBoundingClientRect()
      setMenuPos({ top: rect.bottom + window.scrollY + 8, right: window.innerWidth - rect.right - window.scrollX })
    }

    updatePosition()
    window.addEventListener('scroll', updatePosition, true)
    window.addEventListener('resize', updatePosition)
    return () => {
      window.removeEventListener('scroll', updatePosition, true)
      window.removeEventListener('resize', updatePosition)
    }
  }, [open])

  return (
    <div className="relative">
      <button
        ref={buttonRef}
        type="button"
        onClick={() => setOpen((o) => !o)}
        className="inline-flex h-9 items-center gap-1.5 rounded-md border border-border bg-background px-4 text-sm font-medium text-foreground transition-colors duration-150 hover:bg-muted"
      >
        Aksi
        <ChevronDown className={cn('h-4 w-4 transition-transform', open && 'rotate-180')} />
      </button>

      {open && menuPos && createPortal(
        <>
          <div className="fixed inset-0 z-40" onClick={() => setOpen(false)} />
          <div
            className="fixed z-50 w-64 overflow-hidden rounded-lg border border-border bg-popover py-1 shadow-md"
            style={{ top: menuPos.top, right: menuPos.right }}
          >
            <Link href={r('supervisor.students.documents', student.id)} className={itemClass}>
              <FileText className="h-4 w-4 text-blue-500" /> Lihat Dokumen
            </Link>

            <div className="my-1 border-t border-border" />

            <Link
              href={hasFinalAssessment
                ? r('supervisor.students.assessment.edit', student.id)
                : r('supervisor.students.assessment.create', student.id)}
              className={itemClass}
            >
              <Award className="h-4 w-4 text-amber-500" />
              {hasFinalAssessment ? 'Edit Penilaian' : 'Berikan Penilaian'}
            </Link>

            <div className="my-1 border-t border-border" />

            {documentsComplete && hasFinalAssessment ? (
              <a href={r('supervisor.pdf.certificate.generate', student.id)} target="_blank" rel="noreferrer" className={itemClass}>
                <Download className="h-4 w-4 text-green-500" />
                {certificateGenerated ? 'Download Sertifikat' : 'Generate Sertifikat'}
              </a>
            ) : (
              <div className="px-4 py-2 text-sm text-muted-foreground">
                <div className="flex items-center gap-3">
                  <Download className="h-4 w-4" /> Generate Sertifikat
                </div>
                <p className="ml-7 mt-1 text-xs text-destructive">
                  {!documentsComplete ? 'Dokumen belum lengkap' : 'Belum dinilai'}
                </p>
              </div>
            )}
          </div>
        </>,
        document.body,
      )}
    </div>
  )
}

function StatusDot({ ok, label }) {
  return (
    <div className="flex items-center gap-2 text-xs">
      <span className={cn('h-2 w-2 rounded-full', ok ? 'bg-green-500' : 'bg-muted-foreground/40')} />
      <span className="text-muted-foreground">{label}</span>
    </div>
  )
}

export default function Index({ students }) {
  const completedDocs = students.data.filter((student) => {
    const hasProposal = student.documents.some((d) => d.type === 'proposal')
    const hasLaporanAkhir = student.documents.some((d) => d.type === 'laporan_akhir')
    return hasProposal && hasLaporanAkhir
  }).length

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Daftar Mahasiswa Bimbingan"
          description="Kelola dan pantau progress mahasiswa yang Anda bimbing"
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <StatCard icon={Users} label="Total Mahasiswa" value={students.total} tone="blue" index={0} />
          <StatCard icon={FileCheck} label="Dokumen Lengkap" value={completedDocs} tone="green" index={1} />
        </div>

        <div className="space-y-4">
          {students.data.length ? (
            students.data.map((student) => {
              const hasProposal = student.documents.some((d) => d.type === 'proposal')
              const hasLaporanAkhir = student.documents.some((d) => d.type === 'laporan_akhir')
              const documentsComplete = hasProposal && hasLaporanAkhir
              const hasFinalAssessment = student.final_assessment !== null
              const certificateGenerated = Boolean(student.final_assessment?.certificate_generated_at)

              let progress = 0
              if (hasProposal) progress += 25
              if (hasLaporanAkhir) progress += 25
              if (hasFinalAssessment) progress += 25
              if (certificateGenerated) progress += 25

              return (
                <Card key={student.id}>
                  <CardContent className="p-5">
                    <div className="flex items-start justify-between gap-4">
                      <div className="min-w-0 flex-1">
                        <div className="flex flex-wrap items-center gap-2">
                          <UserCell
                            name={student.user.name}
                            subtitle={[student.nim ?? 'NIM belum diisi', student.university].filter(Boolean).join(' · ')}
                          />
                          {certificateGenerated && (
                            <Badge variant="success">
                              <CheckCircle2 /> Selesai
                            </Badge>
                          )}
                        </div>

                        <div className="mt-4 max-w-md">
                          <div className="mb-1.5 flex items-center justify-between text-sm">
                            <span className="font-medium text-foreground">Progress Magang</span>
                            <span className="tabular-nums text-muted-foreground">{progress}%</span>
                          </div>
                          <Progress value={progress} />
                        </div>

                        <div className="mt-4 flex flex-wrap gap-x-5 gap-y-2">
                          <StatusDot ok={hasProposal} label="Proposal" />
                          <StatusDot ok={hasLaporanAkhir} label="Laporan Akhir" />
                          <StatusDot ok={hasFinalAssessment} label="Penilaian" />
                          <StatusDot ok={certificateGenerated} label="Sertifikat" />
                        </div>
                      </div>

                      <ActionsMenu
                        student={student}
                        documentsComplete={documentsComplete}
                        hasFinalAssessment={hasFinalAssessment}
                        certificateGenerated={certificateGenerated}
                      />
                    </div>
                  </CardContent>
                </Card>
              )
            })
          ) : (
            <Card>
              <EmptyState
                icon={Users}
                title="Belum ada mahasiswa bimbingan"
                description="Mahasiswa akan muncul di sini setelah admin melakukan plotting pembimbing."
              />
            </Card>
          )}
        </div>

        {students.links?.length > 3 && <Pagination links={students.links} />}
      </div>
    </SupervisorLayout>
  )
}
