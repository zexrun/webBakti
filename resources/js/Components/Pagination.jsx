import { Link } from '@inertiajs/react'
import { cn } from '@/lib/utils'

/**
 * Renders Laravel's paginator links (the `links` array present on any
 * paginated Inertia prop, e.g. announcements.links). Mirrors what
 * {{ $paginator->links() }} produces in Blade.
 */
export default function Pagination({ links }) {
  if (!links || links.length <= 3) return null

  return (
    <nav className="flex flex-wrap items-center gap-1">
      {links.map((link, index) => {
        if (!link.url) {
          return (
            <span
              key={index}
              className="px-3 py-1.5 text-sm text-muted-foreground"
              dangerouslySetInnerHTML={{ __html: link.label }}
            />
          )
        }

        return (
          <Link
            key={index}
            href={link.url}
            preserveScroll
            className={cn(
              'rounded-md px-3 py-1.5 text-sm transition-colors',
              link.active
                ? 'bg-primary text-primary-foreground'
                : 'text-foreground hover:bg-accent',
            )}
            dangerouslySetInnerHTML={{ __html: link.label }}
          />
        )
      })}
    </nav>
  )
}
