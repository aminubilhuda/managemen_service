# PRD (Product Requirement Document) — Aplikasi Mobile "Wahyu Teknik Indotama"

**Versi Dokumen:** 1.0  
**Tanggal:** 17 September 2026  
**Target Platform:** Mobile (Android & iOS)  
**Framework Utama:** React Native (Expo Managed Workflow) + TypeScript  
**Backend API:** Laravel 13 REST API v1 (`/api/v1`)  
**Bahasa UI:** Bahasa Indonesia (Resmi / Standar POS & Servis HP)

---

## 1. Latar Belakang & Tujuan Produk

Aplikasi Mobile **Wahyu Teknik Indotama** dirancang untuk mendampingi operasional harian gerai servis smartphone secara cepat (*fast intake*), fleksibel, dan terintegrasi penuh dengan backend web Wahyu Teknik Indotama.

### 1.1 Masalah yang Diselesaikan
1. **Mobilitas Teknisi di Meja Kerja:** Teknisi membutuhkan cara praktis memperbarui status perbaikan (*dikerjakan*, *menunggu sparepart*, *selesai*) langsung dari meja servis tanpa harus membuka PC kasir.
2. **Dokumentasi Fisik Unit yang Cepat:** Mengambil foto kondisi fisik HP (layar retak, lecet casing, segel terbuka) saat penerimaan unit langsung menggunakan kamera smartphone.
3. **Pencarian Cepat Unit Servis:** Memanfaatkan kamera HP sebagai pemindai barcode/QR code pada nota tanda terima atau nomor IMEI perangkat.
4. **Kasir Cepat & Cetak Struk:** Admin dan kasir dapat menerbitkan invoice, menerima pembayaran (Tunai, QRIS, Transfer), serta membagikan tanda terima/nota PDF melalui WhatsApp atau printer thermal Bluetooth.

### 1.2 Target Pengguna (Roles)
- **Teknisi:** Fokus pada daftar tugas servis saya (*My Tasks*), pembaruan status pengerjaan, unggah foto dokumentasi, dan pengecekan stok sparepart.
- **Admin / Kasir:** Penerimaan servis baru (*intake*), pengelolaan data pelanggan, pembuatan invoice servis, pencatatan pembayaran, dan pencatatan kas keluar (*pengeluaran*).
- **Super Admin & Owner:** Pemantauan ringkasan omset harian/bulanan, stok menipis, dan seluruh riwayat servis serta pembatalan invoice.

---

## 2. Tech Stack & Arsitektur Mobile

Aplikasi dibangun menggunakan ekosistem **Expo** yang stabil, ringan, dan mudah di-generate otomatis tanpa dependensi native yang rumit.

| Komponen | Pilihan Teknologi | Keterangan & Alasan |
|---|---|---|
| **Core Framework** | React Native 0.74+ / Expo SDK 51+ (TypeScript) | Managed workflow, stabil, cross-platform Android & iOS |
| **Routing / Navigation** | Expo Router v3 (File-based) atau React Navigation v6 | Mendukung Deep Linking, Tabs, dan Modal Stack |
| **State Management** | Zustand | Sangat ringan, boilerplate minimal, cocok untuk auth & global state |
| **Server State & Caching** | TanStack Query (React Query) v5 | Otomatis menangani caching, pull-to-refresh, dan pagination |
| **HTTP Client** | Axios | Interceptor Bearer Token otomatis, error handling terpusat (401, 422, 500) |
| **Local / Secure Storage** | `expo-secure-store` | Menyimpan token Bearer dan konfigurasi base URL server |
| **Camera & Barcode** | `expo-camera` / `expo-barcode-scanner` | Scan QR No Tiket & barcode produk / IMEI |
| **Image & Media** | `expo-image-picker` & `expo-image-manipulator` | Ambil foto kamera/galeri, kompresi otomatis sebelum upload |
| **File & Sharing** | `expo-sharing` & `expo-file-system` | Unduh file PDF nota/invoice dan bagikan ke WhatsApp |
| **Icons** | `@expo/vector-icons` (Feather / MaterialIcons / Ionicons) | Icon standar bawaan Expo tanpa instalasi aset berat |

---

## 3. Sistem Desain UI & Standar Warna

UI menggunakan pendekatan **Clean Utility / Standard Material** dengan palet warna netral dan kontras tinggi. Tujuannya agar komponen mudah digenerate dengan React Native StyleSheet atau NativeWind/Tailwind standar tanpa aset grafis kustom yang berlebihan.

### 3.1 Palet Warna Standar (Color Tokens)

