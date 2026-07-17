import { usePage } from '@inertiajs/react'
import AdminLayout from '@/Layouts/AdminLayout'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import StudentLayout from '@/Layouts/StudentLayout'

const layoutByRole = {
  admin: AdminLayout,
  supervisor: SupervisorLayout,
  student: StudentLayout,
}

/**
 * Picks the correct sidebar/navbar layout based on the authenticated
 * user's role, mirroring layouts/app.blade.php's @if(auth()->user()->role)
 * partial switching. Use this for any page reachable by multiple roles
 * (Announcements, Messages, Profile, ...).
 */
export default function RoleLayout({ children }) {
  const { auth } = usePage().props
  const Layout = layoutByRole[auth?.user?.role] ?? AdminLayout

  return <Layout>{children}</Layout>
}
