# Konteks Project untuk Perbaikan UI — webBakti

Dokumen ini disiapkan untuk AI/asisten lain yang akan membantu memperbaiki
tampilan (UI/UX) aplikasi webBakti. Baca ini dulu sebelum mengubah kode
supaya tidak mengulang kesalahan yang sudah ditemukan, dan tidak merusak
hal yang sudah bekerja dengan benar.

## 1. Apa aplikasi ini

Sistem manajemen program magang (internship) berbasis web, dipakai oleh
3 role: **Admin**, **Supervisor** (dosen pembimbing), **Student**
(mahasiswa). Fitur utama: absensi (check-in/out dengan foto+GPS), tugas
& penilaian, logbook harian, dokumen, pengumuman, pesan internal,
plotting pembimbing, monitoring, dan sertifikat kelulusan (PDF).

## 2. Stack teknis

- **Backend**: Laravel 12 (PHP)
- **Frontend**: React 19 + Inertia.js v3.1 (bukan SPA terpisah — server
  render props ke komponen React lewat `Inertia::render()`)
- **Build**: Vite 5
- **Routing di frontend**: Ziggy (`window.route('nama.route', params)`)
  — hasil generate dari `php artisan ziggy:generate`, tersimpan di
  `resources/js/ziggy.js`. Regenerate ini kalau ada route baru.
- **Styling**: Tailwind CSS **v3** (bukan v4 — penting, lihat §5) +
  komponen shadcn/ui yang di-generate via CLI resmi (base Radix UI)
- **Ikon**: lucide-react
- **Font**: Fraunces (heading/serif) + Geist Variable (body/sans), via
  `@fontsource-variable/*`

## 3. Status migrasi (konteks besar)

Seluruh aplikasi baru saja selesai dimigrasi dari Blade templates lama
ke React + Inertia — proses migrasi module-by-module sudah **selesai
100%**. Blade yang masih tersisa di `resources/views/` hanya:
- `app.blade.php` — root template Inertia (bukan halaman)
- `admin/dashboard.blade.php`, `student/dashboard.blade.php`,
  `supervisor/dashboard.blade.php` — sengaja dual-route (Blade lama +
  versi React di route `*-new`), belum di-cutover penuh
- `supervisor/pdf/*.blade.php` — template DomPDF untuk sertifikat &
  rekap nilai, **tetap Blade selamanya** (PDF generation, tidak relevan
  untuk redesign UI web)

Semua halaman lain sudah React murni di `resources/js/Pages/`.

## 4. Struktur folder frontend yang relevan

```
resources/js/
├── app.jsx                     # entry point Inertia
├── bootstrap.js                 # setup window.axios
├── ziggy.js                     # generated, JANGAN edit manual
├── lib/
│   ├── utils.js                 # cn() = clsx + tailwind-merge
│   └── deadline.js              # helper format deadline tugas
├── hooks/
│   ├── useCamera.js              # dipakai fitur absensi (foto)
│   └── useGeolocation.js         # dipakai fitur absensi (GPS)
├── Components/
│   ├── ui/                       # komponen shadcn (lihat §6)
│   └── Pagination.jsx            # render Laravel paginator links
├── Layouts/
│   ├── AdminLayout.jsx           # sidebar+navbar untuk role admin
│   ├── SupervisorLayout.jsx      # sidebar+navbar untuk role supervisor
│   ├── StudentLayout.jsx         # sidebar+navbar untuk role student
│   ├── RoleLayout.jsx            # auto-pilih 1 dari 3 layout di atas
│   │                              berdasarkan auth.user.role — dipakai
│   │                              halaman lintas-role (Announcements,
│   │                              Messages, Profile, Notifications,
│   │                              Search)
│   ├── GuestLayout.jsx           # untuk halaman auth (login, register, dst)
│   └── Layout.jsx                # ⚠️ SISA FILE LAMA, tidak dipakai di
│                                    mana pun — kemungkinan besar dead
│                                    code dari proof-of-concept awal.
│                                    Cek dulu sebelum hapus/edit.
└── Pages/
    ├── Admin/…
    ├── Supervisor/…
    ├── Student/…
    ├── Auth/…
    ├── Announcements/, Messages/, Notifications/, Profile/, Search/  (lintas-role)
    └── Dashboard.jsx             # ⚠️ sisa proof-of-concept awal
                                     (/test-inertia), BUKAN halaman
                                     dashboard sungguhan
```

