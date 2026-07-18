import { Link, useForm } from '@inertiajs/react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'

const roleOptions = [
  { value: 'admin', label: '👨‍💼 Admin' },
  { value: 'supervisor', label: '👨‍🏫 Pembimbing (Supervisor)' },
  { value: 'student', label: '👨‍🎓 Mahasiswa (Student)' },
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
        ? data.target_roles.filter((r) => r !== role)
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
        <div>
          <h1 className="text-2xl font-bold text-foreground">Edit Pengumuman</h1>
          <p className="text-muted-foreground">Ubah konten pengumuman</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
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
                <Select
                  value={data.priority}
                  onValueChange={(v) => setData('priority', v)}
                >
                  <SelectTrigger id="priority">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="low">🟢 Low</SelectItem>
                    <SelectItem value="normal">🟡 Normal</SelectItem>
                    <SelectItem value="high">🟠 High</SelectItem>
                    <SelectItem value="urgent">🔴 Urgent</SelectItem>
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

              <div className="rounded-lg border border-blue-200 bg-blue-50 p-4">
                {announcement.published_at ? (
                  <p className="text-sm text-blue-800">
                    <strong>Status:</strong>{' '}
                    {new Date(announcement.published_at).toLocaleString('id-ID', {
                      day: '2-digit',
                      month: 'short',
                      year: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit',
                    })}{' '}
                    - Dipublikasikan
                  </p>
                ) : (
                  <p className="text-sm text-blue-800">
                    <strong>Status:</strong> Draft (belum dipublikasikan)
                  </p>
                )}
              </div>

              <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                  Simpan Perubahan
                </Button>
                <Link href={r('admin.announcements.index')}>
                  <Button type="button" variant="secondary">
                    Batal
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
