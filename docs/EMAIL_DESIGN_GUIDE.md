# Email Design Guide - WebBakti

## 🎨 Design System

Email templates di WebBakti menggunakan design system yang modern, profesional, dan mobile-friendly.

### Filosofi Desain

- **Modern Professional:** Gradient header dengan color palette yang konsisten
- **Responsive:** Optimal tampilan di semua device (desktop, tablet, mobile)
- **Accessible:** Typography yang readable, contrast ratio yang baik
- **Consistent:** Standardisasi komponen dan spacing di semua template

---

## 📐 Layout Structure

Semua email menggunakan base layout: `resources/views/emails/layout.blade.php`

```
┌─────────────────────────────────┐
│   HEADER (Gradient)             │  ← Branding, logo, subtitle
├─────────────────────────────────┤
│                                 │
│   CONTENT                       │  ← Main message
│                                 │
├─────────────────────────────────┤
│   FOOTER                        │  ← Links, copyright
└─────────────────────────────────┘
```

### Color Palette

| Warna | Hex Code | Penggunaan |
|-------|----------|-----------|
| Primary Gradient | `#667eea → #764ba2` | Header, buttons, accents |
| Text Primary | `#2d3748` | Main content text |
| Text Secondary | `#4a5568` | Secondary content |
| Text Light | `#718096` | Labels, metadata |
| Background | `#f7fafc` | Section backgrounds |
| Border | `#e2e8f0` | Dividers, borders |

### Typography

| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Logo | System | 28px | 800 | White |
| Title | System | 22px | 700 | #1a202c |
| Greeting | System | 16px | 600 | #2d3748 |
| Body | System | 15px | 400 | #4a5568 |
| Label | System | 14px | 600 | #4a5568 |
| Footer | System | 13px | 400 | #718096 |

---

## 🧩 Components

### 1. Email Section

Container untuk mengelompokkan konten:

```blade
<div class="email-section">
    <div class="email-section-title">📋 Detail</div>
    <p class="email-paragraph">Konten section...</p>
</div>
```

**Styling:**
- Background: Light gray (`#f7fafc`)
- Border-left: 4px solid primary color
- Padding: 24px
- Border-radius: 8px
- Margin: 32px 0

### 2. Info Box

Display key-value pairs:

```blade
<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Label:</div>
        <div class="email-info-value">Value</div>
    </div>
    <div class="email-info-row">
        <div class="email-info-label">Label 2:</div>
        <div class="email-info-value">Value 2</div>
    </div>
</div>
```

**Features:**
- Flex layout untuk alignment optimal
- Border-bottom antar rows
- Last row tidak punya border
- Responsive di mobile

### 3. Alert Boxes

Highlight informasi penting dengan 4 varian:

```blade
<!-- Info Alert -->
<div class="email-alert email-alert-info">
    <strong>ℹ️ Informasi</strong><br>
    Pesan info Anda...
</div>

<!-- Success Alert -->
<div class="email-alert email-alert-success">
    <strong>✓ Berhasil</strong><br>
    Pesan success Anda...
</div>

<!-- Warning Alert -->
<div class="email-alert email-alert-warning">
    <strong>⚠️ Peringatan</strong><br>
    Pesan warning Anda...
</div>

<!-- Danger Alert -->
<div class="email-alert email-alert-danger">
    <strong>✗ Kesalahan</strong><br>
    Pesan error Anda...
</div>
```

**Varian:**

| Alert | Background | Border | Text Color |
|-------|-----------|--------|-----------|
| Info | `#eff6ff` | `#3b82f6` | `#1e40af` |
| Success | `#f0fdf4` | `#22c55e` | `#166534` |
| Warning | `#fffbeb` | `#f59e0b` | `#92400e` |
| Danger | `#fef2f2` | `#ef4444` | `#991b1b` |

### 4. Buttons

Dua varian button:

```blade
<!-- Primary Button -->
<div class="email-button-group">
    <a href="{{ url('/action') }}" class="email-button">
        Lihat Detail
    </a>
</div>

<!-- Secondary Button -->
<a href="{{ url('/action') }}" class="email-button email-button-secondary">
    Tombol Sekunder
</a>
```

**Styling:**
- Primary: Gradient background dengan shadow
- Secondary: Gray background
- Padding: 14px 40px
- Border-radius: 6px
- Font-weight: 600
- Hover state: translateY(-2px) dengan enhanced shadow

### 5. Lists

Dua tipe list:

```blade
<!-- Unordered List -->
<ul class="email-list">
    <li>Item 1</li>
    <li>Item 2 dengan <strong>bold text</strong></li>
</ul>

<!-- Ordered List (numbered) -->
<ol class="email-list">
    <li><strong>Step 1:</strong> Baca instruksi</li>
    <li><strong>Step 2:</strong> Lakukan action</li>
</ol>
```

**Styling:**
- Margin-left: 24px (indentation)
- Margin-bottom: 10px per item
- Color: #4a5568
- Strong text: #2d3748

### 6. Divider

Pemisah visual antar sections:

```blade
<hr class="email-divider">
```

**Styling:**
- Height: 1px
- Background: #e2e8f0
- Margin: 32px 0

---

## 📝 How to Extend

### Membuat Email Template Baru

1. **Extends layout:**
```blade
@extends('emails.layout', ['title' => 'Email Title - WebBakti'])
```

2. **Gunakan title class:**
```blade
<div class="email-title">✨ Judul Email</div>
```

3. **Tambah greeting:**
```blade
<p class="email-greeting">Halo {{ $notifiable->name }},</p>
```

4. **Main content:**
```blade
<p class="email-paragraph">Konten utama...</p>

<div class="email-section">
    <div class="email-section-title">📋 Title</div>
    <!-- Content -->
</div>
```

