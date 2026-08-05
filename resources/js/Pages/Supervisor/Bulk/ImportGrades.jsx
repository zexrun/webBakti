import { useRef } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, Download, UploadCloud, CheckCircle2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import FlashBanner from '@/Components/FlashBanner'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Button } from '@/Components/ui/button'
import UploadProgress from '@/Components/UploadProgress'
import { cn } from '@/lib/utils'

export default function ImportGrades() {
  const { flash } = usePage().props
  const fileInputRef = useRef(null)
  const { data, setData, post, processing, progress, errors } = useForm({ file: null })
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.bulk.import-grades.submit'), { forceFormData: true })
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <PageHeader
          title="Impor Nilai"
          description="Impor file CSV untuk memasukkan nilai banyak submission sekaligus"
          actions={
            <Button asChild variant="outline" size="sm">
              <Link href={r('supervisor.submissions.index')}>
                <ArrowLeft /> Kembali
              </Link>
            </Button>
          }
        />

        {flash?.success && <FlashBanner type="success">{flash.success}</FlashBanner>}

        {flash?.import_errors && flash.import_errors.length > 0 && (
          <FlashBanner type="danger" className="font-normal">
            <p className="font-semibold">Error Log:</p>
            <ul className="mt-1 space-y-1">
              {flash.import_errors.map((error, index) => (
                <li key={index}>{error}</li>
              ))}
            </ul>
          </FlashBanner>
        )}

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="lg:col-span-2">
            <Card>
              <CardHeader className="border-b">
                <CardTitle>Impor File CSV</CardTitle>
              </CardHeader>
              <CardContent className="pt-6">
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="space-y-2">
                    <Label>Pilih File CSV</Label>
                    <button
                      type="button"
                      onClick={() => fileInputRef.current?.click()}
                      className="w-full rounded-lg border-2 border-dashed border-border p-8 text-center transition-colors duration-150 hover:bg-muted"
                    >
                      <UploadCloud className="mx-auto mb-3 h-10 w-10 text-muted-foreground" />
                      <p className="text-sm font-medium text-foreground">Klik untuk memilih file CSV</p>
                      <p className="mt-1 text-xs text-muted-foreground">File harus berformat CSV, max 5MB</p>
                    </button>
                    <input
                      ref={fileInputRef}
                      type="file"
                      accept=".csv,.txt"
                      className="hidden"
                      onChange={(e) => setData('file', e.target.files[0] ?? null)}
                    />
                    <p className={cn('flex items-center gap-1.5 text-sm', data.file ? 'text-green-700 dark:text-green-400' : 'text-muted-foreground')}>
                      {data.file && <CheckCircle2 className="h-4 w-4" />}
                      {data.file ? data.file.name : 'File belum dipilih'}
                    </p>
                    {errors.file && <p className="text-sm text-destructive">{errors.file}</p>}
                  </div>

                  <UploadProgress progress={progress} />

                  <Button type="submit" disabled={processing} className="w-full">Impor Nilai</Button>
                </form>
              </CardContent>
            </Card>
          </div>

          <Card className="self-start">
            <CardHeader className="border-b">
              <CardTitle>Panduan Format CSV</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4 pt-6">
              <Button asChild variant="outline" className="w-full">
                <a href={r('supervisor.bulk.import-grades.template')}>
                  <Download /> Unduh Template Nilai
                </a>
              </Button>

              <div>
                <p className="mb-2 text-sm font-medium text-foreground">Struktur File</p>
                <pre className="overflow-x-auto rounded-lg bg-muted p-3 font-mono text-xs text-foreground">{`submission_id,grade,feedback
1,85,Bagus
2,92,Excellent work
3,78,Perlu perbaikan`}</pre>
              </div>

              <div>
                <p className="mb-2 text-sm font-medium text-foreground">Penjelasan Kolom</p>
                <ul className="space-y-1.5 text-sm text-muted-foreground">
                  <li><strong className="text-foreground">submission_id:</strong> ID submission (wajib)</li>
                  <li><strong className="text-foreground">grade:</strong> Nilai 0–100 (wajib)</li>
                  <li><strong className="text-foreground">feedback:</strong> Catatan/feedback (opsional)</li>
                </ul>
              </div>

              <FlashBanner type="info" className="font-normal">
                <strong>Tips:</strong> Klik "Unduh Template Nilai" untuk mendapatkan file CSV yang sudah
                terisi submission_id, nama siswa, dan judul tugas dari seluruh submission Anda saat ini —
                tinggal isi kolom grade dan feedback, lalu upload kembali.
              </FlashBanner>
            </CardContent>
          </Card>
        </div>
      </div>
    </SupervisorLayout>
  )
}
