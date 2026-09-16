<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tiket\StoreTiketRequest;
use App\Http\Requests\Api\V1\Tiket\UpdateStatusRequest;
use App\Http\Requests\Api\V1\Tiket\UpdateTiketRequest;
use App\Http\Resources\Api\V1\TiketServisResource;
use App\Http\Traits\ApiResponse;
use App\Models\Pelanggan;
use App\Models\RiwayatStatusTiket;
use App\Models\TiketServis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiketServisController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated list of service tickets with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TiketServis::with(['pelanggan', 'teknisi', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teknisi_id')) {
            $query->where('teknisi_id', $request->teknisi_id);
        }

        if ($request->boolean('my_tickets')) {
            $query->where('teknisi_id', $request->user()->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                    ->orWhere('perangkat', 'like', "%{$search}%")
                    ->orWhere('imei_sn', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($p) use ($search) {
                        $p->where('nama_pelanggan', 'like', "%{$search}%")
                            ->orWhere('no_telp', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('created_at', [$request->dari, $request->sampai.' 23:59:59']);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->paginatedResponse($tickets, 'Daftar tiket servis berhasil dimuat.', TiketServisResource::class);
    }

    /**
     * Store a new service ticket (intake).
     */
    public function store(StoreTiketRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $ticket = DB::transaction(function () use ($validated, $request) {
            $pelangganId = $validated['pelanggan_id'] ?? null;

            if (! $pelangganId && ! empty($validated['nama_pelanggan'])) {
                $pelanggan = Pelanggan::firstOrCreate(
                    ['no_telp' => $validated['no_telp'] ?? '-'],
                    [
                        'nama_pelanggan' => $validated['nama_pelanggan'],
                        'alamat' => $validated['alamat'] ?? null,
                    ]
                );
                $pelangganId = $pelanggan->id;
            }

            $ticket = TiketServis::create([
                'no_tiket' => TiketServis::generateNoTiket(),
                'pelanggan_id' => $pelangganId,
                'teknisi_id' => $validated['teknisi_id'] ?? null,
                'perangkat' => $validated['perangkat'],
                'imei_sn' => $validated['imei_sn'] ?? null,
                'kelengkapan' => $validated['kelengkapan'] ?? null,
                'keluhan' => $validated['keluhan'],
                'kondisi_awal' => $validated['kondisi_awal'] ?? null,
                'estimasi_biaya' => $validated['estimasi_biaya'] ?? null,
                'estimasi_selesai' => $validated['estimasi_selesai'] ?? null,
                'status' => 'diterima',
            ]);

            RiwayatStatusTiket::create([
                'tiket_id' => $ticket->id,
                'status_sebelum' => null,
                'status_sesudah' => 'diterima',
                'catatan' => 'Penerimaan unit servis baru dari aplikasi mobile.',
                'diubah_oleh' => $request->user()->id,
            ]);

            return $ticket;
        });

        $ticket->load(['pelanggan', 'teknisi']);

        return $this->successResponse(new TiketServisResource($ticket), 'Tiket servis berhasil dibuat.', 201);
    }

    /**
     * Get single service ticket details.
     */
    public function show(int $id): JsonResponse
    {
        $ticket = TiketServis::with([
            'pelanggan',
            'teknisi',
            'riwayatStatus.user',
            'dokumentasi.user',
            'invoice.pembayaran',
        ])->find($id);

        if (! $ticket) {
            return $this->errorResponse('Tiket servis tidak ditemukan.', 404);
        }

        return $this->successResponse(new TiketServisResource($ticket), 'Detail tiket servis berhasil dimuat.');
    }

    /**
     * Update service ticket info.
     */
    public function update(UpdateTiketRequest $request, int $id): JsonResponse
    {
        $ticket = TiketServis::find($id);

        if (! $ticket) {
            return $this->errorResponse('Tiket servis tidak ditemukan.', 404);
        }

        $ticket->update($request->validated());
        $ticket->load(['pelanggan', 'teknisi']);

        return $this->successResponse(new TiketServisResource($ticket), 'Tiket servis berhasil diperbarui.');
    }

    /**
     * Quick status update for technician.
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $ticket = TiketServis::find($id);

        if (! $ticket) {
            return $this->errorResponse('Tiket servis tidak ditemukan.', 404);
        }

        $oldStatus = $ticket->status;
        $newStatus = $request->input('status');

        $ticket->status = $newStatus;

        if ($newStatus === 'selesai' && ! $ticket->garansi_sampai) {
            $ticket->garansi_sampai = now()->addDays(30)->toDateString();
        }

        $ticket->save();

        RiwayatStatusTiket::create([
            'tiket_id' => $ticket->id,
            'status_sebelum' => $oldStatus,
            'status_sesudah' => $newStatus,
            'catatan' => $request->input('catatan'),
            'diubah_oleh' => $request->user()->id,
        ]);

        $ticket->load(['pelanggan', 'teknisi', 'riwayatStatus.user']);

        return $this->successResponse(new TiketServisResource($ticket), 'Status tiket berhasil diubah menjadi '.TiketServis::STATUSES[$newStatus]);
    }

    /**
     * Scan ticket lookup by barcode / QR / IMEI / No Tiket.
     */
    public function scan(string $code): JsonResponse
    {
        $cleanCode = trim($code);

        $ticket = TiketServis::with(['pelanggan', 'teknisi', 'invoice'])
            ->where('no_tiket', $cleanCode)
            ->orWhere('imei_sn', $cleanCode)
            ->orWhere('no_tiket', 'like', "%{$cleanCode}%")
            ->latest()
            ->first();

        if (! $ticket) {
            return $this->errorResponse('Data servis untuk kode/barcode "'.$cleanCode.'" tidak ditemukan.', 404);
        }

        return $this->successResponse(new TiketServisResource($ticket), 'Tiket ditemukan.');
    }

    /**
     * Get receipt / tanda terima URL and details.
     */
    public function tandaTerima(int $id): JsonResponse
    {
        $ticket = TiketServis::with(['pelanggan', 'teknisi'])->find($id);

        if (! $ticket) {
            return $this->errorResponse('Tiket servis tidak ditemukan.', 404);
        }

        return $this->successResponse([
            'tiket' => new TiketServisResource($ticket),
            'cetak_url' => route('tiket-servis.cetak-tanda-terima', $ticket->id),
        ], 'Informasi tanda terima berhasil dimuat.');
    }
}