Setiap halaman = 1 file di `Pages/`, dibungkus salah satu Layout di
atas. Pola umum di setiap halaman:

```jsx
export default function Index({ data, filters }) {
  const r = (name, params) => (window.route ? window.route(name, params) : '#')
  return (
    <AdminLayout> {/* atau Supervisor/Student/RoleLayout */}
      <div className="space-y-6">
        {/* konten pakai komponen dari @/Components/ui */}
      </div>
    </AdminLayout>
  )
}
```

## 5. Sistem desain saat ini — BARU SAJA DIPERBAIKI, baca baik-baik

### 5.1 Riwayat singkat

1. Awalnya semua komponen shadcn ditulis manual (tiruan gaya shadcn,
   bukan hasil CLI resmi).
2. Kemudian di-migrasi ke **CLI resmi shadcn** (`npx shadcn@latest add
   ...`, base Radix UI, preset "Nova"). Ini mengganti total isi
   `Card`, `Button`, `Badge`, `Input`, `Textarea`, `Label`, `Checkbox`,
   `Progress`, `Select`.
3. Setelah migrasi CLI itu, **tampilan sempat jadi sangat rusak** —
   semua warna terlihat flat abu-abu, card tidak kelihatan (putih di
   atas putih tanpa shadow), teks sidebar nyaris tak terbaca. Dua bug
   fondasi ditemukan dan sudah diperbaiki (lihat §5.2). Redesign warna
   & font (§5.3) dilakukan bersamaan dengan perbaikan bug ini.

### 5.2 Dua bug fondasi yang HARUS diketahui sebelum menyentuh CSS/warna

**Bug #1 — `hsl()` membungkus variabel `oklch()`.**
`tailwind.config.js` awalnya menulis semua warna sebagai
`hsl(var(--primary))` dst. Tapi `resources/css/app.css` mendefinisikan
`--primary` dkk sebagai fungsi warna **oklch() lengkap**, misal
`oklch(0.38 0.11 265)`. Hasilnya adalah CSS tidak valid:
`hsl(oklch(0.38 0.11 265))` — nested color function, browser tidak
bisa parse, fallback ke warna default/tak terduga.

✅ **Sudah diperbaiki**: `tailwind.config.js` sekarang pakai
`var(--primary)` langsung, TANPA pembungkus `hsl()`/`rgb()` apa pun.

⚠️ **Aturan untuk warna baru**: kalau menambah CSS variable warna baru
di `app.css`, selalu pakai format `oklch(L C H)` penuh, dan di
`tailwind.config.js` cukup `var(--nama-variabel)` — jangan pernah
bungkus dengan fungsi warna lain.

**Bug #2 — Opacity modifier Tailwind gagal pada warna custom.**
Class seperti `bg-card/95`, `text-sidebar-foreground/70`,
`hover:bg-destructive/15` **diam-diam tidak menghasilkan CSS apa pun**
di Tailwind v3 ketika warna dasarnya adalah custom color yang
didefinisikan lewat `var(--x)` polos (bukan format channel RGB/HSL
terpisah yang dibutuhkan Tailwind untuk mendukung modifier opacity).
Tidak ada error saat build — class-nya cuma hilang begitu saja dari
CSS akhir. Ini menyebabkan navbar transparan dan teks sidebar nyaris
tidak terlihat sebelum diperbaiki.

