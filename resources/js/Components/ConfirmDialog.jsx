import { Button } from '@/Components/ui/button'

/**
 * Presentational confirm modal. Controlled entirely by useConfirm()'s
 * provider - this component has no state of its own, it just renders
 * whatever the provider currently holds.
 */
export default function ConfirmDialog({ open, title, description, confirmLabel, cancelLabel, variant, onConfirm, onCancel }) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-[100] bg-black/50" onClick={(e) => e.target === e.currentTarget && onCancel()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-lg">
          <h3 className="text-lg font-semibold text-foreground">{title}</h3>
          {description && <p className="mt-2 text-sm text-muted-foreground">{description}</p>}
          <div className="mt-6 flex gap-2">
            <Button type="button" variant="outline" onClick={onCancel} className="flex-1">
              {cancelLabel || 'Batal'}
            </Button>
            <Button
              type="button"
              variant={variant === 'destructive' ? 'destructive' : 'default'}
              onClick={onConfirm}
              className="flex-1"
            >
              {confirmLabel || 'Konfirmasi'}
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}
