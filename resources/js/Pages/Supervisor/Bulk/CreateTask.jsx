import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Plus, Trash2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Checkbox } from '@/Components/ui/checkbox'
import { Button } from '@/Components/ui/button'

function emptyTask() {
  return { title: '', description: '', due_date: '', student_ids: [] }
}

export default function CreateTask({ students }) {
  const [tasks, setTasks] = useState([emptyTask()])
  const { post, processing, errors } = useForm()
  const r = (name) => (window.route ? window.route(name) : '#')

  function updateTask(index, field, value) {
    setTasks((prev) => prev.map((t, i) => (i === index ? { ...t, [field]: value } : t)))
  }

  function toggleStudent(index, studentId) {
    setTasks((prev) =>
      prev.map((t, i) => {
        if (i !== index) return t
        const has = t.student_ids.includes(studentId)
        return { ...t, student_ids: has ? t.student_ids.filter((id) => id !== studentId) : [...t.student_ids, studentId] }
      })
    )
  }

  function addTask() {
    setTasks((prev) => [...prev, emptyTask()])
  }

  function removeTask(index) {
    setTasks((prev) => prev.filter((_, i) => i !== index))
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.bulk.store-task'), { data: { tasks }, forceFormData: false })
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-4xl space-y-6">
        <PageHeader
          title="Buat Tugas Massal"
          description="Buat multiple tugas sekaligus untuk mahasiswa"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.tasks.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <FlashBanner type="info">
          <strong>Panduan:</strong> Tambahkan tugas di bawah, pilih mahasiswa untuk setiap tugas, kemudian submit sekaligus.
        </FlashBanner>

        <form onSubmit={handleSubmit} className="space-y-6">
          {tasks.map((task, index) => (
            <Card key={index}>
              <div className="flex items-center justify-between border-b border-border px-6 py-3.5">
                <h3 className="text-base font-semibold text-foreground">Tugas {index + 1}</h3>
                {tasks.length > 1 && (
                  <Button
                    type="button"
                    size="xs"
                    variant="ghost"
                    className="text-destructive hover:text-destructive"
                    onClick={() => removeTask(index)}
                  >
                    <Trash2 /> Hapus
                  </Button>
                )}
              </div>
              <CardContent className="space-y-5 pt-6">
                <div className="space-y-2">
                  <Label htmlFor={`title-${index}`}>Judul Tugas</Label>
                  <Input
                    id={`title-${index}`}
                    value={task.title}
                    onChange={(e) => updateTask(index, 'title', e.target.value)}
                    placeholder="Contoh: Membuat dokumentasi API"
                    required
                  />
                  {errors[`tasks.${index}.title`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.title`]}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor={`desc-${index}`}>Deskripsi (Opsional)</Label>
                  <Textarea
                    id={`desc-${index}`}
                    rows={3}
                    value={task.description}
                    onChange={(e) => updateTask(index, 'description', e.target.value)}
                    placeholder="Masukkan deskripsi tugas..."
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor={`due-${index}`}>Deadline</Label>
                  <Input
                    id={`due-${index}`}
                    type="date"
                    value={task.due_date}
                    onChange={(e) => updateTask(index, 'due_date', e.target.value)}
                    required
                    className="w-56"
                  />
                  {errors[`tasks.${index}.due_date`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.due_date`]}</p>}
                </div>

                <div className="space-y-2">
                  <Label>Pilih Mahasiswa (minimal 1)</Label>
                  <div className="max-h-64 overflow-y-auto rounded-lg border border-border">
                    {students.length ? (
                      students.map((student) => (
                        <label key={student.id} className="flex cursor-pointer items-center gap-3 border-b border-border px-3 py-2.5 transition-colors duration-150 last:border-b-0 hover:bg-muted">
                          <Checkbox
                            checked={task.student_ids.includes(student.id)}
                            onCheckedChange={() => toggleStudent(index, student.id)}
                          />
                          <span className="text-sm text-foreground">
                            {student.user.name} <span className="text-muted-foreground">({student.nim})</span>
                          </span>
                        </label>
                      ))
                    ) : (
                      <p className="p-4 text-sm text-muted-foreground">Belum ada mahasiswa</p>
                    )}
                  </div>
                  {errors[`tasks.${index}.student_ids`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.student_ids`]}</p>}
                </div>
              </CardContent>
            </Card>
          ))}

          <Button type="button" variant="outline" onClick={addTask}>
            <Plus /> Tambah Tugas Lagi
          </Button>

          <div className="flex justify-end gap-2 border-t border-border pt-5">
            <Button asChild type="button" variant="outline">
              <Link href={r('supervisor.tasks.index')}>Batal</Link>
            </Button>
            <Button type="submit" disabled={processing}>Buat Tugas Massal</Button>
          </div>
        </form>
      </div>
    </SupervisorLayout>
  )
}
