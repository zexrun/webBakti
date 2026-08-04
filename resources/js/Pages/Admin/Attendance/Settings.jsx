import { useEffect } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { Clock, MapPin, Camera, ArrowLeft, Save, LocateFixed } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'
import { useGeolocation } from '@/hooks/useGeolocation'

export default function Settings({ settings }) {
  const { flash } = usePage().props
  const geo = useGeolocation()

  const { data, setData, post, processing, errors } = useForm({
    work_start_time: settings?.work_start_time?.slice(0, 5) ?? '08:00',
    work_end_time: settings?.work_end_time?.slice(0, 5) ?? '17:00',
    late_tolerance_minutes: settings?.late_tolerance_minutes ?? 15,
    require_location: settings?.require_location ?? false,
    office_latitude: settings?.office_latitude ?? '',
    office_longitude: settings?.office_longitude ?? '',
    location_radius_meters: settings?.location_radius_meters ?? 100,
    office_address: settings?.office_address ?? '',
    require_photo: settings?.require_photo ?? false,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('admin.attendance.settings.update'))
  }

  useEffect(() => {
    if (geo.position) {
      setData('office_latitude', geo.position.latitude)
      setData('office_longitude', geo.position.longitude)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [geo.position])

  return (
    <AdminLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <PageHeader
          title="Pengaturan Presensi"
          description="Kelola pengaturan sistem absensi dan konfigurasi operasional"
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}
        {flash?.error && <FlashBanner type="danger">{flash.error}</FlashBanner>}
        {geo.status === 'success' && (
          <FlashBanner type="success">
            Lokasi berhasil didapatkan! Latitude: {geo.position.latitude.toFixed(6)}, Longitude: {geo.position.longitude.toFixed(6)}
          </FlashBanner>
        )}
        {geo.status === 'error' && <FlashBanner type="danger">{geo.message}</FlashBanner>}

        <form onSubmit={handleSubmit} className="space-y-6">
          <Card>
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Clock className="h-4 w-4 text-blue-600 dark:text-blue-400" /> Jam Kerja
              </CardTitle>
              <CardDescription>Atur jam kerja dan toleransi keterlambatan</CardDescription>
            </CardHeader>
            <CardContent className="grid grid-cols-1 gap-5 pt-6 md:grid-cols-3">
              <div className="space-y-2">
                <Label htmlFor="work_start_time">Jam Masuk</Label>
                <Input
                  id="work_start_time"
                  type="time"
                  value={data.work_start_time}
                  onChange={(e) => setData('work_start_time', e.target.value)}
                />
                {errors.work_start_time && <p className="text-sm text-destructive">{errors.work_start_time}</p>}
              </div>
              <div className="space-y-2">
                <Label htmlFor="work_end_time">Jam Pulang</Label>
                <Input
                  id="work_end_time"
                  type="time"
                  value={data.work_end_time}
                  onChange={(e) => setData('work_end_time', e.target.value)}
                />
                {errors.work_end_time && <p className="text-sm text-destructive">{errors.work_end_time}</p>}
              </div>
              <div className="space-y-2">
                <Label htmlFor="late_tolerance_minutes">Toleransi Keterlambatan (Menit)</Label>
                <Input
                  id="late_tolerance_minutes"
                  type="number"
                  min={0}
                  max={120}
                  value={data.late_tolerance_minutes}
                  onChange={(e) => setData('late_tolerance_minutes', e.target.value)}
                />
                {errors.late_tolerance_minutes && <p className="text-sm text-destructive">{errors.late_tolerance_minutes}</p>}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <MapPin className="h-4 w-4 text-green-600 dark:text-green-400" /> Pengaturan Lokasi
              </CardTitle>
              <CardDescription>Konfigurasi verifikasi lokasi untuk absensi</CardDescription>
            </CardHeader>
            <CardContent className="space-y-5 pt-6">
              <label className="flex items-center gap-3 rounded-lg bg-muted p-4">
                <Checkbox
                  checked={data.require_location}
                  onCheckedChange={(checked) => setData('require_location', checked)}
                />
                <span className="text-sm font-medium text-foreground">Wajibkan Verifikasi Lokasi</span>
              </label>

              <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="office_latitude">Latitude Kantor</Label>
                  <Input
                    id="office_latitude"
                    type="number"
                    step="0.00000001"
                    placeholder="Contoh: -6.200000"
                    value={data.office_latitude}
                    onChange={(e) => setData('office_latitude', e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="office_longitude">Longitude Kantor</Label>
                  <Input
                    id="office_longitude"
                    type="number"
                    step="0.00000001"
                    placeholder="Contoh: 106.816666"
                    value={data.office_longitude}
                    onChange={(e) => setData('office_longitude', e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="location_radius_meters">Radius Lokasi (Meter)</Label>
                  <Input
                    id="location_radius_meters"
                    type="number"
                    min={10}
                    max={5000}
                    value={data.location_radius_meters}
                    onChange={(e) => setData('location_radius_meters', e.target.value)}
                  />
                  {errors.location_radius_meters && <p className="text-sm text-destructive">{errors.location_radius_meters}</p>}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="office_address">Alamat Kantor</Label>
                  <Input
                    id="office_address"
                    value={data.office_address}
                    onChange={(e) => setData('office_address', e.target.value)}
                    placeholder="Masukkan alamat kantor"
                  />
                </div>
              </div>

              <Button type="button" variant="outline" onClick={() => geo.request()} disabled={geo.status === 'loading'}>
                <LocateFixed /> {geo.status === 'loading' ? 'Mendapatkan Lokasi...' : 'Gunakan Lokasi Saat Ini'}
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="border-b">
              <CardTitle className="flex items-center gap-2">
                <Camera className="h-4 w-4 text-orange-600 dark:text-orange-400" /> Pengaturan Foto
              </CardTitle>
              <CardDescription>Konfigurasi persyaratan foto untuk absensi</CardDescription>
            </CardHeader>
            <CardContent className="pt-6">
              <label className="flex items-center gap-3 rounded-lg bg-muted p-4">
                <Checkbox
                  checked={data.require_photo}
                  onCheckedChange={(checked) => setData('require_photo', checked)}
                />
                <span className="text-sm font-medium text-foreground">Wajibkan Foto Selfie saat Absen</span>
              </label>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-2">
            <Button asChild type="button" variant="outline">
              <Link href={r('admin.attendance.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
            <Button type="submit" disabled={processing}>
              <Save /> Simpan Pengaturan
            </Button>
          </div>
        </form>
      </div>
    </AdminLayout>
  )
}
