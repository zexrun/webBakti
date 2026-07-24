const fs = require('fs')
const React = require('react')
const { Document, Page, View, Text, StyleSheet, renderToFile } = require('@react-pdf/renderer')

const styles = StyleSheet.create({
  page: {
    padding: '15mm 20mm',
    fontFamily: 'Times-Roman',
    fontSize: 9,
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
  agencyAddress: {
    fontSize: 7,
    color: '#333333',
    lineHeight: 1.3,
    marginTop: 4,
  },
  titleBlock: {
    textAlign: 'center',
    marginVertical: 10,
  },
  titleMain: {
    fontFamily: 'Times-Bold',
    fontSize: 13,
    textTransform: 'uppercase',
    textDecoration: 'underline',
  },
  documentNumber: {
    fontSize: 9,
    marginTop: 3,
  },
  sectionTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 10,
    textDecoration: 'underline',
    marginVertical: 5,
  },
  detailRow: {
    flexDirection: 'row',
    marginBottom: 2,
  },
  detailLabel: {
    width: 140,
    fontSize: 9,
  },
  detailSeparator: {
    width: 15,
    textAlign: 'center',
    fontSize: 9,
  },
  detailValue: {
    fontSize: 9,
    flex: 1,
  },
  logsTable: {
    marginTop: 6,
    borderWidth: 1,
    borderColor: '#000000',
  },
  tableHeader: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderColor: '#000000',
    backgroundColor: '#f5f5f5',
  },
  tableRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderColor: '#000000',
  },
  colNo: {
    width: '6%',
    padding: 4,
    textAlign: 'center',
    fontSize: 8,
    fontFamily: 'Times-Bold',
  },
  colDate: {
    width: '13%',
    padding: 4,
    fontSize: 8,
  },
  colTime: {
    width: '10%',
    padding: 4,
    textAlign: 'center',
    fontSize: 8,
  },
  colActivity: {
    width: '45%',
    padding: 4,
    fontSize: 8,
  },
  colFeeling: {
    width: '12%',
    padding: 4,
    textAlign: 'center',
    fontSize: 8,
  },
  colStatus: {
    width: '15%',
    padding: 4,
    textAlign: 'center',
    fontSize: 8,
  },
  emptyState: {
    textAlign: 'center',
    padding: 20,
    fontStyle: 'italic',
    fontSize: 9,
  },
  summaryRow: {
    flexDirection: 'row',
    marginTop: 8,
    marginBottom: 2,
  },
  summaryLabel: {
    width: 180,
    fontSize: 9,
  },
  summarySeparator: {
    width: 15,
    textAlign: 'center',
    fontSize: 9,
  },
  summaryValue: {
    fontSize: 9,
  },
  signatureSection: {
    marginTop: 20,
    alignItems: 'flex-end',
  },
  signatureBlock: {
    width: '50%',
    alignItems: 'center',
  },
  signatureTitle: {
    fontFamily: 'Times-Bold',
    fontSize: 9,
    marginBottom: 35,
  },
  signatureName: {
    fontFamily: 'Times-Bold',
    fontSize: 9,
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

function LogsTable({ logs }) {
  if (!logs || logs.length === 0) {
    return React.createElement(
      View,
      { style: styles.emptyState },
      React.createElement(Text, null, 'Tidak ada data logbook'),
    )
  }

  const rows = [
    React.createElement(
      View,
      { style: styles.tableHeader, key: 'header' },
      React.createElement(Text, { style: styles.colNo }, 'No'),
      React.createElement(Text, { style: styles.colDate }, 'Tanggal'),
      React.createElement(Text, { style: styles.colTime }, 'Waktu'),
      React.createElement(Text, { style: styles.colActivity }, 'Kegiatan'),
      React.createElement(Text, { style: styles.colFeeling }, 'Perasaan'),
      React.createElement(Text, { style: styles.colStatus }, 'Status'),
    ),
  ]

  logs.forEach((log, index) => {
    rows.push(
      React.createElement(
        View,
        { style: styles.tableRow, key: `row-${index}` },
        React.createElement(Text, { style: styles.colNo }, String(index + 1)),
        React.createElement(Text, { style: styles.colDate }, log.date || '-'),
        React.createElement(Text, { style: styles.colTime }, log.time || '-'),
        React.createElement(Text, { style: styles.colActivity }, log.title || '-'),
        React.createElement(Text, { style: styles.colFeeling }, log.feeling || '-'),
        React.createElement(Text, { style: styles.colStatus }, log.status || '-'),
      )
    )
  })

  return React.createElement(View, { style: styles.logsTable }, ...rows)
}

function buildDocument(data) {
  return React.createElement(
    Document,
    null,
    React.createElement(
      Page,
      { size: 'A4', style: styles.page },
      // Header
      React.createElement(
        View,
        { style: styles.header },
        React.createElement(Text, { style: styles.ministryName }, 'Kementerian Komunikasi dan Digital Republik Indonesia'),
        React.createElement(Text, { style: styles.agencyName }, 'Badan Aksesibilitas Telekomunikasi dan Informasi'),
        React.createElement(
          Text,
          { style: styles.agencyAddress },
          'Centennial Tower Lt. 42-45, Jl. Gatot Subroto Kav. 24-25, Jakarta 12930\nTelp. 021-31936590 (Hunting) · www.baktikominfo.id',
        ),
      ),
      // Title
      React.createElement(
        View,
        { style: styles.titleBlock },
        React.createElement(Text, { style: styles.titleMain }, 'Rekap Logbook Kegiatan'),
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
        DetailRow({ label: 'Program Studi', value: data.studentProgramStudi }),
      ),
      // Supervisor Data
      React.createElement(Text, { style: styles.sectionTitle }, 'Pembimbing'),
      React.createElement(
        View,
        null,
        DetailRow({ label: 'Nama', value: data.supervisorName }),
        DetailRow({ label: 'Jabatan', value: data.supervisorPosition }),
      ),
      // Logs Table
      React.createElement(Text, { style: styles.sectionTitle }, 'Daftar Logbook'),
      LogsTable({ logs: data.logs }),
      // Summary
      React.createElement(Text, { style: styles.sectionTitle }, 'Ringkasan'),
      React.createElement(
        View,
        null,
        DetailRow({ label: 'Total Logbook', value: data.totalLogs }),
        DetailRow({ label: 'Periode', value: data.period }),
      ),
      // Signature
      React.createElement(
        View,
        { style: styles.signatureSection },
        React.createElement(
          View,
          { style: styles.signatureBlock },
          React.createElement(Text, null, `Jakarta, ${data.signatureDateFormatted}`),
          React.createElement(Text, { style: styles.signatureTitle }, data.supervisorPosition),
          React.createElement(Text, { style: styles.signatureName }, data.supervisorName),
        ),
      ),
    ),
  )
}

async function main() {
  const [, , inputPath, outputPath] = process.argv

  if (!inputPath || !outputPath) {
    console.error('Usage: node render-logbook-recap.cjs <input-json-path> <output-pdf-path>')
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
