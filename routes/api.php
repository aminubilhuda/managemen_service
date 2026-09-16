<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DokumentasiUnitController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\KategoriProdukController;
use App\Http\Controllers\Api\V1\MasterController;
use App\Http\Controllers\Api\V1\PelangganController;
use App\Http\Controllers\Api\V1\PembayaranController;
use App\Http\Controllers\Api\V1\PengeluaranController;
use App\Http\Controllers\Api\V1\ProdukController;
use App\Http\Controllers\Api\V1\TiketServisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // 1. Autentikasi Publik
    Route::post('/auth/login', [AuthController::class, 'login']);

    // 2. Protected Routes (Harus menyertakan Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {

        // Profil & Sesi
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'updatePassword']);
        });

        // Dashboard Ringkasan
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Tiket Servis
        Route::prefix('tiket')->group(function () {
            Route::get('/', [TiketServisController::class, 'index']);
            Route::post('/', [TiketServisController::class, 'store']);
            Route::get('/scan/{code}', [TiketServisController::class, 'scan']);
            Route::get('/{id}', [TiketServisController::class, 'show']);
            Route::put('/{id}', [TiketServisController::class, 'update']);
            Route::patch('/{id}/status', [TiketServisController::class, 'updateStatus']);
            Route::get('/{id}/tanda-terima', [TiketServisController::class, 'tandaTerima']);

            // Dokumentasi Foto Kamera
            Route::post('/{id}/dokumentasi', [DokumentasiUnitController::class, 'upload']);
            Route::delete('/dokumentasi/{id}', [DokumentasiUnitController::class, 'destroy']);
        });

        // Pelanggan
        Route::prefix('pelanggan')->group(function () {
            Route::get('/', [PelangganController::class, 'index']);
            Route::get('/search', [PelangganController::class, 'search']);
            Route::post('/', [PelangganController::class, 'store']);
            Route::get('/{id}', [PelangganController::class, 'show']);
            Route::put('/{id}', [PelangganController::class, 'update']);
        });

        // Produk & Sparepart
        Route::get('/kategori-produk', [KategoriProdukController::class, 'index']);
        Route::prefix('produk')->group(function () {
            Route::get('/', [ProdukController::class, 'index']);
            Route::get('/search', [ProdukController::class, 'search']);
            Route::post('/', [ProdukController::class, 'store']);
            Route::get('/{id}', [ProdukController::class, 'show']);
            Route::put('/{id}', [ProdukController::class, 'update']);
        });

        // Invoice & Kasir Pembayaran
        Route::prefix('invoice')->group(function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::post('/', [InvoiceController::class, 'store']);
            Route::get('/{id}', [InvoiceController::class, 'show']);
            Route::put('/{id}/void', [InvoiceController::class, 'void']);
            Route::post('/{id}/bayar', [PembayaranController::class, 'store']);
        });

        // Pengeluaran Toko
        Route::prefix('pengeluaran')->group(function () {
            Route::get('/', [PengeluaranController::class, 'index']);
            Route::post('/', [PengeluaranController::class, 'store']);
            Route::delete('/{id}', [PengeluaranController::class, 'destroy']);
        });

        // Master Data & Pengaturan
        Route::prefix('master')->group(function () {
            Route::get('/perusahaan', [MasterController::class, 'perusahaan']);
            Route::get('/teknisi', [MasterController::class, 'teknisi']);
            Route::get('/status-tiket', [MasterController::class, 'statusTiket']);
            Route::get('/pajak', [MasterController::class, 'pajak']);
        });
    });
});
