import { useState } from 'react'
import { Link, useForm } from '@inertiajs/react'
import { Plus, Trash2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Buat Tugas Massal</h1>
            <p className="text-muted-foreground">Buat multiple tugas sekaligus untuk mahasiswa</p>
          </div>
          <Link href={r('supervisor.tasks.index')} className="text-sm font-medium text-primary hover:underline">
            ← Kembali
          </Link>
        </div>

        <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
          <strong>Panduan:</strong> Tambahkan tugas di bawah, pilih mahasiswa untuk setiap tugas, kemudian submit sekaligus.
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          {tasks.map((task, index) => (
            <Card key={index}>
              <CardContent className="space-y-4 p-6">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-foreground">Tugas {index + 1}</h3>
                  {tasks.length > 1 && (
                    <button type="button" onClick={() => removeTask(index)} className="flex items-center gap-1 text-sm font-medium text-destructive hover:underline">
                      <Trash2 className="h-4 w-4" /> Hapus
                    </button>
                  )}
                </div>

                <div className="space-y-2">
                  <Label>Judul Tugas</Label>
                  <Input
                    value={task.title}
                    onChange={(e) => updateTask(index, 'title', e.target.value)}
                    placeholder="Contoh: Membuat dokumentasi API"
                    required
                  />
                  {errors[`tasks.${index}.title`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.title`]}</p>}
                </div>

                <div className="space-y-2">
                  <Label>Deskripsi (Opsional)</Label>
                  <Textarea
                    rows={3}
                    value={task.description}
                    onChange={(e) => updateTask(index, 'description', e.target.value)}
                    placeholder="Masukkan deskripsi tugas..."
                  />
                </div>

                <div className="space-y-2">
                  <Label>Deadline</Label>
                  <Input
                    type="date"
                    value={task.due_date}
                    onChange={(e) => updateTask(index, 'due_date', e.target.value)}
                    required
                  />
                  {errors[`tasks.${index}.due_date`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.due_date`]}</p>}
                </div>

                <div className="space-y-2">
                  <Label>Pilih Mahasiswa (minimal 1)</Label>
                  <div className="max-h-64 overflow-y-auto rounded-lg border border-border bg-muted p-3">
                    {students.length ? (
                      students.map((student) => (
                        <label key={student.id} className="flex cursor-pointer items-center rounded px-2 py-2 hover:bg-accent">
                          <input
                            type="checkbox"
                            checked={task.student_ids.includes(student.id)}
                            onChange={() => toggleStudent(index, student.id)}
                            className="rounded"
                          />
                          <span className="ml-3 text-sm text-foreground">
                            {student.user.name} ({student.nim})
                          </span>
                        </label>
                      ))
                    ) : (
                      <p className="text-sm text-muted-foreground">Belum ada mahasiswa</p>
                    )}
                  </div>
                  {errors[`tasks.${index}.student_ids`] && <p className="text-sm text-destructive">{errors[`tasks.${index}.student_ids`]}</p>}
                </div>
              </CardContent>
            </Card>
          ))}

          <Button type="button" variant="secondary" onClick={addTask}>
            <Plus className="h-4 w-4" /> Tambah Tugas Lagi
          </Button>

          <div className="flex gap-3">
            <Button type="submit" disabled={processing} className="bg-green-600 hover:bg-green-700">
              Buat Tugas Massal
            </Button>
            <Link href={r('supervisor.tasks.index')}>
              <Button type="button" variant="secondary">Batal</Button>
            </Link>
          </div>
        </form>
      </div>
    </SupervisorLayout>
  )
}
