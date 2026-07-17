import { useEffect, useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { LogIn, LogOut, FileText, History, CheckCircle2, Circle, AlertTriangle } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import AttendanceModal from './AttendanceModal'
import ExceptionModal from './ExceptionModal'

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

const statusVariant = {
  present: 'success',
  late: 'destructive',
  pending: 'warning',
}

export default function Index({ todayAttendance, recentAttendances, pendingExceptions }) {
  const [now, setNow] = useState(new Date())
  const [checkInOpen, setCheckInOpen] = useState(false)
  const [checkOutOpen, setCheckOutOpen] = useState(false)
  const [exceptionOpen, setExceptionOpen] = useState(false)
  const [submitting, setSubmitting] = useState(false)

  const r = (name) => (window.route ? window.route(name) : '#')

  useEffect(() => {
    const timer = setInterval(() => setNow(new Date()), 1000)
    return () => clearInterval(timer)
  }, [])

  async function submitAttendance(endpoint, { latitude, longitude, photo, notes }) {
    setSubmitting(true)
    const formData = new FormData()
    formData.append('latitude', latitude)
    formData.append('longitude', longitude)
    formData.append('photo', photo, 'photo.jpg')
    formData.append('notes', notes)

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
      })
      const data = await response.json()

      if (data.success) {
        setCheckInOpen(false)
        setCheckOutOpen(false)
        router.reload()
      } else {
        alert('Error: ' + data.message)
      }
    } catch (err) {
      alert('Terjadi kesalahan saat memproses absensi.')
    } finally {
      setSubmitting(false)
    }
  }

  const presentCount = recentAttendances.filter((a) => a.status === 'present').length
  const lateCount = recentAttendances.filter((a) => a.status === 'late').length

  return (
    <StudentLayout>
      <div className="space-y-6">
        <Card>
          <CardContent className="p-6">
            <h1 className="text-2xl font-bold text-foreground">Absensi Harian</h1>
            <p className="text-muted-foreground">{now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</p>
            <p className="mt-2 text-sm text-muted-foreground">{now.toLocaleTimeString('id-ID')}</p>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-foreground">Status Hari Ini</h3>

            {todayAttendance ? (
              <>
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div className="rounded-lg border border-border bg-muted p-4">
                    <div className="mb-3 flex items-center justify-between">
                      <span className="flex items-center gap-2 text-sm font-medium text-foreground">
                        <LogIn className="h-5 w-5 text-blue-600" /> Check In
                      </span>
                      {todayAttendance.check_in ? (
                        <Badge variant="success" className="gap-1"><CheckCircle2 className="h-3 w-3" /> Sudah</Badge>
                      ) : (
                        <Badge variant="secondary" className="gap-1"><Circle className="h-3 w-3" /> Belum</Badge>
                      )}
                    </div>

                    {todayAttendance.check_in ? (
                      <div className="text-center">
                        <p className="mb-1 text-2xl font-bold text-foreground">
                          {new Date(todayAttendance.check_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                        </p>
                        <p className="text-sm text-muted-foreground">
                          {new Date(todayAttendance.check_in).toLocaleDateString('id-ID')}
                        </p>
                        {todayAttendance.is_late && (
                          <Badge variant="destructive" className="mt-2">Terlambat</Badge>
                        )}
                      </div>
                    ) : (
                      <Button onClick={() => setCheckInOpen(true)} className="w-full">
                        <LogIn className="h-4 w-4" /> Check In Sekarang
                      </Button>
                    )}
                  </div>

                  <div className="rounded-lg border border-border bg-muted p-4">
                    <div className="mb-3 flex items-center justify-between">
                      <span className="flex items-center gap-2 text-sm font-medium text-foreground">
                        <LogOut className="h-5 w-5 text-red-600" /> Check Out
                      </span>
                      {todayAttendance.check_out ? (
                        <Badge variant="success" className="gap-1"><CheckCircle2 className="h-3 w-3" /> Sudah</Badge>
                      ) : (
                        <Badge variant="secondary" className="gap-1"><Circle className="h-3 w-3" /> Belum</Badge>
                      )}
                    </div>

                    {todayAttendance.check_out ? (
                      <div className="text-center">
                        <p className="mb-1 text-2xl font-bold text-foreground">
                          {new Date(todayAttendance.check_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                        </p>
                        <p className="text-sm text-muted-foreground">
                          Durasi: {todayAttendance.working_hours ? Number(todayAttendance.working_hours).toFixed(1) : '0'} jam
                        </p>
                      </div>
                    ) : todayAttendance.check_in ? (
                      <Button onClick={() => setCheckOutOpen(true)} className="w-full bg-red-600 hover:bg-red-700">
                        <LogOut className="h-4 w-4" /> Check Out Sekarang
                      </Button>
                    ) : (
                      <p className="text-center text-sm text-muted-foreground">Check in terlebih dahulu</p>
                    )}
                  </div>
                </div>

                <div className="mt-6 flex items-center justify-between">
                  <Badge variant={statusVariant[todayAttendance.status] ?? 'secondary'}>
                    {todayAttendance.status.charAt(0).toUpperCase() + todayAttendance.status.slice(1)}
                  </Badge>
                  {todayAttendance.notes && <p className="text-sm text-muted-foreground">{todayAttendance.notes}</p>}
                </div>
              </>
            ) : (
              <div className="py-12 text-center">
                <AlertTriangle className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum Absen Hari Ini</h3>
                <p className="mb-6 text-muted-foreground">Silakan lakukan check-in untuk memulai absensi</p>
                <Button onClick={() => setCheckInOpen(true)}>
                  <LogIn className="h-4 w-4" /> Check In Sekarang
                </Button>
              </div>
            )}
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                <CheckCircle2 className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Hadir (7 hari)</p>
                <p className="text-2xl font-bold text-foreground">{presentCount}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                <AlertTriangle className="h-6 w-6 text-orange-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Terlambat (7 hari)</p>
                <p className="text-2xl font-bold text-foreground">{lateCount}</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="flex items-center gap-4 p-6">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <FileText className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-muted-foreground">Pengajuan Pending</p>
                <p className="text-2xl font-bold text-foreground">{pendingExceptions ?? 0}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <div className="flex items-center justify-between border-b border-border p-6">
            <h3 className="text-lg font-semibold text-foreground">Riwayat Terbaru</h3>
            <Link href={r('student.attendance.history')} className="text-sm font-medium text-primary hover:underline">
              Lihat Semua →
            </Link>
          </div>
          <CardContent className="p-0">
            {recentAttendances.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Check In</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Check Out</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                      <th className="hidden px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground lg:table-cell">Durasi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {recentAttendances.map((attendance) => (
                      <tr key={attendance.id} className="hover:bg-accent">
                        <td className="whitespace-nowrap px-6 py-4">
                          <p className="text-sm font-medium text-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                          </p>
                          <p className="text-sm text-muted-foreground">
                            {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                          </p>
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          {attendance.check_in ? (
                            <>
                              <p className="text-sm font-medium text-foreground">
                                {new Date(attendance.check_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                              </p>
                              {attendance.is_late && <p className="text-xs text-destructive">Terlambat</p>}
                            </>
                          ) : (
                            <span className="text-muted-foreground">-</span>
                          )}
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-foreground md:table-cell">
                          {attendance.check_out ? new Date(attendance.check_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                        </td>
                        <td className="whitespace-nowrap px-6 py-4">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                          </Badge>
                        </td>
                        <td className="hidden whitespace-nowrap px-6 py-4 text-sm text-foreground lg:table-cell">
                          {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <History className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-1 text-sm font-medium text-foreground">Belum ada data absensi</h3>
                <p className="text-sm text-muted-foreground">Mulai dengan melakukan check-in hari ini.</p>
              </div>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-foreground">Aksi Cepat</h3>
            <div className="flex flex-wrap gap-4">
              <Button onClick={() => setExceptionOpen(true)} className="bg-yellow-600 hover:bg-yellow-700">
                <FileText className="h-4 w-4" /> Ajukan Izin/Sakit
              </Button>
              <Link href={r('student.attendance.history')}>
                <Button variant="secondary">
                  <History className="h-4 w-4" /> Lihat Riwayat Lengkap
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>

      <AttendanceModal
        open={checkInOpen}
        onClose={() => setCheckInOpen(false)}
        title="Check In"
        subtitle="Lakukan absensi masuk"
        accent="bg-blue-600 hover:bg-blue-700"
        notesPlaceholder={{ optional: true, text: 'Tulis aktivitas atau catatan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-in'), payload)}
      />

      <AttendanceModal
        open={checkOutOpen}
        onClose={() => setCheckOutOpen(false)}
        title="Check Out"
        subtitle="Lakukan absensi keluar"
        accent="bg-red-600 hover:bg-red-700"
        notesPlaceholder={{ optional: false, text: 'Ringkasan kegiatan yang telah dikerjakan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-out'), payload)}
      />

      <ExceptionModal open={exceptionOpen} onClose={() => setExceptionOpen(false)} />
    </StudentLayout>
  )
}
