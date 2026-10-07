<?php

namespace App\Models;

use App\Enums\ReminderType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'type',
        'notify_before_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReminderType::class,
            'notify_before_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Domain method: Aktifkan pengingat.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Domain method: Nonaktifkan pengingat.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Domain method: Dapatkan tanggal pemicu pengingat (H minus notify_before_days).
     */
    public function getTriggerDate(): ?Carbon
    {
        $subscription = $this->subscription;
        if (!$subscription) {
            return null;
        }

        if ($this->type === ReminderType::PAYMENT_DUE && $subscription->next_payment_date) {
            return $subscription->next_payment_date->copy()->subDays($this->notify_before_days)->startOfDay();
        }

        if ($this->type === ReminderType::FREE_TRIAL_END && $subscription->freeTrial?->end_date) {
            return $subscription->freeTrial->end_date->copy()->subDays($this->notify_before_days)->startOfDay();
        }

        return null;
    }

    /**
     * Domain method: Cek apakah pengingat sudah jatuh tempo pada tanggal tertentu (default: hari ini).
     */
    public function isDue(?Carbon $onDate = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = ($onDate ?? Carbon::today())->copy()->startOfDay();
        $subscription = $this->subscription;

        if (!$subscription) {
            return false;
        }

        $triggerDate = $this->getTriggerDate();
        if (!$triggerDate) {
            return false;
        }

        // Jika pemicu adalah tagihan jatuh tempo
        if ($this->type === ReminderType::PAYMENT_DUE && $subscription->next_payment_date) {
            $dueDate = $subscription->next_payment_date->copy()->endOfDay();
            return $today->gte($triggerDate) && $today->lte($dueDate);
        }

        // Jika pemicu adalah berakhirnya masa free trial
        if ($this->type === ReminderType::FREE_TRIAL_END && $subscription->freeTrial?->end_date) {
            $dueDate = $subscription->freeTrial->end_date->copy()->endOfDay();
            return $today->gte($triggerDate) && $today->lte($dueDate);
        }

        return false;
    }
}

