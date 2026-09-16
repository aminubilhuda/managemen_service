<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\KategoriProdukResource;
use App\Http\Traits\ApiResponse;
use App\Models\KategoriProduk;
use Illuminate\Http\JsonResponse;

class KategoriProdukController extends Controller
{
    use ApiResponse;

    /**
     * List all product categories.
     */
    public function index(): JsonResponse
    {
        $categories = KategoriProduk::withCount('produk')->orderBy('nama_kategori')->get();

        return $this->successResponse(KategoriProdukResource::collection($categories), 'Daftar kategori produk berhasil dimuat.');
    }
}
