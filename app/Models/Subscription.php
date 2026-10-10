<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'payment_method_id',
        'name',
        'price',
        'currency',
        'billing_period',
        'next_payment_date',
        'status',
        'is_free_trial',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_period' => BillingPeriod::class,
            'status' => SubscriptionStatus::class,
            'next_payment_date' => 'date',
            'is_free_trial' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function freeTrial(): HasOne
    {
        return $this->hasOne(FreeTrial::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * Domain method: Cek apakah pembayaran akan jatuh tempo dalam N hari ke depan.
     */
    public function isUpcoming(int $days = 7): bool
    {
        if (!$this->next_payment_date) {
            return false;
        }

        $today = Carbon::today();
        $target = Carbon::today()->addDays($days);

        return $this->next_payment_date->betweenIncluded($today, $target);
    }

    /**
     * Domain method: Majukan next_payment_date per billing_period sampai tidak lagi lewat dari hari ini.
     * Mengembalikan true jika tanggal berubah.
     */
    public function rollForwardNextPaymentDate(?Carbon $today = null): bool
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        if (!$this->next_payment_date || !$this->billing_period || $this->next_payment_date->gte($today)) {
            return false;
        }

        $date = $this->next_payment_date->copy();
        while ($date->lt($today)) {
            $date = $this->billing_period->nextDateAfter($date);
        }

        return $this->update(['next_payment_date' => $date]);
    }

    /**
     * Domain method: Hitung estimasi biaya tahunan berdasarkan billing_period.
     */
    public function getAnnualCost(): float
    {
        return $this->billing_period->toYearlyCost((float) $this->price);
    }

    /**
     * Domain method: Hitung estimasi biaya bulanan berdasarkan billing_period.
     */
    public function getMonthlyCost(): float
    {
        return $this->billing_period->toMonthlyCost((float) $this->price);
    }
}
