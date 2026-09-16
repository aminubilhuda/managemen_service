<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'jumlah_dibayar' => (float) $this->jumlah_dibayar,
            'metode_bayar' => $this->metode_bayar,
            'tanggal_bayar' => $this->tanggal_bayar?->toISOString(),
            'dicatat_oleh' => $this->user?->name ?? 'Kasir',
            'bukti_bayar_url' => $this->bukti_bayar ? asset('storage/'.$this->bukti_bayar) : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
