import { useRef, useState } from 'react'
import { router, useForm, usePage } from '@inertiajs/react'
import { FileText, Upload, Eye, Trash2, Calendar } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'

const typeConfig = {
  proposal: { label: '📋 Proposal', variant: 'default' },
  laporan_akhir: { label: '📊 Laporan Akhir', variant: 'success' },
  lainnya: { label: '📄 Lainnya', variant: 'secondary' },
}

export default function Index({ documents, student }) {
  const { flash, errors: pageErrors } = usePage().props
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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Manajemen Dokumen</h1>
            <p className="text-muted-foreground">Kelola dokumen magang Anda dengan mudah 📄</p>
          </div>
          <Badge variant="secondary" className="text-sm">
            Total Dokumen: {documents.length}
          </Badge>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4">
            <p className="font-medium text-green-800">{flash.success}</p>
          </div>
        )}
        {flash?.error && (
          <div className="rounded-lg border border-red-200 bg-red-50 p-4">
            <p className="font-medium text-red-800">{flash.error}</p>
          </div>
        )}

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-base">
              <Upload className="h-5 w-5 text-blue-600" /> Upload Dokumen Baru
            </CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
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
                      <SelectItem value="proposal">📋 Proposal</SelectItem>
                      <SelectItem value="laporan_akhir">📊 Laporan Akhir</SelectItem>
                      <SelectItem value="lainnya">📄 Lainnya</SelectItem>
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
                    className="block w-full rounded-md border-2 border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                  />
                  <p className="text-xs text-muted-foreground">Format: PDF, DOC, DOCX (Max: 10MB)</p>
                  {filePreview && (
                    <p className="text-xs text-primary">
                      {filePreview.name} ({filePreview.sizeMb} MB)
                    </p>
                  )}
                  {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
                </div>
              </div>

              <div className="text-right">
                <Button type="submit" disabled={processing}>
                  <Upload className="h-4 w-4" /> Upload Dokumen
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-base">
              <FileText className="h-5 w-5 text-blue-600" /> Daftar Dokumen
            </CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            {documents.length ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Nama Dokumen</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tipe</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Tanggal Upload</th>
                      <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {documents.map((document) => {
                      const config = typeConfig[document.type] ?? typeConfig.lainnya
                      return (
                        <tr key={document.id} className="hover:bg-accent">
                          <td className="whitespace-nowrap px-6 py-4">
                            <div className="text-sm font-medium text-foreground">{document.document_name}</div>
                            <div className="text-sm text-muted-foreground">{document.original_filename}</div>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4">
                            <Badge variant={config.variant}>{config.label}</Badge>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                            <span className="flex items-center gap-2">
                              <Calendar className="h-4 w-4" />
                              {new Date(document.created_at).toLocaleDateString('id-ID')}
                            </span>
                          </td>
                          <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">
                            <div className="flex items-center gap-3">
                              <a
                                href={r('student.documents.download', document.id)}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200"
                              >
                                <Eye className="h-3 w-3" /> Lihat
                              </a>
                              <button
                                type="button"
                                onClick={() => handleDelete(document)}
                                className="inline-flex items-center gap-1 rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200"
                              >
                                <Trash2 className="h-3 w-3" /> Hapus
                              </button>
                            </div>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <FileText className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-medium text-foreground">Belum ada dokumen</h3>
                <p className="text-muted-foreground">Upload dokumen pertama Anda untuk memulai</p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
