# Dokumentasi REST API Cekat Cell (v1)

Dokumentasi lengkap REST API v1 untuk integrasi aplikasi mobile (Android, iOS, Flutter, React Native) pada sistem manajemen servis **Cekat Cell**.

---

## 1. Konfigurasi Dasar & Header

### Base URL
- **Lokal Dev**: `http://localhost:8000/api/v1` atau `http://<IP-Komputer-Server>:8000/api/v1`
- **Produksi**: `https://<domain-anda>/api/v1`

### Header Wajib
Semua endpoint (kecuali upload file multipart) wajib menyertakan header berikut:
```http
Accept: application/json
Content-Type: application/json
```

Untuk endpoint yang membutuhkan autentikasi (*Protected*), tambahkan header:
```http
Authorization: Bearer <token_dari_login>
```

---

## 2. Standar Struktur Respon JSON

### A. Respon Sukses (Single Object / Action) — HTTP 200 / 201
```json
{
  "success": true,
  "message": "Operasi berhasil",
  "data": { ... }
}
```

### B. Respon Sukses dengan Paginasi — HTTP 200
```json
{
  "success": true,
  "message": "Daftar data berhasil dimuat",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 52
  },
  "links": {
    "first": "http://domain/api/v1/... ?page=1",
    "last": "http://domain/api/v1/... ?page=4",
    "prev": null,
    "next": "http://domain/api/v1/... ?page=2"
  }
}
```

### C. Respon Validasi Gagal — HTTP 422
```json
{
  "message": "Validasi input gagal",
  "errors": {
    "perangkat": [
      "Nama atau tipe perangkat wajib diisi."
    ],
    "keluhan": [
      "Keluhan kerusakan wajib diisi."
    ]
  }
}
```

### D. Respon Tidak Diizinkan / Unauthenticated — HTTP 401
```json
{
  "message": "Unauthenticated."
}
```

### E. Respon Data Tidak Ditemukan — HTTP 404
```json
{
  "success": false,
  "message": "Tiket servis tidak ditemukan."
}
```

---

## 3. Daftar Endpoint Lengkap

---

### A. MODUL 1: AUTENTIKASI & AKUN (`/api/v1/auth`)

#### 1. Login Akun Mobile
Mengautentikasi pengguna menggunakan **email** atau **username** dan menerbitkan Personal Access Token (Bearer Token).

- **Method**: `POST`
- **Endpoint**: `/api/v1/auth/login`
- **Akses**: Publik
- **Request Body**:
```json
{
  "login": "admin@cekatcell.com",
  "password": "password",
  "device_name": "Samsung Galaxy S23"
}
```
*Catatan: `login` bisa diisi alamat email atau username akun.*

- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|qWeRtYuIoP1234567890abcdef...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@cekatcell.com",
      "username": "admin",
      "roles": ["super-admin"],
      "permissions": ["tiket.view", "tiket.create", "tiket.update_status", ...],
      "created_at": "2026-09-16T04:28:20.000000Z"
    }
  }
}
```

#### 2. Profil User Login Saat Ini
- **Method**: `GET`
- **Endpoint**: `/api/v1/auth/me`
- **Akses**: Protected (`auth:sanctum`)
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Profil pengguna berhasil dimuat.",
  "data": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@cekatcell.com",
    "username": "admin",
    "roles": ["super-admin"],
    "permissions": [...]
  }
}
```

#### 3. Update Profil Pengguna
- **Method**: `PUT`
- **Endpoint**: `/api/v1/auth/profile`
- **Akses**: Protected
- **Request Body**:
```json
{
  "name": "Wahyu Teknisi",
  "email": "wahyu@cekatcell.com",
  "username": "wahyu_cell"
}
```

#### 4. Ganti Kata Sandi
- **Method**: `PUT`
- **Endpoint**: `/api/v1/auth/password`
- **Akses**: Protected
- **Request Body**:
```json
{
  "current_password": "password",
  "password": "new_password_123",
  "password_confirmation": "new_password_123"
}
```

