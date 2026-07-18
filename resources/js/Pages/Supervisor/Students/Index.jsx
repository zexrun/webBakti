import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { CheckCircle2, ChevronDown, FileText, Award, Download } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import Pagination from '@/Components/Pagination'

function ActionsMenu({ student, hasProposal, hasLaporanAkhir, documentsComplete, hasFinalAssessment, certificateGenerated }) {
  const [open, setOpen] = useState(false)
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <div className="relative">
      <button
        type="button"
        onClick={() => setOpen((o) => !o)}
        className="inline-flex items-center gap-2 rounded-md border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-accent"
      >
        Aksi
        <ChevronDown className="h-4 w-4" />
      </button>

      {open && (
        <>
          <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
          <div className="absolute right-0 z-20 mt-2 w-64 rounded-md border border-border bg-background shadow-lg">
            <div className="py-1">
              <Link
                href={r('supervisor.students.documents', student.id)}
                className="flex items-center gap-3 px-4 py-2 text-sm text-foreground hover:bg-accent"
              >
                <FileText className="h-4 w-4 text-blue-500" /> Lihat Dokumen
              </Link>

              <div className="my-1 border-t border-border" />

              {hasFinalAssessment ? (
                <Link
                  href={r('supervisor.students.assessment.edit', student.id)}
                  className="flex items-center gap-3 px-4 py-2 text-sm text-orange-700 hover:bg-orange-50"
                >
                  <Award className="h-4 w-4 text-orange-500" /> Edit Penilaian
                </Link>
              ) : (
                <Link
                  href={r('supervisor.students.assessment.create', student.id)}
                  className="flex items-center gap-3 px-4 py-2 text-sm text-orange-700 hover:bg-orange-50"
                >
                  <Award className="h-4 w-4 text-orange-500" /> Berikan Penilaian
                </Link>
              )}

              <div className="my-1 border-t border-border" />

              {documentsComplete && hasFinalAssessment ? (
                <a
                  href={r('supervisor.pdf.certificate.generate', student.id)}
                  target="_blank"
                  rel="noreferrer"
                  className="flex items-center gap-3 px-4 py-2 text-sm text-green-700 hover:bg-green-50"
                >
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
          </div>
        </>
      )}
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
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Daftar Mahasiswa Bimbingan</h1>
            <p className="text-muted-foreground">Kelola dan pantau progress mahasiswa yang Anda bimbing</p>
          </div>
          <div className="flex gap-4">
            <Card>
              <CardContent className="border-l-4 border-blue-500 p-4">
                <p className="text-sm text-muted-foreground">Total Mahasiswa</p>
                <p className="text-2xl font-bold text-foreground">{students.total}</p>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="border-l-4 border-green-500 p-4">
                <p className="text-sm text-muted-foreground">Dokumen Lengkap</p>
                <p className="text-2xl font-bold text-foreground">{completedDocs}</p>
              </CardContent>
            </Card>
          </div>
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
                  <CardContent className="p-6">
                    <div className="flex items-start justify-between gap-4">
                      <div className="flex flex-1 items-start gap-4">
                        <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600">
                          <span className="text-lg font-semibold text-white">
                            {student.user.name.charAt(0).toUpperCase()}
                          </span>
                        </div>

                        <div className="min-w-0 flex-1">
                          <div className="mb-1 flex items-center gap-2">
                            <h3 className="truncate text-lg font-semibold text-foreground">{student.user.name}</h3>
                            {certificateGenerated && (
                              <Badge variant="success">
                                <CheckCircle2 className="mr-1 h-3 w-3" /> Selesai
                              </Badge>
                            )}
                          </div>

                          <div className="grid grid-cols-1 gap-2 text-sm text-muted-foreground sm:grid-cols-3">
                            <div><span className="font-medium">NIM:</span> {student.nim ?? 'Belum diisi'}</div>
                            <div className="truncate"><span className="font-medium">Email:</span> {student.user.email}</div>
                            <div className="truncate"><span className="font-medium">Universitas:</span> {student.universitas ?? 'Belum diisi'}</div>
                          </div>

                          <div className="mt-4">
                            <div className="mb-1 flex items-center justify-between text-sm">
                              <span className="font-medium text-foreground">Progress Magang</span>
                              <span className="text-muted-foreground">{progress}%</span>
                            </div>
                            <div className="h-2 w-full rounded-full bg-muted">
                              <div
                                className="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-300"
                                style={{ width: `${progress}%` }}
                              />
                            </div>
                          </div>

                          <div className="mt-4 flex flex-wrap gap-3">
                            <StatusDot ok={hasProposal} label="Proposal" />
                            <StatusDot ok={hasLaporanAkhir} label="Laporan Akhir" />
                            <StatusDot ok={hasFinalAssessment} label="Penilaian" />
                            <StatusDot ok={certificateGenerated} label="Sertifikat" />
                          </div>
                        </div>
                      </div>

                      <ActionsMenu
                        student={student}
                        hasProposal={hasProposal}
                        hasLaporanAkhir={hasLaporanAkhir}
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
            <div className="py-12 text-center">
              <h3 className="text-lg font-medium text-foreground">Belum ada mahasiswa bimbingan</h3>
              <p className="mt-2 text-sm text-muted-foreground">
                Anda belum memiliki mahasiswa yang dibimbing. Mahasiswa akan muncul di sini setelah admin melakukan plotting pembimbing.
              </p>
            </div>
          )}
        </div>

        <Pagination links={students.links} />
      </div>
    </SupervisorLayout>
  )
}

function StatusDot({ ok, label }) {
  return (
    <div className="flex items-center text-xs">
      <div className={`mr-2 h-2 w-2 rounded-full ${ok ? 'bg-green-400' : 'bg-red-400'}`} />
      <span className="text-muted-foreground">{label}</span>
    </div>
  )
}
