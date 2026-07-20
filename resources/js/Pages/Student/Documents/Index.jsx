import { useRef, useState } from 'react'
import { router, useForm, usePage } from '@inertiajs/react'
import { FileText, Upload, Trash2 } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import EmptyState from '@/Components/EmptyState'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

const typeConfig = {
  proposal: { label: 'Proposal', variant: 'default' },
  laporan_akhir: { label: 'Laporan Akhir', variant: 'success' },
  lainnya: { label: 'Lainnya', variant: 'secondary' },
}

export default function Index({ documents }) {
  const { flash } = usePage().props
  const fileInputRef = useRef(null)
  const [filePreview, setFilePreview] = useState(null)

  const { data, setData, post, processing, errors, reset } = useForm({
    document_name: '',
    type: '',
    file: null,
  })

  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  function handleFileChange(e) {
    const file = e.target.files[0]
    setData('file', file)
    setFilePreview(file ? { name: file.name, sizeMb: (file.size / 1024 / 1024).toFixed(2) } : null)
  }

  function handleSubmit(e) {
    e.preventDefault()
    post(r('student.documents.store'), {
      forceFormData: true,
      onSuccess: () => {
        reset()
        setFilePreview(null)
        if (fileInputRef.current) fileInputRef.current.value = ''
      },
    })
  }

  function handleDelete(document) {
    if (confirm(`Yakin ingin menghapus dokumen ${document.document_name}?`)) {
      router.delete(r('student.documents.destroy', document.id))
    }
  }

  return (
    <StudentLayout>
      <div className="space-y-6">
        <PageHeader
          title="Manajemen Dokumen"
          description="Kelola dokumen magang Anda"
          actions={<Badge variant="secondary">Total: {documents.length}</Badge>}
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}
        {flash?.error && <FlashBanner type="danger">{flash.error}</FlashBanner>}

        <Card>
          <CardHeader className="border-b">
            <CardTitle className="flex items-center gap-2">
              <Upload className="h-4 w-4 text-muted-foreground" /> Upload Dokumen Baru
            </CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="grid grid-cols-1 gap-5 md:grid-cols-3">
                <div className="space-y-2">
                  <Label htmlFor="document_name">Nama Dokumen</Label>
                  <Input
                    id="document_name"
                    value={data.document_name}
                    onChange={(e) => setData('document_name', e.target.value)}
                    placeholder="Masukkan nama dokumen"
                  />
                  {errors.document_name && <p className="text-sm text-destructive">{errors.document_name}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="type">Tipe Dokumen</Label>
                  <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                    <SelectTrigger id="type" className="w-full">
                      <SelectValue placeholder="Pilih tipe dokumen" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="proposal">Proposal</SelectItem>
                      <SelectItem value="laporan_akhir">Laporan Akhir</SelectItem>
                      <SelectItem value="lainnya">Lainnya</SelectItem>
                    </SelectContent>
                  </Select>
                  {errors.type && <p className="text-sm text-destructive">{errors.type}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="file">File Dokumen</Label>
                  <input
                    ref={fileInputRef}
                    id="file"
                    type="file"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                    onChange={handleFileChange}
                    className="block w-full rounded-md border border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                  />
                  <p className="text-xs text-muted-foreground">Format: PDF, DOC, DOCX (Max: 10MB)</p>
                  {filePreview && <p className="text-xs text-primary">{filePreview.name} ({filePreview.sizeMb} MB)</p>}
                  {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
                </div>
              </div>

              <div className="flex justify-end">
                <Button type="submit" disabled={processing}>
                  <Upload /> Upload Dokumen
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <div className="border-b border-border px-4 py-3.5">
            <h3 className="text-base font-semibold text-foreground">Daftar Dokumen</h3>
          </div>
          {documents.length ? (
            <Table>
              <TableHeader>
                <TableRow className="hover:bg-transparent">
                  <TableHead>Nama Dokumen</TableHead>
                  <TableHead>Tipe</TableHead>
                  <TableHead>Tanggal Upload</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {documents.map((document) => {
                  const config = typeConfig[document.type] ?? typeConfig.lainnya
                  return (
                    <TableRow key={document.id}>
                      <TableCell>
                        <p className="text-sm font-medium text-foreground">{document.document_name}</p>
                        <p className="text-xs text-muted-foreground">{document.original_filename}</p>
                      </TableCell>
                      <TableCell>
                        <Badge variant={config.variant}>{config.label}</Badge>
                      </TableCell>
                      <TableCell className="tabular-nums text-muted-foreground">
                        {new Date(document.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5">
                          <Button asChild size="xs" variant="outline">
                            <a href={r('student.documents.download', document.id)} target="_blank" rel="noreferrer">Lihat</a>
                          </Button>
                          <Button
                            type="button"
                            size="xs"
                            variant="ghost"
                            className="text-destructive hover:text-destructive"
                            onClick={() => handleDelete(document)}
                          >
                            <Trash2 /> Hapus
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          ) : (
            <EmptyState
              icon={FileText}
              title="Belum ada dokumen"
              description="Upload dokumen pertama Anda untuk memulai."
            />
          )}
        </Card>
      </div>
    </StudentLayout>
  )
}