✅ **Sudah diperbaiki**: semua pemakaian opacity modifier pada warna
custom di layout (`AdminLayout`, `SupervisorLayout`, `StudentLayout`)
sudah dihapus, diganti warna solid. Ditambahkan variabel baru
`--sidebar-muted` (warna solid, bukan hasil opacity) khusus untuk teks
sidebar redup/non-aktif.

⚠️ **Aturan untuk kode baru**: JANGAN pakai `/50`, `/70`, `/95` dkk pada
warna custom kita (`bg-primary/50`, `text-foreground/70`, dsb). Kalau
butuh varian pudar dari suatu warna:
- Tambahkan variabel solid baru di `app.css` (light + dark mode), lalu
  daftarkan di `tailwind.config.js` — pola yang sama dengan
  `--sidebar-muted`, ATAU
- Pakai `color-mix(in oklch, var(--x) 50%, transparent)` inline lewat
  style/className arbitrary value kalau memang cuma butuh sekali pakai.

Opacity modifier pada warna **bawaan Tailwind** (`bg-black/50`,
`text-gray-500/80`, dll — bukan custom var kita) itu **aman**, tidak
kena bug ini. Yang bermasalah spesifik hanya warna yang didefinisikan
lewat `var(--nama)` di `:root`/`.dark`.

### 5.3 Palet & tipografi saat ini

File sumber: `resources/css/app.css` (variabel CSS) dan
`tailwind.config.js` (mapping ke Tailwind).

- **Background**: warm off-white (`oklch(0.99 0.004 90)`), bukan putih
  murni
- **Primary**: indigo deep (`oklch(0.38 0.11 265)`) — dipakai tombol
  utama, link, item sidebar aktif
- **Accent**: amber (`oklch(0.68 0.14 55)`) — dipakai untuk state aktif
  di sidebar, badge tertentu
- **Sidebar**: deep navy gelap (`oklch(0.22 0.03 265)`), teks terang,
  BUKAN putih seperti sebelumnya
- **Font heading** (`--font-heading`): Fraunces Variable (serif) —
  diterapkan **global** ke semua `<h1>`–`<h4>` lewat `@layer base` di
  `app.css` (`h1,h2,h3,h4 { @apply font-heading }`), jadi TIDAK perlu
  tambah class manual di setiap heading
- **Font body** (`--font-sans`): Geist Variable
- **Dark mode**: sudah ada variabel lengkap di `.dark {}`, tapi
  belum ada toggle/switch di UI — kalau mau aktifkan, tinggal tambah
  class `dark` di elemen root

Semua variabel warna didefinisikan di dua tempat: `:root { }` (light,
default) dan `.dark { }` (dark mode) di `resources/css/app.css`.

### 5.4 Komponen `Card` — perubahan penting

`resources/js/Components/ui/card.jsx` sudah ditambahkan
`border border-border` dan `shadow-sm shadow-black/[0.03]` (sebelumnya
cuma `ring-1 ring-foreground/10` tanpa shadow, sehingga card nyaris tak
terlihat di atas background terang). Kalau card di suatu halaman masih
terlihat "menyatu" dengan background, kemungkinan halaman itu override
className Card dengan cara yang menghilangkan border/shadow — cek dulu
props `className` yang dioper ke `<Card>` di halaman tersebut.

## 6. Daftar komponen shadcn yang tersedia

Di `resources/js/Components/ui/`, generated via CLI resmi shadcn
(bukan tulisan manual lagi):

| Komponen | File | Catatan |
|---|---|---|
| Button | `button.jsx` | variant: default/outline/secondary/ghost/destructive/link; size: default/xs/sm/lg/icon dst |
| Card | `card.jsx` | + CardHeader, CardTitle, CardDescription, CardAction, CardContent, CardFooter |
| Badge | `badge.jsx` | variant: default/secondary/destructive/success/warning/outline |
| Input | `input.jsx` | |
| Textarea | `textarea.jsx` | |
| Label | `label.jsx` | Radix Label, butuh `htmlFor` |
| Checkbox | `checkbox.jsx` | **Radix API**: pakai `checked` + `onCheckedChange`, BUKAN `onChange`/`e.target.checked` |
| Select | `select.jsx` | **Radix compound component** — lihat §6.1, API-nya BEDA TOTAL dari native `<select>` |
| Progress | `progress.jsx` | |

