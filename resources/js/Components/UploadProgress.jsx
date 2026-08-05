/**
 * Progress bar for file uploads, driven by Inertia's useForm() `progress`
 * object ({ percentage }). Renders nothing while no upload is in flight.
 */
export default function UploadProgress({ progress }) {
  if (!progress) return null

  return (
    <div className="space-y-1.5">
      <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
        <div
          className="h-full rounded-full bg-primary transition-all duration-150"
          style={{ width: `${progress.percentage}%` }}
        />
      </div>
      <p className="text-xs text-muted-foreground">Mengunggah... {progress.percentage}%</p>
    </div>
  )
}
