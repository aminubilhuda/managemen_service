# PRD — Aplikasi Manajemen Service Center "Cekat Cell"

**Versi:** 1.0
**Tanggal:** 15 September 2026
**Stack:** Laravel 13 (PHP 8.5+), MySQL 8, Tailwind CSS 4 (+ Alpine.js/Livewire opsional)

---

## 1. Latar Belakang & Tujuan

Cekat Cell membutuhkan sistem internal untuk mengelola alur servis HP dari penerimaan unit, tracking pengerjaan, invoicing dengan pajak, sampai laporan keuangan (laba rugi). Sistem menggantikan pencatatan manual/invoice gambar seperti contoh saat ini.

**Tujuan utama:**
1. Mendigitalkan proses tiket servis, invoice, dan pembayaran.
2. Menyimpan riwayat & dokumentasi unit (foto kondisi, histori status) untuk transparansi dan bukti garansi.
3. Menghasilkan invoice otomatis lengkap dengan perhitungan pajak (PPN).
4. Menyediakan cetak/ekspor laporan dalam format **Excel** dan **PDF**.
5. Menyediakan laporan keuangan (Laba Rugi) berbasis data transaksi riil.

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.5+ |
| Database | MySQL 8.0+ |
| Frontend | Blade + Tailwind CSS 4, Alpine.js (interaktivitas ringan) |
| Auth & Role | Laravel Breeze/Fortify + `spatie/laravel-permission` |
| Export Excel | `maatwebsite/excel` |
| Export PDF | `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf` (browser-shot, hasil lebih rapi) |
| Upload File/Foto | Laravel Filesystem (`storage/app/public`), disarankan disk S3-compatible untuk produksi |
| Queue (opsional) | Laravel Queue (database/redis) untuk generate laporan besar |
| Testing | Pest / PHPUnit |

---

## 3. Aktor & Role Pengguna

| Role | Hak Akses |
|---|---|
| **Super Admin** | Akses penuh semua modul + pengaturan sistem & pajak |
| **Admin/Kasir** | Kelola pelanggan, tiket servis, invoice, pembayaran, cetak laporan |
| **Teknisi** | Update status tiket servis, upload dokumentasi unit, catat sparepart terpakai |
| **Owner/Manajemen** | Akses read-only ke laporan keuangan & dashboard |

---

## 4. Skema Database (ERD Naratif)

Skema berikut mengembangkan hasil normalisasi 3NF sebelumnya, ditambah tabel baru untuk kebutuhan role, riwayat unit, dokumentasi, pajak, dan laporan keuangan.

### 4.1 Tabel Inti (dari normalisasi sebelumnya)

**`perusahaan`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| nama_perusahaan | varchar | |
| alamat | text | |
| telp | varchar | |
| logo | varchar | path file |
| npwp | varchar | untuk kebutuhan pajak |

**`pelanggan`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| nama_pelanggan | varchar | |
| alamat | text | |
| telp | varchar | |
| email | varchar | nullable |
| created_at/updated_at | timestamp | |

**`produk`** (master item — sparepart & jasa)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| kategori_id (FK) | bigint | → `kategori_produk` |
| kode_produk | varchar | SKU, unik |
| nama_produk | varchar | |
| tipe | enum | `sparepart` / `jasa` |
| harga_jual | decimal(15,2) | |
| harga_modal | decimal(15,2) | dipakai untuk hitung laba rugi (HPP) |
| garansi_hari | int | nullable, mis. 30 hari |
| stok | int | nullable (khusus tipe sparepart) |

**`tiket_servis`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| no_tiket | varchar | unik, format SRV-YYYY-XXXXXX |
| pelanggan_id (FK) | bigint | → `pelanggan` |
| teknisi_id (FK) | bigint | nullable → `users` |
| perangkat | varchar | mis. Samsung Galaxy A24 |
| imei_sn | varchar | nullable |
| keluhan | text | keluhan awal dari pelanggan |
| kondisi_awal | text | catatan kondisi fisik saat masuk |
| status | enum | `diterima`, `dicek`, `menunggu_sparepart`, `dikerjakan`, `selesai`, `diambil`, `batal` |
| garansi_sampai | date | nullable |
| created_at/updated_at | timestamp | |

**`invoice`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| no_invoice | varchar | unik, format INV-YYYY-XXXXXX |
| tiket_id (FK) | bigint | → `tiket_servis` |
| perusahaan_id (FK) | bigint | → `perusahaan` |
| tanggal_invoice | datetime | |
| subtotal | decimal(15,2) | jumlah sebelum pajak/diskon |
| diskon_persen | decimal(5,2) | default 0 |
| diskon_nominal | decimal(15,2) | dihitung dari subtotal |
| pajak_id (FK) | bigint | → `pengaturan_pajak` |
| pajak_persen | decimal(5,2) | disalin dari master pajak saat invoice dibuat (snapshot, agar histori tak berubah jika tarif pajak diubah kemudian) |
| pajak_nominal | decimal(15,2) | dihitung |
| total_tagihan | decimal(15,2) | subtotal - diskon + pajak |
| status | enum | `unpaid`, `partial`, `paid`, `void` |
| keterangan | text | syarat garansi, catatan |
| created_at/updated_at | timestamp | |

