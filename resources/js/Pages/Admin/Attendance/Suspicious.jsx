import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { ArrowLeft, AlertTriangle, CheckCircle2 } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import Pagination from '@/Components/Pagination'
import ApprovalModal from './ApprovalModal'

const statusVariant = {
  present: 'success',
  late: 'warning',
  absent: 'destructive',
}

export default function Suspicious({ suspiciousAttendances }) {
  const [modalItem, setModalItem] = useState(null)
  const r = (name) => (window.route ? window.route(name) : '#')

  return (
    <AdminLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="flex items-center gap-2 text-2xl font-bold text-foreground">
              <AlertTriangle className="h-6 w-6 text-orange-500" /> Kehadiran Mencurigakan
            </h1>
            <p className="text-muted-foreground">Kehadiran yang memerlukan review manual karena anomali terdeteksi</p>
          </div>
          <Link href={r('admin.attendance.index')}>
            <button type="button" className="inline-flex items-center gap-2 rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-secondary-foreground hover:bg-secondary/80">
              <ArrowLeft className="h-4 w-4" /> Kembali
            </button>
          </Link>
        </div>

        {suspiciousAttendances.data.length === 0 ? (
          <Card className="border-green-200 bg-green-50">
            <CardContent className="p-8 text-center">
              <CheckCircle2 className="mx-auto mb-2 h-10 w-10 text-green-600" />
              <p className="text-lg text-green-800">Tidak ada kehadiran mencurigakan</p>
              <p className="mt-2 text-sm text-green-600">Semua data kehadiran terlihat normal</p>
            </CardContent>
          </Card>
        ) : (
          <Card>
            <CardContent className="p-0">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-red-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Nama</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Tanggal</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Waktu Check-in</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Alasan Anomali</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {suspiciousAttendances.data.map((attendance) => (
                      <tr key={attendance.id} className="hover:bg-accent">
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                              <span className="text-xs font-semibold text-red-700">{attendance.user?.name?.charAt(0)?.toUpperCase()}</span>
                            </div>
                            <div>
                              <p className="font-medium text-foreground">{attendance.user?.name}</p>
                              <p className="text-xs text-muted-foreground">{attendance.user?.email}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">
                          {new Date(attendance.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                        </td>
                        <td className="px-6 py-4 text-sm text-muted-foreground">
                          {attendance.check_in_time ? new Date(attendance.check_in_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                        </td>
                        <td className="px-6 py-4">
                          <Badge variant={statusVariant[attendance.status] ?? 'secondary'}>
                            {attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                          </Badge>
                        </td>
                        <td className="px-6 py-4 text-sm">
                          <div className="space-y-1">
                            {attendance.location_notes && (
                              <p className="text-red-600">📍 {attendance.location_notes}</p>
                            )}
                            {attendance.latitude && attendance.longitude && (
                              <p className="text-xs text-muted-foreground">
                                Koordinat: {Number(attendance.latitude).toFixed(4)}, {Number(attendance.longitude).toFixed(4)}
                              </p>
                            )}
                          </div>
                        </td>
                        <td className="px-6 py-4 text-sm">
                          <button
                            type="button"
                            onClick={() => setModalItem({
                              id: attendance.id,
                              userName: attendance.user?.name,
                              date: new Date(attendance.date).toLocaleDateString('id-ID'),
                            })}
                            className="rounded bg-primary px-3 py-1 text-xs font-medium text-primary-foreground hover:bg-primary/90"
                          >
                            Review
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div className="border-t border-border p-4">
                <Pagination links={suspiciousAttendances.links} />
              </div>
            </CardContent>
          </Card>
        )}
      </div>

      <ApprovalModal
        open={Boolean(modalItem)}
        onClose={() => setModalItem(null)}
        type="suspicious"
        item={modalItem}
        notesRequired
      />
    </AdminLayout>
  )
}
