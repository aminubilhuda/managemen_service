<?php

namespace App\Http\Requests\Api\V1\Tiket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['nullable', 'exists:pelanggan,id'],
            'nama_pelanggan' => ['required_without:pelanggan_id', 'nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'perangkat' => ['required', 'string', 'max:255'],
            'imei_sn' => ['nullable', 'string', 'max:100'],
            'kelengkapan' => ['nullable', 'string', 'max:255'],
            'keluhan' => ['required', 'string'],
            'kondisi_awal' => ['nullable', 'string'],
            'teknisi_id' => ['nullable', 'exists:users,id'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
            'estimasi_selesai' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'nama_pelanggan.required_without' => 'Nama pelanggan wajib diisi jika belum memilih pelanggan terdaftar.',
            'perangkat.required' => 'Nama atau tipe perangkat wajib diisi.',
            'keluhan.required' => 'Keluhan kerusakan wajib diisi.',
            'teknisi_id.exists' => 'Teknisi yang dipilih tidak valid.',
            'estimasi_biaya.numeric' => 'Estimasi biaya harus berupa angka.',
            'estimasi_selesai.date' => 'Format tanggal estimasi selesai tidak valid.',
        ];
    }
}
