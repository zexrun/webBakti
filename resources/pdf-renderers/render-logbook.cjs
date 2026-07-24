const fs = require('fs')
const React = require('react')
const { Document, Page, View, Text, StyleSheet, renderToFile } = require('@react-pdf/renderer')

const styles = StyleSheet.create({
  page: {
    padding: '15mm 20mm',
    fontFamily: 'Times-Roman',
    fontSize: 10,
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
  sectionTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 11,
    textDecoration: 'underline',
    marginVertical: 6,
  },
  detailRow: {
    flexDirection: 'row',
    marginBottom: 2,
  },
  detailLabel: {
    width: 140,
    fontSize: 11,
  },
  detailSeparator: {
    width: 15,
    textAlign: 'center',
    fontSize: 11,
  },
  detailValue: {
    fontSize: 11,
    flex: 1,
  },
  contentBlock: {
    marginVertical: 8,
    padding: 8,
    border: '1pt solid #cccccc',
    fontSize: 10,
    lineHeight: 1.4,
  },
  signatureSection: {
    marginTop: 20,
  },
  signatureRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 40,
  },
  signatureBlock: {
    alignItems: 'center',
    width: '45%',
  },
  signatureTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    marginBottom: 40,
    textAlign: 'center',
  },
  signatureName: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    textDecoration: 'underline',
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
  const today = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
  return React.createElement(
    Document,
    { title: `LaporanHarian_${data.activityDate}_${data.studentName}_${today}` },
    React.createElement(
      Page,
      { size: 'A4', style: styles.page },
      // Header
      React.createElement(
        View,
        { style: styles.header },
        React.createElement(Text, { style: styles.ministryName }, 'Kementerian Komunikasi dan Digital RI'),
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
      // Title
      React.createElement(
        View,
        { style: styles.titleBlock },
        React.createElement(Text, { style: styles.titleMain }, 'Laporan Kegiatan Harian'),
        React.createElement(Text, { style: styles.documentNumber }, `Nomor: ${data.logNumber}/BAKTI/SDA/${data.reportMonth}`),
      ),
      // Student Data
      React.createElement(Text, { style: styles.sectionTitle }, 'Data Mahasiswa'),
      React.createElement(
        View,
        null,
        DetailRow({ label: 'Nama Lengkap', value: data.studentName }),
        DetailRow({ label: 'NIM', value: data.studentNim }),
        DetailRow({ label: 'Perguruan Tinggi', value: data.studentUniversitas }),
      ),
      // Logbook Details
      React.createElement(Text, { style: styles.sectionTitle }, 'Detail Kegiatan'),
      React.createElement(
        View,
        null,
        DetailRow({ label: 'Tanggal', value: data.activityDate }),
        DetailRow({ label: 'Waktu', value: data.activityTime }),
        DetailRow({ label: 'Perasaan', value: data.feeling }),
      ),
      React.createElement(Text, { style: styles.sectionTitle }, 'Deskripsi Kegiatan'),
      React.createElement(Text, { style: styles.contentBlock }, data.description),
      data.feedback && React.createElement(
        React.Fragment,
        { key: 'feedback' },
        React.createElement(Text, { style: styles.sectionTitle }, 'Feedback Pembimbing'),
        React.createElement(Text, { style: styles.contentBlock }, data.feedback),
      ),
      // Signature
      React.createElement(
        View,
        { style: styles.signatureSection },
        React.createElement(
          View,
          { style: styles.signatureRow },
          React.createElement(
            View,
            { style: styles.signatureBlock },
            React.createElement(Text, { style: styles.signatureTitle }, 'Mahasiswa'),
            React.createElement(Text, null, '\n\n\n'),
            React.createElement(Text, { style: styles.signatureName }, data.studentName),
          ),
          React.createElement(
            View,
            { style: styles.signatureBlock },
            React.createElement(Text, { style: styles.signatureTitle }, 'Pembimbing'),
            React.createElement(Text, null, '\n\n\n'),
            React.createElement(Text, { style: styles.signatureName }, data.supervisorName),
          ),
        ),
      ),
    ),
  )
}

async function main() {
  const [, , inputPath, outputPath] = process.argv

  if (!inputPath || !outputPath) {
    console.error('Usage: node render-logbook.cjs <input-json-path> <output-pdf-path>')
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