```typescript
export const Colors = {
  // Brand & Primary
  primary: '#2563EB',        // Blue 600 (Tombol utama, header aktif, tab aktif)
  primaryDark: '#1D4ED8',    // Blue 700 (State pressed/hover)
  primaryLight: '#EFF6FF',   // Blue 50 (Background container aksen)
  
  // Neutral / Greyscale
  background: '#F8FAFC',     // Slate 50 (Warna latar belakang seluruh layar)
  surface: '#FFFFFF',        // Putih murni (Card, Modal, BottomSheet)
  surfaceBorder: '#E2E8F0',  // Slate 200 (Garis batas card, divider)
  
  // Teks
  textPrimary: '#0F172A',    // Slate 900 (Judul, nilai penting, label utama)
  textSecondary: '#64748B',  // Slate 500 (Sub-label, tanggal, placeholder)
  textMuted: '#94A3B8',      // Slate 400 (Icon non-aktif, hint teks)
  
  // State Status Servis & Badge
  status: {
    diterima: { bg: '#F1F5F9', text: '#475569', border: '#CBD5E1' },           // Abu-abu
    dicek: { bg: '#EFF6FF', text: '#2563EB', border: '#BFDBFE' },              // Biru Muda
    menunggu_sparepart: { bg: '#FFFBEB', text: '#D97706', border: '#FDE68A' }, // Kuning / Amber
    dikerjakan: { bg: '#F5F3FF', text: '#7C3AED', border: '#DDD6FE' },         // Ungu
    selesai: { bg: '#ECFDF5', text: '#059669', border: '#A7F3D0' },            // Hijau Muda
    diambil: { bg: '#E0F2FE', text: '#0284C7', border: '#BAE6FD' },            // Cyan / Sky
    batal: { bg: '#FEF2F2', text: '#DC2626', border: '#FECACA' }               // Merah
  },
  
  // Status Pembayaran Invoice
  payment: {
    unpaid: { bg: '#FEF2F2', text: '#DC2626' },   // Merah (Belum Lunas)
    partial: { bg: '#FFFBEB', text: '#D97706' },  // Amber (Sebagian)
    paid: { bg: '#ECFDF5', text: '#059669' },     // Hijau (Lunas)
    void: { bg: '#F1F5F9', text: '#64748B' }      // Abu-abu (Batal)
  },
  
  // Feedback
  success: '#10B981',        // Emerald 500
  warning: '#F59E0B',        // Amber 500
  danger: '#EF4444',         // Red 500
  info: '#3B82F6'            // Blue 500
};
```

### 3.2 Tipografi & Spacing
- **Font Family:** System Default (`Roboto` pada Android, `San Francisco` pada iOS) untuk performa instan tanpa custom font loading.
- **Ukuran Teks:**
  - `Title Large`: 20px, Bold
  - `Title Medium`: 17px, Semi-Bold
  - `Body Regular`: 14px, Normal
  - `Body Small`: 12px, Regular
  - `Caption / Badge`: 11px, Semi-Bold
- **Form Element Standar:**
  - Input Height: 48px, Border Radius: 8px, Border Width: 1px (`#E2E8F0`), Background: `#FFFFFF`.
  - Button Height: 48px, Border Radius: 8px, Padding Horizontal: 16px.

---

## 4. Struktur Navigasi Aplikasi

Aplikasi menggunakan pola **Tab Navigation** untuk layar utama dan **Stack Navigation** untuk alur kerja detail/proses.

```
App Navigator
│
├── (Auth Stack)
│   ├── Layar Login (/auth/login)
│   └── Layar Pengaturan Server IP (/auth/server-config)
│
└── (Main Bottom Tabs) — Protected
    │
    ├── [Tab 1] Beranda (Dashboard)
    │   ├── Layar Detail Tiket Servis
    │   └── Layar Scanner Barcode Kamera
    │
    ├── [Tab 2] Servis (Tiket)
    │   ├── Layar List Tiket (Filter: Semua, Aktif, Tugas Saya, Selesai)
    │   ├── Layar Buat Tiket Baru (Intake)
    │   ├── Layar Detail Tiket Servis
    │   │   ├── Layar Tambah/Kelola Foto Dokumentasi
    │   │   ├── Modal Cepat Update Status
    │   │   └── Layar Preview & Cetak Tanda Terima PDF
    │   └── Layar Scanner Barcode / QR / IMEI
    │
    ├── [Tab 3] Kasir (Invoice)
    │   ├── Layar List Invoice (Filter: Unpaid, Partial, Paid, Void)
    │   ├── Layar Buat Invoice Baru (Billing dari Tiket)
    │   ├── Layar Detail Invoice
    │   │   ├── Modal Catat Pembayaran (Tunai / QRIS / Transfer)
    │   │   ├── Modal Void Invoice
    │   │   └── Layar Preview & Cetak Struk PDF
    │   └── Layar List Pengeluaran (Kas Keluar)
    │       └── Layar Catat Pengeluaran Baru
    │
    ├── [Tab 4] Master (Data)
    │   ├── Layar Kelola Pelanggan (List, Tambah, Edit, Detail Riwayat Servis)
    │   └── Layar Kelola Sparepart & Jasa (List, Filter Stok Tipis, Tambah, Edit)
    │
    └── [Tab 5] Akun & Profil
        ├── Layar Profil Saya
        ├── Layar Ubah Profil & Ganti Sandi
        ├── Layar Informasi Toko
        ├── Pengaturan Konfigurasi Server IP
        └── Tombol Keluar (Logout)
```

---

## 5. Matriks Peran & Izin Pengguna (Role Matrix)

