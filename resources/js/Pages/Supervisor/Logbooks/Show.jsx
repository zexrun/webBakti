import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { AnimatePresence, motion } from 'motion/react'
import { ArrowLeft, Clock, Smile, FileText, Image as ImageIcon, Printer, MessageSquare, CheckCircle2, X } from 'lucide-react'
import SupervisorLayout from '@/Layouts/SupervisorLayout'
import PageHeader from '@/Components/PageHeader'
import UserCell from '@/Components/UserCell'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Textarea } from '@/Components/ui/textarea'

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
  const [feedbackOpen, setFeedbackOpen] = useState(false)
  const [feedbackText, setFeedbackText] = useState(logbook.feedback ?? '')
  const [submitting, setSubmitting] = useState(false)

  function openFeedbackModal() {
    setFeedbackText(logbook.feedback ?? '')
    setFeedbackOpen(true)
  }

  function submitFeedback(e) {
    e.preventDefault()
    setSubmitting(true)
    router.post(r('supervisor.logbooks.feedback', logbook.id), { feedback: feedbackText }, {
      preserveScroll: true,
      onFinish: () => {
        setSubmitting(false)
        setFeedbackOpen(false)
      },
    })
  }

  return (
    <SupervisorLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <PageHeader
          title="Detail Logbook"
          description="Aktivitas harian mahasiswa bimbingan"
          actions={
            <>
              <Button asChild variant="outline" size="sm">
                <a href={r('supervisor.logbooks.export-pdf', logbook.id)} target="_blank" rel="noreferrer">
                  <Printer /> Cetak
                </a>
              </Button>
              <Button size="sm" onClick={openFeedbackModal}>
                <MessageSquare /> Kirim Feedback
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
                <UserCell name={logbook.student?.user?.name} photoUrl={logbook.student?.user?.profile_photo_url} subtitle={logbook.student?.nim ?? 'NIM belum diisi'} />
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

            {logbook.feedback && (
              <div>
                <p className="mb-2 flex items-center gap-2 text-sm font-medium text-foreground">
                  <MessageSquare className="h-4 w-4 text-muted-foreground" /> Feedback Pembimbing
                </p>
                <div className="whitespace-pre-wrap rounded-lg bg-muted p-4 text-sm text-foreground">
                  {logbook.feedback}
                </div>
                {logbook.feedback_at && (
                  <p className="mt-1 text-xs text-muted-foreground">
                    Dikirim {new Date(logbook.feedback_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  </p>
                )}
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      <AnimatePresence>
        {feedbackOpen && (
          <motion.div
            className="fixed inset-0 z-50 bg-black/50"
            onClick={(e) => e.target === e.currentTarget && setFeedbackOpen(false)}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.15 }}
          >
            <div className="flex min-h-screen items-center justify-center p-4">
              <motion.div
                className="w-full max-w-md rounded-xl border border-border bg-card shadow-lg"
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                transition={{ duration: 0.15 }}
              >
                <div className="flex items-center justify-between gap-4 border-b border-border px-6 py-4">
                  <h3 className="text-lg font-semibold text-foreground">Kirim Feedback</h3>
                  <button
                    type="button"
                    onClick={() => setFeedbackOpen(false)}
                    aria-label="Tutup"
                    className="rounded-md p-1 text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
                  >
                    <X className="h-5 w-5" />
                  </button>
                </div>

                <form onSubmit={submitFeedback} className="space-y-4 p-6">
                  <Textarea
                    rows={6}
                    value={feedbackText}
                    onChange={(e) => setFeedbackText(e.target.value)}
                    placeholder="Tulis feedback untuk logbook ini..."
                    required
                  />
                  <div className="flex gap-2">
                    <Button type="button" variant="outline" onClick={() => setFeedbackOpen(false)} className="flex-1">
                      Batal
                    </Button>
                    <Button type="submit" disabled={submitting} className="flex-1">
                      {submitting ? 'Mengirim...' : 'Kirim Feedback'}
                    </Button>
                  </div>
                </form>
              </motion.div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </SupervisorLayout>
  )
}
