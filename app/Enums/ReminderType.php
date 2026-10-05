<?php

namespace App\Enums;

enum ReminderType: string
{
    case PAYMENT_DUE = 'PAYMENT_DUE';
    case FREE_TRIAL_END = 'FREE_TRIAL_END';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::PAYMENT_DUE => 'Jatuh Tempo Pembayaran',
            self::FREE_TRIAL_END => 'Berakhirnya Masa Free Trial',
            self::OTHER => 'Lainnya',
        };
    }
}
