import { Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Upload } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import PageHeader from '@/Components/PageHeader'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Button } from '@/Components/ui/button'

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    document_name: '',
    type: 'proposal',
    file: null,
  })

  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('student.documents.store'), { forceFormData: true })
  }

  return (
    <StudentLayout>
      <div className="mx-auto max-w-2xl space-y-6">
        <PageHeader
          title="Unggah Dokumen"
          description="Tambahkan dokumen magang baru"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('student.documents.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="space-y-2">
                <Label htmlFor="document_name">Nama Dokumen</Label>
                <Input
                  id="document_name"
                  value={data.document_name}
                  onChange={(e) => setData('document_name', e.target.value)}
                  required
                />
                {errors.document_name && <p className="text-sm text-destructive">{errors.document_name}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="type">Tipe Dokumen</Label>
                <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                  <SelectTrigger id="type" className="w-full">
                    <SelectValue />
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
                <Label htmlFor="file">Pilih File</Label>
                <input
                  id="file"
                  type="file"
                  required
                  onChange={(e) => setData('file', e.target.files[0])}
                  className="block w-full rounded-md border border-dashed border-input px-3 py-2 text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
              </div>

              <div className="flex justify-end border-t border-border pt-5">
                <Button type="submit" disabled={processing}>
                  <Upload /> Upload
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
