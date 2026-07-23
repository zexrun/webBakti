import { createContext, useCallback, useContext, useRef, useState } from 'react'
import ConfirmDialog from '@/Components/ConfirmDialog'

const ConfirmContext = createContext(null)

/**
 * Mounted once in AppShell.jsx. Holds the dialog's current options and
 * the pending promise's resolve function, so any descendant component
 * can call useConfirm()'s confirm() and await a boolean result - the
 * same call-site shape as the old window.confirm(), just async.
 */
export function ConfirmDialogProvider({ children }) {
  const [state, setState] = useState(null) // null when closed, else the options object
  const resolveRef = useRef(null)

  const confirm = useCallback((options) => {
    setState(options)
    return new Promise((resolve) => {
      resolveRef.current = resolve
    })
  }, [])

  function handleConfirm() {
    resolveRef.current?.(true)
    setState(null)
  }

  function handleCancel() {
    resolveRef.current?.(false)
    setState(null)
  }

  return (
    <ConfirmContext.Provider value={confirm}>
      {children}
      <ConfirmDialog
        open={Boolean(state)}
        title={state?.title}
        description={state?.description}
        confirmLabel={state?.confirmLabel}
        cancelLabel={state?.cancelLabel}
        variant={state?.variant}
        onConfirm={handleConfirm}
        onCancel={handleCancel}
      />
    </ConfirmContext.Provider>
  )
}

/** Returns confirm({ title, description?, confirmLabel?, cancelLabel?, variant? }) => Promise<boolean> */
export function useConfirm() {
  const confirm = useContext(ConfirmContext)
  if (!confirm) {
    throw new Error('useConfirm() must be used within a ConfirmDialogProvider')
  }
  return confirm
}
