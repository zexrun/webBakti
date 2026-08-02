# Sistem Desain webBakti — Acuan Redesign UI

Arah: **editorial minimalism**. Fraunces (serif) memberi suara editorial pada
heading, body Geist yang tenang, latar warm off-white seperti kertas, aksen
indigo/amber yang hemat, border tipis, grid presisi. Satu fokus per layar.

Dokumen ini mengikat untuk semua halaman yang diredesign. Batasan teknis
di `docs/UI_CONTEXT_FOR_AI.md` (§5.2, §6) tetap berlaku di atas dokumen ini.

## 1. Spacing

| Konteks | Nilai |
|---|---|
| Antar section halaman | `space-y-6` |
| Grid kartu statistik | `gap-4` |
| Grid konten 2 kolom (lg) | `gap-6` |
| Di dalam CardContent | `space-y-4` |
| Field form | `space-y-5` (antar field), `space-y-2` (label→input) |
| Padding konten utama layout | `p-4 sm:p-6 lg:p-8` |

Konten halaman dibatasi `max-w` sesuai jenis: form tunggal `max-w-2xl`,
halaman detail `max-w-4xl`, list/tabel/dashboard penuh (tanpa max-w).

## 2. Tipografi

- `<h1>`–`<h4>` otomatis Fraunces (via `@layer base`) — **jangan** tambah class font.
- Judul halaman: `text-2xl font-bold tracking-tight` — selalu lewat `PageHeader`, tidak pernah di dalam Card.
- `CardTitle`: `text-base` (default shadcn). Deskripsi: `text-sm text-muted-foreground`.
- Label mikro (header tabel, eyebrow): `text-xs font-medium uppercase tracking-wider text-muted-foreground`.
- Angka data: selalu `tabular-nums`; di tabel rata kanan (`text-right`).
- Judul empty state: sans (`text-sm font-medium`), bukan serif — feedback UI, bukan heading editorial.

## 3. Warna

- **Primary (indigo)**: satu tombol primary per view; link; state aktif sidebar.
- **Accent (amber)**: state aktif sidebar, badge tertentu. JANGAN untuk hover permukaan luas.
- **Hover permukaan** (baris tabel, item list): `hover:bg-muted` — bukan `hover:bg-accent`.
- **Destructive**: hanya di modal konfirmasi + aksi ireversibel. Aksi hapus di baris tabel = ghost/outline dengan `text-destructive`, bukan tombol merah solid.
- **Warna kategori semantik dipertahankan** (biru=user, hijau=mahasiswa/sukses, ungu=pembimbing, oranye=perhatian, cyan=presensi…). Gunakan pasangan `bg-*-100 text-*-700` + dark `dark:bg-*-500/15 dark:text-*-300` — opacity modifier aman karena warna bawaan Tailwind, bukan var custom.
- Larangan §5.2: tanpa opacity modifier pada warna var custom; var oklch tidak dibungkus fungsi warna; warna baru = `oklch(L C H)` di `:root` DAN `.dark`.

## 4. Komponen pola (pakai, jangan tulis ulang)

| Pola | Komponen | Catatan |
|---|---|---|
| Header halaman | `Components/PageHeader` | title + description + actions |
| Kartu statistik | `Components/StatCard` | prop `tone` = warna kategori semantik |
| Empty state | `Components/EmptyState` | icon + title + description + action |
| Tabel | `Components/ui/table` | density sudah baked-in (header h-10 uppercase, cell px-4 py-2.5, hover muted) |
| Pagination | `Components/Pagination` | links Laravel paginator |
| Form control | `Components/ui/*` | Select/Checkbox = Radix API (§6) |

Tabel di dalam Card: `<CardContent className="p-0">`, tanpa band `bg-muted`
pembungkus; footer pagination `border-t px-4 py-3`.

## 5. Tombol

- 1 primary per view (aksi utama). Sekunder: `outline`. Tersier/ikon: `ghost`.
- Aksi baris tabel: `size="xs"` `variant="outline"` (edit) / `variant="ghost"` + `text-destructive` (hapus).
- Ikon dalam tombol: `h-4 w-4`; ikon sidebar `h-5 w-5`; tile StatCard `h-5 w-5`.
- Emoji DILARANG sebagai ikon — selalu lucide.

## 6. Permukaan & detail

- Card: border tipis + shadow halus (sudah di `ui/card`). Jangan tumpuk shadow tebal.
- Radius: Card `rounded-xl` (default), kontrol `rounded-md`. Jangan campur radius lain.
- Transisi: `transition-colors duration-150` untuk hover/focus; jangan animasi dekoratif.
- Focus: andalkan focus-visible ring bawaan shadcn; jangan `outline-none` tanpa ganti.
- Modal manual (overlay `bg-black/50`): pertahankan polanya, samakan panel dengan Card.

## 7. Responsif & dark mode

- Uji 375 / 768 / 1440. Tabel lebar: scroll di container-nya (sudah ditangani `ui/table`).
- Grid statistik: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4` (atau 3).
- Setiap warna hardcode Tailwind (`bg-blue-100` dst.) wajib punya varian `dark:`.
- Toggle dark mode: class `dark` di `<html>`, preferensi di localStorage (Tahap 1).
