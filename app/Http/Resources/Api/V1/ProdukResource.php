<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_id' => $this->kategori_id,
            'kategori' => new KategoriProdukResource($this->whenLoaded('kategori')),
            'nama_kategori' => $this->kategori?->nama_kategori ?? '-',
            'kode_produk' => $this->kode_produk,
            'nama_produk' => $this->nama_produk,
            'tipe' => $this->tipe,
            'harga_jual' => (float) $this->harga_jual,
            'harga_modal' => (float) $this->harga_modal,
            'garansi_hari' => (int) ($this->garansi_hari ?? 0),
            'stok' => (int) ($this->stok ?? 0),
            'is_stok_menipis' => ($this->tipe !== 'jasa' && ($this->stok ?? 0) <= 3),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
