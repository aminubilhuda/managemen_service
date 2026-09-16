<?php

namespace App\Http\Requests\Api\V1\Tiket;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perangkat' => ['sometimes', 'required', 'string', 'max:255'],
            'imei_sn' => ['nullable', 'string', 'max:100'],
            'kelengkapan' => ['nullable', 'string', 'max:255'],
            'keluhan' => ['sometimes', 'required', 'string'],
            'kondisi_awal' => ['nullable', 'string'],
            'teknisi_id' => ['nullable', 'exists:users,id'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
            'estimasi_selesai' => ['nullable', 'date'],
            'biaya_final' => ['nullable', 'numeric', 'min:0'],
            'garansi_sampai' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'perangkat.required' => 'Nama atau tipe perangkat wajib diisi.',
            'keluhan.required' => 'Keluhan kerusakan wajib diisi.',
            'teknisi_id.exists' => 'Teknisi yang dipilih tidak valid.',
            'estimasi_biaya.numeric' => 'Estimasi biaya harus berupa angka.',
            'biaya_final.numeric' => 'Biaya final harus berupa angka.',
        ];
    }
}
