<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Izinkan penambahan kategori setelah autentikasi melalui middleware rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi nama unik per pengguna, pilihan ikon, dan warna heksadesimal.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->where('user_id', $this->user()->id)],
            'icon' => ['nullable', 'string', Rule::in(array_keys(Category::ICONS))],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    /**
     * Sediakan pesan validasi berbahasa Indonesia untuk formulir kategori.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'name.unique' => 'Nama kategori sudah digunakan di akun Anda.',
            'icon.string' => 'Ikon kategori harus berupa teks.',
            'icon.in' => 'Pilih ikon kategori yang tersedia.',
            'color.string' => 'Warna kategori harus berupa teks.',
            'color.regex' => 'Warna kategori harus menggunakan format #RRGGBB.',
        ];
    }
}
