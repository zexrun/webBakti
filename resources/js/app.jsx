import './bootstrap'
import '../css/app.css'
import React from 'react'
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { route as ziggyRoute } from 'ziggy-js'
import { Ziggy } from './ziggy'

const appName = import.meta.env.VITE_APP_NAME || 'webBakti'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.jsx`,
      import.meta.glob('./Pages/**/*.jsx'),
    ),
  setup({ el, App, props }) {
    window.route = (name, params, absolute) =>
      ziggyRoute(name, params, absolute, Ziggy)

    const root = createRoot(el)
    root.render(<App {...props} />)
  },
  progress: {
    color: '#4f46e5',
  },
})
