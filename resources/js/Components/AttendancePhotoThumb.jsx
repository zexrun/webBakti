import { useState } from 'react'
import { Camera, CameraOff } from 'lucide-react'
import { cn } from '@/lib/utils'

/**
 * Small clickable photo thumbnail for attendance check-in/check-out
 * photos. Renders a neutral placeholder tile (crossed-out camera icon)
 * when there's no photo, or when the stored file fails to load — photo
 * upload has always been optional, so a missing photo is a normal,
 * expected state, not an error.
 */
export default function AttendancePhotoThumb({ url, label, onClick }) {
  const [failed, setFailed] = useState(false)
  const showPlaceholder = !url || failed

  if (showPlaceholder) {
    return (
      <div
        className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-muted"
        title={`${label}: tidak ada foto`}
        aria-label={`${label}: tidak ada foto`}
      >
        <CameraOff className="h-4 w-4 text-muted-foreground" />
      </div>
    )
  }

  return (
    <button
      type="button"
      onClick={onClick}
      className={cn(
        'group relative h-10 w-10 shrink-0 overflow-hidden rounded-md border border-border',
        'transition-opacity duration-150 hover:opacity-80',
      )}
      title={`Lihat foto ${label}`}
      aria-label={`Lihat foto ${label}`}
    >
      <img
        src={url}
        alt={label}
        onError={() => setFailed(true)}
        className="h-full w-full object-cover"
      />
      <span className="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-opacity duration-150 group-hover:bg-black/20 group-hover:opacity-100">
        <Camera className="h-4 w-4 text-white" />
      </span>
    </button>
  )
}
