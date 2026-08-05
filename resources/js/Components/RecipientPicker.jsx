import { useMemo, useRef, useState } from 'react'
import { Check, ChevronsUpDown, Search } from 'lucide-react'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'
import { cn } from '@/lib/utils'

const roleOrder = ['admin', 'supervisor', 'student']
const roleMeta = {
  admin: { label: 'Admin', avatar: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' },
  supervisor: { label: 'Pembimbing', avatar: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' },
  student: { label: 'Mahasiswa', avatar: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' },
}

const MAX_VISIBLE_PER_GROUP = 8

function initials(name) {
  return (name ?? '?')
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

/**
 * Searchable, role-grouped recipient picker for the message compose form.
 * Filters the already-fetched `recipients` list client-side (no extra
 * request) - fine up to a few hundred users; if the pool grows into the
 * thousands this should switch to a debounced server-side search instead.
 */
export default function RecipientPicker({ recipients, value, onChange, placeholder = 'Pilih penerima...' }) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState('')
  const [triggerWidth, setTriggerWidth] = useState(null)
  const inputRef = useRef(null)
  const triggerRef = useRef(null)

  const selected = useMemo(() => recipients.find((u) => String(u.id) === String(value)), [recipients, value])

  const grouped = useMemo(() => {
    const q = query.trim().toLowerCase()
    const filtered = q ? recipients.filter((u) => u.name.toLowerCase().includes(q)) : recipients

    const groups = {}
    for (const role of roleOrder) groups[role] = []
    for (const user of filtered) {
      if (groups[user.role]) groups[user.role].push(user)
    }
    return groups
  }, [recipients, query])

  const totalMatches = Object.values(grouped).reduce((sum, arr) => sum + arr.length, 0)

  function handleOpenChange(next) {
    setOpen(next)
    if (next) {
      setQuery('')
      setTriggerWidth(triggerRef.current?.offsetWidth ?? null)
      requestAnimationFrame(() => inputRef.current?.focus())
    }
  }

  function handleSelect(user) {
    onChange(String(user.id))
    setOpen(false)
  }

  return (
    <Popover open={open} onOpenChange={handleOpenChange}>
      <PopoverTrigger asChild>
        <button
          ref={triggerRef}
          type="button"
          className="flex h-9 w-full items-center justify-between gap-1.5 rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none transition-colors focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring dark:bg-input"
        >
          {selected ? (
            <span className="flex items-center gap-2 truncate">
              <span className={cn('flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-medium', roleMeta[selected.role]?.avatar)}>
                {initials(selected.name)}
              </span>
              <span className="truncate">{selected.name}</span>
              <span className="shrink-0 text-xs text-muted-foreground">({roleMeta[selected.role]?.label ?? selected.role})</span>
            </span>
          ) : (
            <span className="text-muted-foreground">{placeholder}</span>
          )}
          <ChevronsUpDown className="h-4 w-4 shrink-0 text-muted-foreground" />
        </button>
      </PopoverTrigger>
      <PopoverContent align="start" className="p-0" style={triggerWidth ? { width: triggerWidth } : undefined}>
        <div className="flex items-center gap-2 border-b border-border px-3 py-2">
          <Search className="h-3.5 w-3.5 shrink-0 text-muted-foreground" />
          <input
            ref={inputRef}
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Cari nama..."
            autoComplete="off"
            className="w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground"
          />
        </div>
        <div className="max-h-72 overflow-y-auto p-1">
          {totalMatches === 0 && (
            <p className="px-3 py-4 text-center text-sm text-muted-foreground">Tidak ada pengguna yang cocok.</p>
          )}
          {roleOrder.map((role) => {
            const users = grouped[role]
            if (users.length === 0) return null
            const visible = users.slice(0, MAX_VISIBLE_PER_GROUP)
            const hiddenCount = users.length - visible.length

            return (
              <div key={role}>
                <p className="sticky top-0 bg-muted px-2 py-1 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                  {roleMeta[role].label} · {users.length}
                </p>
                {visible.map((user) => {
                  const isSelected = String(user.id) === String(value)
                  return (
                    <button
                      key={user.id}
                      type="button"
                      onClick={() => handleSelect(user)}
                      className={cn(
                        'flex w-full items-center gap-2 rounded-sm px-2 py-1.5 text-left text-sm outline-none transition-colors hover:bg-muted focus-visible:bg-muted',
                        isSelected && 'bg-muted font-medium',
                      )}
                    >
                      <span className={cn('flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-medium', roleMeta[role].avatar)}>
                        {initials(user.name)}
                      </span>
                      <span className="min-w-0 flex-1 truncate">{user.name}</span>
                      {isSelected && <Check className="h-4 w-4 shrink-0 text-primary" />}
                    </button>
                  )
                })}
                {hiddenCount > 0 && (
                  <p className="px-2 py-1 text-xs text-muted-foreground">+ {hiddenCount} lainnya, ketik untuk mempersempit</p>
                )}
              </div>
            )
          })}
        </div>
      </PopoverContent>
    </Popover>
  )
}
