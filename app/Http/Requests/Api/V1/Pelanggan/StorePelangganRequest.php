<?php

namespace App\Http\Requests\Api\V1\Pelanggan;

use Illuminate\Foundation\Http\FormRequest;

class StorePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
        ];
    }
}
