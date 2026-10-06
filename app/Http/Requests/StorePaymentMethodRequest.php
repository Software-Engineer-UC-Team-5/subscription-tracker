<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentMethodRequest extends FormRequest
{
    /**
     * Izinkan penambahan metode pembayaran setelah autentikasi melalui middleware rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi nama metode pembayaran agar wajib diisi dan unik untuk pengguna yang sedang login.
     */
    public function rules(): array
    {
        return [
            // NFR-004: Hanya nama label alias (misal: "BCA Debit", "Jenius", "GoPay")
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods')->where('user_id', $this->user()->id),
            ],
        ];
    }

    /**
     * Sediakan pesan validasi berbahasa Indonesia untuk formulir tambah.
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
