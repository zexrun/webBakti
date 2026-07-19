import { useState } from 'react'

/**
 * Reads the `dark` class that app.blade.php's inline script set before
 * first paint, and toggles + persists it. Preference lives in
 * localStorage('theme'); absence falls back to prefers-color-scheme.
 */
export default function useTheme() {
  const [dark, setDark] = useState(
    () => typeof document !== 'undefined' && document.documentElement.classList.contains('dark'),
  )

  function toggle() {
    const next = !dark
    setDark(next)
    document.documentElement.classList.toggle('dark', next)
    localStorage.setItem('theme', next ? 'dark' : 'light')
  }

  return { dark, toggle }
}
