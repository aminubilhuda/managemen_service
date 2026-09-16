<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Pengeluaran\StorePengeluaranRequest;
use App\Http\Resources\Api\V1\PengeluaranResource;
use App\Http\Traits\ApiResponse;
use App\Models\Pengeluaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated expense list with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pengeluaran::with('user');

        if ($request->filled('kategori')) {
            $query->where('kategori_pengeluaran', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('kategori_pengeluaran', 'like', "%{$search}%");
            });
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $expenses = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        return $this->paginatedResponse($expenses, 'Daftar pengeluaran berhasil dimuat.', PengeluaranResource::class);
    }

    /**
     * Record a new expense from mobile with optional receipt file.
     */
    public function store(StorePengeluaranRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['dicatat_oleh'] = $request->user()->id;

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        }

        $pengeluaran = Pengeluaran::create($validated);
        $pengeluaran->load('user');

        return $this->successResponse(new PengeluaranResource($pengeluaran), 'Pengeluaran berhasil dicatat.', 201);
    }

    /**
     * Delete an expense record.
     */
    public function destroy(int $id): JsonResponse
    {
        $pengeluaran = Pengeluaran::find($id);

        if (! $pengeluaran) {
            return $this->errorResponse('Pengeluaran tidak ditemukan.', 404);
        }

        if ($pengeluaran->bukti && Storage::disk('public')->exists($pengeluaran->bukti)) {
            Storage::disk('public')->delete($pengeluaran->bukti);
        }

        $pengeluaran->delete();

        return $this->successResponse(null, 'Pengeluaran berhasil dihapus.');
    }
}
