<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Izinkan pendaftaran oleh tamu; akses dibatasi middleware guest pada rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi nama, email unik, dan kata sandi terkonfirmasi untuk akun baru.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
