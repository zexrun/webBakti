import React from 'react'
import Layout from '../Layouts/Layout'

export default function Dashboard({ user }) {
  return (
    <Layout>
      <div className="bg-white rounded-lg shadow p-6">
        <h1 className="text-2xl font-bold text-gray-900 mb-4">
          Selamat datang, {user.name}!
        </h1>
        <p className="text-gray-600">
          Ini adalah halaman dashboard baru dengan React + Inertia.js + shadcn/ui
        </p>
      </div>
    </Layout>
  )
}
