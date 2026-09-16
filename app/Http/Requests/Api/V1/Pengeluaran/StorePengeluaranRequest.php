<?php

namespace App\Http\Requests\Api\V1\Pengeluaran;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_pengeluaran' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'tanggal' => ['required', 'date'],
            'bukti' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_pengeluaran.required' => 'Kategori pengeluaran wajib diisi.',
            'deskripsi.required' => 'Deskripsi pengeluaran wajib diisi.',
            'jumlah.required' => 'Nominal pengeluaran wajib diisi.',
            'tanggal.required' => 'Tanggal pengeluaran wajib diisi.',
        ];
    }
}
