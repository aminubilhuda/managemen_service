<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TiketServis;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiketServisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'no_tiket' => $this->no_tiket,
            'pelanggan' => new PelangganResource($this->whenLoaded('pelanggan')),
            'pelanggan_id' => $this->pelanggan_id,
            'nama_pelanggan' => $this->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum',
            'no_telp_pelanggan' => $this->pelanggan?->no_telp ?? '-',
            'teknisi' => new UserResource($this->whenLoaded('teknisi')),
            'teknisi_id' => $this->teknisi_id,
            'nama_teknisi' => $this->teknisi?->name ?? 'Belum Ditugaskan',
            'perangkat' => $this->perangkat,
            'imei_sn' => $this->imei_sn,
            'kelengkapan' => $this->kelengkapan,
            'keluhan' => $this->keluhan,
            'kondisi_awal' => $this->kondisi_awal,
            'status' => $this->status,
            'status_label' => TiketServis::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status)),
            'estimasi_biaya' => (float) ($this->estimasi_biaya ?? 0),
            'estimasi_selesai' => $this->estimasi_selesai?->format('Y-m-d'),
            'biaya_final' => (float) ($this->biaya_final ?? 0),
            'garansi_sampai' => $this->garansi_sampai?->format('Y-m-d'),
            'dokumentasi' => DokumentasiUnitResource::collection($this->whenLoaded('dokumentasi')),
            'riwayat_status' => RiwayatStatusTiketResource::collection($this->whenLoaded('riwayatStatus')),
            'invoice_id' => $this->invoice?->id,
            'no_invoice' => $this->invoice?->no_invoice,
            'status_invoice' => $this->invoice?->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
