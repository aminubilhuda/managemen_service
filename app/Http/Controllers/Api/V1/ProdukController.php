<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Produk\StoreProdukRequest;
use App\Http\Requests\Api\V1\Produk\UpdateProdukRequest;
use App\Http\Resources\Api\V1\ProdukResource;
use App\Http\Traits\ApiResponse;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated products and spare parts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Produk::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->boolean('stok_menipis')) {
            $query->where('tipe', 'sparepart')->where('stok', '<=', 3);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $products = $query->orderBy('nama_produk')->paginate($perPage);

        return $this->paginatedResponse($products, 'Daftar produk berhasil dimuat.', ProdukResource::class);
    }

    /**
     * Quick search / barcode lookup for cashier or invoice builder.
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->query('q', '');

        $products = Produk::with('kategori')
            ->where(function ($q) use ($keyword) {
                $q->where('nama_produk', 'like', "%{$keyword}%")
                    ->orWhere('kode_produk', 'like', "%{$keyword}%");
            })
            ->limit(15)
            ->get();

        return $this->successResponse(ProdukResource::collection($products), 'Hasil pencarian produk.');
    }

    /**
     * Create a new product / spare part.
     */
    public function store(StoreProdukRequest $request): JsonResponse
    {
        $product = Produk::create($request->validated());
        $product->load('kategori');

        return $this->successResponse(new ProdukResource($product), 'Produk berhasil ditambahkan.', 201);
    }

    /**
     * Show single product details.
     */
    public function show(int $id): JsonResponse
    {
        $product = Produk::with('kategori')->find($id);

        if (! $product) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        return $this->successResponse(new ProdukResource($product), 'Detail produk berhasil dimuat.');
    }

    /**
     * Update product details.
     */
    public function update(UpdateProdukRequest $request, int $id): JsonResponse
    {
        $product = Produk::find($id);

        if (! $product) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        $product->update($request->validated());
        $product->load('kategori');

        return $this->successResponse(new ProdukResource($product), 'Produk berhasil diperbarui.');
    }
}
