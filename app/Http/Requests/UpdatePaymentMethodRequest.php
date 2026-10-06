<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    /**
     * Batasi pembaruan pada metode pembayaran milik pengguna yang sedang login.
     */
    public function authorize(): bool
    {
        return $this->route('payment_method')->user_id === $this->user()->id;
    }

    /**
     * Validasi nama agar unik per pengguna dengan mengecualikan data yang sedang diedit.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods')->where('user_id', $this->user()->id)
                    ->ignore($this->route('payment_method')),
            ],
        ];
    }

    /**
     * Sediakan pesan validasi berbahasa Indonesia untuk formulir edit.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama metode pembayaran wajib diisi.',
            'name.string' => 'Nama metode pembayaran harus berupa teks.',
            'name.max' => 'Nama metode pembayaran maksimal 100 karakter.',
            'name.unique' => 'Nama metode pembayaran sudah digunakan di akun Anda.',
        ];
    }
}
