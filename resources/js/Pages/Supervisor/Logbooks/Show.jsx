import { Link } from '@inertiajs/react'
import { ArrowLeft, Clock, Smile, FileText, Image as ImageIcon, Printer, Mail, CheckCircle2 } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import UserCell from '@/Components/UserCell'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'

const feelingEmoji = {
  Senang: '😊', Biasa: '😐', Sedih: '😢', Bingung: '😕', Semangat: '🤩',
  Lelah: '😴', 'Biasa Saja': '😐', 'Menemukan Kendala': '😥',
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
    <SupervisorLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <PageHeader
          title="Detail Logbook"
          description="Aktivitas harian mahasiswa bimbingan"
          actions={
            <>
              <Button variant="outline" size="sm" onClick={() => window.print()}>
                <Printer /> Cetak
              </Button>
              <Button asChild size="sm">
                <a href={`mailto:${logbook.student?.user?.email}?subject=Feedback untuk Laporan ${logbook.title}`}>
                  <Mail /> Kirim Feedback
                </a>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link href={r('supervisor.logbooks.index')}>
                  <ArrowLeft /> Kembali
                </Link>
              </Button>
            </>
          }
        />

        <Card>
          <div className="flex flex-col gap-4 border-b border-border p-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <Badge variant="outline" className="mb-2 tabular-nums">
                {new Date(logbook.activity_date).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}
              </Badge>
              <h2 className="font-heading text-lg font-semibold text-foreground">{logbook.title}</h2>
              <div className="mt-3">
                <UserCell name={logbook.student?.user?.name} subtitle={logbook.student?.nim ?? 'NIM belum diisi'} />
              </div>
            </div>
            <Badge variant="success">
              <CheckCircle2 /> Telah Diverifikasi
            </Badge>
          </div>

          <CardContent className="space-y-5 p-5">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div className="rounded-lg bg-muted p-4">
                <p className="flex items-center gap-2 text-sm font-medium text-foreground">
                  <Clock className="h-4 w-4 text-muted-foreground" /> Waktu Kegiatan
                </p>
                <p className="mt-1 text-lg font-bold tabular-nums text-foreground">
                  {logbook.start_time.slice(0, 5)}–{logbook.end_time.slice(0, 5)}
                </p>
                <p className="text-xs text-muted-foreground">Durasi: {durationLabel(logbook.start_time, logbook.end_time)}</p>
              </div>

              <div className="rounded-lg bg-muted p-4">
                <p className="flex items-center gap-2 text-sm font-medium text-foreground">
                  <Smile className="h-4 w-4 text-muted-foreground" /> Perasaan
                </p>
                <p className="mt-1 flex items-center gap-2">
                  <span className="text-2xl" aria-hidden="true">{feelingEmoji[logbook.feeling] ?? '😊'}</span>
                  <span className="text-sm font-semibold text-foreground">{logbook.feeling}</span>
                </p>
              </div>
            </div>

            <div>
              <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                <FileText className="h-4 w-4 text-muted-foreground" /> Deskripsi Kegiatan
              </p>
              <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                {logbook.description}
              </div>
            </div>

            {logbook.file_path && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <ImageIcon className="h-4 w-4 text-muted-foreground" /> Foto Lampiran
                </p>
                <a href={`/storage/${logbook.file_path}`} target="_blank" rel="noreferrer">
                  <img
                    src={`/storage/${logbook.file_path}`}
                    alt="Foto Kegiatan"
                    className="w-full rounded-lg border border-border object-cover"
                  />
                </a>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </SupervisorLayout>
  )
}
