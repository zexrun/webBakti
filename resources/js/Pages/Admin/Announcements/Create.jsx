import { Link, useForm } from '@inertiajs/react'
import AdminLayout from '@/Layouts/AdminLayout'
import PageHeader from '@/Components/PageHeader'
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

// Semantic priority colors, shared convention with the announcement badges
const priorityOptions = [
  { value: 'low', label: 'Low', hint: 'Informasi umum', dot: 'bg-slate-400' },
  { value: 'normal', label: 'Normal', hint: 'Informasi penting', dot: 'bg-blue-500' },
  { value: 'high', label: 'High', hint: 'Sangat penting', dot: 'bg-amber-500' },
  { value: 'urgent', label: 'Urgent', hint: 'Sangat mendesak', dot: 'bg-red-500' },
]

function PriorityItem({ option }) {
  return (
    <span className="flex items-center gap-2">
      <span className={cn('h-2 w-2 shrink-0 rounded-full', option.dot)} />
      {option.label}
      <span className="text-muted-foreground">— {option.hint}</span>
    </span>
  )
}

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
        ? data.target_roles.filter((rl) => rl !== role)
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
        <PageHeader title="Buat Pengumuman Baru" description="Buat pengumuman untuk pengguna sistem" />

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
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
                <p className="text-xs text-muted-foreground">Maksimum 5000 karakter</p>
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
                        <PriorityItem option={option} />
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

              <label className="flex items-center gap-3">
                <Checkbox
                  checked={data.publish_now}
                  onCheckedChange={(checked) => setData('publish_now', checked)}
                />
                <span className="text-sm font-medium text-foreground">Publikasikan sekarang</span>
              </label>

              <div className="rounded-lg border border-border bg-muted p-4">
                <p className="mb-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">Preview</p>
                <div className="rounded-lg border border-border bg-card p-4">
                  <p className="text-sm font-semibold text-foreground">{data.title || 'Judul Pengumuman'}</p>
                  <p className="mt-2 whitespace-pre-wrap text-sm text-muted-foreground">
                    {data.content || 'Isi konten pengumuman akan muncul di sini...'}
                  </p>
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-5">
                <Button asChild type="button" variant="outline">
                  <Link href={r('admin.announcements.index')}>Batal</Link>
                </Button>
                <Button type="submit" disabled={processing}>
                  Buat Pengumuman
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  )
}
