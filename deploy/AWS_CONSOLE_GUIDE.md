# Deploy Magang BAKTI ke AWS EC2 untuk Demo (via AWS Console)

Panduan ini membuat 1 instance EC2 Ubuntu, lalu menjalankan script yang
sudah ada di `deploy/` (lihat [README.md](README.md)) untuk memasang
aplikasi. Semua lewat browser (console.aws.amazon.com) dan SSH — tidak perlu
install AWS CLI.

**Perkiraan biaya:** t3.small on-demand ±$0.0208/jam (region ap-southeast-1
Singapore). Kalau nyala 8 jam untuk presentasi lalu dihentikan, biayanya
sangat kecil. **Jangan lupa Stop/Terminate instance setelah demo** — biaya
compute jalan terus selama instance "running", meski idle.

---

## 1. Buat Key Pair (untuk SSH)

1. Login ke [console.aws.amazon.com](https://console.aws.amazon.com)
2. Pilih region di kanan atas — misal **Asia Pacific (Singapore) ap-southeast-1**
3. Cari service **EC2** → sidebar kiri **Key Pairs** → **Create key pair**
4. Nama: `magang-bakti-demo`
5. Key pair type: **RSA**, Private key format: **.pem** (untuk Mac/Linux/WSL) atau **.ppk** (untuk PuTTY di Windows)
6. **Create key pair** → file akan otomatis terdownload, **simpan baik-baik**, tidak bisa didownload ulang

## 2. Buat Security Group (firewall)

1. Sidebar kiri **Security Groups** → **Create security group**
2. Nama: `magang-bakti-demo-sg` (nama tidak boleh diawali `sg-`, itu reserved untuk ID otomatis AWS)
3. Inbound rules → **Add rule** sebanyak 2 kali:
   - Type: **SSH**, Port 22, Source: **My IP** (lebih aman) atau **Anywhere (0.0.0.0/0)** kalau IP Anda berubah-ubah saat presentasi
   - Type: **HTTP**, Port 80, Source: **Anywhere (0.0.0.0/0)** — supaya audiens/juri bisa akses demo
4. **Create security group**

## 3. Launch EC2 Instance

1. Sidebar kiri **Instances** → **Launch instances**
2. Name: `vm-magang-bakti`
3. AMI: **Ubuntu Server** (LTS terbaru yang tersedia, misal 22.04/24.04/26.04 — semua cocok, script `deploy/` tidak bergantung versi spesifik) — pilih yang 64-bit (x86)
4. Instance type: **t3.small**
5. Key pair: pilih `magang-bakti-demo` yang dibuat di langkah 1
6. Network settings → **Edit** → Firewall (security groups): pilih **Select existing security group** → `magang-bakti-demo-sg`
7. Configure storage: default 8GB biasanya cukup, tapi disarankan naikkan ke **20 GiB** (node_modules + build assets + MySQL lumayan makan disk)
8. **Launch instance**
9. Tunggu status **Running** (~1 menit), klik instance → catat **Public IPv4 address** (misal `13.x.x.x`)

## 4. Login via SSH

Dari komputer Anda:

**Jika pakai file `.pem` (Mac/Linux/WSL/Git Bash):**
```bash
chmod 400 magang-bakti-demo.pem
ssh -i magang-bakti-demo.pem ubuntu@13.x.x.x
```

**Jika di Windows PowerShell:**
```powershell
icacls magang-bakti-demo.pem /inheritance:r
icacls magang-bakti-demo.pem /grant:r "$($env:USERNAME):(R)"
ssh -i magang-bakti-demo.pem ubuntu@13.x.x.x
```

Username default AMI Ubuntu di AWS adalah **`ubuntu`** (bukan `azureuser`/`ec2-user`).

## 5. Upload/clone kode project

Paling praktis: push branch project ke GitHub dulu (kalau belum), lalu clone
langsung di instance.

**Penting:** kalau kode yang mau di-demo ada di branch selain `main` (misal
`new-ui`), `git clone` biasa akan checkout branch default repo, bukan branch
Anda. Langsung clone branch yang dituju dengan `-b`:

```bash
# di dalam instance (setelah SSH)
sudo mkdir -p /var/www
sudo chown ubuntu:ubuntu /var/www
cd /var/www
git clone -b new-ui <URL_REPO_GITHUB_ANDA> webbakti
cd webbakti
git branch --show-current   # pastikan hasilnya "new-ui"
```

Ganti `new-ui` dengan nama branch yang sebenarnya mau Anda demokan kalau
berbeda.

Kalau repo private dan belum ada remote GitHub, beri tahu saya — ada cara
lain (misal `scp` folder langsung dari komputer Anda ke instance).

## 6. Jalankan script provisioning & deploy

Script yang dipakai sama seperti deployment biasa (lihat
[README.md](README.md) untuk detail tiap langkah) — tidak ada penyesuaian
khusus AWS karena semuanya berbasis apt/Ubuntu vanilla:

```bash
cd /var/www/webbakti
sudo bash deploy/01-provision-server.sh
```

Setelah selesai (~5-10 menit, install PHP/Node/MySQL/Apache/Supervisor):

```bash
sudo mysql_secure_installation
bash deploy/03-create-database.sh
```

Deploy aplikasi (run pertama akan berhenti untuk minta Anda edit `.env`):

```bash
sudo bash deploy/02-deploy-app.sh --update
```

Edit `.env` sesuai instruksi yang muncul di terminal (isi `DB_*` dari langkah
sebelumnya, `APP_URL=http://13.x.x.x` — pakai Public IPv4 instance Anda),
lalu jalankan ulang:

```bash
sudo bash deploy/02-deploy-app.sh --update
```

## 7. Setup Apache, queue worker, scheduler

```bash
sudo cp deploy/webbakti.apache.conf /etc/apache2/sites-available/webbakti.conf
sudo sed -i 's/magang-bakti.local/13.x.x.x/' /etc/apache2/sites-available/webbakti.conf
sudo a2ensite webbakti.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

sudo cp deploy/webbakti-worker.conf /etc/supervisor/conf.d/webbakti-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start webbakti-worker:*

sudo bash deploy/04-setup-scheduler-cron.sh /var/www/webbakti
```

## 8. Buka di browser

```
http://13.x.x.x
```

Ganti `13.x.x.x` dengan Public IPv4 instance Anda dari langkah 3.

**Catatan:** Public IP EC2 standar (bukan Elastic IP) **berubah setiap kali
instance di-stop lalu di-start ulang**. Kalau presentasi butuh IP yang sama
di beberapa sesi berbeda, alokasikan **Elastic IP** (EC2 → Elastic IPs →
Allocate, lalu Associate ke instance) — gratis selama terpasang ke instance
yang running, tapi dikenai biaya kecil kalau dibiarkan menganggur tanpa
instance.

## 9. Seed data demo (opsional)

```bash
cd /var/www/webbakti
php artisan db:seed --class=AdminSeeder
```

Login dengan `admin@bakti.com` / password `1`, **langsung ganti password**
di halaman profil sebelum melanjutkan demo — instance ini punya IP publik
yang bisa diakses siapa saja yang tahu alamatnya.

## 10. Setelah presentasi selesai

**Penting untuk menghindari biaya tidak perlu:**

- **Stop instance** (bukan Terminate) kalau mau dipakai lagi nanti: EC2 →
  pilih instance → **Instance state** → **Stop instance**. Storage (EBS)
  tetap dikenai biaya kecil selama di-stop, tapi compute-nya tidak.
- **Terminate instance** kalau sudah selesai total dan tidak akan dipakai
  lagi: **Instance state** → **Terminate instance** — menghapus semuanya,
  termasuk EBS volume (kecuali diset "Delete on Termination" = No).
- Kalau ada Elastic IP yang dialokasikan, **Release** juga (EC2 → Elastic
  IPs → Release) supaya tidak kena biaya idle.

---

## Troubleshooting cepat

| Masalah | Kemungkinan sebab |
|---|---|
| Browser tidak bisa akses IP | Security group belum allow port 80, atau Apache belum reload |
| SSH "Permission denied (publickey)" | Salah username (harus `ubuntu`, bukan `ec2-user`), atau permission file `.pem` belum 400 |
| Error 500 / halaman putih | Cek `sudo tail -f /var/www/webbakti/storage/logs/laravel.log` |
| PDF sertifikat/logbook gagal | Cek `sudo -u www-data node -v` — kalau gagal, Node tidak terpasang global dengan benar |
| Email tidak terkirim | Cek `.env` `MAIL_*` — AWS EC2 **blokir outbound port 25 secara default** (anti-spam), tapi port **587/2525** (TLS, dipakai Mailtrap/SendGrid) biasanya tidak diblokir, jadi ini sudah aman untuk Mailtrap |
| IP berubah setelah restart | Normal untuk Public IP biasa, pakai Elastic IP kalau butuh IP tetap |
| `npm run build` lambat/OOM | t3.small (2GB RAM) biasanya cukup; kalau masih OOM, tambahkan swap 2GB sementara |
