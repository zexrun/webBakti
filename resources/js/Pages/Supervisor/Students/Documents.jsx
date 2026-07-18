import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { ArrowLeft, FileText, Eye, Download, LayoutGrid, List } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'

const fileLabels = {
  pdf: 'PDF',
  doc: 'Word',
  docx: 'Word',
  xls: 'Excel',
  xlsx: 'Excel',
  ppt: 'PowerPoint',
  pptx: 'PowerPoint',
  jpg: 'Image',
  jpeg: 'Image',
  png: 'Image',
  gif: 'Image',
}

const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']

function formatBytes(bytes) {
  if (!bytes) return ''
  const units = ['B', 'KB', 'MB', 'GB']
  let value = bytes
  let unitIndex = 0
  while (value >= 1024 && unitIndex < units.length - 1) {
    value /= 1024
    unitIndex++
  }
  return `${value.toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`
}

function fileMeta(document) {
  const fileName = document.original_filename || document.file_path.split('/').pop()
  const extension = fileName.split('.').pop().toLowerCase()
  return {
    fileName,
    extension,
    label: fileLabels[extension] ?? extension.toUpperCase(),
    isImage: imageExtensions.includes(extension),
  }
}

export default function Documents({ student, documents }) {
  const [view, setView] = useState('card')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div className="flex items-center gap-4">
            <Link href={r('supervisor.students.list.index')}>
              <button type="button" className="inline-flex items-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm font-medium text-foreground hover:bg-accent">
                <ArrowLeft className="h-4 w-4" /> Kembali
              </button>
            </Link>
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600">
                <span className="text-sm font-semibold text-white">{student.user.name.charAt(0).toUpperCase()}</span>
              </div>
              <div>
                <h1 className="text-xl font-bold text-foreground">Dokumen Mahasiswa</h1>
                <p className="text-sm text-muted-foreground">{student.user.name} • {student.nim ?? 'NIM belum diisi'}</p>
              </div>
            </div>
          </div>

          <div className="flex items-center gap-3">
            <span className="hidden items-center gap-1 text-sm text-muted-foreground sm:flex">
              <FileText className="h-4 w-4 text-blue-500" /> {documents.length} Dokumen
            </span>
            <div className="flex items-center gap-1 rounded-lg border border-border p-1">
              <button
                type="button"
                onClick={() => setView('card')}
                className={`flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium ${view === 'card' ? 'bg-blue-100 text-blue-700' : 'text-muted-foreground hover:text-foreground'}`}
              >
                <LayoutGrid className="h-4 w-4" /> Card
              </button>
              <button
                type="button"
                onClick={() => setView('list')}
                className={`flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium ${view === 'list' ? 'bg-blue-100 text-blue-700' : 'text-muted-foreground hover:text-foreground'}`}
              >
                <List className="h-4 w-4" /> List
              </button>
            </div>
          </div>
        </div>

        {documents.length === 0 ? (
          <Card>
            <CardContent className="p-16 text-center">
              <FileText className="mx-auto mb-4 h-16 w-16 text-muted-foreground" />
              <h3 className="mb-2 text-xl font-medium text-foreground">Belum ada dokumen</h3>
              <p className="mb-6 text-muted-foreground">Mahasiswa {student.user.name} belum mengupload dokumen apapun.</p>
              <Link href={r('supervisor.students.list.index')} className="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Kembali ke Daftar Mahasiswa
              </Link>
            </CardContent>
          </Card>
        ) : view === 'card' ? (
          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {documents.map((document) => {
              const { fileName, label, isImage } = fileMeta(document)
              return (
                <Card key={document.id} className="overflow-hidden">
                  <div className="flex h-32 items-center justify-center bg-muted p-6">
                    {isImage ? (
                      <FileText className="h-12 w-12 text-purple-500" />
                    ) : (
                      <div className="text-center">
                        <FileText className="mx-auto h-12 w-12 text-muted-foreground" />
                        <Badge variant="secondary" className="mt-2">{label}</Badge>
                      </div>
                    )}
                  </div>
                  <CardContent className="p-4">
                    <div className="mb-2">
                      <Badge>{document.type === 'laporan_akhir' ? 'Laporan Akhir' : document.type.charAt(0).toUpperCase() + document.type.slice(1)}</Badge>
                    </div>
                    <h3 className="mb-2 truncate font-medium text-foreground" title={fileName}>{fileName}</h3>
                    <div className="space-y-1 text-sm text-muted-foreground">
                      {document.file_size && <div>{formatBytes(document.file_size)}</div>}
                      <div>{new Date(document.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                  </CardContent>
                  <div className="flex items-center justify-between border-t border-border bg-muted px-4 py-3">
                    <div className="flex gap-2">
                      <a href={r('supervisor.documents.download', document.id)} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200">
                        <Eye className="h-3 w-3" /> Lihat
                      </a>
                      <a href={r('supervisor.documents.download', document.id)} className="inline-flex items-center gap-1 rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">
                        <Download className="h-3 w-3" /> Download
                      </a>
                    </div>
                  </div>
                </Card>
              )
            })}
          </div>
        ) : (
          <Card>
            <CardContent className="p-0">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Dokumen</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Kategori</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Ukuran</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Tanggal Upload</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {documents.map((document) => {
                      const { fileName, label } = fileMeta(document)
                      return (
                        <tr key={document.id} className="hover:bg-accent">
                          <td className="px-6 py-4">
                            <div className="flex items-center gap-3">
                              <div className="flex h-10 w-10 items-center justify-center rounded bg-muted">
                                <FileText className="h-6 w-6 text-muted-foreground" />
                              </div>
                              <div>
                                <p className="max-w-xs truncate text-sm font-medium text-foreground" title={fileName}>{fileName}</p>
                                <p className="text-sm text-muted-foreground">{label}</p>
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4">
                            <Badge>{document.type === 'laporan_akhir' ? 'Laporan Akhir' : document.type.charAt(0).toUpperCase() + document.type.slice(1)}</Badge>
                          </td>
                          <td className="px-6 py-4 text-sm text-muted-foreground">{document.file_size ? formatBytes(document.file_size) : '-'}</td>
                          <td className="px-6 py-4 text-sm text-muted-foreground">
                            {new Date(document.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                          </td>
                          <td className="px-6 py-4 text-sm font-medium">
                            <div className="flex gap-3">
                              <a href={r('supervisor.documents.download', document.id)} target="_blank" rel="noreferrer" className="text-blue-600 hover:text-blue-800">Lihat</a>
                              <a href={r('supervisor.documents.download', document.id)} className="text-green-600 hover:text-green-800">Download</a>
                            </div>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
