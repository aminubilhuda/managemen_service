<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'produk_id' => $this->produk_id,
            'deskripsi' => $this->deskripsi,
            'qty' => (int) $this->qty,
            'harga_satuan' => (float) $this->harga_satuan,
            'harga_modal_satuan' => (float) $this->harga_modal_satuan,
            'jumlah' => (float) $this->jumlah,
            'produk' => new ProdukResource($this->whenLoaded('produk')),
        ];
    }
}
