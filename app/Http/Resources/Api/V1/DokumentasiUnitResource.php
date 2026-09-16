<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DokumentasiUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tiket_id' => $this->tiket_id,
            'tipe_dokumentasi' => $this->tipe_dokumentasi,
            'file_path' => $this->file_path,
            'foto_url' => $this->foto_url,
            'keterangan' => $this->keterangan,
            'diupload_oleh' => $this->user?->name ?? 'Sistem',
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