`components.json` ada di root project — alias sudah disesuaikan ke
`@/Components` (huruf C besar, ikut konvensi folder project ini,
BUKAN default CLI yang pakai huruf kecil `@/components`). Kalau
menjalankan `npx shadcn add <komponen-baru>`, cek lagi apakah file
baru masuk ke `resources/js/Components/ui/` (huruf besar) dengan
benar — Windows filesystem case-insensitive jadi kadang menyamarkan
masalah casing yang sebenarnya ada.

### 6.1 Select — PENTING, API Radix bukan native

Ini BUKAN `<select><option>` biasa. Pola pemakaian yang benar:

```jsx
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'

<Select value={data.status} onValueChange={(v) => setData('status', v)}>
  <SelectTrigger className="w-full">
    <SelectValue />
  </SelectTrigger>
  <SelectContent>
    <SelectItem value="all">Semua</SelectItem>
    <SelectItem value="pending">Pending</SelectItem>
  </SelectContent>
</Select>
```

Aturan penting:
- Pakai `value` + `onValueChange`, BUKAN `onChange`
- `SelectItem` **tidak boleh** punya `value=""` (string kosong) — Radix
  akan throw error runtime. Untuk opsi "Semua"/placeholder, project ini
  pakai konvensi sentinel `value="all"`, lalu di logic submit
  diterjemahkan balik: kalau filter === 'all', key-nya di-omit dari
  query string yang dikirim ke backend (bukan dikirim literal
  `"all"`).
- Untuk form yang submit via GET query string (halaman filter/search),
  Select harus jadi **controlled state** (`useState`), karena Radix
  Select tidak berpartisipasi di native form submission — tidak bisa
  dibaca lewat `form.namaField.value` seperti input HTML biasa.

Total ada 24 halaman yang pakai Select dengan pola ini — semuanya
sudah dikonversi konsisten, jadi kalau menambah Select baru, ikuti pola
yang sudah ada di halaman sejenis (cari `SelectTrigger` di
`resources/js/Pages/` untuk contoh).

## 7. Hal-hal LAIN yang perlu diperhatikan saat redesign

- **Warna badge/ikon per-kategori masih hardcode Tailwind** (misal
  `bg-blue-100 text-blue-700` untuk kartu statistik "Total Users",
  `bg-green-100` untuk "Mahasiswa", dst — lihat
  `resources/js/Pages/Admin/Dashboard.jsx`). Ini **disengaja**, bukan
  bug — dipakai konsisten di banyak halaman untuk membedakan kategori
  data secara semantik (biru=user, hijau=mahasiswa/sukses,
  oranye=perhatian, dst). Jangan diseragamkan jadi satu warna primary
  kecuali memang diminta eksplisit — mengubahnya berarti audit ulang
  makna warna di puluhan tempat.
- **Beberapa modal ditulis manual** (fixed overlay + `bg-black/50`),
  bukan pakai Radix Dialog/AlertDialog resmi. Ini pilihan yang diambil
  sengaja untuk membatasi scope migrasi shadcn CLI ke 9 komponen dasar
  saja (Button/Card/Badge/Input/Textarea/Label/Checkbox/Select/Progress).
  Kalau mau upgrade ke Dialog resmi, itu pekerjaan terpisah yang belum
  dikerjakan.
- **CSRF meta tag**: `resources/views/app.blade.php` sudah punya
  `<meta name="csrf-token">` (sebelumnya tidak ada, sempat jadi bug
  nyata di fitur notification bell — sudah diperbaiki di sesi migrasi
  sebelumnya, tidak relevan untuk redesign tapi disebut untuk konteks
  kalau ada fetch()/axios call yang butuh token CSRF).