| Modul / Layar | Super Admin | Admin / Kasir | Teknisi | Owner |
|---|:---:|:---:|:---:|:---:|
| **Login, Profil, Ganti Sandi** | Ya | Ya | Ya | Ya |
| **Lihat Dashboard & Statistik Omset** | Ya | Ya | Terbatas (Tanpa Omset) | Ya (Hanya Baca) |
| **Lihat Daftar Tiket Servis** | Semua | Semua | Tiket Saya / Semua | Semua |
| **Buat Tiket Servis Baru (Intake)** | Ya | Ya | Ya | Tidak |
| **Update Status & Catatan Servis** | Ya | Ya | Ya | Tidak |
| **Unggah Foto Dokumentasi Unit** | Ya | Ya | Ya | Tidak |
| **Cetak Tanda Terima Servis (PDF)** | Ya | Ya | Ya | Ya |
| **Pencarian / Scan Barcode & IMEI** | Ya | Ya | Ya | Ya |
| **Kelola Pelanggan (Tambah/Edit)** | Ya | Ya | Hanya Baca | Hanya Baca |
| **Kelola Sparepart & Jasa** | Ya | Ya | Hanya Cek Stok | Hanya Baca |
| **Buat Invoice & Catat Pembayaran** | Ya | Ya | Tidak | Hanya Baca |
| **Batalkan (Void) Invoice** | Ya | Ya (dengan alasan) | Tidak | Hanya Baca |
| **Catat Pengeluaran Kas Toko** | Ya | Ya | Tidak | Hanya Baca |

---

## 6. Spesifikasi Rinci Setiap Layar & Alur Kerja (Screen-by-Screen)

---

### MODUL 1: AUTENTIKASI & SETUP

#### S-01: Layar Pengaturan Server IP / Base URL
- **Tujuan:** Mengizinkan teknisi/kasir memasukkan alamat IP lokal server backend Laravel saat pengujian di jaringan WiFi toko (misal: `http://192.168.1.100:8000/api/v1`) atau URL domain cloud.
- **Komponen UI:**
  - Input Teks: *Base URL Backend* (Default: `http://localhost:8000/api/v1` atau disimpan di `expo-secure-store`).
  - Tombol: *Uji Koneksi Server* (Memanggil endpoint ping / cek status).
  - Tombol: *Simpan Konfigurasi*.

#### S-02: Layar Login
- **Endpoint:** `POST /api/v1/auth/login`
- **Komponen UI:**
  - Logo toko & Nama Aplikasi ("Wahyu Teknik Indotama").
  - Input Teks: *Email atau Username* (`login`).
  - Input Teks: *Kata Sandi* (`password`) dengan toggle show/hide.
  - Tombol: *Masuk*.
  - Link Bawah: *Atur Alamat Server / IP*.
- **Validasi & State:**
  - Tampilkan pesan error validasi HTTP 422 atau peringatan kredensial salah jika 401.
  - Simpan `token`, `user`, `roles`, dan `permissions` ke `Zustand` & `expo-secure-store`.
  - Redirect ke Dashboard.

#### S-03: Layar Profil & Ganti Kata Sandi
- **Endpoint:** `GET /api/v1/auth/me`, `PUT /api/v1/auth/profile`, `PUT /api/v1/auth/password`, `POST /api/v1/auth/logout`
- **Fitur:**
  - Informasi akun: Nama, Username, Email, Badge Role (Super Admin/Admin/Teknisi).
  - Form ubah nama, email, username.
  - Form ganti kata sandi: Kata sandi saat ini, kata sandi baru, konfirmasi sandi.
  - Tombol Logout dengan modal konfirmasi: "Apakah Anda yakin ingin keluar?".

---

### MODUL 2: BERANDA / DASHBOARD

#### S-04: Layar Beranda (Dashboard)
- **Endpoint:** `GET /api/v1/dashboard`
- **Perilaku Tampilan:**
  - **Header:** Nama pengguna yang login, sapaan ("Halo, Budi"), role badge, dan tombol notifikasi/pencarian.
  - **Statistik Cepat (Status Tiket):**
    - Grid 4 kotak: *Aktif*, *Menunggu Sparepart*, *Dikerjakan*, *Selesai*.
    - Kartu khusus teknisi: *Tugas Servis Saya* (menampilkan angka `my_tasks_count`).
  - **Statistik Omset (Khusus Admin / Owner / Super Admin):**
    - Banner Ringkasan: *Pendapatan Hari Ini* (Rp xxx) & *Bulan Ini* (Rp xxx).
  - **Tombol Aksi Cepat (Quick Actions):**
    - `[+] Servis Baru`
    - `[Scan] Barcode / IMEI`
    - `[Kasir] Buat Invoice`
    - `[+] Kas Keluar`
  - **Daftar Tiket Terbaru:**
    - List 5 tiket paling anyar (No Tiket, Nama Pelanggan, Perangkat, Badge Status).
    - Klik item untuk membuka Detail Tiket Servis.
  - **Fitur Tambahan:** *Pull to Refresh* untuk memuat ulang data.

---

### MODUL 3: TIKET SERVIS (ALUR UTAMA SERVIS HP)

