<?php

namespace App\Http\Requests;

use App\Enums\ReminderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'type' => ['required', new Enum(ReminderType::class)],
            'notify_before_days' => ['required', 'integer', 'min:0', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription wajib dipilih.',
            'type.required' => 'Jenis pengingat wajib ditentukan.',
            'notify_before_days.required' => 'Jumlah hari sebelum jatuh tempo wajib diisi.',
        ];
    }
}
