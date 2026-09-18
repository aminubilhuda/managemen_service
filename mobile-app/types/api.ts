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
  no_telp?: string;
  teknisi?: { id: number; name: string } | null;
  teknisi_id?: number | null;
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
  cetak_url?: string;
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

export interface KategoriProduk {
  id: number;
  nama_kategori: string;
  produk_count?: number;
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
  nama_produk?: string;
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

export interface Perusahaan {
  id: number;
  nama_perusahaan: string;
  alamat?: string;
  telp?: string;
  logo_url?: string;
  npwp?: string;
}

export interface TeknisiItem {
  id: number;
  name: string;
  email: string;
}

export interface StatusMasterItem {
  value: StatusTiket;
  label: string;
}