#### 5. Logout Akun Mobile
Mencabut (*revoke*) token perangkat yang sedang aktif.
- **Method**: `POST`
- **Endpoint**: `/api/v1/auth/logout`
- **Akses**: Protected
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Logout berhasil.",
  "data": null
}
```

---

### B. MODUL 2: DASHBOARD MOBILE (`/api/v1/dashboard`)

#### 1. Ringkasan Statistik & Dashboard
Mengambil metrik ringkas untuk halaman beranda aplikasi mobile (status tiket, tugas teknisi login, omset, dan tiket terbaru).

- **Method**: `GET`
- **Endpoint**: `/api/v1/dashboard`
- **Akses**: Protected
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Dashboard ringkasan mobile berhasil dimuat.",
  "data": {
    "status_counts": {
      "total_aktif": 12,
      "diterima": 4,
      "dicek": 3,
      "menunggu_sparepart": 2,
      "dikerjakan": 3,
      "selesai": 15,
      "diambil": 40,
      "batal": 1
    },
    "my_tasks_count": 5,
    "pendapatan": {
      "hari_ini": 450000.0,
      "bulan_ini": 8750000.0
    },
    "tiket_terbaru": [
      {
        "id": 14,
        "no_tiket": "SRV-2026-000014",
        "nama_pelanggan": "Budi Santoso",
        "perangkat": "iPhone 11",
        "status": "dikerjakan",
        "status_label": "Sedang Dikerjakan"
      }
    ]
  }
}
```

---

### C. MODUL 3: TIKET SERVIS (`/api/v1/tiket`)

#### 1. Daftar Tiket Servis (List)
- **Method**: `GET`
- **Endpoint**: `/api/v1/tiket`
- **Akses**: Protected
- **Query Parameters**:
  - `status` (string, opsional): `diterima`, `dicek`, `menunggu_sparepart`, `dikerjakan`, `selesai`, `diambil`, `batal`
  - `teknisi_id` (integer, opsional)
  - `my_tickets` (boolean, opsional): `true` untuk memfilter hanya tiket yang ditugaskan ke teknisi login
  - `search` (string, opsional): Cari nomor tiket, nama pelanggan, no telepon, tipe perangkat, IMEI
  - `dari` & `sampai` (date YYYY-MM-DD, opsional)
  - `per_page` (integer, default: 15, max: 50)
  - `page` (integer, default: 1)

#### 2. Buat Tiket Servis Baru (Intake)
- **Method**: `POST`
- **Endpoint**: `/api/v1/tiket`
- **Akses**: Protected
- **Request Body**:
```json
{
  "pelanggan_id": 1,
  "perangkat": "Samsung Galaxy A52",
  "imei_sn": "358921098471923",
  "kelengkapan": "Unit HP + Dus",
  "keluhan": "Layar sentuh tidak merespons setelah jatuh",
  "kondisi_awal": "Layar retak di pojok kanan atas, tombol fisik aman",
  "estimasi_biaya": 450000,
  "estimasi_selesai": "2026-09-18",
  "teknisi_id": 2
}
```
*Catatan: Jika pelanggan belum ada, kosongkan `pelanggan_id` dan kirim `nama_pelanggan`, `no_telp`, serta `alamat` (opsional).*

