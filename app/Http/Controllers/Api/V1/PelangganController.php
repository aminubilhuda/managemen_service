<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Pelanggan\StorePelangganRequest;
use App\Http\Requests\Api\V1\Pelanggan\UpdatePelangganRequest;
use App\Http\Resources\Api\V1\PelangganResource;
use App\Http\Resources\Api\V1\TiketServisResource;
use App\Http\Traits\ApiResponse;
use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated customer list.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pelanggan::withCount('tiketServis');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('telp', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $customers = $query->orderBy('nama_pelanggan')->paginate($perPage);

        return $this->paginatedResponse($customers, 'Daftar pelanggan berhasil dimuat.', PelangganResource::class);
    }

    /**
     * Quick search for mobile autocompletion.
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->query('q', '');

        $pelanggan = Pelanggan::where('nama_pelanggan', 'like', "%{$keyword}%")
            ->orWhere('telp', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return $this->successResponse(PelangganResource::collection($pelanggan), 'Hasil pencarian pelanggan.');
    }

    /**
     * Create a new customer.
     */
    public function store(StorePelangganRequest $request): JsonResponse
    {
        $pelanggan = Pelanggan::create($request->validated());

        return $this->successResponse(new PelangganResource($pelanggan), 'Pelanggan berhasil ditambahkan.', 201);
    }

    /**
     * Get customer details and service history.
     */
    public function show(int $id): JsonResponse
    {
        $pelanggan = Pelanggan::withCount('tiketServis')->with(['tiketServis' => function ($q) {
            $q->with('teknisi', 'invoice')->latest()->take(10);
        }])->find($id);

        if (! $pelanggan) {
            return $this->errorResponse('Pelanggan tidak ditemukan.', 404);
        }

        return $this->successResponse([
            'pelanggan' => new PelangganResource($pelanggan),
            'riwayat_servis' => TiketServisResource::collection($pelanggan->tiketServis),
        ], 'Detail pelanggan berhasil dimuat.');
    }

    /**
     * Update customer.
     */
    public function update(UpdatePelangganRequest $request, int $id): JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (! $pelanggan) {
            return $this->errorResponse('Pelanggan tidak ditemukan.', 404);
        }

        $pelanggan->update($request->validated());

        return $this->successResponse(new PelangganResource($pelanggan), 'Data pelanggan berhasil diperbarui.');
    }
}
