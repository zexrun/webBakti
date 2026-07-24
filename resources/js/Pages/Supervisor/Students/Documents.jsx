import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { ArrowLeft, FileText, FileImage, Eye, Download, LayoutGrid, List } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'
import { cn } from '@/lib/utils'

const fileLabels = {
  pdf: 'PDF', doc: 'Word', docx: 'Word', xls: 'Excel', xlsx: 'Excel',
  ppt: 'PowerPoint', pptx: 'PowerPoint', jpg: 'Image', jpeg: 'Image', png: 'Image', gif: 'Image',
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

function typeLabel(type) {
  return type === 'laporan_akhir' ? 'Laporan Akhir' : type.charAt(0).toUpperCase() + type.slice(1)
}

function ViewToggle({ view, onChange }) {
  return (
    <div className="flex items-center gap-1 rounded-md border border-border p-1">
      {[
        { key: 'card', icon: LayoutGrid, label: 'Card' },
        { key: 'list', icon: List, label: 'List' },
      ].map(({ key, icon: Icon, label }) => (
        <button
          key={key}
          type="button"
          onClick={() => onChange(key)}
          className={cn(
            'flex items-center gap-1.5 rounded-sm px-2.5 py-1 text-sm font-medium transition-colors duration-150',
            view === key ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground',
          )}
        >
          <Icon className="h-4 w-4" /> {label}
        </button>
      ))}
    </div>
  )
}

export default function Documents({ student, documents }) {
  const [view, setView] = useState('card')
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Dokumen Mahasiswa"
          description={`${student.user.name} · ${student.nim ?? 'NIM belum diisi'}`}
          actions={
            <>
              <span className="hidden items-center gap-1.5 pb-1 text-sm text-muted-foreground sm:flex">
                <FileText className="h-4 w-4" /> {documents.length} Dokumen
              </span>
              <ViewToggle view={view} onChange={setView} />
              <Button asChild variant="outline" size="sm">
                <Link href={r('supervisor.students.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
            </>
          }
        />

        {documents.length === 0 ? (
          <Card>
            <EmptyState
              icon={FileText}
              title="Belum ada dokumen"
              description={`${student.user.name} belum mengupload dokumen apapun.`}
              action={
                <Button asChild variant="outline">
                  <Link href={r('supervisor.students.index')}>Kembali ke Daftar Mahasiswa</Link>
                </Button>
              }
            />
          </Card>
        ) : view === 'card' ? (
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {documents.map((document) => {
              const { fileName, label, isImage } = fileMeta(document)
              const Icon = isImage ? FileImage : FileText
              return (
                <Card key={document.id} className="flex flex-col">
                  <div className="flex h-28 items-center justify-center border-b border-border bg-muted">
                    <div className="text-center">
                      <Icon className={cn('mx-auto h-10 w-10', isImage ? 'text-purple-500' : 'text-muted-foreground')} />
                      <Badge variant="secondary" className="mt-2">{label}</Badge>
                    </div>
                  </div>
                  <CardContent className="flex-1 p-4">
                    <Badge variant="outline" className="mb-2">{typeLabel(document.type)}</Badge>
                    <h3 className="truncate text-sm font-medium text-foreground" title={fileName}>{fileName}</h3>
                    <div className="mt-1 space-y-0.5 text-xs text-muted-foreground">
                      {document.file_size && <div className="tabular-nums">{formatBytes(document.file_size)}</div>}
                      <div className="tabular-nums">
                        {new Date(document.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </div>
                    </div>
                  </CardContent>
                  <div className="flex gap-1.5 border-t border-border p-3">
                    <Button asChild size="xs" variant="outline">
                      <a href={r('supervisor.documents.download', document.id)} target="_blank" rel="noreferrer">
                        <Eye /> Lihat
                      </a>
                    </Button>
                    <Button asChild size="xs" variant="outline">
                      <a href={r('supervisor.documents.download', document.id)}>
                        <Download /> Download
                      </a>
                    </Button>
                  </div>
                </Card>
              )
            })}
          </div>
        ) : (
          <Card>
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Dokumen</TableHead>
                  <TableHead>Kategori</TableHead>
                  <TableHead className="text-right">Ukuran</TableHead>
                  <TableHead>Tanggal Upload</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {documents.map((document) => {
                  const { fileName, label, isImage } = fileMeta(document)
                  const Icon = isImage ? FileImage : FileText
                  return (
                    <TableRow key={document.id}>
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-muted">
                            <Icon className="h-5 w-5 text-muted-foreground" />
                          </div>
                          <div className="min-w-0">
                            <p className="max-w-xs truncate text-sm font-medium text-foreground" title={fileName}>{fileName}</p>
                            <p className="text-xs text-muted-foreground">{label}</p>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline">{typeLabel(document.type)}</Badge>
                      </TableCell>
                      <TableCell className="text-right tabular-nums text-muted-foreground">
                        {document.file_size ? formatBytes(document.file_size) : '-'}
                      </TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">
                        {new Date(document.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
                          <Button asChild size="xs" variant="outline">
                            <a href={r('supervisor.documents.download', document.id)} target="_blank" rel="noreferrer">Lihat</a>
                          </Button>
                          <Button asChild size="xs" variant="outline">
                            <a href={r('supervisor.documents.download', document.id)}>Download</a>
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </Card>
        )}
      </div>
    </SupervisorLayout>
  )
}