#### 3. Detail Tiket Servis
- **Method**: `GET`
- **Endpoint**: `/api/v1/tiket/{id}`
- **Akses**: Protected
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Detail tiket servis berhasil dimuat.",
  "data": {
    "id": 1,
    "no_tiket": "SRV-2026-000001",
    "pelanggan": {
      "id": 1,
      "nama_pelanggan": "Andi Setiawan",
      "no_telp": "081234567890",
      "alamat": "Jl. Basuki Rahmat No. 5"
    },
    "teknisi": {
      "id": 2,
      "name": "Budi Teknisi"
    },
    "perangkat": "Samsung Galaxy A52",
    "imei_sn": "358921098471923",
    "kelengkapan": "Unit HP + Dus",
    "keluhan": "Layar sentuh tidak merespons",
    "kondisi_awal": "Layar retak",
    "status": "dikerjakan",
    "status_label": "Sedang Dikerjakan",
    "estimasi_biaya": 450000.0,
    "estimasi_selesai": "2026-09-18",
    "biaya_final": 450000.0,
    "garansi_sampai": "2026-10-18",
    "dokumentasi": [
      {
        "id": 1,
        "tipe_dokumentasi": "sebelum",
        "foto_url": "http://domain/storage/dokumentasi/1/kondisi_masuk.jpg",
        "keterangan": "Kondisi fisik saat diterima"
      }
    ],
    "riwayat_status": [
      {
        "id": 2,
        "status_sebelum": "diterima",
        "status_sesudah": "dikerjakan",
        "status_label": "Sedang Dikerjakan",
        "catatan": "Mulai membongkar casing",
        "diubah_oleh": "Budi Teknisi",
        "created_at": "2026-09-16T05:10:00.000000Z"
      }
    ],
    "invoice_id": null,
    "no_invoice": null,
    "status_invoice": null
  }
}
```

#### 4. Update Informasi Tiket
- **Method**: `PUT`
- **Endpoint**: `/api/v1/tiket/{id}`
- **Akses**: Protected
- **Request Body**:
```json
{
  "perangkat": "Samsung Galaxy A52",
  "keluhan": "Ganti LCD & Baterai",
  "estimasi_biaya": 550000,
  "estimasi_selesai": "2026-09-19",
  "teknisi_id": 2
}
```

#### 5. Quick Update Status Tiket (Mobile Teknisi)
Endpoint cepat untuk teknisi saat memperbarui progres pengerjaan di lapangan.

- **Method**: `PATCH`
- **Endpoint**: `/api/v1/tiket/{id}/status`
- **Akses**: Protected
- **Request Body**:
```json
{
  "status": "menunggu_sparepart",
  "catatan": "Menunggu kiriman sparepart LCD Original dari distributor"
}
```
*Pilihan status yang valid:*
- `diterima` — Diterima
- `dicek` — Sedang Dicek
- `menunggu_sparepart` — Menunggu Sparepart
- `dikerjakan` — Sedang Dikerjakan
- `selesai` — Selesai (Siap Diambil) *(otomatis mengaktifkan garansi 30 hari)*
- `diambil` — Sudah Diambil Pelanggan
- `batal` — Dibatalkan

#### 6. Scan QR / Barcode / IMEI (Kamera HP)
Mencari tiket servis secara instan dari hasil scan kamera barcode/QR atau IMEI.

- **Method**: `GET`
- **Endpoint**: `/api/v1/tiket/scan/{code}`
- **Akses**: Protected
- **Contoh Request**: `/api/v1/tiket/scan/SRV-2026-000001` atau `/api/v1/tiket/scan/358921098471923`
- **Respon**: Mengembalikan objek `TiketServisResource` jika ditemukan, atau 404 jika tidak ditemukan.

#### 7. Tanda Terima Cetak Penerimaan Unit
- **Method**: `GET`
- **Endpoint**: `/api/v1/tiket/{id}/tanda-terima`
- **Akses**: Protected
- **Respon**: Mengembalikan detail tiket beserta `cetak_url` (URL file PDF tanda terima yang siap dishare via WhatsApp atau dicetak ke Bluetooth thermal printer).

---

### D. MODUL 4: DOKUMENTASI FOTO FISIK UNIT (`/api/v1/tiket/{id}/dokumentasi`)

#### 1. Upload Foto Dokumentasi Kamera
- **Method**: `POST`
- **Endpoint**: `/api/v1/tiket/{id}/dokumentasi`
- **Akses**: Protected
- **Content-Type**: `multipart/form-data`
- **Form Data**:
  - `foto` (File gambar: `image/jpeg`, `image/png`, `image/webp`, max: 5MB) — **Wajib**
  - `tipe_dokumentasi` (String, opsional): `kondisi_masuk` (atau `sebelum`), `pengerjaan` (atau `proses`), `kondisi_selesai` (atau `sesudah`)
  - `keterangan` (String, opsional): Catatan kondisi fisik

- **Contoh Respon (HTTP 201)**:
```json
{
  "success": true,
  "message": "Foto dokumentasi berhasil diunggah.",
  "data": {
    "id": 5,
    "tiket_id": 1,
    "tipe_dokumentasi": "sebelum",
    "file_path": "dokumentasi/1/xyz123.jpg",
    "foto_url": "http://domain/storage/dokumentasi/1/xyz123.jpg",
    "keterangan": "Kondisi fisik saat diterima",
    "diupload_oleh": "Budi Teknisi",
    "created_at": "2026-09-16T05:30:00.000000Z"
  }
}
```

#### 2. Hapus Foto Dokumentasi
- **Method**: `DELETE`
- **Endpoint**: `/api/v1/tiket/dokumentasi/{id}`
- **Akses**: Protected

---

### E. MODUL 5: PELANGGAN (`/api/v1/pelanggan`)

#### 1. List Pelanggan
- **Method**: `GET`
- **Endpoint**: `/api/v1/pelanggan`
- **Query Parameters**: `search` (nama/no telepon), `page`, `per_page`

#### 2. Autocomplete Pencarian Pelanggan (Mobile Quick Lookup)
- **Method**: `GET`
- **Endpoint**: `/api/v1/pelanggan/search?q={keyword}`
- **Akses**: Protected

#### 3. Tambah Pelanggan
- **Method**: `POST`
- **Endpoint**: `/api/v1/pelanggan`
- **Request Body**:
```json
{
  "nama_pelanggan": "Rina Wijaya",
  "no_telp": "087788990011",
  "alamat": "Jl. Diponegoro No. 8"
}
```

#### 4. Detail Pelanggan & Riwayat Servis
- **Method**: `GET`
- **Endpoint**: `/api/v1/pelanggan/{id}`
- **Respon**: Data pelanggan beserta array `riwayat_servis` (10 tiket servis terakhir milik pelanggan ini).

#### 5. Update Pelanggan
- **Method**: `PUT`
- **Endpoint**: `/api/v1/pelanggan/{id}`

---

### F. MODUL 6: PRODUK & SPAREPART (`/api/v1/produk`)

#### 1. List Kategori Produk
- **Method**: `GET`
- **Endpoint**: `/api/v1/kategori-produk`

#### 2. List Produk / Sparepart
- **Method**: `GET`
- **Endpoint**: `/api/v1/produk`
- **Query Parameters**:
  - `kategori_id` (integer)
  - `tipe` (`sparepart` atau `jasa`)
  - `stok_menipis` (`true` untuk memfilter sparepart dengan stok <= 3)
  - `search` (nama atau kode produk / barcode)
  - `page`, `per_page`

#### 3. Pencarian Cepat Produk / Barcode Scanner
- **Method**: `GET`
- **Endpoint**: `/api/v1/produk/search?q={barcode_atau_nama}`

#### 4. Tambah Produk / Sparepart
- **Method**: `POST`
- **Endpoint**: `/api/v1/produk`
- **Request Body**:
```json
{
  "kategori_id": 1,
  "kode_produk": "LCD-SAM-A52",
  "nama_produk": "LCD Touchscreen Samsung A52 Original",
  "tipe": "sparepart",
  "harga_jual": 450000,
  "harga_modal": 300000,
  "garansi_hari": 30,
  "stok": 5
}
```

#### 5. Update Produk
- **Method**: `PUT`
- **Endpoint**: `/api/v1/produk/{id}`

---

### G. MODUL 7: INVOICE KASIR & PEMBAYARAN (`/api/v1/invoice`)

#### 1. List Invoice
- **Method**: `GET`
- **Endpoint**: `/api/v1/invoice`
- **Query Parameters**:
  - `status` (`unpaid`, `partial`, `paid`, `void`)
  - `search` (no invoice, nama pelanggan, no telp)
  - `dari` & `sampai` (tanggal)

#### 2. Buat Invoice Baru (Billing Kasir)
Mendukung kalkulasi pajak dinamis bertingkat (otomatis menentukan tarif pajak, misalnya PPN jika >= Rp 2.000.000 atau PPh jika < Rp 2.000.000 berdasarkan aturan aktif di database) serta otomatis memotong stok sparepart yang digunakan.

- **Method**: `POST`
- **Endpoint**: `/api/v1/invoice`
- **Request Body**:
```json
{
  "tiket_id": 1,
  "items": [
    {
      "produk_id": 2,
      "deskripsi": "LCD Samsung A52 Original",
      "qty": 1,
      "harga_satuan": 450000,
      "harga_modal_satuan": 300000
    },
    {
      "produk_id": null,
      "deskripsi": "Jasa Pemasangan & Kalibrasi Layar",
      "qty": 1,
      "harga_satuan": 50000,
      "harga_modal_satuan": 0
    }
  ],
  "diskon": 25000,
  "keterangan": "Diskon promosi member"
}
```
*Catatan: `pajak_id` bersifat opsional. Jika tidak diisi, sistem otomatis memilih pajak yang sesuai berdasarkan total belanja.*

#### 3. Detail Invoice
- **Method**: `GET`
- **Endpoint**: `/api/v1/invoice/{id}`
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Detail invoice berhasil dimuat.",
  "data": {
    "id": 1,
    "no_invoice": "INV-2026-000001",
    "tiket_id": 1,
    "nama_pelanggan": "Andi Setiawan",
    "subtotal": 500000.0,
    "diskon_nominal": 25000.0,
    "pajak_persen": 2.0,
    "pajak_nominal": 9500.0,
    "total_tagihan": 484500.0,
    "total_dibayar": 0.0,
    "sisa_tagihan": 484500.0,
    "status": "unpaid",
    "pdf_url": "http://domain/invoice/1/cetak-pdf",
    "detail_items": [ ... ],
    "pembayaran": [ ... ]
  }
}
```