#### S-05: Layar Daftar Tiket Servis
- **Endpoint:** `GET /api/v1/tiket`
- **Komponen UI:**
  - **Search Bar:** Input pencarian instan (Nomor tiket, nama pelanggan, no telepon, tipe HP, IMEI).
  - **Tombol Icon Kamera:** Membuka pemindai barcode untuk mencari tiket langsung.
  - **Filter Tab Horizontal:**
    - *Semua*, *Tugas Saya* (`my_tickets=true`), *Aktif*, *Dikerjakan*, *Menunggu Sparepart*, *Selesai*.
  - **Card Tiket:**
    - Header Card: `No. Tiket` (mis. `SRV-2026-000001`) & Badge Status berwarna standar.
    - Baris 1: Nama Pelanggan & Nomor WhatsApp (dengan ikon kontak).
    - Baris 2: Nama Perangkat & Keluhan singkat.
    - Footer Card: Nama Teknisi penanggung jawab, estimasi biaya, dan tanggal masuk.
  - **Floating Action Button (FAB):** Tombol `+` untuk membuat tiket baru.
  - **Fitur Paginasi:** Infinite scroll / *Load More* otomatis jika data > 15.

#### S-06: Layar Penerimaan Servis Baru (Intake Form)
- **Endpoint:** `POST /api/v1/tiket`
- **Alur Kerja Pengguna:**
  1. **Data Pelanggan:**
     - Switcher/Pilihan: *Pilih Pelanggan Terdaftar* ATAU *Pelanggan Baru*.
     - Jika terdaftar: Dropdown autocomplete pelanggan via `GET /api/v1/pelanggan/search?q=`.
     - Jika baru: Input Teks *Nama Pelanggan* (wajib), *No. Telepon / WA* (wajib), *Alamat* (opsional).
  2. **Data Perangkat HP:**
     - *Nama & Tipe Perangkat* (Wajib, misal: "Xiaomi Redmi Note 10 Pro").
     - *IMEI / Serial Number* (Opsional, dilengkapi tombol scan barcode untuk auto-fill).
     - *Kelengkapan Unit* (Opsional, misal: "Unit HP saja", "HP + Dus", "HP + Charger").
     - *Keluhan Kerusakan* (Wajib, misal: "Mati total setelah kena air").
     - *Kondisi Awal Fisik* (Wajib, misal: "Layar retak sudut kiri, backdoor mulus, tombol volume keras").
  3. **Penugasan & Estimasi:**
     - *Pilih Teknisi*: Dropdown user role teknisi dari `GET /api/v1/master/teknisi`.
     - *Estimasi Biaya*: Input nominal Rupiah.
     - *Estimasi Tanggal Selesai*: Date Picker.
  4. **Aksi Simpan:**
     - Tombol *Simpan & Terbitkan Tiket*.
     - Dialog sukses: Tawarkan langsung untuk *Ambil Foto Kondisi Masuk* atau *Cetak / Kirim Nota Penerimaan*.

#### S-07: Layar Detail Tiket Servis
- **Endpoint:** `GET /api/v1/tiket/{id}`, `PUT /api/v1/tiket/{id}`, `PATCH /api/v1/tiket/{id}/status`
- **Blok Tampilan:**
  1. **Header & Status:**
     - Nomor Tiket besar, status badge, tombol aksi cepat ubah status.
  2. **Informasi Pelanggan:**
     - Nama, No Telp (tombol *Call* & tombol *Chat WA* langsung membuka aplikasi WhatsApp), Alamat.
  3. **Detail Perangkat & Kerusakan:**
     - Perangkat, IMEI/SN, Keluhan, Kondisi Awal, Garansi berlaku sampai.
  4. **Panel Aksi Cepat Teknisi:**
     - Tombol `Update Status Pengerjaan` (Membuka BottomSheet / Modal).
     - Pilihan status resmi: `diterima` ➔ `dicek` ➔ `menunggu_sparepart` ➔ `dikerjakan` ➔ `selesai` ➔ `diambil` ➔ `batal`.
     - Input catatan perubahan status (misal: "IC Power sudah diganti, proses running test 2 jam").
  5. **Dokumentasi Foto Fisik:**
     - Horizontal Carousel / Grid Foto thumbnail (Sebelum, Pengerjaan, Selesai).
     - Tombol `[+] Tambah Foto`.
     - Klik foto untuk membuka layar *Full Screen Viewer*.
  6. **Riwayat Perubahan Status (Audit Trail):**
     - Komponen Timeline vertikal: Status lama ➔ Status baru, nama pengubah, tanggal & jam, dan catatan teknisi.
  7. **Informasi Invoice / Kasir:**
     - Jika belum ada invoice: Tombol `Buat Invoice Kasir`.
     - Jika sudah ada: Tampilkan Nomor Invoice, status pembayaran (*Unpaid/Paid*), dan total tagihan.
  8. **Footer Tombol Cetak:**
     - Tombol `Bagikan / Cetak Tanda Terima PDF`.

#### S-08: Layar Scan Barcode / QR / IMEI (Kamera)
- **Endpoint:** `GET /api/v1/tiket/scan/{code}`
- **Komponen UI:**
  - Viewfinder kamera full screen dengan overlay kotak bidik.
  - Tombol Flash on/off.
  - Input manual: Jika kamera gagal membaca, sediakan input teks di bagian bawah.
