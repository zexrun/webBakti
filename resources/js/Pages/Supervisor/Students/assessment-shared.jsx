import { cn } from '@/lib/utils'

// Shared between AssessmentCreate and AssessmentEdit.

export const grades = [
  { value: 'A', label: 'Sangat Baik', description: 'Kinerja luar biasa', text: 'text-green-700 dark:text-green-400', selected: 'border-green-500 bg-green-50 dark:bg-green-500/10' },
  { value: 'B', label: 'Baik', description: 'Kinerja baik', text: 'text-blue-700 dark:text-blue-400', selected: 'border-blue-500 bg-blue-50 dark:bg-blue-500/10' },
  { value: 'C', label: 'Cukup', description: 'Kinerja cukup', text: 'text-amber-700 dark:text-amber-400', selected: 'border-amber-500 bg-amber-50 dark:bg-amber-500/10' },
  { value: 'D', label: 'Kurang', description: 'Perlu perbaikan', text: 'text-red-700 dark:text-red-400', selected: 'border-red-500 bg-red-50 dark:bg-red-500/10' },
]

export const gradeBadgeClass = {
  A: 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-300',
  B: 'bg-blue-100 text-blue-800 dark:bg-blue-500/15 dark:text-blue-300',
  C: 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
  D: 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300',
}

export function GradeSelector({ value, onChange }) {
  return (
    <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
      {grades.map((grade) => {
        const active = value === grade.value
        return (
          <label key={grade.value} className="relative">
            <input
              type="radio"
              name="final_grade"
              value={grade.value}
              checked={active}
              onChange={(e) => onChange(e.target.value)}
              className="peer sr-only"
              required
            />
            <div
              className={cn(
                'cursor-pointer rounded-lg border-2 p-4 text-center transition-colors duration-150',
                active ? grade.selected : 'border-border hover:bg-muted',
              )}
            >
              <div className={cn('text-2xl font-bold', grade.text)}>{grade.value}</div>
              <div className="mt-1 text-sm font-medium text-foreground">{grade.label}</div>
              <div className="text-xs text-muted-foreground">{grade.description}</div>
            </div>
          </label>
        )
      })}
    </div>
  )
}

export function InfoRow({ label, value }) {
  return (
    <div>
      <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">{label}</p>
      <p className="mt-1 text-sm text-foreground">{value ?? '-'}</p>
    </div>
  )
}

export function GradingGuide() {
  return (
    <div className="rounded-lg border border-border bg-muted/50 p-5">
      <h3 className="mb-3 text-sm font-semibold text-foreground">Panduan Penilaian</h3>
      <dl className="space-y-2 text-sm">
        {grades.map((g) => (
          <div key={g.value} className="flex gap-2">
            <dt className={cn('shrink-0 font-semibold', g.text)}>Nilai {g.value} ({g.label}):</dt>
            <dd className="text-muted-foreground">
              {g.value === 'A' && 'Menunjukkan kinerja luar biasa, melebihi ekspektasi dalam semua aspek.'}
              {g.value === 'B' && 'Menunjukkan kinerja baik dan memenuhi sebagian besar ekspektasi.'}
              {g.value === 'C' && 'Menunjukkan kinerja yang memadai dengan beberapa area yang perlu diperbaiki.'}
              {g.value === 'D' && 'Menunjukkan kinerja di bawah ekspektasi dan memerlukan perbaikan signifikan.'}
            </dd>
          </div>
        ))}
      </dl>
    </div>
  )
}
