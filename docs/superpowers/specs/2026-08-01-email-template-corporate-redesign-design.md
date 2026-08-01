# Email Template Redesign — Corporate/Formal Style
## Design Spec

**Date:** 1 Agustus 2026
**Status:** Approved
**Scope:** `resources/views/emails/layout.blade.php` and all templates that extend it

---

## Background

The email layout was redesigned on 30 Juli 2026 into a "Minimalist & Spacious" style (blue gradient header, rounded corners, generous whitespace — see `docs/EMAIL_LAYOUT_REDESIGN.md`). This spec supersedes that visual direction with a **Corporate/Formal** style, chosen through visual brainstorming to better match the institutional context (BAKTI / Kementerian Komunikasi dan Digital RI).

The change is visual only — no new email types, no new data being passed to templates, no changes to when/how emails are sent.

## Design Decisions

Selected through iterative visual review (4 initial directions → gold accent exploration → 4 label-color alternatives → 4 header layout variants → full 6-template preview, all approved).

### Color Palette

| Token | Value | Usage |
|---|---|---|
| Primary navy | `#0F2A5C` | Header background, category label text, CTA button background |
| Success accent | `#2E7D32` text / `#1B5E20` bg tint `#F3F8F3` | Success alert box (replaces previous green alert) |
| Danger accent | `#C0392B` border / `#922B21` text / bg tint `#FDF2F2` | Danger/urgent alert box |
| Body text | `#111` (headings), `#444` (paragraphs), `#666` (info labels) | Content text |
| Borders | `#e2e2e2` (container), `#eee` (info-row dividers) | Structural lines |
| Footer bg | `#fafbfc` | Footer background |
| Footer text | `#8a8f97` (muted), `#555` (strong) | Footer copy |

No gradient. No gold/amber accent (tried, rejected as "kurang enak dilihat" — replaced with navy-on-white for category labels).

