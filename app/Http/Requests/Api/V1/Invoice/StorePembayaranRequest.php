<?php

namespace App\Http\Requests\Api\V1\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah_dibayar' => ['required', 'numeric', 'min:1'],
            'metode_bayar' => ['required', 'string', 'in:tunai,transfer,qris,debit,kredit'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_bayar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'jumlah_dibayar.required' => 'Nominal pembayaran wajib diisi.',
            'jumlah_dibayar.min' => 'Nominal pembayaran minimal Rp 1.',
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
            'metode_bayar.in' => 'Metode bayar harus salah satu dari: tunai, transfer, qris, debit, kredit.',
        ];
    }
}
