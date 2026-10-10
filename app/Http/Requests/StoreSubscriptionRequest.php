<?php

namespace App\Http\Requests;

use App\Enums\BillingPeriod;
use App\Enums\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'billing_period' => ['required', new Enum(BillingPeriod::class)],
            'next_payment_date' => ['required', 'date'],
            'status' => ['nullable', new Enum(SubscriptionStatus::class)],
            // Hanya kategori dan metode bayar milik user sendiri yang boleh dipasang
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
            'payment_method_id' => ['nullable', Rule::exists('payment_methods', 'id')->where('user_id', $this->user()->id)],
            'is_free_trial' => ['nullable', 'boolean'],
            'trial_start_date' => ['nullable', 'date'],
            'trial_end_date' => ['nullable', 'date', 'after_or_equal:trial_start_date'],
            'cancel_before_days' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'payment_method_id.exists' => 'Metode pembayaran yang dipilih tidak valid.',
            'name.required' => 'Nama langganan wajib diisi.',
            'price.required' => 'Biaya langganan wajib diisi.',
            'billing_period.required' => 'Periode pembayaran wajib dipilih.',
            'next_payment_date.required' => 'Tanggal pembayaran berikutnya wajib diisi.',
        ];
    }
}
