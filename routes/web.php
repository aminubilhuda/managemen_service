<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengaturanPajakController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TiketServisController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pelanggan
    Route::resource('pelanggan', PelangganController::class);
    Route::get('/api/pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');

    // Kategori Produk
    Route::resource('kategori-produk', KategoriProdukController::class)
        ->except(['show'])
        ->parameters(['kategori-produk' => 'kategoriProduk']);

    // Produk
    Route::resource('produk', ProdukController::class)->except(['show']);
    Route::get('/api/produk/search', [ProdukController::class, 'search'])->name('produk.search');

    // Tiket Servis
    Route::resource('tiket-servis', TiketServisController::class)
        ->parameters(['tiket-servis' => 'tiketServis']);
    Route::put('/tiket-servis/{tiketServis}/status', [TiketServisController::class, 'updateStatus'])->name('tiket-servis.update-status');
    Route::post('/tiket-servis/{tiketServis}/dokumentasi', [TiketServisController::class, 'uploadDokumentasi'])->name('tiket-servis.upload-dokumentasi');
    Route::delete('/tiket-servis/dokumentasi/{dokumentasi}', [TiketServisController::class, 'hapusDokumentasi'])->name('tiket-servis.hapus-dokumentasi');
    Route::get('/tiket-servis/{tiketServis}/cetak-tanda-terima', [TiketServisController::class, 'cetakTandaTerima'])->name('tiket-servis.cetak-tanda-terima');

    // Invoice
    Route::resource('invoice', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::put('/invoice/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoice.update-status');
    Route::post('/invoice/{invoice}/bayar', [InvoiceController::class, 'bayar'])->name('invoice.bayar');
    Route::put('/invoice/{invoice}/void', [InvoiceController::class, 'void'])->name('invoice.void');
    Route::get('/invoice/{invoice}/cetak-pdf', [InvoiceController::class, 'cetakPdf'])->name('invoice.cetak-pdf');

    // Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class)->except(['show']);

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/invoice', [LaporanController::class, 'invoice'])->name('invoice');
        Route::get('/invoice/export-excel', [LaporanController::class, 'exportInvoiceExcel'])->name('invoice.export-excel');
        Route::get('/tiket', [LaporanController::class, 'tiket'])->name('tiket');
        Route::get('/tiket/export-excel', [LaporanController::class, 'exportTiketExcel'])->name('tiket.export-excel');
        Route::get('/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('/laba-rugi/export-excel', [LaporanController::class, 'exportLabaRugiExcel'])->name('laba-rugi.export-excel');
    });

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::resource('pajak', PengaturanPajakController::class)->except(['show']);
        Route::get('/perusahaan', [PerusahaanController::class, 'edit'])->name('perusahaan');
        Route::put('/perusahaan', [PerusahaanController::class, 'update'])->name('perusahaan.update');
    });

    // Manajemen User
    Route::resource('users', UserController::class)->except(['show']);
});

require __DIR__.'/auth.php';
