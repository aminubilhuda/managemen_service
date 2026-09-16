<?php

namespace App\Http\Requests\Api\V1\Produk;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori_produk,id'],
            'kode_produk' => ['required', 'string', 'max:50', 'unique:produk,kode_produk'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:sparepart,jasa'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'harga_modal' => ['required', 'numeric', 'min:0'],
            'garansi_hari' => ['nullable', 'integer', 'min:0'],
            'stok' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori produk wajib dipilih.',
            'kode_produk.required' => 'Kode produk / barcode wajib diisi.',
            'kode_produk.unique' => 'Kode produk sudah digunakan.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'tipe.required' => 'Tipe produk wajib dipilih (sparepart/jasa).',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_modal.required' => 'Harga modal wajib diisi.',
        ];
    }
}
