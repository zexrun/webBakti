import { Link, useForm } from '@inertiajs/react'
import AdminLayout from '@/Layouts/AdminLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Select } from '@/Components/ui/select'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'

const roleOptions = [
  { value: 'admin', label: '👨‍💼 Admin' },
  { value: 'supervisor', label: '👨‍🏫 Pembimbing (Supervisor)' },
  { value: 'student', label: '👨‍🎓 Mahasiswa (Student)' },
]

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    title: '',
    content: '',
    priority: 'normal',
    target_roles: ['admin', 'supervisor', 'student'],
    publish_now: false,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

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
    post(r('admin.announcements.store'))
  }

  return (
    <AdminLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Buat Pengumuman Baru</h1>
          <p className="text-muted-foreground">Buat pengumuman untuk pengguna sistem</p>
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
                  placeholder="Masukkan judul..."
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
                  placeholder="Tulis konten pengumuman..."
                />
                <p className="text-xs text-muted-foreground">Maximum 5000 karakter</p>
                {errors.content && <p className="text-sm text-destructive">{errors.content}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="priority">Prioritas</Label>
                <Select
                  id="priority"
                  value={data.priority}
                  onChange={(e) => setData('priority', e.target.value)}
                >
                  <option value="low">🟢 Low - Informasi umum</option>
                  <option value="normal">🟡 Normal - Informasi penting</option>
                  <option value="high">🟠 High - Sangat penting</option>
                  <option value="urgent">🔴 Urgent - Sangat mendesak</option>
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

              <label className="flex items-center gap-3">
                <Checkbox
                  checked={data.publish_now}
                  onCheckedChange={(checked) => setData('publish_now', checked)}
                />
                <span className="text-sm font-medium text-foreground">Publikasikan sekarang</span>
              </label>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-2 text-sm font-medium text-foreground">Preview:</p>
                <div className="rounded border border-border bg-background p-3">
                  <p className="text-sm font-semibold text-foreground">{data.title || 'Judul Pengumuman'}</p>
                  <p className="mt-3 whitespace-pre-wrap text-sm text-muted-foreground">
                    {data.content || 'Isi konten pengumuman akan muncul di sini...'}
                  </p>
                </div>
              </div>

              <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                  Buat Pengumuman
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
