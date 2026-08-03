# Deploy Magang BAKTI ke Azure VM untuk Demo (via Azure Portal)

Panduan ini membuat 1 VM Ubuntu di Azure, lalu menjalankan script yang sudah
ada di `deploy/` (lihat [README.md](README.md)) untuk memasang aplikasi.
Tidak perlu install apapun di komputer Anda — semua lewat browser
(portal.azure.com) dan SSH.

**Perkiraan biaya:** VM B2s ±$0.05/jam (~Rp750). Kalau nyala 8 jam untuk
presentasi lalu di-stop, biayanya sangat kecil. **Jangan lupa stop/delete VM
setelah demo** — biaya jalan terus selama VM "Running", meski idle.

---

## 1. Buat Resource Group

1. Login ke [portal.azure.com](https://portal.azure.com)
2. Cari "Resource groups" → **Create**
3. Nama: `rg-magang-bakti-demo`
4. Region: pilih yang terdekat (misal **Southeast Asia** untuk Indonesia)
5. **Review + Create** → **Create**

## 2. Buat Virtual Machine

1. Cari "Virtual machines" → **Create** → **Azure virtual machine**
2. **Basics tab:**
   - Resource group: `rg-magang-bakti-demo`
   - Virtual machine name: `vm-magang-bakti`
   - Region: sama seperti resource group
   - Image: **Ubuntu Server 22.04 LTS - x64 Gen2**
   - Size: klik "See all sizes" → cari **Standard_B2s** (2 vCPU, 4 GiB RAM)
   - Authentication type: **SSH public key** (lebih aman) atau **Password**
     jika Anda belum punya SSH key — untuk demo cepat, Password lebih simpel
   - Username: `azureuser` (atau nama lain, catat baik-baik)
   - Jika pakai Password: set password yang kuat, catat
3. **Disks tab:** biarkan default (biasanya Premium SSD atau Standard SSD, keduanya cukup)
4. **Networking tab:**
   - Pastikan **Public IP** dibuat (default sudah begitu)
   - NIC network security group: **Basic**
   - Public inbound ports: **Allow selected ports**
   - Select inbound ports: centang **SSH (22)** dan **HTTP (80)**
5. **Review + create** → tunggu validasi → **Create**
6. Tunggu deployment selesai (~1-2 menit), lalu klik **Go to resource**

## 3. Catat IP publik

Di halaman VM (Overview), catat **Public IP address** — misal `20.x.x.x`.
Ini alamat yang nanti dipakai untuk demo (`http://20.x.x.x`).

## 4. Login via SSH

Dari komputer Anda (Windows: pakai PowerShell/Terminal/Bash tool yang tersedia):

```bash
ssh azureuser@20.x.x.x
```

Kalau pakai Password auth, akan diminta password yang tadi di-set. Kalau
pakai SSH key, pastikan private key-nya sudah di-load (`ssh -i path/to/key.pem azureuser@20.x.x.x`).

## 5. Upload/clone kode project

Paling praktis: push dulu branch project ke GitHub (kalau belum), lalu clone
langsung di VM:

```bash
# di dalam VM (setelah SSH)
git clone <URL_REPO_GITHUB_ANDA> webbakti
cd webbakti
```

Kalau repo private dan belum ada GitHub remote, beri tahu saya — ada cara
lain (scp folder langsung dari komputer Anda ke VM).

## 6. Jalankan script provisioning & deploy

Ini adalah script yang sudah dibuat sebelumnya di `deploy/` (lihat
[README.md](README.md) untuk detail tiap langkah):

```bash
cd ~/webbakti
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

Kalau ini clone baru (bukan hasil `git clone` manual di langkah 5, tapi mau
pakai script clone otomatis), gunakan:

```bash
sudo bash deploy/02-deploy-app.sh <url-repo> /var/www/webbakti
```

Edit `.env` sesuai instruksi yang muncul di terminal (isi `DB_*` dari langkah
sebelumnya, `APP_URL=http://20.x.x.x` — pakai IP publik VM Anda), lalu jalankan
ulang:

```bash
sudo bash deploy/02-deploy-app.sh --update
```

## 7. Setup Apache, queue worker, scheduler

```bash
sudo cp deploy/webbakti.apache.conf /etc/apache2/sites-available/webbakti.conf
sudo sed -i 's/magang-bakti.local/20.x.x.x/' /etc/apache2/sites-available/webbakti.conf
sudo a2ensite webbakti.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

sudo cp deploy/webbakti-worker.conf /etc/supervisor/conf.d/webbakti-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start webbakti-worker:*

sudo bash deploy/04-setup-scheduler-cron.sh /var/www/webbakti
```

**Catatan:** karena kita clone ke `~/webbakti` di langkah 5 tapi script Apache
mengasumsikan `/var/www/webbakti`, pastikan konsisten — paling gampang, dari
awal clone/deploy langsung ke `/var/www/webbakti` (butuh `sudo`), bukan ke
home directory. Kalau sudah clone ke `~/webbakti`, pindahkan dulu:

```bash
sudo mv ~/webbakti /var/www/webbakti
cd /var/www/webbakti
```

## 8. Buka di browser

```
http://20.x.x.x
```

Ganti `20.x.x.x` dengan Public IP VM Anda dari langkah 3.

## 9. Seed data demo (opsional)

```bash
cd /var/www/webbakti
php artisan db:seed --class=AdminSeeder
```

Login dengan `admin@bakti.com` / password `1`, **langsung ganti password**
di halaman profil sebelum melanjutkan demo — VM ini punya IP publik yang bisa
diakses siapa saja yang tahu alamatnya.

Kalau butuh data contoh lebih lengkap (student, task, logbook, dll) untuk
demo yang lebih hidup, jalankan seeder lain yang relevan atau
`php artisan db:seed` (seeder utama) — beri tahu saya kalau perlu bantuan
pilih seeder mana yang aman untuk demo.

## 10. Setelah presentasi selesai

**Penting untuk menghindari biaya tidak perlu:**

- **Stop VM** (bukan hanya shutdown dari dalam OS) via portal: VM → **Stop**
  di toolbar atas. Ini menghentikan biaya compute (disk masih kena biaya kecil).
- Atau **Delete** resource group `rg-magang-bakti-demo` sepenuhnya kalau VM
  ini benar-benar hanya untuk demo sekali dan tidak akan dipakai lagi —
  ini menghapus semua biaya terkait.

---

## Troubleshooting cepat

| Masalah | Kemungkinan sebab |
|---|---|
| Browser tidak bisa akses IP | NSG belum allow port 80 (cek langkah 2.4), atau Apache belum reload |
| Error 500 / halaman putih | Cek `sudo tail -f /var/www/webbakti/storage/logs/laravel.log` |
| PDF sertifikat/logbook gagal | Cek `sudo -u www-data node -v` — kalau gagal, Node tidak terpasang global dengan benar |
| Email tidak terkirim | Cek `.env` `MAIL_*`, VM Azure kadang blokir outbound port 25/587 tergantung subscription — coba pakai Mailtrap dulu untuk demo |
| Halaman lambat pertama kali | Normal, cache Laravel/OPcache belum warm; refresh lagi |
