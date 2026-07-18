export default function GuestLayout({ children }) {
  return (
    <div className="relative min-h-screen overflow-hidden bg-gradient-to-br from-blue-50 via-white to-indigo-50">
      <div className="pointer-events-none absolute -right-32 -top-32 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-32 -left-32 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl" />

      <div className="relative flex min-h-screen flex-col items-center justify-center p-4">
        <div className="w-full max-w-sm">
          {children}

          <footer className="mt-6 text-center">
            <div className="flex justify-center space-x-4 text-xs">
              <a href="#" className="text-gray-500 transition-colors hover:text-gray-700">Bantuan</a>
              <a href="#" className="text-gray-500 transition-colors hover:text-gray-700">Kontak</a>
              <a href="#" className="text-gray-500 transition-colors hover:text-gray-700">Privasi</a>
            </div>
            <p className="mt-2 text-xs text-gray-400">© {new Date().getFullYear()} Sistem Monitoring Magang</p>
          </footer>
        </div>
      </div>
    </div>
  )
}