#### 4. Catat Pembayaran Kasir
- **Method**: `POST`
- **Endpoint**: `/api/v1/invoice/{id}/bayar`
- **Content-Type**: `multipart/form-data` (jika mengupload bukti bayar) atau `application/json`
- **Body / Form Data**:
  - `jumlah_dibayar` (Number, wajib)
  - `metode_bayar` (String, wajib): `tunai`, `transfer`, `qris`, `debit`, `kredit`
  - `tanggal_bayar` (Datetime, opsional)
  - `bukti_bayar` (File gambar, opsional)

*Catatan: Jika total bayar telah melunasi tagihan, sistem otomatis mengubah status invoice menjadi `paid` dan memperbarui `biaya_final` tiket servis.*

#### 5. Batalkan Invoice (Void)
- **Method**: `PUT`
- **Endpoint**: `/api/v1/invoice/{id}/void`
- **Request Body**:
```json
{
  "alasan": "Pelanggan membatalkan servis dan unit ditarik kembali"
}
```
*Catatan: Membatalkan invoice otomatis mengembalikan stok sparepart yang sebelumnya telah dipotong.*

---

### H. MODUL 8: PENGELUARAN TOKO / KAS KELUAR (`/api/v1/pengeluaran`)

#### 1. List Pengeluaran
- **Method**: `GET`
- **Endpoint**: `/api/v1/pengeluaran`
- **Query Parameters**: `kategori`, `search`, `dari`, `sampai`, `page`

