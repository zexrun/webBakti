import { useState } from 'react'
import { CheckCircle2, AlertCircle, Eye, EyeOff } from 'lucide-react'
import { Input } from '@/Components/ui/input'
import { cn } from '@/lib/utils'

/** Centered brand header for auth cards. Optional lucide icon in a tile. */
export function AuthHeader({ icon: Icon, title, description }) {
  return (
    <div className="mb-6 text-center">
      {Icon && (
        <div className="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-sidebar text-sidebar-primary-foreground">
          <Icon className="h-5 w-5" />
        </div>
      )}
      <h1 className="text-xl font-bold text-foreground">{title}</h1>
      {description && <p className="mt-1 text-sm text-muted-foreground">{description}</p>}
    </div>
  )
}

/** Dark-safe status / error banner. `type`: success | error | info. */
export function AuthBanner({ type = 'info', children, className }) {
  const styles = {
    success: 'border-green-200 bg-green-50 text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-300',
    error: 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300',
    info: 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300',
  }
  const Icon = type === 'success' ? CheckCircle2 : type === 'error' ? AlertCircle : null
  return (
    <div className={cn('mb-4 flex items-start gap-2 rounded-lg border p-3 text-sm', styles[type], className)}>
      {Icon && <Icon className="mt-0.5 h-4 w-4 shrink-0" />}
      <div>{children}</div>
    </div>
  )
}

/** Password input with a show/hide toggle; optional leading icon. */
export function PasswordInput({ id, value, onChange, placeholder, autoComplete, autoFocus, icon: Icon }) {
  const [visible, setVisible] = useState(false)
  return (
    <div className="relative">
      {Icon && <Icon className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />}
      <Input
        id={id}
        type={visible ? 'text' : 'password'}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        autoComplete={autoComplete}
        autoFocus={autoFocus}
        required
        className={cn('pr-10', Icon && 'pl-10')}
      />
      <button
        type="button"
        onClick={() => setVisible((v) => !v)}
        aria-label={visible ? 'Sembunyikan password' : 'Tampilkan password'}
        className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground transition-colors hover:text-foreground"
      >
        {visible ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
      </button>
    </div>
  )
}
