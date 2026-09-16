<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengeluaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_pengeluaran' => $this->kategori_pengeluaran,
            'kategori_label' => ucwords(str_replace('_', ' ', $this->kategori_pengeluaran)),
            'deskripsi' => $this->deskripsi,
            'jumlah' => (float) $this->jumlah,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'dicatat_oleh' => $this->user?->name ?? 'Staf',
            'bukti_url' => $this->bukti ? asset('storage/'.$this->bukti) : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
