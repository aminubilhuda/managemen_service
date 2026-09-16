<?php

namespace App\Http\Requests\Api\V1\Tiket;

use Illuminate\Foundation\Http\FormRequest;

class UploadDokumentasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'tipe_dokumentasi' => ['nullable', 'string', 'in:kondisi_masuk,pengerjaan,kondisi_selesai,lainnya'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'File foto wajib diunggah.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }
}
