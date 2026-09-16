<?php

namespace App\Http\Requests\Api\V1\Tiket;

use App\Models\TiketServis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(array_keys(TiketServis::STATUSES))],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status servis wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }
}
