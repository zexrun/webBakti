import { Link } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'

export default function TaskAnalytics({
  taskInfo,
  submissionStats,
  gradeDistribution,
  gradeStats,
  studentPerformance,
  notSubmittedStudents,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const maxDistribution = Math.max(...Object.values(gradeDistribution), 1)
  const totalSubmitted = submissionStats.graded + submissionStats.pending_grade
  const gradedPercentage = totalSubmitted > 0 ? Math.round((submissionStats.graded / totalSubmitted) * 1000) / 10 : 0

  return (
    <SupervisorLayout>
      <div className="space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Analisis Tugas</h1>
            <p className="text-muted-foreground">{taskInfo.title}</p>
          </div>
          <Link href={r('supervisor.analytics.dashboard')}>
            <Button type="button" variant="secondary">
              <ArrowLeft className="h-4 w-4" /> Kembali
            </Button>
          </Link>
        </div>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Informasi Tugas</h2>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div>
                <p className="text-sm text-muted-foreground">Judul</p>
                <p className="text-lg font-medium text-foreground">{taskInfo.title}</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Dibuat</p>
                <p className="text-lg font-medium text-foreground">
                  {new Date(taskInfo.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                </p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Deadline</p>
                <p className="text-lg font-medium text-foreground">
                  {new Date(taskInfo.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                </p>
              </div>
            </div>
            {taskInfo.description && (
              <div className="mt-4 border-t border-border pt-4">
                <p className="mb-2 text-sm text-muted-foreground">Deskripsi</p>
                <p className="text-foreground/80">{taskInfo.description}</p>
              </div>
            )}
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Total Assigned</p>
              <p className="text-3xl font-bold text-blue-600">{submissionStats.total_assigned}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Submitted</p>
              <p className="text-3xl font-bold text-green-600">{submissionStats.submitted}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Not Submitted</p>
              <p className="text-3xl font-bold text-red-600">{submissionStats.not_submitted}</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <p className="mb-1 text-sm text-muted-foreground">Submission Rate</p>
              <p className="text-3xl font-bold text-orange-600">{submissionStats.submission_rate}%</p>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Statistik Nilai</h2>
              <div className="space-y-4">
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Rata-rata</span>
                  <span className="text-2xl font-bold text-blue-600">{gradeStats.average}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Tertinggi</span>
                  <span className="text-2xl font-bold text-green-600">{gradeStats.highest}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Terendah</span>
                  <span className="text-2xl font-bold text-red-600">{gradeStats.lowest}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Median</span>
                  <span className="text-2xl font-bold text-purple-600">{gradeStats.median}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Distribusi Nilai</h2>
              <div className="space-y-3">
                {Object.entries(gradeDistribution).map(([range, count]) => (
                  <div key={range}>
                    <div className="mb-1 flex items-center justify-between">
                      <span className="text-sm text-foreground">{range}</span>
                      <span className="text-sm font-medium text-foreground">{count}</span>
                    </div>
                    <div className="h-2 w-full rounded-full bg-muted">
                      <div
                        className="h-2 rounded-full bg-gradient-to-r from-blue-600 to-purple-600"
                        style={{ width: `${(count / maxDistribution) * 100}%` }}
                      />
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Status Penilaian</h2>
            <div className="space-y-3">
              <div className="flex items-center justify-between rounded-lg bg-green-50 p-3">
                <span className="text-sm text-gray-600">Sudah Dinilai</span>
                <span className="text-2xl font-bold text-green-600">{submissionStats.graded}</span>
              </div>
              <div className="flex items-center justify-between rounded-lg bg-yellow-50 p-3">
                <span className="text-sm text-gray-600">Menunggu Penilaian</span>
                <span className="text-2xl font-bold text-yellow-600">{submissionStats.pending_grade}</span>
              </div>
              <div className="mt-4 h-3 w-full rounded-full bg-muted">
                <div className="h-3 rounded-full bg-green-600" style={{ width: `${gradedPercentage}%` }} />
              </div>
              <p className="mt-2 text-center text-xs text-muted-foreground">{gradedPercentage}% Complete</p>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h2 className="mb-4 text-lg font-semibold text-foreground">Performa Mahasiswa</h2>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="border-b border-border bg-muted">
                  <tr>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Nama Mahasiswa</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Waktu Submit</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Nilai</th>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Feedback</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {studentPerformance.length ? (
                    studentPerformance.map((submission, index) => (
                      <tr key={index} className="hover:bg-accent">
                        <td className="px-6 py-4">
                          <Link href={r('supervisor.analytics.student', submission.student_id)} className="font-medium text-primary hover:underline">
                            {submission.student_name}
                          </Link>
                        </td>
                        <td className="px-6 py-4 text-center text-sm text-muted-foreground">
                          {new Date(submission.submitted_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </td>
                        <td className="px-6 py-4 text-center">
                          {submission.grade !== null ? (
                            <span className="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{submission.grade}</span>
                          ) : (
                            <span className="text-muted-foreground">-</span>
                          )}
                        </td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">
                          {submission.feedback ? `${submission.feedback.slice(0, 50)}...` : '-'}
                        </td>
                        <td className="px-6 py-4 text-center">
                          {submission.status === 'Graded' ? (
                            <span className="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Graded</span>
                          ) : (
                            <span className="rounded bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800">Pending</span>
                          )}
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={5} className="px-6 py-8 text-center text-muted-foreground">Belum ada submission</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        {notSubmittedStudents.length > 0 && (
          <Card className="border-red-200 bg-red-50">
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-red-900">Mahasiswa Belum Submit</h2>
              <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                {notSubmittedStudents.map((student, index) => (
                  <div key={index} className="rounded-lg border border-red-200 bg-white p-3">
                    <p className="font-medium text-gray-900">{student.name}</p>
                    <p className="text-sm text-gray-600">{student.nim}</p>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