- **Logika:**
  - Saat barcode terbaca, panggil endpoint scan.
  - Jika tiket ditemukan, langsung navigasi ke Layar Detail Tiket.
  - Jika tidak ditemukan, tampilkan Toast "Data tidak ditemukan".

#### S-09: Layar & Aksi Cetak / Bagikan Tanda Terima Servis
- **Endpoint:** `GET /api/v1/tiket/{id}/tanda-terima`
- **Fitur:**
  - Menerima respon URL file PDF tanda terima dari backend.
  - Opsi 1: Buka WebView / In-App PDF Viewer.
  - Opsi 2: Bagikan link / dokumen langsung ke WhatsApp pelanggan menggunakan `expo-sharing`.
  - Opsi 3: Kirim template pesan WA otomatis: *"Halo [Nama Pelanggan], unit [Perangkat] Anda telah kami terima di Wahyu Teknik Indotama dengan No Tiket [No Tiket]. Pantau progres servis Anda..."*.

---

### MODUL 4: DOKUMENTASI FOTO UNIT

#### S-10: Layar Kelola Foto Dokumentasi
- **Endpoint:** `POST /api/v1/tiket/{id}/dokumentasi` (Multipart), `DELETE /api/v1/tiket/dokumentasi/{id}`
- **Fitur:**
  - Pilih sumber gambar: *Kamera Langsung* atau *Galeri*.
  - Dropdown Tipe Dokumentasi:
    - `kondisi_masuk` (Kondisi saat diterima)
    - `pengerjaan` (Proses perbaikan / saat dibongkar)
    - `kondisi_selesai` (Kondisi setelah selesai diservis)
  - Input Teks: *Keterangan Foto* (misal: "Baret parah di dekat port charger").
  - Optimasi: Kompresi gambar otomatis di sisi client (maksimal lebar 1280px, kualitas 80%) menggunakan `expo-image-manipulator` sebelum dikirim.
  - Konfirmasi hapus foto dengan dialog konfirmasi.

---

### MODUL 5: KELOLA PELANGGAN

#### S-11: Layar Daftar Pelanggan
- **Endpoint:** `GET /api/v1/pelanggan`, `GET /api/v1/pelanggan/search?q=`
- **Fitur:**
  - List pelanggan dengan pencarian nama & no HP.
  - Card Pelanggan: Nama, No Telepon, Alamat singkat, dan jumlah riwayat servis.
  - Tombol Tambah Pelanggan Baru.

#### S-12: Layar Detail Pelanggan & Riwayat Servis
- **Endpoint:** `GET /api/v1/pelanggan/{id}`, `PUT /api/v1/pelanggan/{id}`
- **Fitur:**
  - Informasi kontak pelanggan & aksi cepat (Call / WhatsApp).
  - Form edit data pelanggan.
  - **Daftar 10 Riwayat Servis:** Menampilkan histori seluruh unit HP yang pernah diservis oleh pelanggan ini beserta tanggal dan status akhirnya.

---

### MODUL 6: PRODUK, SPAREPART & JASA

#### S-13: Layar Katalog Sparepart & Jasa
- **Endpoint:** `GET /api/v1/produk`, `GET /api/v1/kategori-produk`, `GET /api/v1/produk/search?q=`
- **Fitur:**
  - Filter Kategori (LCD, Baterai, IC, Fleksibel, Jasa Servis, dll).
  - Filter Switch: *Stok Menipis* (`stok_menipis=true`) untuk memantau sparepart yang stoknya <= 3 unit.
  - Search bar (bisa scan barcode produk).
  - Item Card: Kode SKU, Nama Produk, Tipe (`sparepart` atau `jasa`), Sisa Stok (dengan badge merah jika <= 3), Harga Jual (Rp).
  - Floating Action Button (FAB): Tambah Produk Baru (Khusus Admin/Super Admin).

#### S-14: Layar Tambah / Edit Produk
- **Endpoint:** `POST /api/v1/produk`, `PUT /api/v1/produk/{id}`
- **Form Input:**
  - Kategori Produk (Dropdown).
  - Kode Produk / Barcode (Input + tombol scan kamera).
  - Nama Produk.
  - Tipe Produk (`sparepart` / `jasa`).
  - Harga Modal (HPP) & Harga Jual.
  - Garansi (Jumlah Hari).
  - Stok Awal (Hanya aktif jika tipe = `sparepart`).

---

### MODUL 7: KASIR, INVOICE & PEMBAYARAN

#### S-15: Layar Daftar Invoice
- **Endpoint:** `GET /api/v1/invoice`
- **Fitur:**
  - Tab Status: *Semua*, *Belum Lunas (Unpaid)*, *Sebagian (Partial)*, *Lunas (Paid)*, *Batal (Void)*.
  - Filter Tanggal: Tanggal Dari & Sampai.
  - Search: Nomor Invoice / Nama Pelanggan.
  - Card Invoice: Nomor INV, Nama Pelanggan, No Tiket Terkait, Total Tagihan (Rp), Sisa Tagihan (Rp), Badge Status.

