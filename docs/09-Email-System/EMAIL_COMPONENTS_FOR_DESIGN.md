# Email Template Components & Architecture
## Dokumentasi untuk Design System (Gemini + Stitch)

**Tujuan:** Menjelaskan struktur dan komponen email templates WebBakti agar Gemini dapat memahami arsitektur dan Stitch dapat membuat design yang tepat.

---

## 📋 Daftar Isi

1. [Struktur Email Global](#1-struktur-email-global)
2. [Komponen Utama](#2-komponen-utama)
3. [Color Palette & Typography](#3-color-palette--typography)
4. [Komponen Reusable](#4-komponen-reusable)
5. [Template Patterns](#5-template-patterns)
6. [Layout Sections](#6-layout-sections)
7. [Responsive Design](#7-responsive-design)
8. [Implementation Examples](#8-implementation-examples)

---

## 1. Struktur Email Global

### Wrapper Structure
```
┌─────────────────────────────────────────────────┐
│  email-wrapper (Background: #f5f7fa)            │
│  ┌───────────────────────────────────────────┐  │
│  │  email-container (Max-width: 600px)       │  │
│  │  ┌─────────────────────────────────────┐  │  │
│  │  │  email-header (Gradient)            │  │  │
│  │  │  🎓 WebBakti                        │  │  │
│  │  │  Sistem Manajemen Magang Online     │  │  │
│  │  └─────────────────────────────────────┘  │  │
│  │                                             │  │
│  │  ┌─────────────────────────────────────┐  │  │
│  │  │  email-body (Content padding)       │  │  │
│  │  │  - Titles, paragraphs               │  │  │
│  │  │  - Sections, alerts, buttons        │  │  │
│  │  │  - Info boxes, lists, tables        │  │  │
│  │  └─────────────────────────────────────┘  │  │
│  │                                             │  │
│  │  ┌─────────────────────────────────────┐  │  │
│  │  │  email-footer                       │  │  │
│  │  │  Copyright & Links                  │  │  │
│  │  └─────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

### Key Properties
| Property | Value | Purpose |
|----------|-------|---------|
| Max Width | 600px | Optimal email width |
| Background | #f5f7fa | Outer wrapper color |
| Container BG | #FFFFFF | White email container |
| Border Radius | 12px | Modern rounded corners |
| Shadow | 0 4px 12px rgba(0,0,0,0.08) | Subtle depth |
| Border | 1px #e2e8f0 | Subtle edge definition |

---

## 2. Komponen Utama

### A. Header Component
**Fungsi:** Branding utama, identitas visual aplikasi

**Structure:**
```html
<div class="email-header">
    <div class="email-header-logo">🎓 WebBakti</div>
    <div class="email-header-subtitle">Sistem Manajemen Magang Online</div>
</div>
```

**Styling:**
- **Background:** Linear gradient 135deg, `#2E5FDB` → `#1E3FA0`
- **Padding:** 50px 40px
- **Logo Font Size:** 28px, weight 800, color white
- **Subtitle Font Size:** 14px, weight 500, opacity 0.95
- **Text Align:** Center
- **Letter Spacing:** Logo -0.5px, Subtitle 0.3px

**Design Notes:**
- Gradient membership sudut 135 derajat untuk dynamic look
- Logo menggunakan emoji + text untuk recognizable branding
- Subtitle memberikan context tentang aplikasi
- White text dengan opacity pada subtitle untuk hierarchy

---

### B. Body Content Area
**Fungsi:** Area utama untuk isi email

**Key Elements:**

#### 1. **email-title**
```html
<div class="email-title">✨ Judul Email Utama</div>
```
- Font Size: 22px
- Font Weight: 700
- Color: #1a202c (Dark)
- Margin Bottom: 24px
- Line Height: 1.3

#### 2. **email-greeting**
```html
<p class="email-greeting">Halo {{ $notifiable->name }},</p>
```
- Font Size: 16px
- Font Weight: 600
- Color: #2d3748
- Margin Bottom: 20px

#### 3. **email-paragraph**
```html
<p class="email-paragraph">
    Konten paragraf utama email...
</p>
```
- Font Size: 15px
- Line Height: 1.7
- Color: #4a5568 (Muted foreground)
- Margin Bottom: 16px

---

### C. Footer Component
**Fungsi:** Informasi organisasi, links, copyright

**Structure:**
```html
<div class="email-footer">
    <div class="email-footer-content">
        <strong>WebBakti System</strong><br>
        Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI)<br>
        Kementerian Komunikasi dan Digital Republik Indonesia
    </div>
    <div class="email-footer-links">
        <a href="{{ url('/') }}">Kunjungi Aplikasi</a>
        <a href="{{ url('/help') }}">Bantuan</a>
    </div>
    <div class="email-footer-copyright">
        &copy; {{ date('Y') }} BAKTI. Semua hak dilindungi.
    </div>
</div>
```

**Styling:**
- **Background:** #f7fafc (Light gray)
- **Padding:** 32px 40px
- **Border Top:** 1px #e2e8f0
- **Text Align:** Center
- **Font Size:** 13px (content), 12px (copyright)
- **Color:** #718096 (Muted)
- **Links Color:** #2E5FDB (Primary)
- **Links Font Weight:** 500

---

## 3. Color Palette & Typography

### Primary Colors
```
Primary:          #2E5FDB (Modern Blue)
Primary Dark:     #1E3FA0 (For gradient)
Accent:           #E6B800 (Golden Yellow)
```

### Neutral Colors
```
Background:       #f5f7fa (Outer wrapper)
Container:        #FFFFFF (Email container)
Content BG:       #f7fafc (Section backgrounds)
Foreground:       #1a202c (Dark text)
Text Primary:     #2d3748 (Normal text)
Text Secondary:   #4a5568 (Muted text)
Border:           #e2e8f0 (Light borders)
```

### Semantic Colors
```
Success:          #22C55E (Green)
Warning:          #F59E0B (Orange)
Error:            #D63E3E (Red)
Info:             #3B82F6 (Light Blue)
```

### Typography Hierarchy
```
H1 (Titles):      22px, weight 700, color #1a202c
H2 (Section):     16px, weight 700, color #1a202c
H3 (Labels):      14px, weight 600, color #2d3748
Body:             15px, weight 400, color #4a5568
Small (Footer):   12px, weight 400, color #718096
```

**Font Stack:**
```
-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif
```

---

## 4. Komponen Reusable

### 4.1 Button Component

#### Primary Button
```html
<div class="email-button-group">
    <a href="{{ url('/action') }}" class="email-button">
        Lihat Detail
    </a>
</div>
```

**Styling:**
- **Background:** Gradient `#2E5FDB` → `#1E3FA0`
- **Color:** White
- **Padding:** 14px 40px
- **Border Radius:** 6px
- **Font Weight:** 600
- **Font Size:** 14px
- **Shadow:** 0 4px 12px rgba(46, 95, 219, 0.3)
- **Hover:** translateY(-2px), enhanced shadow

#### Secondary Button
```html
<a href="{{ url('/action') }}" class="email-button email-button-secondary">
    Batalkan
</a>
```

**Styling:**
- **Background:** #e2e8f0
- **Color:** #2d3748
- **Shadow:** None
- **Hover:** Background #cbd5e0

---

### 4.2 Alert Boxes

#### Info Alert
```html
<div class="email-alert email-alert-info">
    <strong>ℹ️ Informasi</strong><br>
    Pesan informatif Anda...
</div>
```

**Styling:**
- **Background:** #eff6ff
- **Border Left:** 4px solid #3b82f6
- **Color:** #1e40af
- **Padding:** 16px 20px
- **Border Radius:** 8px

#### Success Alert
```html
<div class="email-alert email-alert-success">
    <strong>✓ Berhasil</strong><br>
    Pesan success Anda...
</div>
```

**Styling:**
- **Background:** #f0fdf4
- **Border Left:** 4px solid #22c55e
- **Color:** #166534

#### Warning Alert
```html
<div class="email-alert email-alert-warning">
    <strong>⚠️ Peringatan</strong><br>
    Pesan warning...
</div>
```

**Styling:**
- **Background:** #fffbeb
- **Border Left:** 4px solid #f59e0b
- **Color:** #92400e

#### Danger Alert
```html
<div class="email-alert email-alert-danger">
    <strong>✗ Kesalahan</strong><br>
    Pesan error...
</div>
```

**Styling:**
- **Background:** #fef2f2
- **Border Left:** 4px solid #ef4444
- **Color:** #991b1b

---

### 4.3 Section Component

```html
<div class="email-section">
    <div class="email-section-title">📋 Judul Section</div>
    <p>Konten section...</p>
</div>
```

**Styling:**
- **Background:** #f7fafc
- **Padding:** 24px
- **Margin:** 32px 0
- **Border Radius:** 8px
- **Border Left:** 4px solid #2E5FDB
- **Title Font Size:** 16px, weight 700
- **Title Margin Bottom:** 12px

---

### 4.4 Info Box Component

```html
<div class="email-info-box">
    <div class="email-info-row">
        <div class="email-info-label">Label:</div>
        <div class="email-info-value">Value</div>
    </div>
</div>
```

**Styling:**
- **Row Padding:** 12px 0
- **Row Border Bottom:** 1px solid #e2e8f0
- **Last Row:** No border
- **Label Font Weight:** 600
- **Label Width:** 120px
- **Label Color:** #4a5568
- **Value Color:** #2d3748

---

### 4.5 List Component

```html
<ul class="email-list">
    <li>Item dengan <strong>bold text</strong></li>
    <li>Item kedua</li>
</ul>
```

**Styling:**
- **Margin:** 16px 0
- **Padding Left:** 24px
- **List Item Margin Bottom:** 10px
- **Color:** #4a5568
- **Strong Color:** #2d3748

---

### 4.6 Divider Component

```html
<hr class="email-divider">
```

**Styling:**
- **Border:** 0
- **Height:** 1px
- **Background:** #e2e8f0
- **Margin:** 32px 0

---

## 5. Template Patterns

### Pattern 1: Status Update Email
**Used For:** Attendance Approval, Exception Approval, Submission Graded

**Structure:**
```
1. Header (Brand)
2. Title (Status message)
3. Greeting
4. Intro paragraph
5. Alert box (Status-specific: success/danger)
6. Section with info box (Details)
7. Optional: Additional section with notes/instructions
8. Button group (Call to action)
9. Divider
10. Footer info
11. Footer links
12. Copyright
```

**Key Elements:**
- Status-specific alert (success/danger)
- Key-value pair info box
- Primary CTA button
- Clear visual hierarchy

---

### Pattern 2: Feedback/Message Email
**Used For:** Logbook Feedback

**Structure:**
```
1. Header
2. Title
3. Greeting
4. Intro paragraph
5. Section with details (Title, date, supervisor name)
6. Section with highlighted message/feedback
7. Button to view full content
8. Divider
9. Closing paragraph
10. Footer
```

**Key Elements:**
- Featured feedback box (highlighted)
- Clear subject matter details
- Link to original content
- Encouragement/next steps

---

### Pattern 3: Reminder/Deadline Email
**Used For:** Task Deadline Reminder

**Structure:**
```
1. Header
2. Dynamic title (based on urgency: URGENT/PENTING/Pengingat)
3. Greeting
4. Intro paragraph
5. Urgency-based alert (danger/warning/info)
6. Section with task details (Info box)
7. Section with description (Limited preview)
8. Optional: Files section
9. Section with step-by-step instructions (Numbered list)
10. Button (CTA)
11. Urgency warning box
12. Closing
13. Footer
```

**Key Elements:**
- Color-coded urgency (Red/Orange/Blue)
- Clear deadline information
- Step-by-step guidance
- Strong call-to-action

---

### Pattern 4: Test Email
**Used For:** Email Sandbox Testing

**Structure:**
```
1. Header
2. Title (Success message)
3. Greeting
4. Intro paragraph
5. Success alert
6. Section with email metadata
7. Section with custom message
8. Button group (Dual CTA)
9. Alert with next steps
```

**Key Elements:**
- Clear success confirmation
- Metadata display (time, environment, etc)
- Dual CTAs (App + External link)
- Guidance on next steps

---

## 6. Layout Sections

### A. Content Section (email-body)
**Padding:** 40px  
**Max Width:** 600px (enforced by container)

**Typical Flow:**
1. Title (22px)
2. Greeting (16px)
3. Paragraph (15px)
4. Sections/alerts/boxes (mixed)
5. Buttons/actions
6. Closing paragraph

---

### B. Footer Section (email-footer)
**Padding:** 32px 40px  
**Background:** #f7fafc  
**Border Top:** 1px #e2e8f0

**Typical Flow:**
1. Organization info (strong text)
2. Organization address/details
3. Links divider (visual separation)
4. Footer links (Kunjungi Aplikasi, Bantuan, Preferensi)
5. Copyright notice
6. Small text disclaimer

---

## 7. Responsive Design

### Mobile Breakpoint: 600px

#### Mobile Adjustments
```css
@media only screen and (max-width: 600px) {
    .email-header {
        padding: 30px 20px;  /* Reduced from 50px 40px */
    }
    
    .email-body {
        padding: 20px;       /* Reduced from 40px */
    }
    
    .email-footer {
        padding: 20px;       /* Reduced from 32px 40px */
    }
    
    .email-title {
        font-size: 18px;     /* Reduced from 22px */
    }
    
    .email-button {
        display: block;      /* Full width */
        width: 100%;
        margin: 10px 0;
    }
    
    .email-info-row {
        flex-direction: column;  /* Stack vertically */
    }
    
    .email-info-label {
        width: 100%;
        margin-bottom: 4px;
    }
}
```

### Dark Mode Support
```css
@media (prefers-color-scheme: dark) {
    body {
        background-color: #1a1a1a;
    }
    
    .email-container {
        background-color: #2a2a2a;
    }
    
    .email-paragraph {
        color: #e0e0e0;
    }
    
    .email-section {
        background-color: #363636;
    }
    
    /* More dark mode colors as needed */
}
```

---

## 8. Implementation Examples

### Example 1: Complete Email Template

```html
@extends('emails.layout', ['title' => 'Status Kehadiran - WebBakti'])

<!-- Header & wrapper handled by layout -->

<div class="email-title">✅ Kehadiran Anda Disetujui</div>

<p class="email-greeting">Halo {{ $notifiable->name }},</p>

<p class="email-paragraph">
    Status kehadiran Anda telah dikaji dan diverifikasi oleh sistem.
</p>

<!-- Alert box -->
<div class="email-alert email-alert-success">
    <strong>✓ Kehadiran DISETUJUI</strong><br>
    Kehadiran Anda telah diverifikasi dan diterima oleh sistem.
</div>

<!-- Info section -->
<div class="email-section">
    <div class="email-section-title">📋 Detail Kehadiran</div>
    <div class="email-info-box">
        <div class="email-info-row">
            <div class="email-info-label">Tanggal & Waktu:</div>
            <div class="email-info-value">
                {{ $attendance->check_in_time->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="email-info-row">
            <div class="email-info-label">Lokasi:</div>
            <div class="email-info-value">{{ $attendance->location }}</div>
        </div>
    </div>
</div>

<!-- Button group -->
<div class="email-button-group">
    <a href="{{ url('/student/attendance') }}" class="email-button">
        Lihat Riwayat Kehadiran
    </a>
</div>

<!-- Divider -->
<hr class="email-divider">

<!-- Closing paragraph -->
<p class="email-paragraph">
    Apabila memiliki pertanyaan, silakan hubungi admin sistem.
</p>

<!-- Footer handled by layout -->
```

---

### Example 2: Component Integration Pattern

```html
<!-- Multiple sections -->
<div class="email-section">
    <div class="email-section-title">✅ Langkah Berikutnya</div>
    <ol class="email-list">
        <li><strong>Baca</strong> deskripsi task</li>
        <li><strong>Download</strong> file attachment</li>
        <li><strong>Kerjakan</strong> task sesuai instruksi</li>
    </ol>
</div>

<!-- Alert with list -->
<div class="email-alert email-alert-warning">
    <strong>⚠️ Catatan Penting</strong>
    <ul class="email-list" style="margin-top: 10px;">
        <li>Pastikan deadline diperhatikan</li>
        <li>Upload sebelum batas waktu</li>
    </ul>
</div>
```

---

## 📐 Component Reference Quick Guide

| Component | Class Name | Primary Use | Key Properties |
|-----------|-----------|------------|-----------------|
| Header | `.email-header` | Branding | Gradient bg, white text |
| Title | `.email-title` | Main heading | 22px, bold, dark color |
| Greeting | `.email-greeting` | Personal address | 16px, semi-bold |
| Paragraph | `.email-paragraph` | Body text | 15px, muted color |
| Button | `.email-button` | CTA | Gradient, shadow, hover |
| Section | `.email-section` | Content grouping | Light bg, left border |
| Alert | `.email-alert-*` | Status messages | Color-coded (4 types) |
| Info Box | `.email-info-box` | Key-value display | Flex layout, borders |
| List | `.email-list` | Bullet/numbered | Proper indentation |
| Divider | `.email-divider` | Visual separator | 1px line, muted |
| Footer | `.email-footer` | Organization info | Light bg, muted text |

---

## 🎨 Design Guidelines untuk Stitch

### DO ✅
- Use primary color #2E5FDB for important elements
- Maintain consistent spacing (8px, 16px, 24px, 32px)
- Use semantic alerts for status messages
- Keep text color hierarchy clear
- Ensure sufficient contrast (4.5:1 minimum)
- Test responsive at 600px breakpoint
- Use gradients sparingly for impact

### DON'T ❌
- Don't exceed 600px width (unless full bleed)
- Don't use more than 2 font sizes for body text
- Don't forget mobile padding adjustments
- Don't use colors outside the palette
- Don't add elements without proper spacing
- Don't forget dark mode considerations
- Don't use complex layouts (email clients limited)

---

## 📝 Template Checklist untuk Design

- [ ] Header with gradient #2E5FDB → #1E3FA0
- [ ] Logo with emoji + text (white, 28px)
- [ ] Title section (22px, dark)
- [ ] Greeting with name placeholder
- [ ] Intro paragraph
- [ ] Primary alert (status-specific color)
- [ ] Section with info box (key-value pairs)
- [ ] Additional sections as needed
- [ ] Button group with CTA
- [ ] Optional divider before closing
- [ ] Closing paragraph
- [ ] Footer with organization info
- [ ] Footer links (3-4 links)
- [ ] Copyright notice
- [ ] Responsive checks at 600px
- [ ] Dark mode color adjustments

---

**Created:** 27 Juli 2026  
**Format:** Markdown untuk AI Comprehension  
**Purpose:** Email Template Design & Development  
**Target Audience:** Gemini (AI Design Understanding) + Stitch (Design Tool)
