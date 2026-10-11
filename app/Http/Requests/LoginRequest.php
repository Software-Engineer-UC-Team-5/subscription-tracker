<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Izinkan login oleh tamu; akses dibatasi middleware guest pada rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi email dan kata sandi untuk proses login.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}