**`detail_invoice`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| invoice_id (FK) | bigint | → `invoice` |
| produk_id (FK) | bigint | → `produk` |
| deskripsi | varchar | catatan spesifik baris |
| qty | int | |
| harga_satuan | decimal(15,2) | snapshot harga saat transaksi |
| harga_modal_satuan | decimal(15,2) | snapshot modal saat transaksi, untuk HPP laba rugi |
| jumlah | decimal(15,2) | `qty * harga_satuan` (disimpan sebagai snapshot, bukan dihitung ulang — beda dengan pendekatan 3NF murni, karena histori invoice tidak boleh berubah walau harga produk master berubah) |

> **Catatan desain:** Pada level normalisasi murni, `jumlah`, `subtotal`, `pajak_nominal`, `total_tagihan` sebaiknya dihitung on-the-fly. Namun untuk **aplikasi invoicing nyata**, nilai-nilai ini **disimpan sebagai snapshot** di level aplikasi agar invoice yang sudah terbit tidak berubah jika harga produk/tarif pajak diubah di kemudian hari. Ini adalah trade-off yang umum dan disengaja (denormalisasi terkontrol untuk integritas historis).

**`pembayaran`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| invoice_id (FK) | bigint | → `invoice` |
| jumlah_dibayar | decimal(15,2) | |
| metode_bayar | enum | `tunai`, `transfer`, `qris`, `kartu` |
| tanggal_bayar | datetime | |
| dicatat_oleh (FK) | bigint | → `users` |
| bukti_bayar | varchar | nullable, path file |

### 4.2 Tabel Tambahan

**`users`** (Laravel default + tambahan)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| name, email, password | — | default Laravel |
| role | via `spatie/laravel-permission` | roles & permissions |

**`kategori_produk`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| nama_kategori | varchar | mis. LCD, Baterai, Jasa Service |

**`pengaturan_pajak`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| nama_pajak | varchar | mis. PPN |
| persentase | decimal(5,2) | mis. 11.00 |
| aktif | boolean | tarif yang sedang berlaku |
| berlaku_mulai | date | histori perubahan tarif |

**`riwayat_status_tiket`** (history status unit)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| tiket_id (FK) | bigint | → `tiket_servis` |
| status_sebelum | varchar | nullable |
| status_sesudah | varchar | |
| catatan | text | nullable |
| diubah_oleh (FK) | bigint | → `users` |
| created_at | timestamp | |

**`dokumentasi_unit`** (foto/video bukti kondisi unit)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| tiket_id (FK) | bigint | → `tiket_servis` |
| tipe_dokumentasi | enum | `sebelum`, `sesudah`, `proses` |
| file_path | varchar | |
| keterangan | varchar | nullable |
| diupload_oleh (FK) | bigint | → `users` |
| created_at | timestamp | |

**`pengeluaran`** (untuk laporan laba rugi — biaya operasional)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id (PK) | bigint | |
| kategori_pengeluaran | varchar | mis. sewa, listrik, gaji, pembelian stok |
| deskripsi | varchar | |
| jumlah | decimal(15,2) | |
| tanggal | date | |
| dicatat_oleh (FK) | bigint | → `users` |
| bukti | varchar | nullable, path file |

**`pengaturan_aplikasi`** (settings umum)
| Kolom | Tipe | Keterangan |
|---|---|---|
| key | varchar (PK) | mis. `format_no_invoice`, `format_no_tiket` |
| value | text | |

---

## 5. Modul & Fitur

### 5.1 Autentikasi & Manajemen User
- Login, role & permission (Super Admin, Admin/Kasir, Teknisi, Owner).
- Manajemen user (CRUD, assign role).

### 5.2 Manajemen Pelanggan
- CRUD data pelanggan.
- Riwayat semua tiket servis & invoice per pelanggan.

### 5.3 Manajemen Produk & Kategori
- CRUD sparepart & jasa, harga jual & harga modal.
- Manajemen stok sparepart (opsional: notifikasi stok menipis).

### 5.4 Tiket Servis (Unit Masuk)
- Form penerimaan unit: data pelanggan, perangkat, IMEI/SN, keluhan, kondisi awal.
- **Upload dokumentasi unit** (foto sebelum servis wajib, sesudah servis opsional).
- **Timeline riwayat status** — setiap perubahan status tercatat otomatis (siapa, kapan, catatan).
- Assign teknisi.
- Cetak tanda terima servis (PDF).

### 5.5 Invoice
- Generate invoice dari tiket servis (tarik data produk/jasa yang dipakai).
- **Perhitungan otomatis:** Subtotal → Diskon → Pajak (PPN, snapshot tarif) → Total Tagihan.
- Multi-pembayaran (cicil/parsial), status otomatis update (`unpaid`/`partial`/`paid`).
- Cetak invoice **PDF** (desain profesional, logo perusahaan, syarat garansi).
- Export daftar invoice ke **Excel**.
- Void/cancel invoice dengan alasan (audit trail).

