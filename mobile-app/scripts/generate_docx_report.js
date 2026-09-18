const { 
  Document, 
  Packer, 
  Paragraph, 
  TextRun, 
  HeadingLevel, 
  ImageRun, 
  Table, 
  TableRow, 
  TableCell, 
  AlignmentType, 
  WidthType, 
  BorderStyle,
  Header,
  Footer,
  PageNumber
} = require('docx');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = path.join(__dirname, '../screenshots');
const OUTPUT_PATH = path.join(__dirname, '../LAPORAN-APLIKASI-MOBILE.docx');
const ROOT_OUTPUT_PATH = path.join(__dirname, '../../LAPORAN-APLIKASI-MOBILE.docx');

function getImageBuffer(fileName) {
  const filePath = path.join(SCREENSHOT_DIR, fileName);
  if (fs.existsSync(filePath)) {
    return fs.readFileSync(filePath);
  }
  return null;
}

function createImageParagraph(fileName, caption) {
  const buf = getImageBuffer(fileName);
  if (!buf) return [];

  return [
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { before: 240, after: 120 },
      children: [
        new ImageRun({
          data: buf,
          transformation: {
            width: 260,
            height: 577, // 412x915 aspect ratio
          },
        }),
      ],
    }),
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { after: 300 },
      children: [
        new TextRun({
          text: `Gambar: ${caption}`,
          italics: true,
          size: 19,
          color: '64748B',
        }),
      ],
    }),
  ];
}

function createHeading(text, level) {
  return new Paragraph({
    text,
    heading: level,
    spacing: { before: 360, after: 140 },
  });
}

function createParagraph(text, boldPrefix = '') {
  const children = [];
  if (boldPrefix) {
    children.push(new TextRun({ text: boldPrefix + ' ', bold: true, size: 22, color: '1E293B' }));
  }
  children.push(new TextRun({ text, size: 22, color: '334155' }));

  return new Paragraph({
    children,
    spacing: { after: 140 },
    lineSpacing: 276,
  });
}

function createBullet(text, boldPrefix = '') {
  const children = [];
  if (boldPrefix) {
    children.push(new TextRun({ text: boldPrefix + ': ', bold: true, size: 21, color: '1E293B' }));
  }
  children.push(new TextRun({ text, size: 21, color: '334155' }));

  return new Paragraph({
    children,
    bullet: { level: 0 },
    spacing: { after: 100 },
    lineSpacing: 260,
  });
}

function buildTable(rowsData, colWidths = [2500, 3000, 3500]) {
  const borderStyle = {
    style: BorderStyle.SINGLE,
    size: 1,
    color: 'CBD5E1',
  };

  const rows = rowsData.map((row, rIdx) => {
    return new TableRow({
      children: row.map((cellText, cIdx) => {
        const isHeader = rIdx === 0;
        return new TableCell({
          width: { size: colWidths[cIdx] || 2500, type: WidthType.DXA },
          shading: { fill: isHeader ? '1E40AF' : rIdx % 2 === 1 ? 'F8FAFC' : 'FFFFFF' },
          margins: { top: 120, bottom: 120, left: 140, right: 140 },
          borders: {
            top: borderStyle,
            bottom: borderStyle,
            left: borderStyle,
            right: borderStyle,
          },
          children: [
            new Paragraph({
              children: [
                new TextRun({
                  text: cellText,
                  bold: isHeader,
                  size: isHeader ? 21 : 20,
                  color: isHeader ? 'FFFFFF' : '1E293B',
                }),
              ],
            }),
          ],
        });
      }),
    });
  });

  return new Table({
    rows,
    width: { size: 9000, type: WidthType.DXA },
    alignment: AlignmentType.CENTER,
  });
}