5. **Call-to-action:**
```blade
<div class="email-button-group">
    <a href="{{ url('/action') }}" class="email-button">
        Tombol CTA
    </a>
</div>
```

6. **Footer content:**
Otomatis dirender oleh layout

### Best Practices

1. **Emoji Usage:**
   - Gunakan untuk visual interest, tidak kelebihan
   - Contoh: `✨`, `📋`, `💬`, `✅`, `⚠️`
   - Hindari emoji yang tidak relevan

2. **Typography:**
   - Judul: Gunakan `<div class="email-title">`
   - Body: Gunakan `<p class="email-paragraph">`
   - Labels: Gunakan `<div class="email-info-label">`

3. **Spacing:**
   - Antar paragraf: 16px (margin-bottom)
   - Antar section: 32px (margin)
   - Antar item di list: 10px

4. **Color:**
   - Jangan hardcode warna baru
   - Gunakan existing color palette
   - Jika butuh warna baru, update CSS di layout.blade.php

5. **Mobile Responsiveness:**
   - Test di semua breakpoint
   - Layout otomatis responsive via CSS
   - Buttons menjadi full-width di mobile

---

## 🎯 Email Templates

### 1. Test Email (test.blade.php)

**Penggunaan:** Testing email sandbox
**Flow:** Command `php artisan email:test`
**Features:**
- Success alert
- Email metadata (waktu, environment)
- Next steps guidance

### 2. Attendance Approval (attendance-approval.blade.php)

**Penggunaan:** Status kehadiran user
**Status:** Approved / Rejected
**Features:**
- Status-specific alerts
- Attendance metadata
- Rejection reasons (jika ada)
- Action button ke attendance page

### 3. Exception Approval (exception-approval.blade.php)

**Penggunaan:** Status pengajuan sakit/izin
**Status:** Approved / Rejected
**Features:**
- Exception detail (tipe, alasan, dokumen)
- Admin notes (jika ditolak)
- Next steps guidance

### 4. Logbook Feedback (logbook-feedback.blade.php)

**Penggunaan:** Feedback dari pembimbing
**Features:**
- Logbook detail
- Feedback text in highlighted box
- Link ke logbook di aplikasi

### 5. Submission Graded (submission-graded.blade.php)

**Penggunaan:** Nilai tugas dari pembimbing
**Features:**
- Task detail
- Grade display dengan gradient background
- Comments dari pembimbing
- Tips untuk improvement

### 6. Task Deadline Reminder (task-deadline-reminder.blade.php)

**Penggunaan:** Pengingat deadline tugas
**Urgency Levels:**
- Today (Danger - Red)
- Tomorrow (Warning - Orange)
- Multiple days (Info - Blue)
**Features:**
- Urgency indicators
- Task detail lengkap
- Step-by-step instructions
- Important reminders

---

## 🔧 Customization

### Mengubah Primary Color

Edit `layout.blade.php` dan ganti semua `#667eea` dan `#764ba2`:

```css
background: linear-gradient(135deg, #NEW_COLOR_1 0%, #NEW_COLOR_2 100%);
```

### Mengubah Font

Edit `layout.blade.php` dan ubah `font-family`:

```css
font-family: 'Your Font', sans-serif;
```

### Mengubah Spacing

Modify CSS variables di `layout.blade.php`:

```css
/* Header padding */
.email-header { padding: 40px 50px; }

/* Content padding */
.email-body { padding: 40px; }
```

---

## 📧 Testing

### Local Testing

```bash
# Send test email
php artisan email:test --to=your-email@example.com

# Custom message
php artisan email:test --to=your-email@example.com --message="Custom message"
```

### Mailtrap Verification

1. Buka https://mailtrap.io
2. Go to Demo Inbox
3. Lihat email yang dikirim
4. Check HTML rendering, responsive, metadata

### Email Clients Testing

Test tampilan di berbagai client:
- Gmail
- Outlook
- Apple Mail
- Thunderbird
- Mobile (iOS Mail, Gmail Mobile)

---

## 📊 Performance

### File Size

Email templates minimal dengan inline CSS optimal:
- Layout: ~9KB (CSS inline)
- Per template: ~2-3KB
- Total per email: ~10-15KB

### Load Time

- Rendering: < 100ms
- Network: Tergantung mail provider
- Delivery: Instant via SMTP

---

## 🚀 Deployment

### Before Production

- [ ] Test semua email templates di Mailtrap
- [ ] Verify HTML rendering di berbagai clients
- [ ] Update MAIL_FROM_ADDRESS ke domain official
- [ ] Update MAIL_FROM_NAME ke official company name
- [ ] Configure SPF/DKIM/DMARC records
- [ ] Switch dari Mailtrap ke production provider

### Production Providers

| Provider | Setup | Cost | Recommended For |
|----------|-------|------|-----------------|
| SendGrid | 5 min | Paid | High volume |
| AWS SES | 10 min | Pay-per-email | AWS infra |
| Mailgun | 5 min | Paid | Production |
| Gmail | 3 min | Free (limited) | Small volume |

---

## 💡 Tips

1. **Always test** email templates sebelum push
2. **Use emojis** untuk improve readability
3. **Keep it simple** - jangan terlalu banyak informasi
4. **Mobile first** - design untuk mobile dulu
5. **Test links** - pastikan semua URL bekerja
6. **Review metadata** - check From address, Subject line
7. **Monitor delivery** - track bounce dan spam rates

---

## 📚 References

- [Email Template Best Practices](https://www.campaignmonitor.com/resources/guides/mobile-friendly-email/)
- [Responsive Email Design](https://mjml.io/)
- [Email Accessibility](https://www.smashingmagazine.com/2017/01/introduction-building-sending-html-email-for-web-developers/)