### 5.6 Pembayaran
- Catat pembayaran dengan metode & bukti transfer.
- Riwayat pembayaran per invoice.

### 5.7 Laporan
| Laporan | Format | Keterangan |
|---|---|---|
| Laporan Invoice/Transaksi | Excel, PDF | filter tanggal, status, pelanggan |
| Laporan Tiket Servis | Excel, PDF | filter status, teknisi, periode |
| Kartu Riwayat Unit | PDF | histori status + dokumentasi foto per tiket |
| **Laporan Laba Rugi** | Excel, PDF | lihat detail §5.8 |
| Laporan Stok Sparepart | Excel | stok masuk/keluar |

### 5.8 Laporan Keuangan — Laba Rugi
Dihitung per periode (harian/bulanan/tahunan), berbasis data riil:

```
Pendapatan Jasa & Sparepart   = SUM(detail_invoice.jumlah) dari invoice berstatus paid/partial (bagian yang sudah dibayar)
Harga Pokok Penjualan (HPP)   = SUM(detail_invoice.qty * harga_modal_satuan)
Laba Kotor                    = Pendapatan - HPP
Pajak Keluaran (PPN)          = SUM(invoice.pajak_nominal)   [ditampilkan terpisah, bukan pendapatan]
Beban Operasional             = SUM(pengeluaran.jumlah) per periode
Laba Bersih                   = Laba Kotor - Beban Operasional
```
- Ditampilkan sebagai tabel + grafik tren (Chart.js) di dashboard.
- Export ke Excel (format standar laporan laba rugi) dan PDF.

### 5.9 Dashboard
- Ringkasan: jumlah tiket aktif, invoice belum lunas, pendapatan bulan berjalan, laba bersih bulan berjalan.
- Grafik pendapatan vs pengeluaran (bulanan).
- Daftar tiket yang mendekati/melewati estimasi selesai.

---

## 6. Kebutuhan Non-Fungsional

- **Responsive**, mobile-friendly (Tailwind CSS, dipakai teknisi di lapangan untuk upload foto).
- **Keamanan:** hash password, role-based access control, validasi upload file (tipe & ukuran).
- **Audit trail:** siapa membuat/mengubah invoice, status tiket, pengeluaran.
- **Snapshot data finansial** pada invoice tidak berubah meski master data berubah (lihat §4.1).
- **Backup database** terjadwal.
- **Performa:** query laporan besar dijalankan via queue job + cache jika diperlukan.

---

## 7. Struktur Rute Utama (Ringkas)

```
/login, /logout
/dashboard
/pelanggan (resource)
/produk (resource), /kategori-produk (resource)
/tiket-servis (resource) 
    /tiket-servis/{id}/dokumentasi (upload/list foto)
    /tiket-servis/{id}/riwayat-status
/invoice (resource)
    /invoice/{id}/cetak-pdf
    /invoice/{id}/pembayaran (store)
/pengeluaran (resource)
/laporan/invoice (excel, pdf)
/laporan/tiket-servis (excel, pdf)
/laporan/laba-rugi (excel, pdf)
/pengaturan/pajak (resource)
/pengaturan/aplikasi
/users (resource, role management)
```

---

## 8. Package Laravel yang Direkomendasikan

| Kebutuhan | Package |
|---|---|
| Role & Permission | `spatie/laravel-permission` |
| Export Excel | `maatwebsite/excel` |
| Export/Cetak PDF | `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf` |
| Upload & Media | `spatie/laravel-medialibrary` (opsional, memudahkan kelola dokumentasi unit) |
| Activity Log/Audit | `spatie/laravel-activitylog` |
| Nomor urut otomatis | Custom trait/service (format INV-YYYY-XXXXXX & SRV-YYYY-XXXXXX) |

---

## 9. Roadmap Pengembangan (Saran Fase)

1. **Fase 1 — Fondasi:** Auth, role, master data (perusahaan, pelanggan, produk, kategori, pajak).
2. **Fase 2 — Operasional Servis:** Tiket servis, riwayat status, dokumentasi unit.
3. **Fase 3 — Invoicing:** Invoice + perhitungan pajak/diskon, pembayaran multi-cicil.
4. **Fase 4 — Pelaporan:** Export Excel/PDF untuk semua laporan operasional.
5. **Fase 5 — Keuangan:** Modul pengeluaran + laporan laba rugi + dashboard grafik.
6. **Fase 6 — Penyempurnaan:** Notifikasi stok, audit trail lengkap, optimasi performa.

---

## 10. Kriteria Selesai (Definition of Done)

- Semua modul di §5 berfungsi sesuai role masing-masing.
- Invoice PDF menampilkan pajak, diskon, dan total secara akurat sesuai snapshot data.
- Laporan laba rugi menghasilkan angka yang konsisten dengan data invoice & pengeluaran pada periode yang sama.
- Semua laporan bisa diekspor Excel & PDF tanpa error pada dataset besar (uji dengan ≥1000 baris).
- Dokumentasi unit (foto) tersimpan dan dapat ditampilkan kembali di histori tiket.
