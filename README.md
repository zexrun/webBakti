# webBakti

Sistem manajemen program magang untuk **Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI)**, Kementerian Komunikasi dan Digital RI. Mengelola seluruh siklus magang — dari penugasan pembimbing, tugas dan penilaian, logbook harian, presensi berbasis foto/lokasi, hingga penerbitan sertifikat — untuk tiga peran pengguna: **Admin**, **Pembimbing (Supervisor)**, dan **Mahasiswa (Student)**.

## Tech Stack

- **Backend:** Laravel 12 (PHP ^8.2), MySQL
- **Frontend:** React 19 + Inertia.js v3 + Vite 5, Tailwind CSS v3, shadcn/ui (Radix primitives)
- **Animasi:** motion (penerus framer-motion)
- **PDF:**
  - Logbook, rekap logbook, dan nilai akhir → **DomPDF** (`barryvdh/laravel-dompdf`) dari template Blade di `resources/views/supervisor/pdf/`.
  - Sertifikat magang → **React-PDF** (`@react-pdf/renderer`), dijalankan lewat skrip Node (`resources/pdf-renderers/render-certificate.cjs`) yang dipanggil dari controller PHP via `Illuminate\Support\Facades\Process`. Migrasi ini dilakukan karena DomPDF memotong kalimat panjang berbahasa Indonesia secara tidak wajar, sedangkan React-PDF (mesin layout Yoga/flexbox) tidak mengalami masalah tersebut. Membutuhkan binary `node` tersedia di `PATH` server.
- **Deteksi wajah:** `face-api.js` (client-side) untuk verifikasi wajah saat presensi dan foto profil, dibandingkan dengan `face_descriptor` tersimpan di akun pengguna.
- **Verifikasi lokasi presensi:** kombinasi jarak GPS (Haversine) terhadap titik kantor, pengecekan EXIF GPS pada foto, dan skor kecurigaan (spoofing score) — lihat `app/Services/LocationVerificationService.php` dan `app/Services/FaceVerificationService.php`.

## Peran Pengguna

### Admin
Administrasi penuh sistem: kelola akun pengguna, plotting mahasiswa ke pembimbing, monitoring seluruh mahasiswa/pembimbing, kelola data referensi (direktorat, jabatan, universitas), kelola presensi sistem-lebar (approval, review kecurigaan, laporan, ekspor CSV, pengaturan jam kerja & geofence), dan kelola pengumuman.

### Pembimbing (Supervisor)
Mengelola mahasiswa bimbingannya: buat & nilai tugas, review logbook (termasuk ekspor PDF per-entry dan rekap), penilaian akhir, **generate sertifikat magang**, operasi massal (tugas massal, notifikasi massal, import nilai), analitik performa mahasiswa, approval presensi, serta akses dokumen mahasiswa.

### Mahasiswa (Student)
Lihat & kumpulkan tugas, kelola logbook harian, presensi check-in/check-out (dengan foto + lokasi), ajukan pengecualian presensi (izin/sakit/cuti), kelola dokumen (proposal, laporan akhir, dll), dan unduh sertifikat setelah digenerate pembimbing.

### Fitur lintas-peran
Pengumuman, notifikasi, pencarian global/lanjutan (dengan simpan pencarian), dan pesan internal antar pengguna.

## Instalasi

### Prasyarat
- PHP ^8.2, Composer
- Node.js (untuk build frontend **dan** generate sertifikat via `node` di runtime)
- MySQL (atau sesuaikan `DB_CONNECTION` bila memakai driver lain)

### Langkah setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env` (`DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Bila integrasi database presensi eksternal terpisah digunakan, tambahkan juga variabel `ABSEN_DB_HOST`, `ABSEN_DB_PORT`, `ABSEN_DB_DATABASE`, `ABSEN_DB_USERNAME`, `ABSEN_DB_PASSWORD` (lihat koneksi `absensi_mysql` di `config/database.php`) — variabel ini tidak disertakan di `.env.example` secara default.

```bash
php artisan migrate --seed
```

Atau gunakan skrip reseed untuk migrasi fresh + seed sekaligus (menghapus seluruh data yang ada):

```bash
# Windows
./reseed.ps1

# macOS/Linux
./reseed.sh
```

### Menjalankan aplikasi

```bash
composer run dev
```

Menjalankan `php artisan serve`, queue listener, log viewer (`pail`), dan `npm run dev` secara bersamaan. Atau jalankan manual: `php artisan serve` + `npm run dev` di terminal terpisah.

> Untuk pemakaian normal (bukan sedang aktif mengembangkan frontend), cukup jalankan `php artisan serve` saja tanpa `npm run dev` — Laravel akan otomatis memakai hasil `npm run build` di `public/build/`.

### Akun uji (hasil seeder)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@bakti.com` | `1` |
| Supervisor | `dede@baktitest.com` | `1` |
| Student | `mrifqy821@gmail.com` | `1` |

## Model Data Utama

| Model | Deskripsi |
|---|---|
| `User` | Akun dasar (admin/supervisor/student), termasuk foto profil dan `face_descriptor` untuk verifikasi wajah |
| `Student` | Profil mahasiswa (NIM, universitas, program studi, periode magang) |
| `Supervisor` | Profil pembimbing (NIP, jabatan, direktorat) |
| `Task` / `Submission` | Tugas dari pembimbing dan pengumpulan tugas oleh mahasiswa |
| `Logbook` | Jurnal aktivitas harian mahasiswa, termasuk feedback pembimbing |
| `Document` | Dokumen mahasiswa (proposal, laporan akhir, dll) |
| `FinalAssessment` | Nilai akhir magang, melacak `certificate_generated_at` |
| `Attendance` / `AttendanceException` / `AttendanceSetting` | Presensi harian, pengajuan izin, dan konfigurasi jam kerja/geofence |
| `Message` / `Announcement` / `SavedSearch` | Pesan internal, pengumuman, dan pencarian tersimpan |

## Struktur Dokumentasi Development

- `docs/superpowers/specs/` — spesifikasi desain fitur
- `docs/superpowers/plans/` — rencana implementasi bertahap
- `docs/00-Overview/` dst. — dokumentasi teknis lebih lanjut (arsitektur, keamanan, deployment)
