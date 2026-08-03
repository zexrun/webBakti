import { Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

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
    post(r('supervisor.bulk.automation-settings.submit'))
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <PageHeader
          title="Pengaturan Automasi"
          description="Kelola pengingat otomatis untuk deadline dan submission"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.dashboard')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-3 rounded-lg border border-border p-4">
                <label className="flex items-center gap-3">
                  <Checkbox
                    checked={data.auto_deadline_reminder}
                    onCheckedChange={(checked) => setData('auto_deadline_reminder', checked)}
                  />
                  <span className="text-sm font-medium text-foreground">Aktifkan Pengingat Deadline Otomatis</span>
                </label>
                <p className="text-xs text-muted-foreground">Kirim notifikasi ke mahasiswa sebelum deadline tugas tiba</p>

                {data.auto_deadline_reminder && (
                  <div className="space-y-2 border-t border-border pt-3">
                    <Label>Kirim Pengingat (hari sebelum deadline)</Label>
                    <div className="flex flex-wrap gap-2">
                      {reminderDayOptions.map((day) => {
                        const active = data.reminder_days_before.includes(day)
                        return (
                          <label
                            key={day}
                            className={cn(
                              'flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors duration-150',
                              active ? 'border-primary bg-indigo-50 dark:bg-indigo-500/10' : 'border-border hover:bg-muted',
                            )}
                          >
                            <Checkbox checked={active} onCheckedChange={() => toggleReminderDay(day)} />
                            <span className="text-foreground">{day} hari</span>
                          </label>
                        )
                      })}
                    </div>
                    {errors.reminder_days_before && <p className="text-sm text-destructive">{errors.reminder_days_before}</p>}
                  </div>
                )}
              </div>

              <div className="space-y-3 rounded-lg border border-border p-4">
                <label className="flex items-center gap-3">
                  <Checkbox
                    checked={data.auto_submission_reminder}
                    onCheckedChange={(checked) => setData('auto_submission_reminder', checked)}
                  />
                  <span className="text-sm font-medium text-foreground">Aktifkan Pengingat Submission Otomatis</span>
                </label>
                <p className="text-xs text-muted-foreground">Kirim notifikasi ke mahasiswa yang belum submit tugas</p>

                {data.auto_submission_reminder && (
                  <div className="space-y-2 border-t border-border pt-3">
                    <Label htmlFor="submission_reminder_days">Kirim Pengingat Setiap (hari)</Label>
                    <Input
                      id="submission_reminder_days"
                      type="number"
                      min={1}
                      max={30}
                      value={data.submission_reminder_days}
                      onChange={(e) => setData('submission_reminder_days', e.target.value)}
                      className="max-w-[12rem]"
                    />
                    {errors.submission_reminder_days && <p className="text-sm text-destructive">{errors.submission_reminder_days}</p>}
                  </div>
                )}
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-5">
                <Button asChild type="button" variant="outline">
                  <Link href={r('supervisor.dashboard')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>Simpan Pengaturan</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
