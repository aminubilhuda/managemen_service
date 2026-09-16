<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tiket\UploadDokumentasiRequest;
use App\Http\Resources\Api\V1\DokumentasiUnitResource;
use App\Http\Traits\ApiResponse;
use App\Models\DokumentasiUnit;
use App\Models\TiketServis;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DokumentasiUnitController extends Controller
{
    use ApiResponse;

    /**
     * Upload photo documentation for a service ticket.
     */
    public function upload(UploadDokumentasiRequest $request, int $tiketId): JsonResponse
    {
        $ticket = TiketServis::find($tiketId);

        if (! $ticket) {
            return $this->errorResponse('Tiket servis tidak ditemukan.', 404);
        }

        $file = $request->file('foto');
        $path = $file->store('dokumentasi/'.$ticket->id, 'public');

        $tipe = $request->input('tipe_dokumentasi', 'sebelum');
        // Standardize labels if aliases used
        $tipe = match ($tipe) {
            'kondisi_masuk' => 'sebelum',
            'pengerjaan' => 'proses',
            'kondisi_selesai' => 'sesudah',
            default => $tipe,
        };

        $dokumentasi = DokumentasiUnit::create([
            'tiket_id' => $ticket->id,
            'tipe_dokumentasi' => $tipe,
            'file_path' => $path,
            'keterangan' => $request->input('keterangan'),
            'diupload_oleh' => $request->user()->id,
        ]);

        $dokumentasi->load('user');

        return $this->successResponse(new DokumentasiUnitResource($dokumentasi), 'Foto dokumentasi berhasil diunggah.', 201);
    }

    /**
     * Delete a photo documentation.
     */
    public function destroy(int $id): JsonResponse
    {
        $dokumentasi = DokumentasiUnit::find($id);

        if (! $dokumentasi) {
            return $this->errorResponse('Foto dokumentasi tidak ditemukan.', 404);
        }

        if ($dokumentasi->file_path && Storage::disk('public')->exists($dokumentasi->file_path)) {
            Storage::disk('public')->delete($dokumentasi->file_path);
        }

        $dokumentasi->delete();

        return $this->successResponse(null, 'Foto dokumentasi berhasil dihapus.');
    }
}
