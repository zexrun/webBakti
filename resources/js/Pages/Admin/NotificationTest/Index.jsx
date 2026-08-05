import { useState } from 'react'
import { useForm, usePage } from '@inertiajs/react'
import { Send, TestTube2 } from 'lucide-react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import RecipientPicker from '@/Components/RecipientPicker'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Index({ users, catalog }) {
  const { flash } = usePage().props
  const { data, setData, post, processing, errors } = useForm({
    user_id: '',
    notification_key: '',
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('admin.notification-test.send'))
  }

  return (
    <AdminLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <PageHeader
          title="Uji Notifikasi"
          description="Kirim notifikasi in-app dan email secara manual, tanpa harus menyelesaikan alur sesungguhnya (grading, persetujuan presensi, dll)"
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}
        {flash?.error && <FlashBanner type="danger">{flash.error}</FlashBanner>}

        <Card>
          <CardHeader className="border-b">
            <CardTitle className="flex items-center gap-2">
              <TestTube2 className="h-4 w-4 text-muted-foreground" /> Kirim Notifikasi Uji
            </CardTitle>
            <CardDescription>
              Menggunakan data contoh yang sudah ada di database (submission, task, presensi, dll) sebagai subjek notifikasi.
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-5 pt-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label htmlFor="user_id">Kirim ke</Label>
                <RecipientPicker
                  recipients={users}
                  value={data.user_id}
                  onChange={(id) => setData('user_id', id)}
                  placeholder="Pilih penerima..."
                />
                {errors.user_id && <p className="text-sm text-destructive">{errors.user_id}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="notification_key">Jenis Notifikasi</Label>
                <Select value={data.notification_key} onValueChange={(v) => setData('notification_key', v)}>
                  <SelectTrigger id="notification_key" className="w-full">
                    <SelectValue placeholder="Pilih jenis notifikasi..." />
                  </SelectTrigger>
                  <SelectContent>
                    {catalog.map((entry) => (
                      <SelectItem key={entry.key} value={entry.key}>
                        {entry.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.notification_key && <p className="text-sm text-destructive">{errors.notification_key}</p>}
              </div>

              <div className="flex justify-end border-t border-border pt-5">
                <Button type="submit" disabled={processing || !data.user_id || !data.notification_key}>
                  <Send /> Kirim
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <p className="text-xs text-muted-foreground">
          Notifikasi akan langsung muncul di bell in-app milik penerima (dalam ±10 detik lewat polling) dan
          terkirim ke email penerima sesuai konfigurasi SMTP saat ini. Jika data contoh yang dibutuhkan belum
          ada di database (misal belum ada submission yang dinilai), pengiriman akan gagal dengan pesan error.
        </p>
      </div>
    </AdminLayout>
  )
}