#### S-16: Layar Buat Invoice Baru (Billing Kasir)
- **Endpoint:** `POST /api/v1/invoice`
- **Alur Kerja Kasir:**
  1. **Pilih Tiket Servis:** Dropdown / lookup tiket servis yang statusnya `selesai` atau sedang dikerjakan.
  2. **Daftar Item Tagihan (Dynamic Rows):**
     - Tombol `[+] Tambah Sparepart dari Master`: Memilih dari katalog produk, harga jual otomatis terisi.
     - Tombol `[+] Tambah Jasa / Custom Item`: Menuliskan deskripsi custom dan nominal biaya manual.
     - Pengaturan Qty, Harga Satuan, dan Harga Modal Satuan.
  3. **Diskon & Potongan:**
     - Input Diskon Nominal (Rp).
  4. **Pajak:**
     - Otomatis dihitung oleh backend berdasarkan pengaturan pajak aktif (mis. PPN / PPh), atau opsi pilih manual.
  5. **Ringkasan Kalkulasi Live:**
     - Subtotal: Rp xxx
     - Diskon: - Rp xxx
     - Pajak: + Rp xxx
     - **Total Tagihan Akhir:** **Rp xxx**
  6. **Aksi:**
     - Tombol *Terbitkan Invoice*.

#### S-17: Layar Detail Invoice & Kasir
- **Endpoint:** `GET /api/v1/invoice/{id}`, `POST /api/v1/invoice/{id}/bayar`, `PUT /api/v1/invoice/{id}/void`
- **Komponen Tampilan:**
  - Rincian item tagihan (Sparepart, Jasa, Qty, Subtotal).
  - Status pembayaran: *Unpaid*, *Partial*, atau *Paid*.
  - Riwayat pembayaran yang sudah masuk (Nominal, Metode, Tanggal, Dicatat Oleh).
  - **Tombol Kasir Utama:**
    - `[Bayar Sekarang]`: Membuka Modal Input Pembayaran.
    - `[Cetak / Bagikan Struk PDF]`: Membuka PDF struk nota pembayaran.
    - `[Batalkan (Void)]`: Membatalkan transaksi (mengembalikan stok sparepart) dengan input alasan pembatalan.

#### S-18: Modal Input Pembayaran Kasir
- **Form Input:**
  - Sisa Tagihan saat ini (Readonly).
  - Jumlah Dibayar (Input Rupiah, default diisi sisa tagihan untuk pelunasan cepat).
  - Metode Bayar (Tombol Pilihan Chip):
    - `Tunai` (Cash)
    - `Transfer Bank`
    - `QRIS`
    - `Kartu Debit`
    - `Kartu Kredit`
  - Upload Bukti Bayar: Ambil foto struk EDC / bukti transfer (Opsional).
- **Hasil:**
  - Jika lunas, status otomatis berubah menjadi `Paid` dan status tiket disinkronkan.

---

### MODUL 8: PENGELUARAN TOKO (KAS KELUAR)

#### S-19: Layar Daftar & Catat Kas Keluar
- **Endpoint:** `GET /api/v1/pengeluaran`, `POST /api/v1/pengeluaran`, `DELETE /api/v1/pengeluaran/{id}`
- **Fitur:**
  - List pengeluaran operasional toko (List card: Kategori, Deskripsi, Jumlah Rp, Tanggal).
  - Total pengeluaran bulan ini.
  - **Form Input Kas Keluar Baru:**
    - Kategori Pengeluaran (Operasional, Belanja Sparepart, Listrik/Internet, Konsumsi, Lainnya).
    - Deskripsi Pengeluaran.
    - Nominal (Rp).
    - Tanggal Pengeluaran.
    - Ambil Foto Bukti Nota/Struk Belanja (Kamera / Galeri).

---

## 7. Model Data & TypeScript Interfaces

Semua tipe data diselaraskan 100% dengan kontrak JSON di `Rest-API.md`.

