<?php

namespace Tests\Feature;

use App\Models\DokumentasiUnit;
use App\Models\Invoice;
use App\Models\KategoriProduk;
use App\Models\Pelanggan;
use App\Models\PengaturanPajak;
use App\Models\Perusahaan;
use App\Models\Produk;
use App\Models\TiketServis;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UIFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('username', 'admin')->first() ?? User::where('email', 'admin@cekatcell.com')->first() ?? User::first();
    }

    public function test_login_page_renders_with_store_branding(): void
    {
        Perusahaan::updateOrCreate(['id' => 1], [
            'nama_perusahaan' => 'Wahyu Teknik Indotama',
            'deskripsi' => 'Pusat Layanan Servis Terpadu',
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Wahyu Teknik Indotama');
        $response->assertSee('Selamat Datang Kembali');
        $response->assertSee('Akses Cepat Akun Demo');
    }

    public function test_login_page_hides_demo_credentials_in_production(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('Akses Cepat Akun Demo');
    }

    public function test_authenticated_dashboard_renders_with_kpi(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Operasional Servis');
        $response->assertSee('Tiket Aktif');
        $response->assertSee('Invoice Belum Lunas');
    }

    public function test_pelanggan_pages_render(): void
    {
        $response = $this->actingAs($this->admin)->get('/pelanggan');
        $response->assertStatus(200);
        $response->assertSee('Data Pelanggan');

        $responseCreate = $this->actingAs($this->admin)->get('/pelanggan/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Tambah Pelanggan Baru');
    }

    public function test_tiket_servis_pages_render(): void
    {
        $response = $this->actingAs($this->admin)->get('/tiket-servis');
        $response->assertStatus(200);
        $response->assertSee('Daftar Tiket Servis');

        $responseCreate = $this->actingAs($this->admin)->get('/tiket-servis/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Buat Tiket Servis Baru');

        $pelanggan = Pelanggan::firstOrCreate(
            ['telp' => '08123456789'],
            ['nama_pelanggan' => 'Pelanggan Test', 'alamat' => 'Jl. Test']
        );

        $tiket = TiketServis::create([
            'no_tiket' => TiketServis::generateNoTiket(),
            'pelanggan_id' => $pelanggan->id,
            'teknisi_id' => $this->admin->id,
            'perangkat' => 'iPhone 13 Pro',
            'keluhan' => 'Layar bergaris',
            'status' => 'diterima',
        ]);

        $responseShow = $this->actingAs($this->admin)->get("/tiket-servis/{$tiket->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee($tiket->perangkat);
        $responseShow->assertSee('value="menunggu_sparepart"', false);

        // Test update status to menunggu_sparepart
        $statusResp = $this->actingAs($this->admin)->put("/tiket-servis/{$tiket->id}/status", [
            'status' => 'menunggu_sparepart',
            'catatan' => 'Menunggu LCD datang dari supplier',
        ]);
        $statusResp->assertRedirect();
        $this->assertEquals('menunggu_sparepart', $tiket->fresh()->status);
        $this->assertDatabaseHas('riwayat_status_tiket', [
            'tiket_id' => $tiket->id,
            'status_sesudah' => 'menunggu_sparepart',
            'catatan' => 'Menunggu LCD datang dari supplier',
        ]);

        // Test upload dokumentasi
        Storage::fake('public');
        $uploadResp = $this->actingAs($this->admin)->post("/tiket-servis/{$tiket->id}/dokumentasi", [
            'tipe_dokumentasi' => 'sebelum',
            'keterangan' => 'Layar retak sudut kanan',
            'foto' => [
                UploadedFile::fake()->image('unit1.jpg', 600, 600),
            ],
        ]);
        $uploadResp->assertRedirect();
        $dok = DokumentasiUnit::where('tiket_id', $tiket->id)->first();
        $this->assertNotNull($dok);
        $this->assertEquals('sebelum', $dok->tipe_dokumentasi);
        $this->assertEquals('Layar retak sudut kanan', $dok->keterangan);
        Storage::disk('public')->assertExists($dok->file_path);

        // Test delete dokumentasi
        $delResp = $this->actingAs($this->admin)->delete("/tiket-servis/dokumentasi/{$dok->id}");
        $delResp->assertRedirect();
        $this->assertDatabaseMissing('dokumentasi_unit', ['id' => $dok->id]);

        $responsePrint = $this->actingAs($this->admin)->get("/tiket-servis/{$tiket->id}/cetak-tanda-terima");
        $responsePrint->assertStatus(200);
    }

    public function test_invoice_pages_render(): void
    {
        $response = $this->actingAs($this->admin)->get('/invoice');
        $response->assertStatus(200);
        $response->assertSee('Invoice');

        $responseCreate = $this->actingAs($this->admin)->get('/invoice/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Buat Invoice Penjualan / Servis');

        // 1. Test creating invoice with manual item (no product_id)
        $respManual = $this->actingAs($this->admin)->post('/invoice', [
            'diskon' => 0,
            'pajak_persen' => 11,
            'items' => [
                [
                    'produk_id' => null,
                    'deskripsi' => 'Jasa Pembersihan dan Pengecekan',
                    'qty' => 1,
                    'harga_satuan' => 50000,
                    'harga_modal_satuan' => 0,
                ],
            ],
        ]);
        $respManual->assertRedirect();
        $this->assertDatabaseHas('invoice', ['status' => 'unpaid']);
        $this->assertDatabaseHas('detail_invoice', ['deskripsi' => 'Jasa Pembersihan dan Pengecekan', 'jumlah' => 50000]);

        // 2. Test creating product and issuing invoice with stock reduction
        $kategori = KategoriProduk::firstOrCreate(['nama_kategori' => 'Sparepart Test']);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'BAT-IP11-TEST',
            'nama_produk' => 'Baterai iPhone 11 Original',
            'tipe' => 'sparepart',
            'harga_modal' => 100000,
            'harga_jual' => 200000,
            'stok' => 10,
        ]);

        $respStock = $this->actingAs($this->admin)->post('/invoice', [
            'diskon' => 0,
            'pajak_persen' => 0,
            'items' => [
                [
                    'produk_id' => $produk->id,
                    'deskripsi' => $produk->nama_produk,
                    'qty' => 3,
                    'harga_satuan' => 200000,
                    'harga_modal_satuan' => 100000,
                ],
            ],
        ]);
        $respStock->assertRedirect();

        // Check stock reduced from 10 to 7
        $this->assertEquals(7, $produk->fresh()->stok);

        // Check invoice created
        $stockInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->assertEquals(600000, $stockInvoice->total_tagihan);

        // 3. Test update status to paid via invoice.update-status
        $manualInvoice = Invoice::where('status', 'unpaid')->first();
        $this->actingAs($this->admin)->put("/invoice/{$manualInvoice->id}/status", [
            'status' => 'paid',
            'keterangan' => 'Dibayar tunai di kasir',
        ])->assertRedirect();
        $this->assertEquals('paid', $manualInvoice->fresh()->status);
        $this->assertDatabaseHas('pembayaran', [
            'invoice_id' => $manualInvoice->id,
            'jumlah_dibayar' => $manualInvoice->total_tagihan,
        ]);

        // 4. Test voiding invoice and verifying stock is restored to 10
        $this->actingAs($this->admin)->put("/invoice/{$stockInvoice->id}/void", [
            'alasan' => 'Dibatalkan oleh pelanggan',
        ]);
        $this->assertEquals(10, $produk->fresh()->stok);
        $this->assertEquals('void', $stockInvoice->fresh()->status);

        // 5. Test updating status of void invoice to unpaid
        $this->actingAs($this->admin)->put("/invoice/{$stockInvoice->id}/status", [
            'status' => 'unpaid',
            'keterangan' => 'Diaktifkan kembali',
        ])->assertRedirect();
        $this->assertEquals('unpaid', $stockInvoice->fresh()->status);
        $this->assertEquals(7, $produk->fresh()->stok);

        // 6. Test partial payment via invoice.bayar
        $this->actingAs($this->admin)->post("/invoice/{$stockInvoice->id}/bayar", [
            'jumlah_dibayar' => 200000,
            'metode_bayar' => 'transfer',
        ])->assertRedirect();
        $this->assertEquals('partial', $stockInvoice->fresh()->status);

        // 7. Test full payment via invoice.bayar
        $this->actingAs($this->admin)->post("/invoice/{$stockInvoice->id}/bayar", [
            'jumlah_dibayar' => 400000,
            'metode_bayar' => 'tunai',
        ])->assertRedirect();
        $this->assertEquals('paid', $stockInvoice->fresh()->status);

        // 8. Test invoice show view and PDF generation render company logo
        Perusahaan::updateOrCreate(['id' => 1], [
            'nama_perusahaan' => 'Wahyu Teknik Indotama',
            'logo' => 'logo/test-logo.png',
        ]);
        $responseShow = $this->actingAs($this->admin)->get("/invoice/{$stockInvoice->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee('storage/logo/test-logo.png');

        $responsePdf = $this->actingAs($this->admin)->get("/invoice/{$stockInvoice->id}/cetak-pdf");
        $responsePdf->assertStatus(200);
    }

    public function test_produk_pages_render(): void
    {
        $response = $this->actingAs($this->admin)->get('/produk');
        $response->assertStatus(200);
        $response->assertSee('Suku Cadang');

        $responseCreate = $this->actingAs($this->admin)->get('/produk/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Tambah Suku Cadang / Produk');
    }

    public function test_pengeluaran_pages_render(): void
    {
        $response = $this->actingAs($this->admin)->get('/pengeluaran');
        $response->assertStatus(200);
        $response->assertSee('Pengeluaran Kas Operasional');

        $responseCreate = $this->actingAs($this->admin)->get('/pengeluaran/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Catat Pengeluaran Operasional');
    }

    public function test_laporan_pages_render(): void
    {
        $responseInv = $this->actingAs($this->admin)->get('/laporan/invoice');
        $responseInv->assertStatus(200);
        $responseInv->assertSee('Laporan Transaksi Invoice');

        $responseTiket = $this->actingAs($this->admin)->get('/laporan/tiket');
        $responseTiket->assertStatus(200);
        $responseTiket->assertSee('Laporan Operasional Tiket Servis');

        $responseLaba = $this->actingAs($this->admin)->get('/laporan/laba-rugi');
        $responseLaba->assertStatus(200);
        $responseLaba->assertSee('Laporan Laba Rugi Finansial');
    }

    public function test_pengaturan_and_users_pages_render(): void
    {
        $responsePajak = $this->actingAs($this->admin)->get('/pengaturan/pajak');
        $responsePajak->assertStatus(200);
        $responsePajak->assertSee('Pengaturan Pajak (PPN)');

        // Test create tax
        $postPajak = $this->actingAs($this->admin)->post('/pengaturan/pajak', [
            'nama_pajak' => 'PPN 12%',
            'persentase' => 12,
            'aktif' => 1,
            'berlaku_mulai' => '2026-01-01',
            'tipe_aturan' => 'semua',
            'nominal_batas' => 0,
        ]);
        $postPajak->assertRedirect('/pengaturan/pajak');
        $pajak = PengaturanPajak::where('nama_pajak', 'PPN 12%')->first();
        $this->assertNotNull($pajak);

        // Test delete tax
        $deletePajak = $this->actingAs($this->admin)->delete("/pengaturan/pajak/{$pajak->id}");
        $deletePajak->assertRedirect('/pengaturan/pajak');
        $this->assertDatabaseMissing('pengaturan_pajak', ['id' => $pajak->id]);

        $responsePerusahaan = $this->actingAs($this->admin)->get('/pengaturan/perusahaan');
        $responsePerusahaan->assertStatus(200);
        $responsePerusahaan->assertSee('Identitas Toko / Nota');

        $updatePerusahaan = $this->actingAs($this->admin)->put('/pengaturan/perusahaan', [
            'nama_perusahaan' => 'Cekat Cell Mandiri',
            'deskripsi' => 'Pusat reparasi gadget terpercaya',
            'email' => 'toko@cekatcell.com',
            'telp' => '08123456789',
            'alamat' => 'Jl. Pemuda No. 45',
            'npwp' => '12.345.678.9-012.000',
        ]);
        $updatePerusahaan->assertSessionHas('success');
        $this->assertDatabaseHas('perusahaan', [
            'nama_perusahaan' => 'Cekat Cell Mandiri',
            'deskripsi' => 'Pusat reparasi gadget terpercaya',
            'email' => 'toko@cekatcell.com',
        ]);

        $responseUsers = $this->actingAs($this->admin)->get('/users');
        $responseUsers->assertStatus(200);
        $responseUsers->assertSee('Manajemen User');
    }

    public function test_dynamic_threshold_tax_calculation(): void
    {
        // Setup threshold tax rules:
        // PPN 11% for >= 2,000,000
        // PPh 2% for < 2,000,000
        PengaturanPajak::query()->delete();

        $ppn = PengaturanPajak::create([
            'nama_pajak' => 'PPN 11%',
            'persentase' => 11,
            'aktif' => true,
            'berlaku_mulai' => now()->subDay(),
            'tipe_aturan' => 'diatas_nominal',
            'nominal_batas' => 2000000,
        ]);

        $pph = PengaturanPajak::create([
            'nama_pajak' => 'PPh 2%',
            'persentase' => 2,
            'aktif' => true,
            'berlaku_mulai' => now()->subDay(),
            'tipe_aturan' => 'dibawah_nominal',
            'nominal_batas' => 2000000,
        ]);

        // Test tentukanPajakOtomatis logic directly
        $taxBelow = PengaturanPajak::tentukanPajakOtomatis(1500000);
        $this->assertEquals($ppn->id !== null ? $pph->id : null, $taxBelow?->id);

        $taxAbove = PengaturanPajak::tentukanPajakOtomatis(2500000);
        $this->assertEquals($ppn->id, $taxAbove?->id);

        $taxExact = PengaturanPajak::tentukanPajakOtomatis(2000000);
        $this->assertEquals($ppn->id, $taxExact?->id);

        // Test invoice creation with automatic tax selection
        // Invoice 1: Total below 2,000,000 -> Should use PPh 2%
        $respBelow = $this->actingAs($this->admin)->post('/invoice', [
            'diskon' => 0,
            'keterangan' => 'Test below 2M',
            'items' => [
                [
                    'deskripsi' => 'Item Murah',
                    'qty' => 1,
                    'harga_satuan' => 1000000,
                    'harga_modal_satuan' => 800000,
                ],
            ],
        ]);
        $respBelow->assertRedirect();
        $invBelow = Invoice::where('keterangan', 'Test below 2M')->first();
        $this->assertNotNull($invBelow);
        $this->assertEquals($pph->id, $invBelow->pajak_id);
        $this->assertEquals(2, $invBelow->pajak_persen);
        $this->assertEquals(20000, $invBelow->pajak_nominal); // 2% of 1,000,000

        // Invoice 2: Total >= 2,000,000 -> Should use PPN 11%
        $respAbove = $this->actingAs($this->admin)->post('/invoice', [
            'diskon' => 0,
            'keterangan' => 'Test above 2M',
            'items' => [
                [
                    'deskripsi' => 'Item Mahal',
                    'qty' => 1,
                    'harga_satuan' => 3000000,
                    'harga_modal_satuan' => 2500000,
                ],
            ],
        ]);
        $respAbove->assertRedirect();
        $invAbove = Invoice::where('keterangan', 'Test above 2M')->first();
        $this->assertNotNull($invAbove);
        $this->assertEquals($ppn->id, $invAbove->pajak_id);
        $this->assertEquals(11, $invAbove->pajak_persen);
        $this->assertEquals(330000, $invAbove->pajak_nominal); // 11% of 3,000,000
    }

    public function test_profile_page_renders_and_is_linked_in_menu(): void
    {
        $response = $this->actingAs($this->admin)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Profil & Keamanan Akun', false);
        $response->assertSee('Informasi Profil Staf');
        $response->assertSee('Perbarui Kata Sandi');

        // Check that dashboard HTML contains links to all modules
        $dashResponse = $this->actingAs($this->admin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee(route('profile.edit'));
        $dashResponse->assertSee(route('tiket-servis.index'));
        $dashResponse->assertSee(route('invoice.index'));
        $dashResponse->assertSee(route('pelanggan.index'));
        $dashResponse->assertSee(route('produk.index'));
        $dashResponse->assertSee(route('pengeluaran.index'));
        $dashResponse->assertSee(route('pengaturan.pajak.index'));
        $dashResponse->assertSee(route('users.index'));
    }
}
