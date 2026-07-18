import { useRef } from 'react'
import { Link, useForm, usePage } from '@inertiajs/react'
import { UploadCloud } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'

export default function ImportGrades() {
  const { flash } = usePage().props
  const fileInputRef = useRef(null)
  const { data, setData, post, processing, errors } = useForm({ file: null })
  const r = (name) => (window.route ? window.route(name) : '#')

  function handleSubmit(e) {
    e.preventDefault()
    post(r('supervisor.bulk.import-grades'), { forceFormData: true })
  }

  return (
    <SupervisorLayout>
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground">Import Nilai Massal</h1>
            <p className="text-muted-foreground">Upload file CSV untuk mengimport nilai multiple submission sekaligus</p>
          </div>
          <Link href={r('supervisor.submissions.index')} className="text-sm font-medium text-primary hover:underline">
            ← Kembali
          </Link>
        </div>

        {flash?.success && (
          <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{flash.success}</div>
        )}

        {flash?.import_errors && flash.import_errors.length > 0 && (
          <div className="rounded-lg border border-red-200 bg-red-50 p-4">
            <h3 className="mb-2 font-semibold text-red-800">Error Log:</h3>
            <ul className="space-y-1 text-sm text-red-700">
              {flash.import_errors.map((error, index) => (
                <li key={index}>{error}</li>
              ))}
            </ul>
          </div>
        )}

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="lg:col-span-2">
            <Card>
              <CardContent className="p-6">
                <h2 className="mb-4 text-lg font-semibold text-foreground">Upload File CSV</h2>
                <form onSubmit={handleSubmit}>
                  <div className="mb-6">
                    <label className="mb-2 block text-sm font-medium text-foreground">Pilih File CSV</label>
                    <div
                      onClick={() => fileInputRef.current?.click()}
                      className="cursor-pointer rounded-lg border-2 border-dashed border-border p-8 text-center transition hover:bg-accent"
                    >
                      <UploadCloud className="mx-auto mb-3 h-12 w-12 text-muted-foreground" />
                      <p className="font-medium text-foreground">Klik untuk memilih file CSV</p>
                      <p className="mt-1 text-sm text-muted-foreground">File harus berformat CSV, max 5MB</p>
                      <input
                        ref={fileInputRef}
                        type="file"
                        accept=".csv,.txt"
                        className="hidden"
                        onChange={(e) => setData('file', e.target.files[0] ?? null)}
                      />
                    </div>
                    <p className={`mt-2 text-sm ${data.file ? 'text-green-600' : 'text-muted-foreground'}`}>
                      {data.file ? `✓ ${data.file.name}` : 'File belum dipilih'}
                    </p>
                    {errors.file && <p className="mt-1 text-sm text-destructive">{errors.file}</p>}
                  </div>

                  <Button type="submit" disabled={processing} className="w-full bg-green-600 hover:bg-green-700">
                    Import Nilai
                  </Button>
                </form>
              </CardContent>
            </Card>
          </div>

          <Card>
            <CardContent className="p-6">
              <h2 className="mb-4 text-lg font-semibold text-foreground">Panduan Format CSV</h2>
              <div className="space-y-4">
                <div>
                  <h3 className="mb-2 font-medium text-foreground">Struktur File:</h3>
                  <div className="overflow-x-auto rounded bg-muted p-3 font-mono text-xs text-foreground">
                    <p>submission_id,grade,feedback</p>
                    <p>1,85,Bagus</p>
                    <p>2,92,Excellent work</p>
                    <p>3,78,Perlu perbaikan</p>
                  </div>
                </div>

                <div>
                  <h3 className="mb-2 font-medium text-foreground">Penjelasan Kolom:</h3>
                  <ul className="space-y-2 text-sm text-foreground/80">
                    <li><strong>submission_id:</strong> ID submission (wajib)</li>
                    <li><strong>grade:</strong> Nilai 0-100 (wajib)</li>
                    <li><strong>feedback:</strong> Catatan/feedback (opsional)</li>
                  </ul>
                </div>

                <div className="rounded border border-blue-200 bg-blue-50 p-3">
                  <p className="text-sm text-blue-800">
                    <strong>Tips:</strong> Submission ID bisa dilihat di halaman penilaian atau dari detail submission.
                  </p>
                </div>

                <Link href={r('supervisor.submissions.index')} className="text-sm font-medium text-primary hover:underline">
                  → Lihat submission
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
          <strong>Download template CSV:</strong>{' '}
          <code className="rounded bg-yellow-100 px-2 py-1">submission_id,grade,feedback</code>
        </div>
      </div>
    </SupervisorLayout>
  )
}
