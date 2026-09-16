<?php

namespace App\Http\Requests\Api\V1\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tiket_id' => ['nullable', 'exists:tiket_servis,id'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pajak_id' => ['nullable', 'exists:pengaturan_pajak,id'],
            'pajak_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => ['nullable', 'exists:produk,id'],
            'items.*.deskripsi' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.harga_modal_satuan' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Daftar item tagihan minimal 1 baris.',
            'items.*.deskripsi.required' => 'Deskripsi item wajib diisi.',
            'items.*.qty.required' => 'Jumlah (qty) item wajib diisi minimal 1.',
            'items.*.harga_satuan.required' => 'Harga satuan item wajib diisi.',
        ];
    }
}