#### 2. Catat Pengeluaran Baru dari HP
- **Method**: `POST`
- **Endpoint**: `/api/v1/pengeluaran`
- **Content-Type**: `multipart/form-data`
- **Form Data**:
  - `kategori_pengeluaran` (String, wajib): Misal `operasional`, `belanja_sparepart`, `listrik`, dll.
  - `deskripsi` (String, wajib): Keterangan kas keluar
  - `jumlah` (Number, wajib): Nominal dalam Rupiah
  - `tanggal` (Date YYYY-MM-DD, wajib)
  - `bukti` (File gambar / PDF struk belanja, opsional)

#### 3. Hapus Pengeluaran
- **Method**: `DELETE`
- **Endpoint**: `/api/v1/pengeluaran/{id}`

---

### I. MODUL 9: MASTER DATA & REFERENSI (`/api/v1/master`)

#### 1. Profil Toko (Receipt Header & Branding)
Mengambil nama toko, nomor telepon, alamat, dan logo URL untuk ditampilkan di aplikasi mobile atau dicetak pada header struk thermal Bluetooth.
- **Method**: `GET`
- **Endpoint**: `/api/v1/master/perusahaan`

#### 2. Daftar Teknisi Aktif
Mengambil daftar user yang memiliki role `teknisi` untuk pilihan penugasan servis.
- **Method**: `GET`
- **Endpoint**: `/api/v1/master/teknisi`

#### 3. Status Tiket Resmi
- **Method**: `GET`
- **Endpoint**: `/api/v1/master/status-tiket`
- **Contoh Respon (HTTP 200)**:
```json
{
  "success": true,
  "message": "Daftar status tiket servis.",
  "data": [
    { "value": "diterima", "label": "Diterima" },
    { "value": "dicek", "label": "Sedang Dicek" },
    { "value": "menunggu_sparepart", "label": "Menunggu Sparepart" },
    { "value": "dikerjakan", "label": "Sedang Dikerjakan" },
    { "value": "selesai", "label": "Selesai (Siap Diambil)" },
    { "value": "diambil", "label": "Sudah Diambil Pelanggan" },
    { "value": "batal", "label": "Dibatalkan" }
  ]
}
```

#### 4. Pengaturan Pajak Aktif
- **Method**: `GET`
- **Endpoint**: `/api/v1/master/pajak`

---

## 4. Tips Integrasi Aplikasi Mobile

1. **Penyimpanan Token**:
   - Simpan token Bearer di *Secure Storage* perangkat (misal `flutter_secure_storage` di Flutter atau `EncryptedSharedPreferences` di Android).
2. **Handling Token Expired**:
   - Jika menerima respon HTTP `401 Unauthenticated`, arahkan pengguna kembali ke layar Login.
3. **Upload Foto Dokumentasi Kamera**:
   - Selalu kompres foto di perangkat sebelum dikirim (maksimal resolusi 1920x1080 atau ukuran < 2MB) agar proses upload cepat dan hemat kuota data.
4. **Offline / Network Interruption**:
   - Untuk scan barcode di area susah sinyal, gunakan cache lokal data produk/sparepart dan sinkronkan secara berkala melalui endpoint `/api/v1/produk`.
