import { Link } from '@inertiajs/react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'

function StatCard({ label, value, color, sub }) {
  return (
    <Card>
      <CardContent className="p-6">
        <p className="mb-1 text-sm text-muted-foreground">{label}</p>
        <p className={`text-3xl font-bold ${color}`}>{value}</p>
        {sub && <p className="mt-2 text-xs text-muted-foreground">{sub}</p>}
      </CardContent>
    </Card>
  )
}

export default function Dashboard({
  totalStudents,
  totalTasks,
  totalSubmissions,
  gradedSubmissions,
  gradeStats,
  gradeDistribution,
  taskPerformance,
  topStudents,
  atRiskStudents,
}) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  const maxDistribution = Math.max(...Object.values(gradeDistribution), 1)
  const completePercent = totalSubmissions > 0 ? Math.round((gradedSubmissions / totalSubmissions) * 1000) / 10 : 0

  return (
    <SupervisorLayout>
      <div className="space-y-8">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Analytics & Performance</h1>
          <p className="text-muted-foreground">Analisis performa mahasiswa dan tugas secara menyeluruh</p>
        </div>

        <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
          <StatCard label="Total Mahasiswa" value={totalStudents} color="text-blue-600" />
          <StatCard label="Total Tugas" value={totalTasks} color="text-purple-600" />
          <StatCard label="Total Submission" value={totalSubmissions} color="text-green-600" />
          <StatCard label="Sudah Dinilai" value={gradedSubmissions} color="text-orange-600" sub={`${completePercent}% Complete`} />
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
                  <span className="text-sm text-muted-foreground">Nilai Tertinggi</span>
                  <span className="text-2xl font-bold text-green-600">{gradeStats.highest}</span>
                </div>
                <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                  <span className="text-sm text-muted-foreground">Nilai Terendah</span>
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
            <h2 className="mb-4 text-lg font-semibold text-foreground">Performa Tugas</h2>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="border-b border-border bg-muted">
                  <tr>
                    <th className="px-6 py-3 text-left text-sm font-semibold text-foreground">Judul Tugas</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Dikumpul</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Dinilai</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Completion Rate</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Nilai Rata-rata</th>
                    <th className="px-6 py-3 text-center text-sm font-semibold text-foreground">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {taskPerformance.length ? (
                    taskPerformance.map((task) => (
                      <tr key={task.id} className="hover:bg-accent">
                        <td className="px-6 py-4 text-sm font-medium text-foreground">{task.title}</td>
                        <td className="px-6 py-4 text-center text-sm text-muted-foreground">{task.submitted}</td>
                        <td className="px-6 py-4 text-center text-sm text-muted-foreground">{task.graded}</td>
                        <td className="px-6 py-4">
                          <div className="flex items-center justify-center">
                            <div className="h-2 w-16 rounded-full bg-muted">
                              <div className="h-2 rounded-full bg-blue-600" style={{ width: `${task.completion_rate}%` }} />
                            </div>
                            <span className="ml-2 text-xs font-medium text-foreground">{task.completion_rate}%</span>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-center">
                          <span className="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{task.average_grade}</span>
                        </td>
                        <td className="px-6 py-4 text-center">
                          <Link href={r('supervisor.analytics.task', task.id)} className="text-sm font-medium text-primary hover:underline">
                            Lihat Detail
                          </Link>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={6} className="px-6 py-8 text-center text-muted-foreground">Belum ada data tugas</td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Mahasiswa Terbaik</h2>
              <div className="space-y-3">
                {topStudents.length ? (
                  topStudents.map((student) => (
                    <div key={student.student_id} className="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 p-3">
                      <div>
                        <p className="text-sm font-medium text-gray-900">{student.student_name}</p>
                        <p className="text-xs text-gray-600">{student.submission_count} submission</p>
                      </div>
                      <div className="text-right">
                        <p className="text-lg font-bold text-green-600">{student.average_grade}</p>
                        <Link href={r('supervisor.analytics.student', student.student_id)} className="text-xs text-green-600 hover:text-green-800">
                          Lihat laporan
                        </Link>
                      </div>
                    </div>
                  ))
                ) : (
                  <p className="py-4 text-center text-sm text-muted-foreground">Belum ada data</p>
                )}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Mahasiswa Perlu Perhatian</h2>
              <div className="space-y-3">
                {atRiskStudents.length ? (
                  atRiskStudents.map((student) => (
                    <div key={student.student_id} className="flex items-center justify-between rounded-lg border border-red-200 bg-red-50 p-3">
                      <div>
                        <p className="text-sm font-medium text-gray-900">{student.student_name}</p>
                        <p className="text-xs text-red-600">{student.reason}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-lg font-bold text-red-600">{student.average_grade}</p>
                        <Link href={r('supervisor.analytics.student', student.student_id)} className="text-xs text-red-600 hover:text-red-800">
                          Lihat laporan
                        </Link>
                      </div>
                    </div>
                  ))
                ) : (
                  <p className="py-4 text-center text-sm text-green-600">Semua mahasiswa baik-baik saja! ✓</p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </SupervisorLayout>
  )
}
