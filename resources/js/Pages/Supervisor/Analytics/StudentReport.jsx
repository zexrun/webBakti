import { Link } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'

export default function StudentReport({
  studentInfo,
  taskStats,
  gradePerformance,
  submissionTimeline,
  attendanceStats,
  finalAssessment,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Laporan Performa Mahasiswa</h1>
            <p className="text-muted-foreground">{studentInfo.name} ({studentInfo.nim})</p>
          </div>
          <Link href={r('supervisor.analytics.dashboard')}>
            <Button type="button" variant="secondary">
              <ArrowLeft className="h-4 w-4" /> Kembali
            </Button>
          </Link>
        </div>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Informasi Mahasiswa</h2>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div>
                <p className="text-sm text-muted-foreground">Nama</p>
                <p className="text-lg font-medium text-foreground">{studentInfo.name}</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">NIM</p>
                <p className="text-lg font-medium text-foreground">{studentInfo.nim}</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Universitas</p>
                <p className="text-lg font-medium text-foreground">{studentInfo.universitas}</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Program Studi</p>
                <p className="text-lg font-medium text-foreground">{studentInfo.program_studi}</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Pembimbing</p>
                <p className="text-lg font-medium text-foreground">{studentInfo.supervisor}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Tugas Diberikan</p>
              <p className="text-3xl font-bold text-blue-600">{taskStats.assigned}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Dikumpulkan</p>
              <p className="text-3xl font-bold text-green-600">{taskStats.submitted}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Dinilai</p>
              <p className="text-3xl font-bold text-purple-600">{taskStats.graded}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Completion Rate</p>
              <p className="text-3xl font-bold text-orange-600">{taskStats.completion_rate}%</p>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Statistik Nilai</h2>
              <div className="space-y-4">
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Rata-rata Nilai</span>
                  <span className="text-2xl font-bold text-blue-600">{gradePerformance.average_grade}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Nilai Tertinggi</span>
                  <span className="text-2xl font-bold text-green-600">{gradePerformance.highest_grade}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Nilai Terendah</span>
                  <span className="text-2xl font-bold text-red-600">{gradePerformance.lowest_grade}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Total Dinilai</span>
                  <span className="text-2xl font-bold text-purple-600">{gradePerformance.total_graded}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Statistik Kehadiran (Bulan Ini)</h2>
              <div className="space-y-4">
                <div className="flex items-center justify-between rounded-lg bg-green-50 p-3">
                  <span className="text-sm text-gray-600">Hadir</span>
                  <span className="text-2xl font-bold text-green-600">{attendanceStats.present}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-orange-50 p-3">
                  <span className="text-sm text-gray-600">Terlambat</span>
                  <span className="text-2xl font-bold text-orange-600">{attendanceStats.late}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-red-50 p-3">
                  <span className="text-sm text-gray-600">Tidak Hadir</span>
                  <span className="text-2xl font-bold text-red-600">{attendanceStats.absent}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3">
                  <span className="text-sm text-gray-600">Kehadiran Rate</span>
                  <span className="text-2xl font-bold text-blue-600">{attendanceStats.rate}%</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Timeline Submission</h2>
            <div className="space-y-3">
              {submissionTimeline.length ? (
                submissionTimeline.map((submission, index) => (
                  <div key={index} className="flex items-start justify-between rounded-lg border border-border p-4 hover:bg-accent">
                    <div className="flex-1">
                      <p className="font-medium text-foreground">{submission.task_title}</p>
                      <p className="text-sm text-muted-foreground">
                        Submitted: {new Date(submission.submitted_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </p>
                      {submission.feedback && (
                        <p className="mt-2 text-sm italic text-foreground/80">Feedback: {submission.feedback}</p>
                      )}
                    </div>
                    <div className="ml-4 text-right">
                      {submission.status === 'Graded' ? (
                        <span className="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">{submission.grade}</span>
                      ) : (
                        <span className="rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">Pending</span>
                      )}
                    </div>
                  </div>
                ))
              ) : (
                <p className="py-8 text-center text-muted-foreground">Belum ada submission</p>
              )}
            </div>
          </CardContent>
        </Card>

        {finalAssessment && (
          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Penilaian Akhir</h2>
              <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                  <p className="mb-1 text-sm text-muted-foreground">Nilai Akhir</p>
                  <p className="text-4xl font-bold text-blue-600">{finalAssessment.final_grade}</p>
                </div>
                <div>
                  <p className="mb-1 text-sm text-muted-foreground">Grade</p>
                  <p className="text-2xl font-semibold text-foreground">{finalAssessment.grade}</p>
                </div>
                <div className="md:col-span-2">
                  <p className="mb-1 text-sm text-muted-foreground">Catatan</p>
                  <p className="text-foreground/80">{finalAssessment.notes ?? '-'}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