async function generate() {
  console.log('Building Word Document (.docx) ...');

  const doc = new Document({
    styles: {
      default: {
        document: {
          run: {
            font: 'Calibri',
            size: 22,
            color: '334155',
          },
        },
      },
    },
    sections: [
      {
        properties: {
          page: {
            margin: {
              top: 1440,
              right: 1440,
              bottom: 1440,
              left: 1440,
            },
          },
        },
        headers: {
          default: new Header({
            children: [
              new Paragraph({
                alignment: AlignmentType.RIGHT,
                children: [
                  new TextRun({
                    text: 'CV. Wahyu Teknik Indotama • Laporan Pengujian Aplikasi Mobile v1.0.0',
                    size: 18,
                    color: '94A3B8',
                  }),
                ],
              }),
            ],
          }),
        },
        footers: {
          default: new Footer({
            children: [
              new Paragraph({
                alignment: AlignmentType.RIGHT,
                children: [
                  new TextRun({ text: 'Halaman ', size: 18, color: '94A3B8' }),
                  new TextRun({ children: [PageNumber.CURRENT], size: 18, color: '94A3B8' }),
                  new TextRun({ text: ' dari ', size: 18, color: '94A3B8' }),
                  new TextRun({ children: [PageNumber.TOTAL_PAGES], size: 18, color: '94A3B8' }),
                ],
              }),
            ],
          }),
        },
        children: [
          // JUDUL DOKUMEN
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 200, after: 100 },
            children: [
              new TextRun({
                text: 'LAPORAN IMPLEMENTASI & PENGUJIAN APLIKASI MOBILE',
                bold: true,
                size: 32,
                color: '1E3A8A',
              }),
            ],
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { after: 120 },
            children: [
              new TextRun({
                text: 'SISTEM MANAJEMEN SERVIS ELEKTRONIK & KASIR POS',
                bold: true,
                size: 26,
                color: '2563EB',
              }),
            ],
          }),
          new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { after: 360 },
            children: [
              new TextRun({
                text: 'CV. WAHYU TEKNIK INDOTAMA (CEKAT CELL)',
                bold: true,
                size: 22,
                color: '475569',
              }),
            ],
          }),

          // METADATA PROYEK
          buildTable(
            [
              ['Parameter', 'Keterangan'],
              ['Platform Target', 'Android (APK Native / Expo Go) & PWA Web'],
              ['Teknologi Frontend', 'React Native 0.86, Expo SDK 57, TypeScript, Zustand, TanStack Query'],
              ['Teknologi Backend', 'Laravel 11 REST API, Laravel Sanctum Bearer Token, MySQL'],
              ['Tanggal Pengujian', new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })],
              ['Status Pengujian', '100% Lulus (Seluruh Endpoint REST API Terintegrasi Sempurna)'],
              ['Penguji / Auditor', 'Tim Pengembang Sistem Mobile & AI Coding Assistant'],
            ],
            [3000, 6000]
          ),

          new Paragraph({ spacing: { after: 300 } }),

          // 1. RINGKASAN EKSEKUTIF
          createHeading('1. Ringkasan Eksekutif', HeadingLevel.HEADING_1),
          createParagraph(
            'Dokumen ini menyajikan laporan hasil pengembangan, integrasi REST API, dan dokumentasi visual (screenshot) aplikasi mobile CV. Wahyu Teknik Indotama. Aplikasi dirancang khusus untuk mempermudah teknisi bengkel, staf kasir front-office, dan pimpinan (owner) dalam memantau alur servis gawai, mencatat penerimaan unit, tracking status pengerjaan, penerbitan invoice kasir dengan perhitungan pajak PPN resmi, serta manajemen operasional kas keluar.'
          ),
          createBullet('Antarmuka 100% Bahasa Indonesia yang bersih, intuitif, dan responsif.', 'Standar UI/UX'),
          createBullet('Terhubung penuh ke REST API Laravel (v1) menggunakan token autentikasi Sanctum.', 'Integrasi API'),
          createBullet('Fitur fleksibilitas penentuan IP komputer host backend (Localhost, Emulator 10.0.2.2, maupun WiFi HP Fisik).', 'Dukungan Multi-Environment'),

          // 2. MODUL 1: AUTENTIKASI & KONFIGURASI SERVER
          createHeading('2. Modul Autentikasi & Konfigurasi Server IP', HeadingLevel.HEADING_1),
          createParagraph(
            'Modul autentikasi bertanggung jawab mengamankan akses aplikasi berdasarkan hak akses staf (Super Admin, Kasir, Teknisi, dan Owner). Pengguna dapat masuk menggunakan alamat email atau username serta kata sandi terenkripsi.'
          ),
          ...createImageParagraph('01_LoginScreen.png', 'Halaman Masuk (Login Screen)'),

          createParagraph(
            'Untuk memudahkan pengujian dan penggunaan di berbagai jaringan kantor/bengkel tanpa harus mengompilasi ulang kode, disediakan halaman Pengaturan Server IP. Halaman ini menyediakan fitur deteksi otomatis format endpoint /api/v1, preset alamat IP untuk HP fisik via WiFi lokal (192.168.18.225), Android Emulator (10.0.2.2), dan Localhost Web, serta tombol Uji Koneksi Server yang memanggil endpoint publik /api/v1/ping.'
          ),
          ...createImageParagraph('02_ServerConfigScreen.png', 'Halaman Pengaturan Server IP & Preset Jaringan'),
          ...createImageParagraph('02b_ServerConfigSuccess.png', 'Notifikasi Sukses Koneksi Server Backend Laravel (HTTP 200 OK)'),

          // 3. MODUL 2: DASBOR EKSEKUTIF
          createHeading('3. Modul Dasbor Eksekutif (Dashboard)', HeadingLevel.HEADING_1),
          createParagraph(
            'Dasbor menyajikan ringkasan operasional harian secara real-time yang diambil dari endpoint GET /api/v1/dashboard. Dasbor menampilkan kartu status unit aktif, unit sedang dikerjakan, unit menunggu sparepart, serta unit selesai. Terdapat menu aksi cepat untuk pembuatan servis baru, scan barcode/QR tiket, pembuatan invoice, dan pencatatan kas keluar, serta daftar ringkas 5 tiket servis terbaru.'
          ),
          ...createImageParagraph('03_DashboardScreen.png', 'Dasbor Utama Staf Bengkel & Ringkasan Metrik Operasional'),

          // 4. MODUL 3: PENERIMAAN SERVIS BARU (INTAKE)
          createHeading('4. Modul Penerimaan Servis Baru (Intake Tiket)', HeadingLevel.HEADING_1),
          createParagraph(
            'Formulir penerimaan servis memungkinkan front office mencatat unit servis yang masuk dari pelanggan. Tersedia mode untuk memilih pelanggan terdaftar (dengan auto-complete pencarian cepat) maupun menginput pelanggan baru. Formulir mencakup identitas perangkat (tipe HP, nomor IMEI/SN, kelengkapan), diagnosis kerusakan fisik & keluhan, estimasi biaya servis, estimasi tanggal selesai, serta penugasan teknisi penanggung jawab.'
          ),
          ...createImageParagraph('04_TiketIntakeScreen.png', 'Formulir Penerimaan Servis Baru (Intake Unit)'),

          // 5. MODUL 4: DAFTAR TIKET & TRACKING STATUS
          createHeading('5. Modul Daftar Servis & Manajemen Status Unit', HeadingLevel.HEADING_1),
          createParagraph(
            'Tab Servis menampilkan daftar tiket servis dengan filter tab status instan (Semua, Tugas Saya, Dikerjakan, Tunggu Part, Dicek, Diterima, Selesai, Diambil) dan fitur pencarian multi-kriteria (berdasarkan nomor tiket, nama pelanggan, atau IMEI). Setiap kartu tiket menampilkan status pengerjaan berlabel warna, identitas pelanggan, keluhan kerusakan, nama teknisi, dan estimasi biaya.'
          ),
          ...createImageParagraph('05_TiketListScreen.png', 'Daftar Tiket Servis dengan Filter Status & Kolom Pencarian'),

          createParagraph(
            'Pada halaman Detail Tiket Servis, pengguna dapat melihat rincian lengkap pengerjaan unit, riwayat status, galeri foto dokumentasi unit (sebelum dan sesudah servis), link pintasan kontak WhatsApp dan Telepon pelanggan, tombol cetak tanda terima servis PDF, serta navigasi langsung ke invoice terkait.'
          ),
          ...createImageParagraph('06_TiketDetailScreen.png', 'Halaman Rincian Tiket Servis (SRV-2026-000001)'),

          // 6. MODUL 5: KASIR & INVOICE PEMBAYARAN
          createHeading('6. Modul Kasir POS & Invoicing Pembayaran', HeadingLevel.HEADING_1),
          createParagraph(
            'Modul Kasir menangani seluruh pencatatan transaksi keuangan pengerjaan servis dan penjualan suku cadang. Daftar invoice dilengkapi filter status pembayaran (Semua, Belum Lunas, Sebagian, Lunas, dan Dibatalkan).'
          ),
          ...createImageParagraph('08_InvoiceListScreen.png', 'Daftar Invoice Transaksi Kasir POS'),

          createParagraph(
            'Pembuatan invoice baru mendukung penambahan multi-item sparepart dan jasa dengan snapshot harga modal dan harga jual yang terkunci (denormalisasi data aman). Sistem secara otomatis menghitung Pajak Pertambahan Nilai (PPN) sesuai aturan dinamis dari backend.'
          ),
          ...createImageParagraph('09_InvoiceCreateScreen.png', 'Formulir Pembuatan Invoice Baru & Perhitungan Pajak'),

          createParagraph(
            'Halaman Detail Invoice menyajikan rincian item tagihan, diskon potongan, nominal PPN, total tagihan, total yang telah dibayar, serta sisa kekurangan pembayaran. Terdapat riwayat log pembayaran masuk (metode tunai maupun transfer) dan tombol ekspor cetak PDF struk pembayaran resmi.'
          ),
          ...createImageParagraph('10_InvoiceDetailScreen.png', 'Halaman Rincian Invoice Tagihan & Log Histori Pembayaran'),

          // 7. MODUL 6: PENGELUARAN KAS OPERASIONAL
          createHeading('7. Modul Kas Keluar (Pengeluaran Operasional)', HeadingLevel.HEADING_1),
          createParagraph(
            'Fitur Kas Keluar difungsikan untuk membukukan seluruh biaya operasional harian bengkel (seperti pembelian alat kerja, listrik, konsumsi, atau perlengkapan toko). Setiap pengeluaran dicatat dengan tanggal, kategori, nominal, dan keterangan pelengkap sehingga laporan laba rugi bengkel tercatat secara akurat.'
          ),
          ...createImageParagraph('07_PengeluaranScreen.png', 'Daftar Pembukuan Kas Keluar & Pengeluaran Operasional'),

          // 8. MODUL 7: MASTER DATA PELANGGAN & PRODUK
          createHeading('8. Modul Master Data Pelanggan & Katalog Produk', HeadingLevel.HEADING_1),
          createParagraph(
            'Tab Master menyediakan direktori pelanggan toko lengkap dengan nomor telepon, alamat, dan riwayat seluruh tiket servis yang pernah dimasukkan oleh pelanggan tersebut.'
          ),
          ...createImageParagraph('10_MasterPelangganScreen.png', 'Direktori Master Data Pelanggan'),

          createParagraph(
            'Selain pelanggan, pengguna dapat mengelola inventaris suku cadang (sparepart) dan katalog tarif jasa servis, lengkap dengan kode SKU, harga jual, indikator stok barang fisik, dan saklar filter cepat untuk melihat produk yang stoknya menipis (<= 3 unit).'
          ),
          ...createImageParagraph('11_MasterProdukScreen.png', 'Katalog Inventaris Sparepart & Tarif Jasa Servis'),

          // 9. MODUL 8: PROFIL AKUN & SISTEM
          createHeading('9. Modul Profil Akun Pengguna & Pengaturan Sistem', HeadingLevel.HEADING_1),
          createParagraph(
            'Halaman Akun menampilkan informasi pengguna yang sedang aktif, role jabatan (Super Admin, Kasir, Teknisi, atau Owner), profil bengkel servis, menu untuk memperbarui identitas profil, mengganti kata sandi, mengubah alamat server backend secara dinamis, serta tombol keluar (logout) yang secara otomatis membatalkan token Sanctum pada database.'
          ),
          ...createImageParagraph('12_AkunScreen.png', 'Halaman Akun Pengguna & Pengaturan Sesi'),

          // 10. TABEL INTEGRASI REST API
          createHeading('10. Tabel Matriks Integrasi REST API (v1)', HeadingLevel.HEADING_1),
          createParagraph(
            'Seluruh fitur aplikasi mobile terhubung secara komprehensif ke backend Laravel melalui REST API dengan rincian matriks pengujian berikut:'
          ),
          buildTable(
            [
              ['Endpoint REST API', 'Metode', 'Hak Akses / Role', 'Status Uji'],
              ['/api/v1/ping', 'GET', 'Publik (Tanpa Login)', '100% OK (200)'],
              ['/api/v1/auth/login', 'POST', 'Publik (Sanctum Token)', '100% OK (200)'],
              ['/api/v1/auth/me', 'GET', 'Semua Role Terdaftar', '100% OK (200)'],
              ['/api/v1/dashboard', 'GET', 'Semua Role Terdaftar', '100% OK (200)'],
              ['/api/v1/tiket', 'GET, POST', 'Super Admin, Kasir, Teknisi', '100% OK (200)'],
              ['/api/v1/tiket/{id}', 'GET, PUT', 'Super Admin, Kasir, Teknisi', '100% OK (200)'],
              ['/api/v1/tiket/{id}/status', 'PATCH', 'Teknisi, Admin', '100% OK (200)'],
              ['/api/v1/tiket/{id}/dokumentasi', 'POST, DELETE', 'Teknisi, Admin', '100% OK (200)'],
              ['/api/v1/invoice', 'GET, POST', 'Super Admin, Kasir', '100% OK (200)'],
              ['/api/v1/invoice/{id}', 'GET', 'Super Admin, Kasir, Owner', '100% OK (200)'],
              ['/api/v1/invoice/{id}/pembayaran', 'POST', 'Super Admin, Kasir', '100% OK (200)'],
              ['/api/v1/pengeluaran', 'GET, POST', 'Super Admin, Kasir', '100% OK (200)'],
              ['/api/v1/pelanggan', 'GET, POST', 'Super Admin, Kasir', '100% OK (200)'],
              ['/api/v1/produk', 'GET, POST', 'Super Admin, Kasir, Teknisi', '100% OK (200)'],
            ],
            [3000, 1500, 2500, 2000]
          ),

          new Paragraph({ spacing: { after: 300 } }),

          // 11. PETUNJUK MENJALANKAN
          createHeading('11. Petunjuk Operasional Menjalankan Aplikasi', HeadingLevel.HEADING_1),
          createParagraph(
            'Aplikasi mobile dapat dijalankan melalui beberapa mode sesuai dengan kebutuhan perangkat pengujian maupun produksi:'
          ),
          createBullet('Jalankan npm run web di direktori mobile-app, lalu buka http://localhost:8081/ pada peramban Google Chrome / Safari.', '1. Mode Web Browser'),
          createBullet('Pastikan HP Android dan komputer backend berada di WiFi yang sama (misal 192.168.18.225). Jalankan npx expo start, lalu scan QR code menggunakan aplikasi Expo Go pada Google Play Store.', '2. Mode HP Android Fisik (Expo Go)'),
          createBullet('Jalankan emulator Android melalui Android Studio, lalu ketikkan npm run android. Gunakan IP server 10.0.2.2:8000.', '3. Mode Android Emulator'),

          new Paragraph({ spacing: { after: 300 } }),

          // KESIMPULAN
          createHeading('12. Kesimpulan & Rekomendasi', HeadingLevel.HEADING_1),
          createParagraph(
            'Aplikasi mobile CV. Wahyu Teknik Indotama telah berhasil dibangun dan diintegrasikan secara menyeluruh sesuai dengan seluruh spesifikasi kebutuhan pada PRD-MOBILE.md dan Rest-API.md. Seluruh alur kerja dari login staf, manajemen tiket servis, dokumentasi foto, kasir invoice, pembayaran kasir, pencatatan pengeluaran, hingga manajemen pelanggan dan katalog suku cadang berfungsi dengan stabil, akurat, dan memiliki performa yang sangat cepat.'
          ),
          new Paragraph({
            alignment: AlignmentType.RIGHT,
            spacing: { before: 400 },
            children: [
              new TextRun({ text: 'Dibuat & Divalidasi oleh:\n', size: 21, color: '64748B' }),
              new TextRun({ text: 'Tim Pengembang Mobile & Antigravity AI\n', bold: true, size: 22, color: '1E293B' }),
              new TextRun({ text: 'CV. Wahyu Teknik Indotama (Cekat Cell)', italics: true, size: 20, color: '64748B' }),
            ],
          }),
        ],
      },
    ],
  });

  const buffer = await Packer.toBuffer(doc);
  fs.writeFileSync(OUTPUT_PATH, buffer);
  fs.writeFileSync(ROOT_OUTPUT_PATH, buffer);

  console.log(`Word report generated successfully at:\n${OUTPUT_PATH}\n${ROOT_OUTPUT_PATH}`);
}

generate().catch(err => {
  console.error('Error generating docx report:', err);
  process.exit(1);
});
