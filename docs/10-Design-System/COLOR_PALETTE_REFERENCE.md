# WebBakti Color Palette Reference
## Lengkap dengan OKLch, Hex, RGB, dan HSL Values

**Last Updated:** 27 Juli 2026  
**Color Model:** OKLch (Primary) + Hex/RGB/HSL (Secondary)  
**Font:** Inter Variable  

---

## 📋 Daftar Isi

1. [Light Mode - Primary Colors](#1-light-mode---primary-colors)
2. [Light Mode - Neutral Colors](#2-light-mode---neutral-colors)
3. [Light Mode - Semantic Colors](#3-light-mode---semantic-colors)
4. [Light Mode - Sidebar Colors](#4-light-mode---sidebar-colors)
5. [Dark Mode - Primary Colors](#5-dark-mode---primary-colors)
6. [Dark Mode - Neutral Colors](#6-dark-mode---neutral-colors)
7. [Dark Mode - Semantic Colors](#7-dark-mode---semantic-colors)
8. [Dark Mode - Sidebar Colors](#8-dark-mode---sidebar-colors)
9. [Chart/Data Visualization Colors](#9-chartdata-visualization-colors)
10. [Email Template Colors](#10-email-template-colors)
11. [CSS Variables Complete List](#11-css-variables-complete-list)
12. [Conversion Reference](#12-conversion-reference)

---

## 1. LIGHT MODE - PRIMARY COLORS

### Primary Color (Brand)
```
Variable:         --primary
OKLch:            oklch(0.38 0.11 265)
Hex:              #2E5FDB
RGB:              46, 95, 219
HSL:              221°, 75%, 52%
Decimal:          3028443
```
**Usage:** CTA buttons, links, active states, primary UI elements

**Visual Reference:**
```
█████████████████████████████████████████ #2E5FDB
```

---

### Primary Foreground (Text on Primary)
```
Variable:         --primary-foreground
OKLch:            oklch(0.98 0.005 90)
Hex:              #FAFBFC
RGB:              250, 251, 252
HSL:              210°, 20%, 100%
```
**Usage:** Text/content on top of primary color background

---

### Primary (Dark Mode)
```
Variable:         --primary (dark)
OKLch:            oklch(0.75 0.1 260)
Hex:              #7DBAFF
RGB:              125, 186, 255
HSL:              217°, 100%, 75%
```
**Usage:** Primary color in dark mode (lighter for contrast)

---

## 2. LIGHT MODE - NEUTRAL COLORS

### Background
```
Variable:         --background
OKLch:            oklch(0.99 0.004 90)
Hex:              #FAFBFC
RGB:              250, 251, 252
HSL:              210°, 20%, 100%
```
**Usage:** Main page/body background

---

### Foreground (Primary Text)
```
Variable:         --foreground
OKLch:            oklch(0.24 0.02 265)
Hex:              #1A1D2E
RGB:              26, 29, 46
HSL:              230°, 28%, 14%
```
**Usage:** Primary text color, headings

---

### Card/Container Background
```
Variable:         --card
OKLch:            oklch(1 0 0)
Hex:              #FFFFFF
RGB:              255, 255, 255
HSL:              0°, 0%, 100%
```
**Usage:** Card backgrounds, component containers

---

### Card Foreground (Text on Card)
```
Variable:         --card-foreground
OKLch:            oklch(0.24 0.02 265)
Hex:              #1A1D2E
RGB:              26, 29, 46
HSL:              230°, 28%, 14%
```
**Usage:** Text on card backgrounds

---

### Secondary (Light Background)
```
Variable:         --secondary
OKLch:            oklch(0.95 0.015 75)
Hex:              #F9F8F6
RGB:              249, 248, 246
HSL:              30°, 33%, 97%
```
**Usage:** Secondary backgrounds, subtle surfaces

---

### Secondary Foreground (Text on Secondary)
```
Variable:         --secondary-foreground
OKLch:            oklch(0.3 0.04 265)
Hex:              #1F2B4D
RGB:              31, 43, 77
HSL:              220°, 42%, 21%
```
**Usage:** Text on secondary background

---

### Muted (Disabled/Inactive)
```
Variable:         --muted
OKLch:            oklch(0.96 0.01 80)
Hex:              #F5F5F5
RGB:              245, 245, 245
HSL:              0°, 0%, 96%
```
**Usage:** Disabled states, inactive elements

---

### Muted Foreground (Text on Muted)
```
Variable:         --muted-foreground
OKLch:            oklch(0.5 0.02 265)
Hex:              #7B8492
RGB:              123, 132, 146
HSL:              214°, 8%, 53%
```
**Usage:** Secondary text, labels, muted content

---

### Border
```
Variable:         --border
OKLch:            oklch(0.9 0.012 75)
Hex:              #E8E8E8
RGB:              232, 232, 232
HSL:              0°, 0%, 91%
```
**Usage:** Borders, dividers, subtle separators

---

### Input Field Background
```
Variable:         --input
OKLch:            oklch(0.9 0.012 75)
Hex:              #E8E8E8
RGB:              232, 232, 232
HSL:              0°, 0%, 91%
```
**Usage:** Form inputs, text fields

---

### Ring/Focus Indicator
```
Variable:         --ring
OKLch:            oklch(0.38 0.11 265 / 0.5)
Hex:              #2E5FDB (50% opacity)
RGB:              46, 95, 219 (50% opacity)
HSL:              221°, 75%, 52% (50% opacity)
```
**Usage:** Focus states, keyboard navigation indicators

---

## 3. LIGHT MODE - SEMANTIC COLORS

### Accent (Highlights/Important Info)
```
Variable:         --accent
OKLch:            oklch(0.68 0.14 55)
Hex:              #E6B800
RGB:              230, 184, 0
HSL:              47°, 100%, 45%
```
**Usage:** Highlights, badges, important information

---

### Accent Foreground (Text on Accent)
```
Variable:         --accent-foreground
OKLch:            oklch(0.18 0.02 60)
Hex:              #4D4400
RGB:              77, 68, 0
HSL:              50°, 100%, 15%
```
**Usage:** Text on accent background

---

### Destructive (Error/Danger)
```
Variable:         --destructive
OKLch:            oklch(0.577 0.215 27.325)
Hex:              #D63E3E
RGB:              214, 62, 62
HSL:              0°, 77%, 54%
```
**Usage:** Error states, danger actions, destructive buttons

---

### Destructive Foreground (Text on Error)
```
Variable:         --destructive-foreground
OKLch:            oklch(0.98 0.005 90)
Hex:              #FAFBFC
RGB:              250, 251, 252
HSL:              210°, 20%, 100%
```
**Usage:** Text on error/danger backgrounds

---

## 4. LIGHT MODE - SIDEBAR COLORS

### Sidebar Background
```
Variable:         --sidebar
OKLch:            oklch(0.22 0.03 265)
Hex:              #2A2D3F
RGB:              42, 45, 63
HSL:              231°, 20%, 21%
```
**Usage:** Sidebar container background

---

### Sidebar Foreground (Text)
```
Variable:         --sidebar-foreground
OKLch:            oklch(0.93 0.01 90)
Hex:              #EDF2F7
RGB:              237, 242, 247
HSL:              209°, 30%, 95%
```
**Usage:** Text on sidebar background

---

### Sidebar Primary (Active Item)
```
Variable:         --sidebar-primary
OKLch:            oklch(0.68 0.14 55)
Hex:              #E6B800
RGB:              230, 184, 0
HSL:              47°, 100%, 45%
```
**Usage:** Active menu items, highlighted navigation

---

### Sidebar Primary Foreground
```
Variable:         --sidebar-primary-foreground
OKLch:            oklch(0.18 0.02 60)
Hex:              #4D4400
RGB:              77, 68, 0
HSL:              50°, 100%, 15%
```
**Usage:** Text on active sidebar items

---

### Sidebar Accent (Secondary Highlight)
```
Variable:         --sidebar-accent
OKLch:            oklch(0.29 0.03 265)
Hex:              #3D4259
RGB:              61, 66, 89
HSL:              231°, 19%, 29%
```
**Usage:** Secondary highlights in sidebar

---

### Sidebar Border
```
Variable:         --sidebar-border
OKLch:            oklch(1 0 0 / 8%)
Hex:              #000000 (8% opacity)
RGB:              0, 0, 0 (8% opacity)
```
**Usage:** Borders within sidebar

---

## 5. DARK MODE - PRIMARY COLORS

### Dark Mode Primary (Lighter for Contrast)
```
Variable:         --primary (dark)
OKLch:            oklch(0.75 0.1 260)
Hex:              #7DBAFF
RGB:              125, 186, 255
HSL:              217°, 100%, 75%
```
**Usage:** Primary buttons, links in dark mode

---

### Dark Mode Primary Foreground
```
Variable:         --primary-foreground (dark)
OKLch:            oklch(0.18 0.02 265)
Hex:              #1F1F2E
RGB:              31, 31, 46
HSL:              240°, 19%, 15%
```
**Usage:** Text on primary in dark mode

---

## 6. DARK MODE - NEUTRAL COLORS

### Dark Mode Background
```
Variable:         --background (dark)
OKLch:            oklch(0.19 0.02 265)
Hex:              #1A1A26
RGB:              26, 26, 38
HSL:              240°, 19%, 12%
```
**Usage:** Main background in dark mode

---

### Dark Mode Foreground (Text)
```
Variable:         --foreground (dark)
OKLch:            oklch(0.94 0.008 90)
Hex:              #F0F0F5
RGB:              240, 240, 245
HSL:              240°, 25%, 95%
```
**Usage:** Primary text in dark mode

---

### Dark Mode Card Background
```
Variable:         --card (dark)
OKLch:            oklch(0.24 0.025 265)
Hex:              #24243F
RGB:              36, 36, 63
HSL:              240°, 27%, 19%
```
**Usage:** Card backgrounds in dark mode

---

### Dark Mode Muted Background
```
Variable:         --muted (dark)
OKLch:            oklch(0.28 0.02 265)
Hex:              #31313F
RGB:              49, 49, 63
HSL:              240°, 12%, 22%
```
**Usage:** Disabled/inactive backgrounds in dark mode

---

### Dark Mode Muted Foreground
```
Variable:         --muted-foreground (dark)
OKLch:            oklch(0.68 0.015 90)
Hex:              #ABABAB
RGB:              171, 171, 171
HSL:              0°, 0%, 67%
```
**Usage:** Muted text in dark mode

---

## 7. DARK MODE - SEMANTIC COLORS

### Dark Mode Accent
```
Variable:         --accent (dark)
OKLch:            oklch(0.7 0.15 55)
Hex:              #FFD84D
RGB:              255, 216, 77
HSL:              47°, 100%, 65%
```
**Usage:** Highlights in dark mode (brighter yellow)

---

### Dark Mode Destructive
```
Variable:         --destructive (dark)
OKLch:            oklch(0.704 0.191 22.216)
Hex:              #FF6B6B
RGB:              255, 107, 107
HSL:              0°, 100%, 71%
```
**Usage:** Error states in dark mode (brighter red)

---

## 8. DARK MODE - SIDEBAR COLORS

### Dark Mode Sidebar Background
```
Variable:         --sidebar (dark)
OKLch:            oklch(0.16 0.02 265)
Hex:              #15151F
RGB:              21, 21, 31
HSL:              240°, 19%, 10%
```
**Usage:** Sidebar in dark mode (darker background)

---

### Dark Mode Sidebar Primary
```
Variable:         --sidebar-primary (dark)
OKLch:            oklch(0.7 0.15 55)
Hex:              #FFD84D
RGB:              255, 216, 77
HSL:              47°, 100%, 65%
```
**Usage:** Active sidebar items in dark mode

---

## 9. CHART/DATA VISUALIZATION COLORS

### Chart Color 1 (Primary Blue)
```
Variable:         --chart-1
OKLch:            oklch(0.38 0.11 265)
Hex:              #2E5FDB
RGB:              46, 95, 219
HSL:              221°, 75%, 52%
```

### Chart Color 2 (Accent Yellow)
```
Variable:         --chart-2
OKLch:            oklch(0.68 0.14 55)
Hex:              #E6B800
RGB:              230, 184, 0
HSL:              47°, 100%, 45%
```

### Chart Color 3 (Sky Blue)
```
Variable:         --chart-3
OKLch:            oklch(0.6 0.12 200)
Hex:              #4A9FD8
RGB:              74, 159, 216
HSL:              200°, 62%, 57%
```

### Chart Color 4 (Warm Orange)
```
Variable:         --chart-4
OKLch:            oklch(0.65 0.16 25)
Hex:              #E8944A
RGB:              232, 148, 74
HSL:              25°, 77%, 60%
```

### Chart Color 5 (Purple)
```
Variable:         --chart-5
OKLch:            oklch(0.55 0.1 320)
Hex:              #A855C1
RGB:              168, 85, 193
HSL:              280°, 60%, 55%
```

---

## 10. EMAIL TEMPLATE COLORS

### Email Primary
```
Hex:              #2E5FDB
Used For:         Header gradient, buttons, links, section borders
```

### Email Primary Dark
```
Hex:              #1E3FA0
Used For:         Header gradient second color
```

### Email Accent
```
Hex:              #E6B800
Used For:         Highlights, important information in emails
```

### Email Background
```
Hex:              #FAFBFC
Used For:         Email container background
```

### Email Foreground
```
Hex:              #1A1D2E
Used For:         Primary text in emails
```

### Email Border
```
Hex:              #E8E8E8
Used For:         Borders, dividers in emails
```

### Email Muted
```
Hex:              #7B8492
Used For:         Secondary text, labels in emails
```

---

## 11. CSS VARIABLES COMPLETE LIST

### Light Mode (Root/Default)
```css
:root {
    /* Primary */
    --primary: oklch(0.38 0.11 265);
    --primary-foreground: oklch(0.98 0.005 90);
    
    /* Neutral */
    --background: oklch(0.99 0.004 90);
    --foreground: oklch(0.24 0.02 265);
    --card: oklch(1 0 0);
    --card-foreground: oklch(0.24 0.02 265);
    --secondary: oklch(0.95 0.015 75);
    --secondary-foreground: oklch(0.3 0.04 265);
    --muted: oklch(0.96 0.01 80);
    --muted-foreground: oklch(0.5 0.02 265);
    --border: oklch(0.9 0.012 75);
    --input: oklch(0.9 0.012 75);
    
    /* Semantic */
    --accent: oklch(0.68 0.14 55);
    --accent-foreground: oklch(0.18 0.02 60);
    --destructive: oklch(0.577 0.215 27.325);
    --destructive-foreground: oklch(0.98 0.005 90);
    
    /* Focus & Ring */
    --ring: oklch(0.38 0.11 265 / 0.5);
    
    /* Sidebar */
    --sidebar: oklch(0.22 0.03 265);
    --sidebar-foreground: oklch(0.93 0.01 90);
    --sidebar-primary: oklch(0.68 0.14 55);
    --sidebar-primary-foreground: oklch(0.18 0.02 60);
    --sidebar-accent: oklch(0.29 0.03 265);
    --sidebar-accent-foreground: oklch(0.95 0.01 90);
    --sidebar-border: oklch(1 0 0 / 8%);
    --sidebar-ring: oklch(0.68 0.14 55 / 0.5);
    --sidebar-muted: oklch(0.75 0.02 265);
    
    /* Charts */
    --chart-1: oklch(0.38 0.11 265);
    --chart-2: oklch(0.68 0.14 55);
    --chart-3: oklch(0.6 0.12 200);
    --chart-4: oklch(0.65 0.16 25);
    --chart-5: oklch(0.55 0.1 320);
    
    /* Radius */
    --radius: 0.75rem;
    
    /* Fonts */
    --font-heading: 'Inter Variable', sans-serif;
    --font-sans: 'Inter Variable', sans-serif;
}
```

### Dark Mode
```css
.dark {
    /* Primary (Lighter) */
    --primary: oklch(0.75 0.1 260);
    --primary-foreground: oklch(0.18 0.02 265);
    
    /* Neutral (Inverted) */
    --background: oklch(0.19 0.02 265);
    --foreground: oklch(0.94 0.008 90);
    --card: oklch(0.24 0.025 265);
    --card-foreground: oklch(0.94 0.008 90);
    --secondary: oklch(0.3 0.025 265);
    --secondary-foreground: oklch(0.94 0.008 90);
    --muted: oklch(0.28 0.02 265);
    --muted-foreground: oklch(0.68 0.015 90);
    --border: oklch(1 0 0 / 10%);
    --input: oklch(1 0 0 / 15%);
    
    /* Semantic (Brighter) */
    --accent: oklch(0.7 0.15 55);
    --accent-foreground: oklch(0.18 0.02 60);
    --destructive: oklch(0.704 0.191 22.216);
    
    /* Focus */
    --ring: oklch(0.75 0.1 260 / 0.5);
    
    /* Sidebar */
    --sidebar: oklch(0.16 0.02 265);
    --sidebar-foreground: oklch(0.93 0.01 90);
    --sidebar-primary: oklch(0.7 0.15 55);
    --sidebar-primary-foreground: oklch(0.18 0.02 60);
    --sidebar-accent: oklch(0.24 0.02 265);
    --sidebar-accent-foreground: oklch(0.95 0.01 90);
    --sidebar-border: oklch(1 0 0 / 8%);
    --sidebar-ring: oklch(0.7 0.15 55 / 0.5);
    --sidebar-muted: oklch(0.68 0.015 265);
    
    /* Charts */
    --chart-1: oklch(0.75 0.1 260);
    --chart-2: oklch(0.7 0.15 55);
    --chart-3: oklch(0.65 0.12 200);
    --chart-4: oklch(0.68 0.16 25);
    --chart-5: oklch(0.6 0.1 320);
}
```

---

## 12. CONVERSION REFERENCE

### OKLch to Hex Conversion Examples

| OKLch | Hex | Color | Use Case |
|-------|-----|-------|----------|
| oklch(0.38 0.11 265) | #2E5FDB | Primary Blue | Buttons, links |
| oklch(0.68 0.14 55) | #E6B800 | Accent Yellow | Highlights |
| oklch(0.24 0.02 265) | #1A1D2E | Dark Text | Headings, body text |
| oklch(0.99 0.004 90) | #FAFBFC | Almost White | Backgrounds |
| oklch(0.9 0.012 75) | #E8E8E8 | Light Gray | Borders |
| oklch(0.5 0.02 265) | #7B8492 | Muted Gray | Secondary text |
| oklch(0.577 0.215 27.325) | #D63E3E | Error Red | Errors, danger |

---

## 🎨 Quick Reference Cards

### PRIMARY PALETTE
```
PRIMARY:          #2E5FDB ■
PRIMARY DARK:     #1E3FA0 ■
ACCENT:           #E6B800 ■
SUCCESS:          #22C55E ■ (from email system)
WARNING:          #F59E0B ■ (from email system)
ERROR:            #D63E3E ■
```

### NEUTRAL PALETTE
```
WHITE:            #FFFFFF ■
LIGHT:            #FAFBFC ■
SECONDARY:        #F9F8F6 ■
GRAY LIGHT:       #F5F5F5 ■
GRAY MEDIUM:      #E8E8E8 ■
GRAY DARK:        #7B8492 ■
TEXT:             #1A1D2E ■
BLACK:            #000000 ■
```

---

## 📱 Responsive Considerations

- **Primary Blue (#2E5FDB):** Maintains high contrast on all backgrounds
- **Light Backgrounds:** Meet WCAG AA standards with dark text
- **Dark Mode:** Primary is brightened to #7DBAFF for contrast
- **Accent Yellow (#E6B800):** Vibrant enough to stand out in light mode, brightened to #FFD84D in dark

---

## 💾 Integration Guidelines

### CSS Usage
```css
/* Light mode (default) */
button {
    background-color: var(--primary);
    color: var(--primary-foreground);
}

/* Dark mode */
.dark button {
    background-color: var(--primary); /* Already overridden */
}

/* Text */
body {
    color: var(--foreground);
    background: var(--background);
}

/* Borders */
.card {
    border: 1px solid var(--border);
}
```

### Tailwind Usage
```html
<!-- Primary button -->
<button class="bg-primary text-primary-foreground">Action</button>

<!-- Secondary text -->
<p class="text-muted-foreground">Secondary text</p>

<!-- Border -->
<div class="border border-border">Content</div>

<!-- Sidebar -->
<nav class="bg-sidebar text-sidebar-foreground">Menu</nav>
```

---

## 📊 Color System Summary

| Category | Light | Dark | Purpose |
|----------|-------|------|---------|
| Primary | #2E5FDB | #7DBAFF | Brand color, CTAs |
| Accent | #E6B800 | #FFD84D | Highlights |
| Success | #22C55E | #22C55E | Positive states |
| Warning | #F59E0B | #F59E0B | Caution states |
| Error | #D63E3E | #FF6B6B | Error states |
| Text | #1A1D2E | #F0F0F5 | Readability |
| Border | #E8E8E8 | #333333 | Separation |
| Background | #FAFBFC | #1A1A26 | Canvas |

---

**Color Reference Version:** 1.0  
**Last Updated:** 27 Juli 2026  
**Maintained By:** WebBakti Design System  
**Format:** Complete palette with OKLch, Hex, RGB, HSL  
**Status:** Production Ready ✅
