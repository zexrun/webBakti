import { useEffect, useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'
import { LogIn, LogOut, FileText, History, CheckCircle2, Circle, AlertTriangle, Clock } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import StatCard from '@/Components/StatCard'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import AttendanceModal from './AttendanceModal'
import ExceptionModal from './ExceptionModal'

const statusVariant = {
  present: 'success',
  late: 'destructive',
  pending: 'warning',
}

const statusLabel = {
  present: 'Hadir',
  late: 'Terlambat',
  pending: 'Pending',
  absent: 'Tidak Hadir',
}

function time(value) {
  return value ? new Date(value).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
}

// Check-in / check-out status panel; the check-out CTA keeps a red accent
// as a deliberately distinct action.
function ClockPanel({ icon: Icon, label, timestamp, done, isLate, subtitle, cta }) {
  return (
    <div className="rounded-lg border border-border bg-muted p-4">
      <div className="mb-3 flex items-center justify-between">
        <span className="flex items-center gap-2 text-sm font-medium text-foreground">
          <Icon className="h-4 w-4 text-muted-foreground" /> {label}
        </span>
        {done ? (
          <Badge variant="success"><CheckCircle2 /> Sudah</Badge>
        ) : (
          <Badge variant="secondary"><Circle /> Belum</Badge>
        )}
      </div>
      {timestamp ? (
        <div className="text-center">
          <p className="text-2xl font-bold tabular-nums text-foreground">{time(timestamp)}</p>
          {subtitle && <p className="text-sm text-muted-foreground">{subtitle}</p>}
          {isLate && <Badge variant="destructive" className="mt-2">Terlambat</Badge>}
        </div>
      ) : (
        cta
      )}
    </div>
  )
}

export default function Index({ todayAttendance, recentAttendances, pendingExceptions }) {
  const [now, setNow] = useState(new Date())
  const [checkInOpen, setCheckInOpen] = useState(false)
  const [checkOutOpen, setCheckOutOpen] = useState(false)
  const [exceptionOpen, setExceptionOpen] = useState(false)
  const [submitting, setSubmitting] = useState(false)

  const r = (name) => (window.route ? window.route(name) : '#')

  const { auth } = usePage().props
  const hasProfilePhoto = Boolean(auth?.user?.profile_photo_url)

  useEffect(() => {
    const timer = setInterval(() => setNow(new Date()), 1000)
    return () => clearInterval(timer)
  }, [])

  async function submitAttendance(endpoint, { latitude, longitude, photo, notes, faceDescriptor }) {
    setSubmitting(true)
    const formData = new FormData()
    formData.append('latitude', latitude)
    formData.append('longitude', longitude)
    formData.append('photo', photo, 'photo.jpg')
    formData.append('notes', notes)
    if (faceDescriptor) {
      formData.append('face_descriptor', JSON.stringify(faceDescriptor))
    }

    try {
      const { data } = await window.axios.post(endpoint, formData)

      if (data.success) {
        setCheckInOpen(false)
        setCheckOutOpen(false)
        router.reload()
      } else {
        alert('Error: ' + data.message)
      }
    } catch (err) {
      // Non-2xx responses (e.g. 403 suspicious location, 500 server error)
      // land here with axios, unlike the previous fetch()-based version -
      // surface the server's own message when it sent one, so this stays
      // as informative as before rather than regressing to a generic alert.
      alert('Error: ' + (err.response?.data?.message ?? 'Terjadi kesalahan saat memproses absensi.'))
    } finally {
      setSubmitting(false)
    }
  }

  const presentCount = recentAttendances.filter((a) => a.status === 'present').length
  const lateCount = recentAttendances.filter((a) => a.status === 'late').length

  return (
    <StudentLayout>
      <div className="space-y-6">
        <PageHeader
          title="Absensi Harian"
          description={now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}
          actions={
            <div className="flex items-center gap-2 pb-1 font-mono text-lg tabular-nums text-muted-foreground">
              <Clock className="h-4 w-4" />
              {now.toLocaleTimeString('id-ID')}
            </div>
          }
        />

        {!hasProfilePhoto && (
          <FlashBanner type="warning">
            Anda belum mengupload foto profil. Foto profil diperlukan untuk verifikasi wajah saat presensi.{' '}
            <Link href={r('profile.edit')} className="underline">Upload foto profil sekarang</Link>.
          </FlashBanner>
        )}

        <Card>
          <CardContent className="p-6">
            <h3 className="mb-4 text-base font-semibold text-foreground">Status Hari Ini</h3>

            {todayAttendance ? (
              <>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <ClockPanel
                    icon={LogIn}
                    label="Check In"
                    timestamp={todayAttendance.check_in}
                    done={Boolean(todayAttendance.check_in)}
                    isLate={todayAttendance.is_late}
                    subtitle={todayAttendance.check_in && new Date(todayAttendance.check_in).toLocaleDateString('id-ID')}
                    cta={
                      hasProfilePhoto ? (
                        <Button onClick={() => setCheckInOpen(true)} className="w-full">
                          <LogIn /> Check In Sekarang
                        </Button>
                      ) : (
                        <p className="text-center text-sm text-muted-foreground">Upload foto profil untuk dapat check-in</p>
                      )
                    }
                  />

                  <ClockPanel
                    icon={LogOut}
                    label="Check Out"
                    timestamp={todayAttendance.check_out}
                    done={Boolean(todayAttendance.check_out)}
                    subtitle={todayAttendance.check_out && `Durasi: ${todayAttendance.working_hours ? Number(todayAttendance.working_hours).toFixed(1) : '0'} jam`}
                    cta={
                      !hasProfilePhoto ? (
                        <p className="text-center text-sm text-muted-foreground">Upload foto profil untuk dapat check-out</p>
                      ) : todayAttendance.check_in ? (
                        <Button onClick={() => setCheckOutOpen(true)} variant="destructive" className="w-full">
                          <LogOut /> Check Out Sekarang
                        </Button>
                      ) : (
                        <p className="text-center text-sm text-muted-foreground">Check in terlebih dahulu</p>
                      )
                    }
                  />
                </div>

                <div className="mt-4 flex items-center justify-between gap-3">
                  <Badge variant={statusVariant[todayAttendance.status] ?? 'secondary'}>
                    {statusLabel[todayAttendance.status] ?? todayAttendance.status}
                  </Badge>
                  {todayAttendance.notes && <p className="truncate text-sm text-muted-foreground">{todayAttendance.notes}</p>}
                </div>
              </>
            ) : (
              <EmptyState
                icon={AlertTriangle}
                title="Belum Absen Hari Ini"
                description="Silakan lakukan check-in untuk memulai absensi."
                action={
                  hasProfilePhoto ? (
                    <Button onClick={() => setCheckInOpen(true)}>
                      <LogIn /> Check In Sekarang
                    </Button>
                  ) : (
                    <Button asChild variant="outline">
                      <Link href={r('profile.edit')}>Upload Foto Profil</Link>
                    </Button>
                  )
                }
              />
            )}
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <StatCard icon={CheckCircle2} label="Hadir (7 hari)" value={presentCount} tone="green" />
          <StatCard icon={AlertTriangle} label="Terlambat (7 hari)" value={lateCount} tone="orange" />
          <StatCard icon={FileText} label="Pengajuan Pending" value={pendingExceptions ?? 0} tone="blue" />
        </div>

        <Card>
          <div className="flex items-center justify-between border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Riwayat Terbaru</h3>
            <Button asChild variant="ghost" size="xs">
              <Link href={r('student.attendance.history')}>Lihat Semua</Link>
            </Button>
          </div>
          {recentAttendances.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Tanggal</TableHead>
                  <TableHead>Check In</TableHead>
                  <TableHead className="hidden md:table-cell">Check Out</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="hidden text-right lg:table-cell">Durasi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {recentAttendances.map((attendance) => (
                  <TableRow key={attendance.id}>
                    <TableCell>
                      <p className="tabular-nums text-foreground">
                        {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {new Date(attendance.date).toLocaleDateString('id-ID', { weekday: 'long' })}
                      </p>
                    </TableCell>
                    <TableCell>
                      <span className="font-medium tabular-nums text-foreground">{time(attendance.check_in)}</span>
                      {attendance.is_late && <p className="text-xs text-red-600 dark:text-red-400">Terlambat</p>}
                    </TableCell>
                    <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                      {time(attendance.check_out)}
                    </TableCell>
                    <TableCell>
                      <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                        {statusLabel[attendance.status] ?? attendance.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="hidden text-right tabular-nums text-muted-foreground lg:table-cell">
                      {attendance.working_hours ? `${Number(attendance.working_hours).toFixed(1)} jam` : '-'}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState
              icon={History}
              title="Belum ada data absensi"
              description="Mulai dengan melakukan check-in hari ini."
            />
          )}
        </Card>

        <div className="flex flex-wrap gap-2">
          <Button onClick={() => setExceptionOpen(true)} variant="outline">
            <FileText /> Ajukan Izin/Sakit
          </Button>
          <Button asChild variant="outline">
            <Link href={r('student.attendance.history')}>
              <History /> Lihat Riwayat Lengkap
            </Link>
          </Button>
        </div>
      </div>

      <AttendanceModal
        open={checkInOpen}
        onClose={() => setCheckInOpen(false)}
        title="Check In"
        subtitle="Lakukan absensi masuk"
        notesPlaceholder={{ optional: true, text: 'Tulis aktivitas atau catatan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-in'), payload)}
        requireFaceCheck
      />

      <AttendanceModal
        open={checkOutOpen}
        onClose={() => setCheckOutOpen(false)}
        title="Check Out"
        subtitle="Lakukan absensi keluar"
        destructive
        notesPlaceholder={{ optional: false, text: 'Ringkasan kegiatan yang telah dikerjakan hari ini...' }}
        submitting={submitting}
        onSubmit={(payload) => submitAttendance(r('student.attendance.check-out'), payload)}
        requireFaceCheck
      />

      <ExceptionModal open={exceptionOpen} onClose={() => setExceptionOpen(false)} />
    </StudentLayout>
  )
}
