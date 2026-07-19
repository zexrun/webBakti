import { Users } from 'lucide-react'
import { Checkbox } from '@/Components/ui/checkbox'

export default function StudentPicker({ students, selectedIds, onChange }) {
  const allSelected = students.length > 0 && selectedIds.length === students.length

  function toggleAll(checked) {
    onChange(checked ? students.map((s) => s.id) : [])
  }

  function toggleOne(id) {
    onChange(selectedIds.includes(id) ? selectedIds.filter((sid) => sid !== id) : [...selectedIds, id])
  }

  return (
    <div className="rounded-md border border-input">
      <div className="border-b border-input bg-muted p-3">
        <label className="flex cursor-pointer items-center gap-2">
          <Checkbox checked={allSelected} onCheckedChange={toggleAll} />
          <span className="text-sm font-medium text-foreground">Pilih Semua Mahasiswa</span>
          <span className="text-xs text-muted-foreground">({students.length} mahasiswa)</span>
        </label>
      </div>

      <div className="max-h-64 overflow-y-auto">
        {students.length ? (
          students.map((student) => (
            <div key={student.id} className="border-b border-border p-3 transition-colors duration-150 last:border-b-0 hover:bg-muted">
              <label className="flex cursor-pointer items-center gap-3">
                <Checkbox checked={selectedIds.includes(student.id)} onCheckedChange={() => toggleOne(student.id)} />
                <div>
                  <div className="text-sm font-medium text-foreground">{student.user?.name}</div>
                  <div className="text-xs text-muted-foreground">{student.user?.email}</div>
                </div>
              </label>
            </div>
          ))
        ) : (
          <div className="p-6 text-center">
            <Users className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
            <p className="text-sm text-muted-foreground">Belum ada mahasiswa bimbingan</p>
          </div>
        )}
      </div>

      {students.length > 0 && (
        <div className="border-t border-border bg-muted p-3">
          <p className="text-sm tabular-nums text-muted-foreground">
            <span className="font-medium text-foreground">{selectedIds.length}</span> dari {students.length} mahasiswa dipilih
          </p>
        </div>
      )}
    </div>
  )
}
