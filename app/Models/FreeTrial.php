<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeTrial extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'start_date',
        'end_date',
        'cancel_before_days',
        'is_converted',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'cancel_before_days' => 'integer',
            'is_converted' => 'boolean',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Domain method: Cek apakah masa percobaan gratis sudah berakhir.
     */
    public function isExpired(): bool
    {
        return Carbon::today()->isAfter($this->end_date);
    }

    /**
     * Domain method: Tandai bahwa masa free trial berhasil dikonversi ke langganan berbayar.
     */
    public function markAsConverted(): void
    {
        $this->update(['is_converted' => true]);
    }
}