```typescript
// types/api.ts

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface PaginatedResponse<T> {
  success: boolean;
  message: string;
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

export type RoleName = 'super-admin' | 'admin' | 'teknisi' | 'owner';

export interface User {
  id: number;
  name: string;
  email: string;
  username: string;
  roles: RoleName[];
  permissions: string[];
  created_at?: string;
}

export interface AuthData {
  token: string;
  token_type: string;
  user: User;
}

export type StatusTiket = 
  | 'diterima'
  | 'dicek'
  | 'menunggu_sparepart'
  | 'dikerjakan'
  | 'selesai'
  | 'diambil'
  | 'batal';

export interface DokumentasiFoto {
  id: number;
  tiket_id?: number;
  tipe_dokumentasi: 'sebelum' | 'pengerjaan' | 'kondisi_selesai' | string;
  file_path?: string;
  foto_url: string;
  keterangan?: string | null;
  diupload_oleh?: string;
  created_at?: string;
}

export interface RiwayatStatus {
  id: number;
  status_sebelum: string;
  status_sesudah: string;
  status_label: string;
  catatan?: string | null;
  diubah_oleh: string;
  created_at: string;
}

export interface Pelanggan {
  id: number;
  nama_pelanggan: string;
  no_telp: string;
  alamat?: string | null;
  total_servis?: number;
  riwayat_servis?: TiketServisItem[];
}

export interface TiketServisItem {
  id: number;
  no_tiket: string;
  pelanggan?: Pelanggan;
  nama_pelanggan?: string;
  teknisi?: { id: number; name: string } | null;
  perangkat: string;
  imei_sn?: string | null;
  kelengkapan?: string | null;
  keluhan: string;
  kondisi_awal?: string | null;
  status: StatusTiket;
  status_label: string;
  estimasi_biaya?: number | null;
  estimasi_selesai?: string | null;
  biaya_final?: number | null;
  garansi_sampai?: string | null;
  dokumentasi?: DokumentasiFoto[];
  riwayat_status?: RiwayatStatus[];
  invoice_id?: number | null;
  no_invoice?: string | null;
  status_invoice?: 'unpaid' | 'partial' | 'paid' | 'void' | null;
  created_at?: string;
}

export interface DashboardSummary {
  status_counts: {
    total_aktif: number;
    diterima: number;
    dicek: number;
    menunggu_sparepart: number;
    dikerjakan: number;
    selesai: number;
    diambil: number;
    batal: number;
  };
  my_tasks_count: number;
  pendapatan: {
    hari_ini: number;
    bulan_ini: number;
  };
  tiket_terbaru: Array<{
    id: number;
    no_tiket: string;
    nama_pelanggan: string;
    perangkat: string;
    status: StatusTiket;
    status_label: string;
  }>;
}

export interface Produk {
  id: number;
  kategori_id?: number;
  kategori_nama?: string;
  kode_produk: string;
  nama_produk: string;
  tipe: 'sparepart' | 'jasa';
  harga_jual: number;
  harga_modal: number;
  garansi_hari?: number;
  stok?: number | null;
}

export interface InvoiceItem {
  id?: number;
  produk_id: number | null;
  deskripsi: string;
  qty: number;
  harga_satuan: number;
  harga_modal_satuan: number;
  jumlah?: number;
}

export interface Pembayaran {
  id: number;
  invoice_id: number;
  jumlah_dibayar: number;
  metode_bayar: 'tunai' | 'transfer' | 'qris' | 'debit' | 'kredit';
  tanggal_bayar: string;
  dicatat_oleh?: string;
  bukti_bayar_url?: string | null;
}

export interface InvoiceDetail {
  id: number;
  no_invoice: string;
  tiket_id: number;
  nama_pelanggan: string;
  subtotal: number;
  diskon_nominal: number;
  pajak_persen: number;
  pajak_nominal: number;
  total_tagihan: number;
  total_dibayar: number;
  sisa_tagihan: number;
  status: 'unpaid' | 'partial' | 'paid' | 'void';
  pdf_url?: string;
  detail_items: InvoiceItem[];
  pembayaran: Pembayaran[];
  created_at?: string;
}

export interface Pengeluaran {
  id: number;
  kategori_pengeluaran: string;
  deskripsi: string;
  jumlah: number;
  tanggal: string;
  bukti_url?: string | null;
}
```

---

## 8. Arsitektur Komunikasi API & State Management

### 8.1 Axios Interceptor & Error Handling
- **Base URL Dinamis:** Disimpan pada `expo-secure-store` dengan key `API_BASE_URL`. Default mengarah ke `http://10.0.2.2:8000/api/v1` (Android Emulator) atau IP LAN.
- **Request Interceptor:**
  - Otomatis menyematkan `Authorization: Bearer <token>` dari secure storage jika user sudah login.
  - Menambahkan header `Accept: application/json`.
- **Response Interceptor:**
  - **401 Unauthenticated:** Hapus token dari secure storage, set user state menjadi `null`, dan arahkan user kembali ke Layar Login.
  - **422 Validation Error:** Kembalikan pesan validasi spesifik ke form UI agar input field dapat menandai teks error merah.
  - **Network Error / Timeout:** Tampilkan Toast "Gagal terhubung ke server backend. Periksa koneksi WiFi atau alamat IP server."

### 8.2 Struktur Folder Proyek Rekomendasi
```
mobile-app/
├── app/                      # Expo Router File-based Screens
│   ├── (auth)/
│   │   ├── login.tsx
│   │   └── server-config.tsx
│   ├── (tabs)/
│   │   ├── _layout.tsx
│   │   ├── index.tsx         # Dashboard Beranda
│   │   ├── servis/           # Tab Tiket Servis
│   │   │   ├── index.tsx
│   │   │   ├── intake.tsx
│   │   │   ├── [id].tsx
│   │   │   └── scan.tsx
│   │   ├── kasir/            # Tab Invoice & Kas Keluar
│   │   │   ├── index.tsx
│   │   │   ├── buat-invoice.tsx
│   │   │   ├── [id].tsx
│   │   │   └── pengeluaran.tsx
│   │   ├── master/           # Tab Master Data
│   │   │   ├── pelanggan.tsx
│   │   │   └── produk.tsx
│   │   └── akun/             # Tab Akun & Pengaturan
│   │       ├── index.tsx
│   │       └── ganti-password.tsx
│   ├── _layout.tsx
│   └── modal-foto-viewer.tsx
├── components/               # Komponen UI Standar Reusable
│   ├── Button.tsx
│   ├── Input.tsx
│   ├── StatusBadge.tsx
│   ├── EmptyState.tsx
│   ├── Card.tsx
│   └── SearchBar.tsx
├── constants/
│   └── Colors.ts             # Token warna standar
├── hooks/                    # Custom hooks TanStack Query
│   ├── useTiket.ts
│   ├── useDashboard.ts
│   ├── useInvoice.ts
│   └── useProduk.ts
├── services/                 # Axios client & API Endpoints
│   ├── api.ts
│   ├── authService.ts
│   ├── tiketService.ts
│   ├── invoiceService.ts
│   └── masterService.ts
├── store/                    # Global state Zustand
│   └── useAuthStore.ts
└── types/                    # TypeScript interfaces
    └── api.ts
```

