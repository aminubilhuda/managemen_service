<?php

namespace App\Http\Requests\Api\V1\Produk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produkId = $this->route('produk') ?? $this->route('id');
        if (is_object($produkId)) {
            $produkId = $produkId->id;
        }

        return [
            'kategori_id' => ['sometimes', 'required', 'exists:kategori_produk,id'],
            'kode_produk' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('produk', 'kode_produk')->ignore($produkId)],
            'nama_produk' => ['sometimes', 'required', 'string', 'max:255'],
            'tipe' => ['sometimes', 'required', 'in:sparepart,jasa'],
            'harga_jual' => ['sometimes', 'required', 'numeric', 'min:0'],
            'harga_modal' => ['sometimes', 'required', 'numeric', 'min:0'],
            'garansi_hari' => ['nullable', 'integer', 'min:0'],
            'stok' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_produk.unique' => 'Kode produk sudah digunakan.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_modal.numeric' => 'Harga modal harus berupa angka.',
        ];
    }
}
