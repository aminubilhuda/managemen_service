<?php

namespace Tests\Feature\Api\V1;

use App\Models\KategoriProduk;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\TiketServis;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('username', 'admin')->first()
            ?? User::where('email', 'admin@cekatcell.com')->first()
            ?? User::first();
    }

    public function test_mobile_login_with_email_and_username(): void
    {
        // 1. Login with email
        $resEmail = $this->postJson('/api/v1/auth/login', [
            'login' => $this->admin->email,
            'password' => 'password',
            'device_name' => 'Pixel 8 Pro',
        ]);

        $resEmail->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'token_type' => 'Bearer',
                ],
            ]);
        $this->assertNotEmpty($resEmail->json('data.token'));

        // 2. Login with username
        $resUser = $this->postJson('/api/v1/auth/login', [
            'login' => $this->admin->username,
            'password' => 'password',
            'device_name' => 'iPhone 15',
        ]);

        $resUser->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Login with wrong password
        $resFail = $this->postJson('/api/v1/auth/login', [
            'login' => $this->admin->email,
            'password' => 'wrong-pass',
        ]);

        $resFail->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_mobile_auth_me_and_logout(): void
    {
        Sanctum::actingAs($this->admin);

        $resMe = $this->getJson('/api/v1/auth/me');
        $resMe->assertStatus(200)
            ->assertJsonPath('data.email', $this->admin->email)
            ->assertJsonPath('data.name', $this->admin->name);

        $resLogout = $this->postJson('/api/v1/auth/logout');
        $resLogout->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_mobile_dashboard_returns_metrics(): void
    {
        Sanctum::actingAs($this->admin);

        $res = $this->getJson('/api/v1/dashboard');
        $res->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'status_counts',
                    'my_tasks_count',
                    'pendapatan' => ['hari_ini', 'bulan_ini'],
                    'tiket_terbaru',
                ],
            ]);
    }

    public function test_mobile_tiket_create_list_show_and_update_status(): void
    {
        Sanctum::actingAs($this->admin);

        // 1. Create ticket via API
        $resCreate = $this->postJson('/api/v1/tiket', [
            'nama_pelanggan' => 'Andi Mobile',
            'no_telp' => '081233445566',
            'alamat' => 'Jl. Merdeka No 12',
            'perangkat' => 'Xiaomi Redmi Note 10',
            'imei_sn' => '861234567890123',
            'kelengkapan' => 'Unit only',
            'keluhan' => 'Layar sentuh tidak merespons',
            'kondisi_awal' => 'Kaca retak sedikit',
            'estimasi_biaya' => 250000,
        ]);

        $resCreate->assertStatus(201)
            ->assertJsonPath('data.perangkat', 'Xiaomi Redmi Note 10')
            ->assertJsonPath('data.status', 'diterima');

        $tiketId = $resCreate->json('data.id');
        $noTiket = $resCreate->json('data.no_tiket');

        // 2. List tickets with filter
        $resList = $this->getJson('/api/v1/tiket?status=diterima');
        $resList->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // 3. Show ticket
        $resShow = $this->getJson("/api/v1/tiket/{$tiketId}");
        $resShow->assertStatus(200)
            ->assertJsonPath('data.no_tiket', $noTiket);

        // 4. Update status to 'dikerjakan'
        $resStatus = $this->patchJson("/api/v1/tiket/{$tiketId}/status", [
            'status' => 'dikerjakan',
            'catatan' => 'Sedang diganti modul LCD touch screen',
        ]);

        $resStatus->assertStatus(200)
            ->assertJsonPath('data.status', 'dikerjakan');

        // 5. Scan ticket by IMEI or No Tiket
        $resScan = $this->getJson('/api/v1/tiket/scan/861234567890123');
        $resScan->assertStatus(200)
            ->assertJsonPath('data.id', $tiketId);
    }

    public function test_mobile_upload_dokumentasi_foto(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->admin);

        $pelanggan = Pelanggan::firstOrCreate(['nama_pelanggan' => 'Test Customer', 'no_telp' => '08123456789']);
        $tiket = TiketServis::firstOrCreate(
            ['no_tiket' => 'SRV-TEST-0001'],
            [
                'pelanggan_id' => $pelanggan->id,
                'perangkat' => 'Samsung A52',
                'keluhan' => 'Layar Blank',
                'status' => 'diterima',
            ]
        );

        $file = UploadedFile::fake()->image('kondisi-masuk.jpg', 640, 480);

        $resUpload = $this->postJson("/api/v1/tiket/{$tiket->id}/dokumentasi", [
            'foto' => $file,
            'tipe_dokumentasi' => 'kondisi_masuk',
            'keterangan' => 'Foto fisik bagian belakang retak',
        ]);

        $resUpload->assertStatus(201)
            ->assertJsonPath('data.tipe_dokumentasi', 'sebelum');

        $fotoId = $resUpload->json('data.id');

        // Delete photo
        $resDel = $this->deleteJson("/api/v1/tiket/dokumentasi/{$fotoId}");
        $resDel->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_mobile_pelanggan_crud_and_search(): void
    {
        Sanctum::actingAs($this->admin);

        // Create
        $resCreate = $this->postJson('/api/v1/pelanggan', [
            'nama_pelanggan' => 'Budi Santoso',
            'no_telp' => '085711223344',
            'alamat' => 'Surabaya',
        ]);
        $resCreate->assertStatus(201)
            ->assertJsonPath('data.nama_pelanggan', 'Budi Santoso');

        $pelangganId = $resCreate->json('data.id');

        // Search
        $resSearch = $this->getJson('/api/v1/pelanggan/search?q=Budi');
        $resSearch->assertStatus(200)
            ->assertJsonPath('data.0.nama_pelanggan', 'Budi Santoso');

        // Update
        $resUpdate = $this->putJson("/api/v1/pelanggan/{$pelangganId}", [
            'nama_pelanggan' => 'Budi Santoso Edit',
            'no_telp' => '085711223399',
        ]);
        $resUpdate->assertStatus(200)
            ->assertJsonPath('data.nama_pelanggan', 'Budi Santoso Edit');
    }

    public function test_mobile_invoice_create_and_payment(): void
    {
        Sanctum::actingAs($this->admin);

        $produk = Produk::where('tipe', 'sparepart')->first();
        if (! $produk) {
            $kategori = KategoriProduk::firstOrCreate(['nama_kategori' => 'LCD']);
            $produk = Produk::create([
                'kategori_id' => $kategori->id,
                'kode_produk' => 'LCD-TEST-01',
                'nama_produk' => 'LCD Test Mobile',
                'tipe' => 'sparepart',
                'harga_jual' => 350000,
                'harga_modal' => 200000,
                'stok' => 10,
            ]);
        }

        // 1. Create Invoice
        $resInvoice = $this->postJson('/api/v1/invoice', [
            'items' => [
                [
                    'produk_id' => $produk->id,
                    'deskripsi' => 'Penggantian LCD Screen',
                    'qty' => 1,
                    'harga_satuan' => 350000,
                ],
            ],
            'diskon' => 0,
            'keterangan' => 'Invoice dari mobile',
        ]);

        $resInvoice->assertStatus(201)
            ->assertJsonPath('data.status', 'unpaid');

        $invoiceId = $resInvoice->json('data.id');
        $totalTagihan = (float) $resInvoice->json('data.total_tagihan');

        // 2. Pay Invoice
        $resPay = $this->postJson("/api/v1/invoice/{$invoiceId}/bayar", [
            'jumlah_dibayar' => $totalTagihan,
            'metode_bayar' => 'qris',
        ]);

        $resPay->assertStatus(201)
            ->assertJsonPath('data.invoice.status', 'paid');
    }

    public function test_mobile_master_data_endpoints(): void
    {
        Sanctum::actingAs($this->admin);

        $this->getJson('/api/v1/master/perusahaan')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/api/v1/master/teknisi')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/api/v1/master/status-tiket')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/api/v1/master/pajak')->assertStatus(200)->assertJson(['success' => true]);
    }
}
