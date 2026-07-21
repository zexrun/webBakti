import { X } from 'lucide-react'

/**
 * Full-size photo overlay. Structurally mirrors ApprovalModal (fixed
 * inset overlay, click-outside-to-close, close button) for visual
 * consistency with the rest of the attendance review UI.
 */
export default function PhotoLightbox({ open, onClose, url, caption }) {
  if (!open || !url) return null

  return (
    <div className="fixed inset-0 z-[60] bg-black/70" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="relative max-w-2xl">
          <button
            type="button"
            onClick={onClose}
            aria-label="Tutup"
            className="absolute -top-10 right-0 rounded-md p-1.5 text-white/80 transition-colors duration-150 hover:bg-white/10 hover:text-white"
          >
            <X className="h-5 w-5" />
          </button>
          <img src={url} alt={caption} className="max-h-[80vh] w-full rounded-lg object-contain" />
          {caption && (
            <p className="mt-2 text-center text-sm text-white/80">{caption}</p>
          )}
        </div>
      </div>
    </div>
  )
}
