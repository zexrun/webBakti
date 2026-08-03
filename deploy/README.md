# Deployment: Magang BAKTI di Ubuntu Server (dari nol)

Urutan yang harus dijalankan, di server Ubuntu target (bukan di mesin lokal Anda).

## 0. Prasyarat

- Ubuntu Server (20.04/22.04/24.04) dengan akses `sudo`
- Akses ke repo git (URL clone)
- Kredensial SMTP (Mailtrap/Gmail/SendGrid/dll) untuk `.env`

## 1. Provision server (PHP, Node, MySQL, Apache, Supervisor)

```bash
sudo bash deploy/01-provision-server.sh
```

Menginstall PHP 8.2 + ekstensi, Composer, Node.js 20.x, library native untuk
paket `canvas` (dipakai face verification), MySQL, Apache + PHP-FPM, dan
Supervisor.

Setelah selesai, amankan instalasi MySQL:

```bash
sudo mysql_secure_installation
```

## 2. Buat database

```bash
bash deploy/03-create-database.sh
```

Script ini akan meminta Anda membuat password untuk user `bakti_user` dan
membuat database `magang_bakti`. Catat kredensial ini untuk langkah berikutnya.

## 3. Clone & deploy aplikasi

```bash
sudo bash deploy/02-deploy-app.sh <url-repo-anda> /var/www/webbakti
```

Pada run pertama, script akan clone repo, install dependensi PHP, lalu
**berhenti** setelah membuat `.env` dari `.env.example` karena `.env` masih
berisi nilai placeholder. Edit `/var/www/webbakti/.env` sekarang:

```env
APP_NAME="Magang BAKTI"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://alamat-server-anda

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magang_bakti
DB_USERNAME=bakti_user
DB_PASSWORD=<password dari langkah 2>

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@...
MAIL_FROM_NAME="Magang BAKTI"
```

Lalu lanjutkan deploy (install Node deps, build asset, migrate, permission,
cache):

```bash
cd /var/www/webbakti
sudo bash deploy/02-deploy-app.sh --update
```

**Catatan penting:** `node_modules` di server produksi **tidak boleh dihapus**
setelah `npm run build`. PDF generation (sertifikat, nilai, logbook) dan
verifikasi wajah server-side memanggil `node` sebagai proses child langsung
dari PHP (`Process::run(['node', ...])`), bukan hanya saat build asset —
lihat `resources/pdf-renderers/*.cjs` dan
`resources/face-verification/extract-descriptor.cjs`.

## 4. Konfigurasi Apache

```bash
sudo cp deploy/webbakti.apache.conf /etc/apache2/sites-available/webbakti.conf
sudo nano /etc/apache2/sites-available/webbakti.conf   # sesuaikan ServerName
sudo a2ensite webbakti.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

## 5. Queue worker (Supervisor)

Email notifikasi (grading, deadline reminder, approval) dikirim lewat queue
database — perlu worker yang jalan terus.

```bash
sudo cp deploy/webbakti-worker.conf /etc/supervisor/conf.d/webbakti-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start webbakti-worker:*
sudo supervisorctl status
```

## 6. Scheduler (cron)

```bash
sudo bash deploy/04-setup-scheduler-cron.sh /var/www/webbakti
```

## 7. Verifikasi

```bash
# Aplikasi bisa diakses
curl -I http://alamat-server-anda

# Worker jalan
sudo supervisorctl status webbakti-worker:*

# www-data bisa panggil node (wajib untuk PDF & face verification)
sudo -u www-data node -v

# Kirim test email
cd /var/www/webbakti && php artisan email:test --to=admin@domain-anda.local
```

## 8. Data awal (opsional, hanya jika dibutuhkan)

```bash
php artisan db:seed --class=AdminSeeder
```

**PERINGATAN:** `AdminSeeder` membuat akun `admin@bakti.com` dengan password
`"1"`. Ini seeder demo/dev, bukan untuk produksi apa adanya — kalau dipakai
untuk membuat akun admin pertama, **segera login dan ganti passwordnya**
sebelum server diakses siapapun selain Anda.

## Deploy ulang (update kode)

Setiap kali ada perubahan kode yang perlu di-deploy:

```bash
cd /var/www/webbakti
sudo bash deploy/02-deploy-app.sh --update
sudo supervisorctl restart webbakti-worker:*
```

---

## Daftar isi folder `deploy/`

| File | Fungsi |
|---|---|
| `01-provision-server.sh` | Install PHP, Composer, Node, MySQL, Apache, Supervisor (sekali di server baru) |
| `02-deploy-app.sh` | Clone/pull repo, install dependensi, build asset, migrate, permission, cache (dipakai juga untuk update) |
| `03-create-database.sh` | Buat database & user MySQL secara interaktif |
| `04-setup-scheduler-cron.sh` | Pasang cron `schedule:run` untuk `www-data` |
| `webbakti.apache.conf` | Template virtual host Apache |
| `webbakti-worker.conf` | Template program Supervisor untuk queue worker |
