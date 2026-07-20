import { Link } from '@inertiajs/react'
import { Search as SearchIcon, RotateCcw } from 'lucide-react'
import { Card, CardContent } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

/**
 * Shared advanced-search filter form: task title + status Select + a
 * deadline range. `statusOptions` = [{ value, label }], `onSubmit(e)`
 * reads the named inputs; `resetHref` routes to the un-filtered page.
 */
export default function SearchFilters({ filters, statusOptions, statusValue, onStatusChange, onSubmit, resetHref }) {
  return (
    <Card>
      <CardContent className="p-5">
        <form onSubmit={onSubmit} className="space-y-4">
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Judul Tugas</label>
              <Input name="task_search" defaultValue={filters.task_search ?? ''} placeholder="Cari judul..." />
            </div>
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Status</label>
              <Select value={statusValue} onValueChange={onStatusChange}>
                <SelectTrigger className="w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {statusOptions.map((opt) => (
                    <SelectItem key={opt.value} value={opt.value}>{opt.label}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Deadline Dari</label>
              <Input type="date" name="due_date_from" defaultValue={filters.due_date_from ?? ''} />
            </div>
            <div className="space-y-2">
              <label className="block text-sm font-medium text-foreground">Deadline Sampai</label>
              <Input type="date" name="due_date_to" defaultValue={filters.due_date_to ?? ''} />
            </div>
          </div>

          <div className="flex gap-2">
            <Button type="submit">
              <SearchIcon /> Cari
            </Button>
            <Button asChild type="button" variant="outline">
              <Link href={resetHref}>
                <RotateCcw /> Reset
              </Link>
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  )
}

export function ResultCount({ shown, total }) {
  return (
    <p className="text-sm text-muted-foreground">
      Menampilkan <strong className="tabular-nums text-foreground">{shown}</strong> dari{' '}
      <strong className="tabular-nums text-foreground">{total}</strong> hasil
    </p>
  )
}
