<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\PengaturanPajak;
use App\Models\Perusahaan;
use App\Models\TiketServis;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MasterController extends Controller
{
    use ApiResponse;

    /**
     * Get store / company profile for mobile app branding & receipt header.
     */
    public function perusahaan(): JsonResponse
    {
        $perusahaan = Perusahaan::first();

        return $this->successResponse([
            'id' => $perusahaan?->id,
            'nama_perusahaan' => $perusahaan?->nama_perusahaan ?? 'Cekat Cell',
            'alamat' => $perusahaan?->alamat ?? '-',
            'telp' => $perusahaan?->telp ?? '-',
            'logo_url' => $perusahaan?->logo ? asset('storage/'.$perusahaan->logo) : null,
        ], 'Data profil toko berhasil dimuat.');
    }

    /**
     * Get active technicians for assignment dropdowns.
     */
    public function teknisi(): JsonResponse
    {
        $teknisi = User::role('teknisi')->orderBy('name')->get();

        return $this->successResponse(UserResource::collection($teknisi), 'Daftar teknisi berhasil dimuat.');
    }

    /**
     * Get official ticket status definitions.
     */
    public function statusTiket(): JsonResponse
    {
        $statuses = [];
        foreach (TiketServis::STATUSES as $key => $label) {
            $statuses[] = [
                'value' => $key,
                'label' => $label,
            ];
        }

        return $this->successResponse($statuses, 'Daftar status tiket servis.');
    }

    /**
     * Get active tax settings and rules.
     */
    public function pajak(): JsonResponse
    {
        $pajak = PengaturanPajak::where('aktif', true)->orderBy('persentase')->get();

        return $this->successResponse($pajak, 'Daftar pengaturan pajak aktif.');
    }
}
