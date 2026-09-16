<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'no_invoice' => $this->no_invoice,
            'tiket_id' => $this->tiket_id,
            'tiket' => new TiketServisResource($this->whenLoaded('tiket')),
            'nama_pelanggan' => $this->tiket?->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum',
            'no_telp_pelanggan' => $this->tiket?->pelanggan?->no_telp ?? '-',
            'tanggal_invoice' => $this->tanggal_invoice?->toISOString(),
            'subtotal' => (float) $this->subtotal,
            'diskon_persen' => (float) $this->diskon_persen,
            'diskon_nominal' => (float) $this->diskon_nominal,
            'pajak_id' => $this->pajak_id,
            'nama_pajak' => $this->pajak?->nama_pajak,
            'pajak_persen' => (float) $this->pajak_persen,
            'pajak_nominal' => (float) $this->pajak_nominal,
            'total_tagihan' => (float) $this->total_tagihan,
            'total_dibayar' => (float) $this->total_dibayar,
            'sisa_tagihan' => (float) $this->sisa_tagihan,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'detail_items' => DetailInvoiceResource::collection($this->whenLoaded('detail')),
            'pembayaran' => PembayaranResource::collection($this->whenLoaded('pembayaran')),
            'pdf_url' => route('invoice.cetak-pdf', $this->id),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
