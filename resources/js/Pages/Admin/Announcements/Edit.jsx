import { Link, useForm } from '@inertiajs/react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

const roleOptions = [
  { value: 'admin', label: 'Admin' },
  { value: 'supervisor', label: 'Pembimbing (Supervisor)' },
  { value: 'student', label: 'Mahasiswa (Student)' },
]

const priorityOptions = [
  { value: 'low', label: 'Low', dot: 'bg-slate-400' },
  { value: 'normal', label: 'Normal', dot: 'bg-blue-500' },
  { value: 'high', label: 'High', dot: 'bg-amber-500' },
  { value: 'urgent', label: 'Urgent', dot: 'bg-red-500' },
]

export default function Edit({ announcement }) {
  const { data, setData, put, processing, errors } = useForm({
    title: announcement.title,
    content: announcement.content,
    priority: announcement.priority,
    target_roles: announcement.target_roles ?? [],
    publish_now: false,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function toggleRole(role) {
    setData(
      'target_roles',
      data.target_roles.includes(role)
        ? data.target_roles.filter((rl) => rl !== role)
        : [...data.target_roles, role],
    )
  }

  function handleSubmit(e) {
    e.preventDefault()
    put(r('admin.announcements.update', announcement.id))
  }

  return (
    <AdminLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <PageHeader title="Edit Pengumuman" description="Ubah konten pengumuman" />

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label htmlFor="title">Judul Pengumuman</Label>
                <Input
                  id="title"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                />
                {errors.title && <p className="text-sm text-destructive">{errors.title}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="content">Konten</Label>
                <Textarea
                  id="content"
                  rows={8}
                  value={data.content}
                  onChange={(e) => setData('content', e.target.value)}
                />
                {errors.content && <p className="text-sm text-destructive">{errors.content}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="priority">Prioritas</Label>
                <Select value={data.priority} onValueChange={(v) => setData('priority', v)}>
                  <SelectTrigger id="priority" className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {priorityOptions.map((option) => (
                      <SelectItem key={option.value} value={option.value}>
                        <span className="flex items-center gap-2">
                          <span className={cn('h-2 w-2 shrink-0 rounded-full', option.dot)} />
                          {option.label}
                        </span>
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.priority && <p className="text-sm text-destructive">{errors.priority}</p>}
              </div>

              <div className="space-y-3">
                <Label>Tampilkan ke Role</Label>
                <div className="space-y-2">
                  {roleOptions.map((role) => (
                    <label key={role.value} className="flex items-center gap-3">
                      <Checkbox
                        checked={data.target_roles.includes(role.value)}
                        onCheckedChange={() => toggleRole(role.value)}
                      />
                      <span className="text-sm text-foreground">{role.label}</span>
                    </label>
                  ))}
                </div>
                {errors.target_roles && <p className="text-sm text-destructive">{errors.target_roles}</p>}
              </div>

              <FlashBanner type="info">
                <strong>Status:</strong>{' '}
                {announcement.published_at
                  ? `Dipublikasikan ${new Date(announcement.published_at).toLocaleString('id-ID', {
                      day: '2-digit',
                      month: 'short',
                      year: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit',
                    })}`
                  : 'Draft (belum dipublikasikan)'}
              </FlashBanner>

              <div className="flex justify-end gap-2 border-t border-border pt-5">
                <Button asChild type="button" variant="outline">
                  <Link href={r('admin.announcements.index')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>
                  Simpan Perubahan
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
