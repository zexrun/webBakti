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
    console.error('Usage: node render-certificate.js <input-json-path> <output-pdf-path>')
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