- **`Layout.jsx`** dan **`Pages/Dashboard.jsx`** (lihat §4) kemungkinan
  besar dead code sisa proof-of-concept awal proyek — verifikasi dulu
  dengan grep sebelum dihapus atau diasumsikan sebagai halaman aktif.

## 8. Cara menjalankan & verifikasi visual

```bash
# 1. Install & build assets
npm install
npm run build          # atau: npx vite build

# 2. Jalankan Laravel dev server
php artisan serve --port=8000

# 3. PENTING: hapus file public/hot kalau ada, supaya Laravel pakai
#    built assets, bukan mencoba fetch dari Vite dev server yang mati
rm -f public/hot

# 4. MySQL harus jalan (project ini pakai Laragon/XAMPP lokal,
#    cek .env untuk host/port — biasanya port non-default, misal 3307)
```

Akun test yang tersedia di database lokal (password sama untuk
ketiganya, HANYA di database development lokal — jangan asumsikan ini
berlaku di environment lain):

| Role | Email | Password |
|---|---|---|
| Admin | admin@bakti.com | `1` |
| Supervisor | dede@baktitest.com | `1` |
| Student | mrifqy821@gmail.com | `1` |

Untuk cek visual tanpa akses browser interaktif, project ini pernah
memakai Playwright headless (`npx playwright install chromium`) untuk
screenshot halaman setelah login — lihat riwayat kerja kalau perlu
script serupa, tidak ada script permanen yang disimpan di repo untuk
ini (semua di scratchpad sesi sebelumnya, sudah hilang).

## 9. Yang sudah diverifikasi bagus (screenshot-tested)

- Halaman login (`/login`)
- Admin dashboard (`/admin/dashboard-new`)
- Admin user management (`/admin/users`)
- Admin attendance monitoring (`/admin/attendance`) — termasuk Select
  filter, banyak Button variant
- Admin announcement create form (`/admin/announcements/create`) —
  Input, Textarea, Select, Checkbox sekaligus
- Modal delete-confirmation di halaman Users
- Supervisor dashboard (`/supervisor/dashboard-new`)
- Student dashboard (`/student/dashboard-new`)

## 10. Yang BELUM diverifikasi / kemungkinan masih perlu kerja

- Halaman-halaman detail/edit yang lebih dalam (belum semua di-screenshot
  satu per satu — baru sample representatif per role)
- Halaman dengan tabel besar/kompleks (Reports, Submissions, dsb)
- Responsif di layar kecil/mobile (semua verifikasi sejauh ini di
  viewport 1440×900 desktop)
- Dark mode (variabel CSS sudah ada tapi belum ada toggle UI, belum
  pernah dites secara visual)
- Konsistensi ikon/badge warna kategori lintas halaman (§7, poin 1) —
  belum diaudit menyeluruh, cuma dikonfirmasi bahwa itu pola yang
  disengaja, bukan berarti sudah rapi 100%
- Halaman-halaman PDF (`supervisor/pdf/*.blade.php`) — di luar scope
  redesign React, tapi kalau ada komplain visual soal sertifikat/rekap
  nilai, itu file Blade terpisah, bukan React

## 11. Larangan / batasan kerja

- **Jangan ubah logic backend** (controller, model, migration) kecuali
  memang menemukan bug nyata yang berdampak ke rendering data —
  fokuskan perubahan di `resources/js/` dan `resources/css/app.css`
- **Jangan hapus/rename route** tanpa memastikan tidak ada pemanggil
  lain (`grep -rn "route('nama.route'" resources/js app`)
- **Jangan jalankan migrasi/seed database** kecuali diminta eksplisit
- Kalau menambah dependency npm baru, jalankan `npm install` dan pastikan
  `package-lock.json` ikut berubah — jangan edit `package.json` manual
  tanpa install