**Warning alert (not visually tested, defined here by extrapolation):** follows the same formula as success/danger — `border-left: 3px solid #B8860B` is *not* used (that's the rejected gold); instead use a muted amber `#B7791F` border, `#FFFBEB` background tint, `#7C4A03` text. This keeps warning visually distinct from danger (red) and success (green) without reintroducing the rejected gold hue for labels.

### Typography

- Font stack: `Arial, Helvetica, sans-serif` (replaces the previous `-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue'` stack). Web-safe, renders identically across all email clients — no reliance on OS-native fonts.
- Category label (eyebrow): 11px, weight 700, navy, uppercase, letter-spacing 0.8px
- Email title: 17px, weight 700, `#111`
- Body paragraph: 13px, `#444`, line-height 1.7
- Info row label: 12px, `#666`
- Info row value: 12px, `#111`, weight 600
- Footer: 10px, `#8a8f97`

### Layout Structure Changes

**Header** — replaces the centered gradient block:
- Left-aligned, flex layout (`justify-content: space-between`)
- Left: "WebBakti" (19px, weight 800) + subtitle "Sistem Manajemen Magang" (10px, uppercase, 75% opacity) stacked
- Right: institutional label "KEMENTERIAN KOMUNIKASI DAN DIGITAL RI" (9px, 60% opacity, right-aligned, two lines)
- Background: solid navy `#0F2A5C` (no gradient)
- Padding: 24px 28px

**Category label** — new element, did not exist in the previous design:
- Sits above every email title as an uppercase eyebrow (e.g. "Status Kehadiran", "Pengingat Deadline", "Feedback Logbook", "Penilaian Tugas", "Status Pengajuan")
- Replaces the emoji-prefixed title pattern (e.g. `✅ Kehadiran Disetujui` becomes category label "Status Kehadiran" + plain title "Kehadiran Disetujui")
- **Emoji usage is dropped throughout** — titles, alerts, and section headers no longer use emoji prefixes, consistent with the formal/institutional direction

**Info sections** — replaces `.email-section` (background box with left border + uppercase title):
- Becomes a plain label-value row list: `display:flex; justify-content:space-between` per row, separated by `border-top`/`border-bottom: 1px solid #eee`, no background fill, no card container
- No section title/heading above the row group — the category label at the top of the email already establishes context

**Lists** (`.email-list`, used for numbered steps in task-deadline-reminder and bulleted "next steps" elsewhere) — keep as plain `<ul>`/`<ol>` with the new body text color/size; no card or background wrapper. Not separately mocked up since it's a minor text-formatting element, not a structural one.

**Alerts** — keep the alert concept (info/success/warning/danger) but restyle:
- Border-left 3px (was 4px) in the accent color
- Background: light tint of the accent hue (was a slightly different, less desaturated tint)
- No bold all-caps status word + icon combo (e.g. drop "✓ Kehadiran Anda DISETUJUI") — replaced with a short plain-language lead-in (e.g. "**Disetujui.** Kehadiran telah tercatat dalam sistem.")

**Buttons**:
- Solid navy `#0F2A5C` background, white text, weight 600
- `border-radius: 2px` (was 5px) — sharp corners throughout the design, not just buttons
- Uppercase label text (e.g. "LIHAT RIWAYAT KEHADIRAN") — was sentence case
- No gradient, no hover-lift shadow animation (formal, static feel)

**Grade display** (submission-graded only) — replaces the purple gradient box:
- Solid navy `#0F2A5C` background
- Small uppercase label "NILAI ANDA" in muted navy-tinted white (`#c9d3e6`)
- Large number (40px, was 56px), no letter-spacing tightening needed at this size

**Footer**:
- Unchanged structurally (org name, institution lines, copyright) but font size/color follow the new type scale (10px, `#8a8f97`)
- Footer links section: keep but restyle to match — no separate exploration was done on footer links, so apply the same navy/Arial treatment as the rest of the footer

**Border-radius**: the whole design moves to sharp corners. Container: no radius (was 12px). Buttons: 2px (was 5px). This is the single biggest structural signature of "corporate/formal" vs. the previous "minimalist/spacious" style.

**Container shadow**: keep a subtle box-shadow (`0 4px 16px rgba(0,0,0,0.1)`) for definition against the email client background, since there's no rounded card look to rely on.

### Explicitly Out of Scope

- No new email types or triggers
- No changes to Mail classes or notification logic in `app/Mail/` or wherever notifications are dispatched
- No dark-mode variant (not requested, not explored)
- No changes to plain-text alternative parts if any exist
- Emoji removal is cosmetic only — does not affect any stored data (e.g. `$logbook->feedback` content itself is untouched, only the template chrome around it)

## Affected Files

All templates extend `resources/views/emails/layout.blade.php`, so the layout file carries the header/footer/CSS changes. Per-template content changes (dropping emoji, restructuring info boxes into label-value rows, restyling alerts) are needed in each:

- `resources/views/emails/layout.blade.php` — header, footer, all shared CSS classes
- `resources/views/emails/attendance-approval.blade.php`
- `resources/views/emails/exception-approval.blade.php`
- `resources/views/emails/logbook-feedback.blade.php`
- `resources/views/emails/submission-graded.blade.php`
- `resources/views/emails/task-deadline-reminder.blade.php`
- `resources/views/emails/test.blade.php` and `test_simple.blade.php` — update if they render visible content used for manual QA; otherwise leave as-is since they're test-only

## Testing

- Use the existing email test command/route (referenced in `docs/EMAIL_SETUP.md`) to send each of the 6 template types and visually verify against the approved mockups
- Check mobile breakpoint (600px) still collapses sensibly — the existing responsive media query approach carries over; the header's flex layout (name left / ministry label right) needs a mobile rule to stack vertically since two-column flex will be cramped under 600px
- Verify Arial renders consistently (it's a system font, low risk, but confirm no fallback issues in common clients: Gmail web, Outlook, Apple Mail)
