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
            'notify_before_days.min' => 'Jumlah hari sebelum jatuh tempo minimal 0 (hari H).',
            'notify_before_days.max' => 'Jumlah hari sebelum jatuh tempo maksimal 30 hari.',
        ];
    }

    /**
     * Validasi lanjutan untuk memeriksa kepemilikan dan batas hari pengingat berdasarkan siklus penagihan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subscriptionId = $this->input('subscription_id');
            $notifyBeforeDays = (int) $this->input('notify_before_days');
            $type = $this->input('type');

            if (!$subscriptionId) {
                return;
            }

            $subscription = \App\Models\Subscription::with('freeTrial')->find($subscriptionId);
            if (!$subscription) {
                return;
            }

            // Validasi jika pengingat untuk masa Free Trial
            if ($type === ReminderType::FREE_TRIAL_END->value) {
                if (!$subscription->is_free_trial || !$subscription->freeTrial) {
                    $validator->errors()->add('type', 'Subscription ini tidak memiliki masa free trial aktif.');
                }
            }

            // Validasi batas maksimum hari pengingat berdasarkan frekuensi penagihan
            if ($type === ReminderType::PAYMENT_DUE->value && $subscription->billing_period) {
                $maxDays = match ($subscription->billing_period) {
                    \App\Enums\BillingPeriod::DAILY => 1,
                    \App\Enums\BillingPeriod::WEEKLY => 6,
                    \App\Enums\BillingPeriod::MONTHLY => 28,
                    \App\Enums\BillingPeriod::QUARTERLY => 30,
                    \App\Enums\BillingPeriod::YEARLY => 30,
                };

                if ($notifyBeforeDays > $maxDays) {
                    $periodLabel = $subscription->billing_period->label();
                    $validator->errors()->add(
                        'notify_before_days',
                        "Untuk langganan {$periodLabel}, waktu pengingat maksimal {$maxDays} hari sebelum jatuh tempo."
                    );
                }
            }
        });
    }
}
