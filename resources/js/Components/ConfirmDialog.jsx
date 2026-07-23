import { AnimatePresence, motion } from 'motion/react'
import { Button } from '@/Components/ui/button'

/**
 * Presentational confirm modal. Controlled entirely by useConfirm()'s
 * provider - this component has no state of its own, it just renders
 * whatever the provider currently holds.
 */
export default function ConfirmDialog({ open, title, description, confirmLabel, cancelLabel, variant, onConfirm, onCancel }) {
  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="fixed inset-0 z-[100] bg-black/50"
          onClick={(e) => e.target === e.currentTarget && onCancel()}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.15 }}
        >
          <div className="flex min-h-screen items-center justify-center p-4">
            <motion.div
              className="w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-lg"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.15 }}
            >
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
            </motion.div>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  )
}
