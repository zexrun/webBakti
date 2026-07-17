# webBakti - New UI Migration (new-ui branch)

## Overview

This branch (`new-ui`) is dedicated to migrating webBakti's frontend from **Blade templating** to **React + Inertia.js + shadcn/ui**.

## Stack

- **Frontend Framework**: React 18+
- **Backend-Frontend Bridge**: Inertia.js (v3.1)
- **Component Library**: shadcn/ui
- **Styling**: Tailwind CSS (existing)
- **UI Primitives**: Radix UI
- **Icons**: Lucide React
- **Build Tool**: Vite
- **Type Safety**: TypeScript (optional but recommended)
- **HTTP Client**: Axios

## Project Structure

```
resources/
├── js/
│   ├── app.jsx              # Inertia app entry point
│   ├── bootstrap.js         # Axios & global config
│   ├── Pages/               # Inertia pages (becomes routes)
│   │   ├── Dashboard.jsx    # Sample page
│   │   ├── Admin/
│   │   ├── Supervisor/
│   │   └── Student/
│   ├── Components/          # Reusable React components
│   │   ├── ui/              # shadcn/ui components
│   │   └── Custom/          # Custom business components
│   └── Layouts/             # Layout components
│       └── Layout.jsx       # Base layout
├── views/
│   ├── app.blade.php        # Inertia root template
│   └── ...                  # Keep old Blade files (backup)
└── css/
    └── app.css              # Tailwind CSS

app/Http/
├── Middleware/
│   └── HandleInertiaRequests.php  # Inertia middleware
└── Controllers/
    └── InertiaTestController.php  # Test controller
```

## Setup Instructions

### Prerequisites
- Node.js 18+
- PHP 8.2+
- Composer
- Existing Laravel 12 installation

### Installation

1. **Install dependencies**:
```bash
npm install
composer install
```

2. **Run migrations** (if needed):
```bash
php artisan migrate
```

3. **Start development server**:
```bash
# Terminal 1: Start Laravel dev server
php artisan serve

# Terminal 2: Start Vite dev server (watch mode)
npm run dev
```

4. **Test Inertia setup**:
   - Login to the application
   - Navigate to `/test-inertia` to see the React Dashboard component

## Development Workflow

### Creating a New Page Component

1. Create component in `resources/js/Pages/[Name].jsx`:
```jsx
import React from 'react'
import Layout from '../Layouts/Layout'

export default function YourPage({ data }) {
  return (
    <Layout>
      <h1>Your Page</h1>
      {/* content */}
    </Layout>
  )
}
```

2. Return it from your controller:
```php
use Inertia\Inertia;

return Inertia::render('YourPage', [
    'data' => $data,
]);
```

### Using shadcn/ui Components

1. Components are pre-installed in `resources/js/Components/ui/`
2. Example usage:
```jsx
import { Button } from '@/Components/ui/button'

export default function MyComponent() {
  return <Button>Click me</Button>
}
```

3. To add more shadcn components, use CLI (after setup):
```bash
npx shadcn-ui@latest add [component-name]
```

## Available Scripts

- `npm run dev` - Start Vite dev server
- `npm run build` - Build for production
- `npm run preview` - Preview production build

## Migration Strategy

### Phase 1: Foundation (Current)
- ✅ Setup Inertia.js + React + shadcn/ui
- ✅ Create base layouts and components
- 🔄 Test with sample page

### Phase 2: Core Pages
- [ ] Migrate Admin Dashboard
- [ ] Migrate Supervisor Dashboard
- [ ] Migrate Student Dashboard

### Phase 3: Feature Modules
- [ ] Attendance Management
- [ ] Task Management
- [ ] Announcements
- [ ] Messages
- [ ] Analytics

### Phase 4: Complete Migration
- [ ] Migrate all remaining pages
- [ ] Replace old Blade routes with Inertia
- [ ] Remove old Blade files (or keep as fallback)

## Shared Data

Data shared to all pages automatically via `HandleInertiaRequests` middleware:
```javascript
{
  auth: {
    user: { /* authenticated user */ }
  },
  // add more here
}
```

Access in components:
```jsx
import { usePage } from '@inertiajs/react'

export default function MyComponent() {
  const { auth } = usePage().props
  // use auth.user
}
```

## TypeScript Setup (Optional)

Add TypeScript support:
```bash
npm install -D typescript @types/react @types/react-dom
npx tsc --init
```

Then rename `.jsx` files to `.tsx`

## Troubleshooting

### Issue: Vite not compiling
- Make sure `npm run dev` is running
- Check browser console for errors
- Clear node_modules: `rm -rf node_modules && npm install`

### Issue: Inertia not rendering
- Verify middleware is registered in `bootstrap/app.php`
- Check that controller returns `Inertia::render()`
- Verify `resources/views/app.blade.php` exists

### Issue: Tailwind styles not loading
- Ensure Tailwind is configured in `tailwind.config.js`
- Import `app.css` in `app.jsx`

## Resources

- [Inertia.js Docs](https://inertiajs.com)
- [React Docs](https://react.dev)
- [shadcn/ui Docs](https://ui.shadcn.com)
- [Tailwind CSS Docs](https://tailwindcss.com)

## Notes

- Keep old Blade files as backup for now
- Test Inertia routes with `Route::middleware('auth')`
- Use Inertia's form helpers for form handling
- Components are reactive - no page reloads

## Branch Management

This is an experimental branch. When ready to merge:
1. Test all migrated pages thoroughly
2. Ensure old Blade routes still work (fallback)
3. Create PR for review before merging to main

---

Last Updated: 2026-07-17
