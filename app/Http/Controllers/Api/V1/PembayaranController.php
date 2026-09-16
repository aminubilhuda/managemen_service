<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Invoice\StorePembayaranRequest;
use App\Http\Resources\Api\V1\InvoiceResource;
use App\Http\Resources\Api\V1\PembayaranResource;
use App\Http\Traits\ApiResponse;
use App\Models\Invoice;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    use ApiResponse;

    /**
     * Record a payment for an invoice.
     */
    public function store(StorePembayaranRequest $request, int $invoiceId): JsonResponse
    {
        $invoice = Invoice::with('pembayaran')->find($invoiceId);

        if (! $invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        if ($invoice->status === 'void') {
            return $this->errorResponse('Tidak dapat mencatat pembayaran pada invoice yang telah dibatalkan (void).', 422);
        }

        if ($invoice->status === 'paid') {
            return $this->errorResponse('Invoice ini sudah lunas.', 422);
        }

        $sisaTagihan = $invoice->sisa_tagihan;
        $jumlahDibayar = (float) $request->input('jumlah_dibayar');

        if ($jumlahDibayar > ($sisaTagihan + 0.01)) {
            return $this->errorResponse('Nominal pembayaran (Rp '.number_format($jumlahDibayar, 0, ',', '.').') melebihi sisa tagihan (Rp '.number_format($sisaTagihan, 0, ',', '.').').', 422);
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('bukti-pembayaran', 'public');
        }

        $pembayaran = DB::transaction(function () use ($invoice, $request, $jumlahDibayar, $buktiPath) {
            $pembayaran = Pembayaran::create([
                'invoice_id' => $invoice->id,
                'jumlah_dibayar' => $jumlahDibayar,
                'metode_bayar' => $request->input('metode_bayar'),
                'tanggal_bayar' => $request->input('tanggal_bayar', now()),
                'dicatat_oleh' => $request->user()->id,
                'bukti_bayar' => $buktiPath,
            ]);

            $invoice->refresh();
            $invoice->updateStatusPembayaran();

            return $pembayaran;
        });

        $invoice->load(['tiket.pelanggan', 'pembayaran.user', 'detail.produk']);

        return $this->successResponse([
            'pembayaran' => new PembayaranResource($pembayaran),
            'invoice' => new InvoiceResource($invoice),
        ], 'Pembayaran berhasil dicatat.', 201);
    }
}
