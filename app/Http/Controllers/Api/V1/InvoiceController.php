<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Api\V1\InvoiceResource;
use App\Http\Traits\ApiResponse;
use App\Models\DetailInvoice;
use App\Models\Invoice;
use App\Models\PengaturanPajak;
use App\Models\Perusahaan;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated invoices with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['tiket.pelanggan', 'pembayaran', 'pajak']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                    ->orWhereHas('tiket.pelanggan', function ($p) use ($search) {
                        $p->where('nama_pelanggan', 'like', "%{$search}%")
                            ->orWhere('no_telp', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_invoice', [$request->dari, $request->sampai.' 23:59:59']);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->paginatedResponse($invoices, 'Daftar invoice berhasil dimuat.', InvoiceResource::class);
    }

    /**
     * Store new invoice with dynamic tax calculation and stock deduction.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! empty($validated['tiket_id'])) {
            $existing = Invoice::where('tiket_id', $validated['tiket_id'])->where('status', '!=', 'void')->first();
            if ($existing) {
                return $this->errorResponse('Tiket ini sudah memiliki invoice aktif ('.$existing->no_invoice.').', 422);
            }
        }

        $perusahaan = Perusahaan::first();
        if (! $perusahaan) {
            $perusahaan = Perusahaan::create([
                'nama_perusahaan' => config('app.name', 'Nama Toko'),
                'alamat' => 'Alamat belum diatur',
                'telp' => '-',
            ]);
        }

        // Calculate subtotal
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ((int) $item['qty']) * ((float) $item['harga_satuan']);
        }
        $diskonNominal = (float) ($validated['diskon'] ?? 0);
        $setelahDiskon = max(0, $subtotal - $diskonNominal);

        // Tax determination
        $pajak = ! empty($validated['pajak_id'])
            ? PengaturanPajak::find($validated['pajak_id'])
            : PengaturanPajak::tentukanPajakOtomatis($setelahDiskon);

        $pajakPersen = isset($validated['pajak_persen'])
            ? (float) $validated['pajak_persen']
            : ($pajak?->persentase ?? 0);

        $invoice = DB::transaction(function () use ($validated, $perusahaan, $pajak, $pajakPersen) {
            $diskonNominal = (float) ($validated['diskon'] ?? 0);
            $diskonPersen = (float) ($validated['diskon_persen'] ?? 0);

            $invoice = Invoice::create([
                'no_invoice' => Invoice::generateNoInvoice(),
                'tiket_id' => ! empty($validated['tiket_id']) ? $validated['tiket_id'] : null,
                'perusahaan_id' => $perusahaan->id,
                'tanggal_invoice' => now(),
                'diskon_persen' => $diskonPersen,
                'diskon_nominal' => $diskonNominal,
                'pajak_id' => $pajak?->id,
                'pajak_persen' => $pajakPersen,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => 'unpaid',
            ]);

            foreach ($validated['items'] as $item) {
                $produk = ! empty($item['produk_id']) ? Produk::find($item['produk_id']) : null;
                $qty = (int) $item['qty'];
                $hargaSatuan = (float) $item['harga_satuan'];
                $hargaModal = $produk ? (float) $produk->harga_modal : (float) ($item['harga_modal_satuan'] ?? 0);

                DetailInvoice::create([
                    'invoice_id' => $invoice->id,
                    'produk_id' => $produk?->id,
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $qty,
                    'harga_satuan' => $hargaSatuan,
                    'harga_modal_satuan' => $hargaModal,
                    'jumlah' => $qty * $hargaSatuan,
                ]);

                if ($produk && $produk->tipe === 'sparepart') {
                    $produk->decrement('stok', $qty);
                }
            }

            $invoice->hitungTotal();

            return $invoice;
        });

        $invoice->load(['tiket.pelanggan', 'detail.produk', 'pembayaran', 'pajak']);

        return $this->successResponse(new InvoiceResource($invoice), 'Invoice berhasil dibuat.', 201);
    }

    /**
     * Show single invoice details.
     */
    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with(['tiket.pelanggan', 'detail.produk', 'pembayaran.user', 'pajak', 'perusahaan'])->find($id);

        if (! $invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        return $this->successResponse(new InvoiceResource($invoice), 'Detail invoice berhasil dimuat.');
    }

    /**
     * Void an invoice and restore stock.
     */
    public function void(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $invoice = Invoice::with('detail.produk')->find($id);

        if (! $invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        if ($invoice->status === 'void') {
            return $this->errorResponse('Invoice ini sudah dibatalkan sebelumnya.', 422);
        }

        DB::transaction(function () use ($invoice, $request) {
            $invoice->update([
                'status' => 'void',
                'keterangan' => ($invoice->keterangan ? $invoice->keterangan."\n" : '').'[VOID] '.$request->input('alasan'),
            ]);

            foreach ($invoice->detail as $item) {
                $produk = $item->produk;
                if ($produk && $produk->tipe === 'sparepart' && $produk->stok !== null) {
                    $produk->increment('stok', $item->qty);
                }
            }
        });

        return $this->successResponse(new InvoiceResource($invoice), 'Invoice berhasil dibatalkan (void).');
    }
}
