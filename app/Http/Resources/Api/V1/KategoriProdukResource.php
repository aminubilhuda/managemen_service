<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kategori' => $this->nama_kategori,
            'total_produk' => $this->whenCounted('produk'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
