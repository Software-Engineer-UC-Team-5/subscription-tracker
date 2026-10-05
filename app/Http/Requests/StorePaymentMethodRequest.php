<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // NFR-004: Hanya nama label alias (misal: "BCA Debit", "Jenius", "GoPay")
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama metode pembayaran (label/alias) wajib diisi.',
            'name.max' => 'Nama metode pembayaran maksimal 100 karakter.',
        ];
    }
}
