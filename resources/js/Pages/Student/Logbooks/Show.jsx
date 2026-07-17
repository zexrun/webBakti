import { Link } from '@inertiajs/react'
import { ArrowLeft, Clock, Smile, FileText, Image as ImageIcon, Pencil } from 'lucide-react'
import StudentLayout from '@/Layouts/StudentLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

const feelingEmoji = {
  Senang: '😊',
  'Biasa Saja': '😐',
  'Menemukan Kendala': '😥',
}

function durationLabel(start, end) {
  const [sh, sm] = start.split(':').map(Number)
  const [eh, em] = end.split(':').map(Number)
  const minutes = eh * 60 + em - (sh * 60 + sm)
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return hours > 0 ? `${hours} jam ${mins} menit` : `${mins} menit`
}

export default function Show({ logbook }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')

  return (
    <StudentLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <div className="flex items-center justify-between">
          <Link href={r('student.logbooks.index')} className="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
            <ArrowLeft className="h-4 w-4" /> Kembali ke Daftar Laporan
          </Link>
          <Link href={r('student.logbooks.edit', logbook.id)}>
            <Button variant="secondary" size="sm">
              <Pencil className="h-4 w-4" /> Edit
            </Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <div className="flex items-start justify-between">
              <div>
                <Badge className="mb-2">
                  {new Date(logbook.activity_date).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                  })}
                </Badge>
                <CardTitle>{logbook.title}</CardTitle>
              </div>
              {logbook.is_verified ? (
                <Badge variant="success">Sudah Dilihat</Badge>
              ) : (
                <Badge variant="warning">Menunggu Review</Badge>
              )}
            </div>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div className="rounded-lg bg-muted p-4">
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <Clock className="h-4 w-4" /> Waktu Kegiatan
                </p>
                <p className="text-lg font-bold text-foreground">
                  {logbook.start_time.slice(0, 5)} - {logbook.end_time.slice(0, 5)}
                </p>
                <p className="text-xs text-muted-foreground">Durasi: {durationLabel(logbook.start_time, logbook.end_time)}</p>
              </div>

              <div className="rounded-lg bg-muted p-4">
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <Smile className="h-4 w-4" /> Perasaan
                </p>
                <p className="text-2xl">{feelingEmoji[logbook.feeling] ?? '😊'}</p>
                <p className="text-sm font-semibold text-foreground">{logbook.feeling}</p>
              </div>
            </div>

            <div>
              <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                <FileText className="h-4 w-4" /> Deskripsi Kegiatan
              </p>
              <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                {logbook.description}
              </div>
            </div>

            {logbook.file_path && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <ImageIcon className="h-4 w-4" /> Dokumentasi Kegiatan
                </p>
                <a href={`/storage/${logbook.file_path}`} target="_blank" rel="noreferrer">
                  <img
                    src={`/storage/${logbook.file_path}`}
                    alt={logbook.title}
                    className="w-full rounded-lg border border-border object-cover"
                  />
                </a>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </StudentLayout>
  )
}
