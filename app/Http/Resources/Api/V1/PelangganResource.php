<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelangganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pelanggan' => $this->nama_pelanggan,
            'no_telp' => $this->no_telp,
            'alamat' => $this->alamat,
            'total_servis' => $this->whenCounted('tiketServis'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
