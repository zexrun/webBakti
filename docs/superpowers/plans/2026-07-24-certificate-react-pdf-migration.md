# Certificate PDF Migration to React-PDF Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace DomPDF rendering of the internship completion certificate with `@react-pdf/renderer`, run via a one-shot Node process invoked from Laravel, to fix a confirmed DomPDF bug where long Indonesian sentences truncate mid-line regardless of container width.

**Architecture:** A new Node script (`resources/pdf-renderers/render-certificate.cjs`) takes an input JSON path and output PDF path as CLI arguments, builds the certificate with `@react-pdf/renderer`'s `Document`/`Page`/`View`/`Text`/`StyleSheet` API via `React.createElement` (no JSX, no build step needed), and writes the PDF via `renderToFile`. `FinalAssessmentController::streamCertificatePdf()` is rewritten to compute display-ready values, write them to a temp JSON file, invoke the script via Laravel's `Process` facade, stream back the resulting PDF, and clean up both temp files.

**Tech Stack:** `@react-pdf/renderer` v4.5.1 (new dependency), Node v24 (already installed), `react`/`react-dom` (pinned separately in the script's own scope — verified compatible with react-pdf v4), Laravel's `Illuminate\Support\Facades\Process`.

---

### Task 1: Add @react-pdf/renderer dependency

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Install the dependency**

```bash
npm install @react-pdf/renderer@4.5.1
```

This adds `@react-pdf/renderer` to `package.json`'s `dependencies` (not `devDependencies`) automatically, since it's required at runtime by the Node render script (Task 2), not only during `npm run build`.

- [ ] **Step 2: Verify it installed correctly**

```bash
node -e "console.log(require('@react-pdf/renderer').version)"
```
Expected: prints `4.5.1`.

- [ ] **Step 3: Commit**

```bash
git add package.json package-lock.json
git commit -m "feat: Add @react-pdf/renderer dependency for PDF generation"
```

---

### Task 2: Node render script for the certificate

**Files:**
- Create: `resources/pdf-renderers/render-certificate.cjs`

- [ ] **Step 1: Write the script**

```javascript
const fs = require('fs')
const React = require('react')
const { Document, Page, View, Text, StyleSheet, renderToFile } = require('@react-pdf/renderer')

const styles = StyleSheet.create({
  page: {
    padding: '15mm 20mm',
    fontFamily: 'Times-Roman',
    fontSize: 11,
    color: '#000000',
  },
  header: {
    textAlign: 'center',
    marginBottom: 12,
    paddingBottom: 8,
    borderBottom: '2pt solid #000000',
  },
  ministryName: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    textTransform: 'uppercase',
    marginBottom: 2,
  },
  agencyName: {
    fontFamily: 'Times-Bold',
    fontSize: 12,
    textTransform: 'uppercase',
    marginBottom: 2,
  },
  agencySubtitle: {
    fontSize: 9,
    fontStyle: 'italic',
    color: '#4a90e2',
    marginBottom: 5,
  },
  contactInfo: {
    fontSize: 7,
    color: '#333333',
    lineHeight: 1.3,
  },
  titleBlock: {
    textAlign: 'center',
    marginVertical: 10,
  },
  titleMain: {
    fontFamily: 'Times-Bold',
    fontSize: 14,
    textTransform: 'uppercase',
    textDecoration: 'underline',
    marginBottom: 5,
  },
  documentNumber: {
    fontSize: 9,
  },
  openingStatement: {
    marginBottom: 10,
    fontSize: 11,
  },
  detailBlock: {
    marginVertical: 10,
    paddingLeft: 30,
  },
  detailRow: {
    flexDirection: 'row',
    marginBottom: 2,
  },
  detailLabel: {
    width: 100,
    fontSize: 11,
  },
  detailSeparator: {
    width: 15,
    fontSize: 11,
  },
  detailValue: {
    fontSize: 11,
    flex: 1,
  },
  mainStatement: {
    marginVertical: 12,
    fontSize: 11,
    lineHeight: 1.5,
  },
  statementHighlight: {
    fontFamily: 'Times-Bold',
  },
  closingStatement: {
    marginVertical: 12,
    fontSize: 11,
    lineHeight: 1.5,
  },
  signatureSection: {
    marginTop: 15,
    alignItems: 'flex-end',
  },
  signatureBlock: {
    width: '50%',
    alignItems: 'center',
  },
  signatureDateLocation: {
    alignSelf: 'flex-end',
    marginBottom: 3,
    fontSize: 10,
  },
  signatureTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    marginBottom: 40,
  },
  signatureName: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    textDecoration: 'underline',
    marginBottom: 2,
  },
  signatureNip: {
    fontSize: 9,
  },
  tembusanSection: {
    marginTop: 10,
    paddingTop: 8,
    borderTop: '1pt solid #cccccc',
  },
  tembusanTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    marginBottom: 5,
  },
  tembusanItem: {
    fontSize: 10,
    marginBottom: 2,
    paddingLeft: 15,
  },
})

function DetailRow({ label, value }) {
  return React.createElement(
    View,
    { style: styles.detailRow },
    React.createElement(Text, { style: styles.detailLabel }, label),
    React.createElement(Text, { style: styles.detailSeparator }, ':'),
    React.createElement(Text, { style: styles.detailValue }, value),
  )
}

function buildDocument(data) {
  const periodeText = data.hasPeriode
    ? `${data.periodeMulaiFormatted} sampai dengan ${data.periodeSelesaiFormatted}.`
    : 'periode yang belum ditentukan.'

  return React.createElement(
    Document,
    null,
    React.createElement(
      Page,
      { size: 'A4', style: styles.page },
      React.createElement(
        View,
        { style: styles.header },
        React.createElement(Text, { style: styles.ministryName }, 'Kementerian Komunikasi dan Informatika Republik Indonesia'),
        React.createElement(Text, { style: styles.agencyName }, 'Badan Aksesibilitas Telekomunikasi dan Informasi'),
        React.createElement(Text, { style: styles.agencySubtitle }, 'Indonesia Terkoneksi - Makin Digital, Makin Maju'),
        React.createElement(
          Text,
          { style: styles.contactInfo },
          'Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930\n' +
          'Telp. : 021-31936590 (Hunting) Fax. : 021-31936516, 31927516\n' +
          'www.baktikominfo.id | humas@baktikominfo.id | mail@baktikominfo.id',
        ),
      ),
      React.createElement(
        View,
        { style: styles.titleBlock },
        React.createElement(Text, { style: styles.titleMain }, 'Surat Keterangan'),
        React.createElement(Text, { style: styles.documentNumber }, `Nomor: ${data.documentNumber}`),
      ),
      React.createElement(Text, { style: styles.openingStatement }, 'Yang bertandatangan dibawah ini :'),
      React.createElement(
        View,
        { style: styles.detailBlock },
        DetailRow({ label: 'Nama', value: data.signerName }),
        DetailRow({ label: 'NIP', value: data.signerNip }),
        DetailRow({ label: 'Jabatan', value: data.signerPosition }),
      ),
      React.createElement(Text, { style: styles.openingStatement }, 'menerangkan bahwa :'),
      React.createElement(
        View,
        { style: styles.detailBlock },
        DetailRow({ label: 'Nama', value: data.studentName }),
        DetailRow({ label: 'NIM', value: data.studentNim }),
        DetailRow({ label: 'Program Studi', value: data.studentProgramStudi }),
        DetailRow({ label: 'Universitas', value: data.studentUniversitas }),
      ),
      React.createElement(
        Text,
        { style: styles.mainStatement },
        'Telah selesai melaksanakan ',
        React.createElement(Text, { style: styles.statementHighlight }, 'Magang/PKL'),
        ' di Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI) Kementerian Komunikasi dan Digital dengan pembimbing Sdr. ',
        data.supervisorName,
        ' selaku Staf Direktorat Sumber Daya dan Administrasi terhitung sejak tanggal ',
        periodeText,
      ),
      React.createElement(
        Text,
        { style: styles.closingStatement },
        'Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.',
      ),
      React.createElement(
        View,
        { style: styles.signatureSection },
        React.createElement(
          View,
          { style: styles.signatureBlock },
          React.createElement(Text, { style: styles.signatureDateLocation }, `Jakarta, ${data.signatureDateFormatted}`),
          React.createElement(Text, { style: styles.signatureTitle }, data.signerPosition),
          React.createElement(Text, { style: styles.signatureName }, data.signerName),
          React.createElement(Text, { style: styles.signatureNip }, `NIP ${data.signerNip}`),
        ),
      ),
      React.createElement(
        View,
        { style: styles.tembusanSection },
        React.createElement(Text, { style: styles.tembusanTitle }, 'Tembusan Yth.'),
        React.createElement(Text, { style: styles.tembusanItem }, '1. Plt. Direktur Sumber Daya dan Administrasi BAKTI'),
      ),
    ),
  )
}

async function main() {
  const [, , inputPath, outputPath] = process.argv

  if (!inputPath || !outputPath) {
    console.error('Usage: node render-certificate.cjs <input-json-path> <output-pdf-path>')
    process.exit(1)
  }

  const data = JSON.parse(fs.readFileSync(inputPath, 'utf8'))
  const document = buildDocument(data)
  await renderToFile(document, outputPath)
}

main().catch((err) => {
  console.error(err.stack || err.message || String(err))
  process.exit(1)
})
```

- [ ] **Step 2: Verify the script runs standalone with sample data**

```bash
mkdir -p storage/app/tmp
cat > storage/app/tmp/test-cert-input.json << 'EOF'
{
  "documentNumber": "161/KOMDIG/BAKTI/SDA/07/2026/PKL.01.24/07/2026",
  "supervisorName": "Pak Dede",
  "signerName": "SUDARMANTO",
  "signerNip": "196907071959031002",
  "signerPosition": "Kepala Divisi SDM dan Humas",
  "studentName": "Rifqy",
  "studentNim": "4854524627",
  "studentProgramStudi": "S1 Informatika",
  "studentUniversitas": "Telkom University",
  "periodeMulaiFormatted": "23 July 2026",
  "periodeSelesaiFormatted": "24 July 2026",
  "hasPeriode": true,
  "signatureDateFormatted": "24 July 2026"
}
EOF
node resources/pdf-renderers/render-certificate.cjs storage/app/tmp/test-cert-input.json storage/app/tmp/test-cert-output.pdf
```
Expected: no output, exit code 0, and `storage/app/tmp/test-cert-output.pdf` exists.

- [ ] **Step 3: Verify the output PDF has correct content and no truncation**

```bash
pdftotext -raw storage/app/tmp/test-cert-output.pdf -
```
Expected: full, unbroken text including the complete sentence "Telah selesai melaksanakan Magang/PKL di Badan Aksesibilitas Telekomunikasi dan Informasi (BAKTI) Kementerian Komunikasi dan Digital dengan pembimbing Sdr. Pak Dede selaku Staf Direktorat Sumber Daya dan Administrasi terhitung sejak tanggal 23 July 2026 sampai dengan 24 July 2026." — no words cut off mid-line (a hyphenated word wrap like "Kementer-ian" at a line boundary is correct behavior, not a bug).

```bash
grep -ao "/Count [0-9]*" storage/app/tmp/test-cert-output.pdf | tail -1
```
Expected: `/Count 1` (single page).

- [ ] **Step 4: Clean up test files**

```bash
rm -f storage/app/tmp/test-cert-input.json storage/app/tmp/test-cert-output.pdf
```

- [ ] **Step 5: Commit**

```bash
git add resources/pdf-renderers/render-certificate.cjs
git commit -m "feat: Add React-PDF render script for the internship certificate"
```

---

### Task 3: Rewrite streamCertificatePdf() to use the Node script

**Files:**
- Modify: `app/Http/Controllers/Supervisor/FinalAssessmentController.php`

- [ ] **Step 1: Add new imports**

At the top of the file, change:
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
```
to:
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

use Inertia\Inertia;
```

(The `Barryvdh\DomPDF\Facade\Pdf` import is removed since this controller no longer uses DomPDF at all after this change.)

- [ ] **Step 2: Replace streamCertificatePdf()**

Change:
```php
    /**
     * Shared PDF-building logic for both generateCertificate() (supervisor)
     * and studentDownload() (student) - the certificate is always
     * regenerated from the view rather than read from storage, so both
     * callers need the same $data shape and filename convention.
     */
    private function streamCertificatePdf(Student $student, FinalAssessment $assessment, string $supervisorName, $generatedAt)
    {
        $pdf = Pdf::loadView('supervisor.pdf.certificate-pdf', [
            'student' => $student,
            'assessment' => $assessment,
            'supervisorName' => $supervisorName,
            'generatedAt' => $generatedAt,
            'generatedDate' => $generatedAt->format('d F Y H:i:s'),
        ]);

        $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

        return $pdf->stream($fileName);
    }
```
to:
```php
    /**
     * Shared PDF-building logic for both generateCertificate() (supervisor)
     * and studentDownload() (student) - the certificate is always
     * regenerated fresh rather than read from storage, so both callers
     * need the same $data shape and filename convention. Rendering runs
     * through a Node/React-PDF script rather than DomPDF, since DomPDF
     * was found to truncate long Indonesian sentences mid-line
     * regardless of container width - a bug React-PDF's Yoga-based
     * layout engine does not exhibit.
     */
    private function streamCertificatePdf(Student $student, FinalAssessment $assessment, string $supervisorName, $generatedAt)
    {
        $certDate = $generatedAt ?? now();
        $certNumber = str_pad(($student->id * 37 + $certDate->day) % 900 + 100, 3, '0', STR_PAD_LEFT);
        $documentNumber = $certNumber . '/KOMDIG/BAKTI/SDA/' . $certDate->format('m/Y') . '/PKL.01.' . $certDate->format('d/m/Y');

        $hasPeriode = isset($student->periode_mulai) && isset($student->periode_selesai);

        $data = [
            'documentNumber' => $documentNumber,
            'supervisorName' => $supervisorName,
            'signerName' => 'SUDARMANTO',
            'signerNip' => '196907071959031002',
            'signerPosition' => 'Kepala Divisi SDM dan Humas',
            'studentName' => $student->user->name,
            'studentNim' => $student->nim ?? '-',
            'studentProgramStudi' => $student->program_studi ?? '-',
            'studentUniversitas' => $student->universitas ?? '-',
            'periodeMulaiFormatted' => $hasPeriode ? date('d F Y', strtotime($student->periode_mulai)) : null,
            'periodeSelesaiFormatted' => $hasPeriode ? date('d F Y', strtotime($student->periode_selesai)) : null,
            'hasPeriode' => $hasPeriode,
            'signatureDateFormatted' => date('d F Y'),
        ];

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $id = (string) Str::uuid();
        $inputPath = $tmpDir . '/cert-' . $id . '-input.json';
        $outputPath = $tmpDir . '/cert-' . $id . '-output.pdf';

        file_put_contents($inputPath, json_encode($data));

        try {
            $scriptPath = base_path('resources/pdf-renderers/render-certificate.cjs');
            $result = Process::timeout(30)->run(['node', $scriptPath, $inputPath, $outputPath]);

            if (!$result->successful()) {
                throw new \RuntimeException('React-PDF certificate render failed: ' . $result->errorOutput());
            }

            $pdfContent = file_get_contents($outputPath);
        } finally {
            if (file_exists($inputPath)) {
                unlink($inputPath);
            }
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
        }

        $fileName = 'certificate_' . str_replace(' ', '_', strtolower($student->user->name)) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
```

- [ ] **Step 3: Verify syntax**

```bash
php -l app/Http/Controllers/Supervisor/FinalAssessmentController.php
```
Expected: `No syntax errors detected`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Supervisor/FinalAssessmentController.php
git commit -m "feat: Render internship certificate via React-PDF instead of DomPDF"
```

---

### Task 4: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Syntax check**

```bash
php -l app/Http/Controllers/Supervisor/FinalAssessmentController.php
node -c resources/pdf-renderers/render-certificate.cjs
```
Expected: both report no syntax errors (`node -c` prints nothing on success).

- [ ] **Step 2: Manual browser check — supervisor generates certificate**

As a supervisor with a student who has a complete final assessment and both required documents (e.g. Rifqy in this project's seeded data), open Supervisor > Daftar Mahasiswa, open the "Aksi" menu, click "Generate Sertifikat" (or "Download Sertifikat" if already generated once). Confirm:
- The PDF opens in a new tab with correct student/supervisor/date data.
- The full "Telah selesai melaksanakan Magang/PKL..." sentence is present and complete, not truncated.
- The document is exactly 1 page.

- [ ] **Step 3: Manual browser check — student downloads certificate**

As the student whose certificate was just generated, navigate to the student certificate download route (`student.pdf.certificate.download`) and confirm the same PDF downloads/opens correctly with matching data.

- [ ] **Step 4: Confirm temp file cleanup**

```bash
ls storage/app/tmp/
```
Expected: empty (or absent), confirming no leftover `cert-*-input.json`/`cert-*-output.pdf` files remain after the manual checks in Steps 2-3.

- [ ] **Step 5: Commit any fixes found during manual verification**

Only if Steps 2-4 surface an issue — fix it, re-verify, then:
```bash
git add <fixed files>
git commit -m "fix: <describe what manual verification caught>"
```
