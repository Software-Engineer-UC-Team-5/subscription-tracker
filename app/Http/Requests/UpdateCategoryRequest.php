<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends StoreCategoryRequest
{
    /**
     * Batasi pembaruan pada kategori milik pengguna yang sedang login.
     */
    public function authorize(): bool
    {
        return $this->route('category')->user_id === $this->user()->id;
    }

    /**
     * Gunakan validasi tambah dengan mengecualikan kategori yang sedang diedit dari aturan unik.
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['name'] = [
            'required',
            'string',
            'max:100',
            Rule::unique('categories')->where('user_id', $this->user()->id)->ignore($this->route('category')),
        ];

        return $rules;
    }
}
