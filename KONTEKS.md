# Prompt: Redesign UI Menyeluruh — webBakti

> Salin seluruh isi di bawah ini ke Claude Code sebagai instruksi awal.
> Baca dulu file `KONTEKS.md` (dokumen konteks project) sebelum menyentuh kode apa pun.

---

## Peran & Tujuan

Kamu redesigner UI/UX untuk aplikasi **webBakti** (Laravel 12 + React 19 + Inertia.js v3 + Vite 5 + Tailwind v3 + shadcn/ui). Tugasmu: **redesign menyeluruh seluruh halaman** menjadi UI **minimalis, rapi, konsisten, profesional** — memanfaatkan komponen shadcn/ui yang sudah tersedia. Hasil akhir harus terasa dirancang oleh desainer manusia, **bukan template generik AI**.

## Aturan Wajib Sebelum Mulai (baca, jangan lewati)

1. **Baca `KONTEKS.md` sampai habis.** Semua batasan §5.2 (dua bug fondasi), §6 (API shadcn/Radix), §7, dan §11 (larangan) mengikat. Melanggarnya merusak build tanpa error.
2. **Jangan sentuh backend** (controller, model, migration) kecuali ada bug rendering data nyata.
3. **Jangan edit** `ziggy.js`, `Layout.jsx`, `Pages/Dashboard.jsx` (dead code — verifikasi via grep dulu).
4. **Kerja bertahap, minta review per-tahap.** Jangan ubah 40 file sekaligus lalu klaim selesai.

## Hard Constraints Teknis (dari §5.2 & §6 — sumber bug paling sering)

- **DILARANG** opacity modifier pada warna custom var: `bg-primary/70`, `text-foreground/50`, `bg-card/95`, dst → menghasilkan CSS kosong tanpa error. Butuh varian pudar? Buat variabel solid baru di `app.css` (light + dark) + daftar di `tailwind.config.js`, ATAU pakai `color-mix(in oklch, var(--x) 50%, transparent)`.
- **DILARANG** membungkus var warna dengan fungsi warna lain. Var sudah `oklch()` penuh. Di config cukup `var(--nama)`.
- Warna baru selalu format `oklch(L C H)`, definisikan di `:root` DAN `.dark`.
- **Select** = Radix compound, bukan native. Pakai `value` + `onValueChange`. `SelectItem` tanpa `value=""` (pakai sentinel `value="all"`). Ikuti pola halaman sejenis.
- **Checkbox** Radix: `checked` + `onCheckedChange`, bukan `onChange`.
- Heading `<h1>`–`<h4>` sudah otomatis Fraunces via `@layer base`. Jangan tambah class font manual.

## Prinsip Desain (anti AI-slop — ini inti tugas)

Terapkan, jangan sekadar sebut:

1. **Hirarki visual jelas.** Satu fokus utama per layar. Ukuran, berat, dan spasi font mencerminkan kepentingan — bukan semua elemen berteriak sama keras.
2. **Spasi sebagai alat desain.** Whitespace konsisten (skala `space-y`/`gap` seragam per konteks). Jangan padat, jangan kosong berlebihan.
3. **Restraint warna.** Primary indigo + accent amber untuk aksi/state, netral untuk sisanya. Warna kategori semantik (§7 poin 1) **dipertahankan** — jangan diseragamkan.
4. **Konsistensi > kreativitas per-halaman.** Card, tabel, form, empty state, loading, badge status: satu pola dipakai di mana-mana. Ekstrak jadi komponen bila berulang.
5. **Density tepat guna.** Tabel besar (Reports, Submissions) butuh baris rapat + alignment angka rata kanan. Dashboard butuh napas.
6. **Detail yang membedakan dari template:** transisi halus (hover/focus), focus ring aksesibel, alignment presisi (grid), radius & border konsisten, ikon lucide berukuran seragam dan sejajar teks.

**Hindari ciri AI-slop:** gradient ungu-biru dekoratif tanpa alasan, emoji sebagai ikon, card berbayang tebal mengambang, teks marketing berlebihan, bento grid asal, semua tombol jadi primary, spacing acak antar section.

## Cakupan (semua halaman, per-role bertahap)

Urutan kerja disarankan:

1. **Audit + sistem desain dulu.** Screenshot state sekarang (Playwright headless, akun test §8). Tetapkan token spacing/typography/pattern. Buat/rapikan komponen berulang di `Components/` (EmptyState, PageHeader, DataTable wrapper, StatCard, dll bila belum ada).
2. **Dark mode toggle** (diminta sekarang): variabel `.dark` sudah ada. Tambah toggle di layout (Admin/Supervisor/Student), simpan preferensi (localStorage), set class `dark` di root. Pastikan semua warna kontras di kedua mode.
3. **Admin** → **Supervisor** → **Student** → **halaman lintas-role** (Announcements/Messages/Profile/Notifications/Search) → **Auth**.
4. Prioritaskan §10 (belum diverifikasi): halaman detail/edit dalam, tabel besar/kompleks, responsif mobile.

## Definition of Done per halaman

- Konsisten dengan sistem desain yang ditetapkan di tahap 1.
- Responsif: uji viewport 375px, 768px, 1440px.
- Light + dark mode dua-duanya benar.
- Tidak ada opacity modifier terlarang; tidak ada API Radix salah.
- Screenshot before/after dilampirkan.

## Verifikasi

```bash
npm install && npm run build
php artisan serve --port=8000
rm -f public/hot
# Playwright screenshot per halaman setelah login (akun test di KONTEKS §8)
```

## Alur Kerja yang Diharapkan

1. Konfirmasi paham konteks + rencana tahap (jangan langsung ngoding).
2. Kerjakan satu tahap → tunjukkan diff ringkas + screenshot → tunggu approve.
3. Lanjut tahap berikutnya.

Kalau ragu soal makna/pola, tanya — jangan berasumsi.