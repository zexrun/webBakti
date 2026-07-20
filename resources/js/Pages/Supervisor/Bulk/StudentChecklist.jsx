import { Checkbox } from '@/Components/ui/checkbox'

/**
 * Scrollable, bordered list of students with checkboxes — shared by the
 * bulk task/notification forms.
 */
export default function StudentChecklist({ students, selectedIds, onToggle }) {
  return (
    <div className="max-h-64 overflow-y-auto rounded-lg border border-border">
      {students.length ? (
        students.map((student) => (
          <label
            key={student.id}
            className="flex cursor-pointer items-center gap-3 border-b border-border px-3 py-2.5 transition-colors duration-150 last:border-b-0 hover:bg-muted"
          >
            <Checkbox checked={selectedIds.includes(student.id)} onCheckedChange={() => onToggle(student.id)} />
            <span className="text-sm text-foreground">
              {student.user.name} <span className="text-muted-foreground">({student.nim})</span>
            </span>
          </label>
        ))
      ) : (
        <p className="p-4 text-sm text-muted-foreground">Belum ada mahasiswa</p>
      )}
    </div>
  )
}

export const priorityOptions = [
  { value: 'low', label: 'Low', hint: 'Informasi umum', dot: 'bg-slate-400' },
  { value: 'normal', label: 'Normal', hint: 'Pemberitahuan standar', dot: 'bg-blue-500' },
  { value: 'high', label: 'High', hint: 'Perhatian dibutuhkan', dot: 'bg-amber-500' },
  { value: 'urgent', label: 'Urgent', hint: 'Segera dibalas', dot: 'bg-red-500' },
]
