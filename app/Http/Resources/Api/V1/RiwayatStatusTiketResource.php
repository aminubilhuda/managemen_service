<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatStatusTiketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tiket_id' => $this->tiket_id,
            'status_sebelum' => $this->status_sebelum,
            'status_sesudah' => $this->status_sesudah,
            'status_label' => $this->status_label,
            'catatan' => $this->catatan,
            'diubah_oleh' => $this->user?->name ?? 'Sistem',
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
