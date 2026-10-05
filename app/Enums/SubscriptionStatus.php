<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case TRIAL = 'TRIAL';
    case ACTIVE = 'ACTIVE';
    case PAUSED = 'PAUSED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Masa Uji Coba',
            self::ACTIVE => 'Aktif',
            self::PAUSED => 'Dijeda',
            self::CANCELLED => 'Dibatalkan',
            self::EXPIRED => 'Kadaluarsa',
        };
    }
}
