const fs = require('fs')
const React = require('react')
const { Document, Page, View, Text, StyleSheet, renderToFile, Table, TableCell, TableRow } = require('@react-pdf/renderer')

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
  gradesTable: {
    marginTop: 6,
    borderWidth: 1.5,
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
    padding: 6,
    textAlign: 'center',
    fontSize: 10,
    fontFamily: 'Times-Bold',
  },
  colTask: {
    width: '32%',
    padding: 6,
    fontSize: 10,
  },
  colGrade: {
    width: '12%',
    padding: 6,
    textAlign: 'center',
    fontSize: 10,
    fontFamily: 'Times-Bold',
  },
  colComment: {
    width: '50%',
    padding: 6,
    fontSize: 10,
  },
  colNoData: {
    width: '6%',
    padding: 6,
    textAlign: 'center',
    fontSize: 10,
    fontFamily: 'Times-Bold',
  },
  colTaskData: {
    width: '32%',
    padding: 6,
    fontSize: 10,
  },
  colGradeData: {
    width: '12%',
    padding: 6,
    textAlign: 'center',
    fontSize: 10,
  },
  colCommentData: {
    width: '50%',
    padding: 6,
    fontSize: 10,
  },
  emptyState: {
    textAlign: 'center',
    padding: 20,
    fontStyle: 'italic',
    fontSize: 10,
  },
  summaryTable: {
    marginTop: 6,
  },
  summaryRow: {
    flexDirection: 'row',
    marginBottom: 2,
  },
  summaryLabel: {
    width: 240,
    fontSize: 11,
  },
  summarySeparator: {
    width: 15,
    textAlign: 'center',
    fontSize: 11,
  },
  summaryValue: {
    fontSize: 11,
    flex: 1,
  },
  signatureSection: {
    marginTop: 24,
    alignItems: 'flex-end',
  },
  signatureBlock: {
    width: '50%',
    alignItems: 'center',
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
  },
  signatureNip: {
    fontSize: 9,
    marginTop: 2,
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

function GradesTable({ submissions }) {
  if (!submissions || submissions.length === 0) {
    return React.createElement(
      View,
      { style: styles.emptyState },
      React.createElement(Text, null, 'Tidak ada data nilai'),
    )
  }

  const rows = [
    React.createElement(
      View,
      { style: styles.tableHeader, key: 'header' },
      React.createElement(Text, { style: styles.colNo }, 'No'),
      React.createElement(Text, { style: styles.colTask }, 'Kegiatan/Tugas'),
      React.createElement(Text, { style: styles.colGrade }, 'Nilai'),
      React.createElement(Text, { style: styles.colComment }, 'Komentar'),
    ),
  ]

  submissions.forEach((submission, index) => {
    rows.push(
      React.createElement(
        View,
        { style: styles.tableRow, key: `row-${index}` },
        React.createElement(Text, { style: styles.colNoData }, String(index + 1)),
        React.createElement(Text, { style: styles.colTaskData }, submission.taskTitle || '-'),
        React.createElement(Text, { style: styles.colGradeData }, String(submission.grade || '-')),
        React.createElement(Text, { style: styles.colCommentData }, submission.comment || '-'),
      )
    )
  })

  return React.createElement(View, { style: styles.gradesTable }, ...rows)
}

function buildDocument(data) {
  const today = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
  return React.createElement(
    Document,
    { title: `NilaiMagang_${data.studentName}_${today}` },
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
        React.createElement(Text, { style: styles.titleMain }, 'Rekap Nilai Magang'),
        React.createElement(Text, { style: styles.documentNumber }, `Nomor: ${data.reportNumber}/BAKTI/SDA/${data.reportMonth}`),
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
        DetailRow({ label: 'Periode Magang', value: data.periodePeriode }),
      ),
      // Supervisor Data
      React.createElement(Text, { style: styles.sectionTitle }, 'Pembimbing'),
      React.createElement(
        View,
        null,
        DetailRow({ label: 'Nama', value: data.supervisorName }),
        DetailRow({ label: 'Jabatan', value: data.supervisorPosition }),
      ),
      // Grades Table
      React.createElement(Text, { style: styles.sectionTitle }, 'Daftar Nilai'),
      GradesTable({ submissions: data.submissions }),
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
          React.createElement(Text, { style: styles.signatureNip }, `NIP ${data.supervisorEmployeeId}`),
        ),
      ),
    ),
  )
}

async function main() {
  const [, , inputPath, outputPath] = process.argv

  if (!inputPath || !outputPath) {
    console.error('Usage: node render-grades.cjs <input-json-path> <output-pdf-path>')
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
