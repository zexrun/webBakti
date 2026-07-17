import { useForm } from '@inertiajs/react'
import { Upload } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select } from '@/Components/ui/select'
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
        <h1 className="text-2xl font-bold text-foreground">Upload Dokumen Baru</h1>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
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
                <Select id="type" value={data.type} onChange={(e) => setData('type', e.target.value)}>
                  <option value="proposal">Proposal</option>
                  <option value="laporan_akhir">Laporan Akhir</option>
                  <option value="lainnya">Lainnya</option>
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
                  className="block w-full text-sm text-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary-foreground"
                />
                {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
              </div>

              <div className="flex justify-end">
                <Button type="submit" disabled={processing}>
                  <Upload className="h-4 w-4" /> Upload
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
