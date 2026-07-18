import { Link, useForm, usePage } from '@inertiajs/react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'

const reminderDayOptions = [1, 3, 7, 14]

export default function AutomationSettings({ settings }) {
  const { flash } = usePage().props
  const { data, setData, post, processing, errors } = useForm({
    auto_deadline_reminder: settings.auto_deadline_reminder,
    reminder_days_before: settings.reminder_days_before ?? [],
    auto_submission_reminder: settings.auto_submission_reminder,
    submission_reminder_days: settings.submission_reminder_days ?? 7,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function toggleReminderDay(day) {
    setData('reminder_days_before', data.reminder_days_before.includes(day)
      ? data.reminder_days_before.filter((d) => d !== day)
      : [...data.reminder_days_before, day])
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.bulk.automation-settings'))
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Pengaturan Automasi</h1>
            <p className="text-muted-foreground">Kelola pengingat otomatis untuk deadline dan submission</p>
          </div>
          <Link href={r('supervisor.dashboard')} className="text-sm font-medium text-primary hover:underline">
            ← Kembali
          </Link>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}

        <Card>
          <CardContent className="space-y-6 p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-3 rounded-lg bg-muted p-4">
                <label className="flex items-center gap-3">
                  <Checkbox
                    checked={data.auto_deadline_reminder}
                    onCheckedChange={(checked) => setData('auto_deadline_reminder', checked)}
                  />
                  <span className="text-sm font-medium text-foreground">Aktifkan Pengingat Deadline Otomatis</span>
                </label>
                <p className="text-xs text-muted-foreground">Kirim notifikasi ke mahasiswa sebelum deadline tugas tiba</p>

                {data.auto_deadline_reminder && (
                  <div className="space-y-2 pt-2">
                    <Label>Kirim Pengingat (hari sebelum deadline)</Label>
                    <div className="flex flex-wrap gap-3">
                      {reminderDayOptions.map((day) => (
                        <label key={day} className="flex items-center gap-2 rounded-lg border border-border px-3 py-2">
                          <input
                            type="checkbox"
                            checked={data.reminder_days_before.includes(day)}
                            onChange={() => toggleReminderDay(day)}
                          />
                          <span className="text-sm text-foreground">{day} hari</span>
                        </label>
                      ))}
                    </div>
                    {errors.reminder_days_before && <p className="text-sm text-destructive">{errors.reminder_days_before}</p>}
                  </div>
                )}
              </div>

              <div className="space-y-3 rounded-lg bg-muted p-4">
                <label className="flex items-center gap-3">
                  <Checkbox
                    checked={data.auto_submission_reminder}
                    onCheckedChange={(checked) => setData('auto_submission_reminder', checked)}
                  />
                  <span className="text-sm font-medium text-foreground">Aktifkan Pengingat Submission Otomatis</span>
                </label>
                <p className="text-xs text-muted-foreground">Kirim notifikasi ke mahasiswa yang belum submit tugas</p>

                {data.auto_submission_reminder && (
                  <div className="space-y-2 pt-2">
                    <Label htmlFor="submission_reminder_days">Kirim Pengingat Setiap (hari)</Label>
                    <Input
                      id="submission_reminder_days"
                      type="number"
                      min={1}
                      max={30}
                      value={data.submission_reminder_days}
                      onChange={(e) => setData('submission_reminder_days', e.target.value)}
                      className="max-w-xs"
                    />
                    {errors.submission_reminder_days && <p className="text-sm text-destructive">{errors.submission_reminder_days}</p>}
                  </div>
                )}
              </div>

              <div className="flex gap-3">
                <Button type="submit" disabled={processing}>Simpan Pengaturan</Button>
                <Link href={r('supervisor.dashboard')}>
                  <Button type="button" variant="secondary">Batal</Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