---

## 9. Desain Komponen UI Standar (Reusable Components)

Komponen UI standar dirancang agar mudah dibaca dan digenerate secara langsung:

### 9.1 `StatusBadge`
- Menampilkan status tiket (`diterima`, `dicek`, `menunggu_sparepart`, `dikerjakan`, `selesai`, `diambil`, `batal`) atau invoice (`unpaid`, `partial`, `paid`, `void`).
- Memiliki background lembut (*soft tone*), border tipis, dan teks kontras sesuai token warna di Bagian 3.1.

### 9.2 `SearchBar`
- Field input pencarian dengan ikon kaca pembesar di sisi kiri, tombol *Clear (X)* di sisi kanan, dan tombol pintasan kamera barcode di sisi paling kanan.

### 9.3 `TiketCard`
- Kartu sentuh (*touchable card*) yang menyajikan ringkasan tiket servis: Nomor tiket, nama perangkat, keluhan, estimasi pengerjaan, nama teknisi, dan status badge.

### 9.4 `CurrencyInput`
- Input angka yang otomatis memformat teks dengan pemisah ribuan Rupiah (contoh: `Rp 450.000`) dan mengembalikan nilai integer bersih (`450000`) ke state.

### 9.5 `EmptyState`
- Tampilan jika data kosong (ikon ramah, judul informatif seperti "Belum Ada Tiket Servis", deskripsi singkat, dan tombol aksi untuk membuat baru).

---

## 10. Alur Pengujian & Checklist Kualitas (QA Acceptance Criteria)

### 10.1 Checklist Autentikasi
- [ ] Berhasil login menggunakan email atau username yang valid.
- [ ] Menampilkan pesan error yang ramah jika kata sandi salah.
- [ ] Token disimpan secara aman di `expo-secure-store`.
- [ ] Logout membersihkan seluruh session lokal dan kembali ke form login.

### 10.2 Checklist Alur Servis & Kamera
- [ ] Berhasil membuat tiket servis baru baik dengan memilih pelanggan lama maupun mengetik pelanggan baru.
- [ ] Berhasil scan nomor tiket `SRV-YYYY-XXXXXX` melalui kamera dan langsung membuka layar detail tiket terkait.
- [ ] Berhasil mengambil foto dokumentasi lewat kamera HP, melakukan auto-kompresi, dan muncul di galeri detail servis.
- [ ] Teknisi dapat memperbarui status pengerjaan via modal cepat (`dikerjakan` ➔ `selesai`) dan riwayat status langsung bertambah di timeline.

### 10.3 Checklist Kasir & Invoice
- [ ] Berhasil membuat invoice dari tiket servis dengan memilih sparepart master (stok otomatis terpotong di backend).
- [ ] Kalkulasi diskon dan perhitungan pajak (PPN/PPh) sesuai dengan nilai respon backend.
- [ ] Berhasil mencatat pembayaran parsial dan pelunasan penuh.
- [ ] Menghasilkan tautan PDF tanda terima / invoice yang bisa dibagikan langsung ke aplikasi WhatsApp pelanggan.

---

## 11. Panduan Memulai Implementasi (Quickstart Prompt untuk Developer / AI)

Untuk memulai generate kode aplikasi mobile ini menggunakan Expo:

1. **Inisialisasi Project:**
   ```bash
   npx create-expo-app@latest mobile-app --template tabs
   cd mobile-app
   npx expo install expo-secure-store expo-camera expo-image-picker expo-image-manipulator expo-sharing expo-file-system @tanstack/react-query axios zustand
   ```
2. **Setup File Konfigurasi:**
   - Masukkan token warna pada `constants/Colors.ts`.
   - Setup Axios instance di `services/api.ts` dengan interceptor token dan handling 401.
   - Buat store auth di `store/useAuthStore.ts` untuk mengelola token dan profile user.
3. **Generate Layar Bertahap:**
   - Tahap 1: Konfigurasi server & Login.
   - Tahap 2: Dashboard & List Tiket Servis.
   - Tahap 3: Intake Servis Baru & Detail Servis + Update Status.
   - Tahap 4: Dokumentasi Foto Kamera & Barcode Scanner.
   - Tahap 5: Kasir, Buat Invoice, Pembayaran, dan Cetak PDF.
