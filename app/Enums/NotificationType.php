<?php

namespace App\Enums;

enum NotificationType: string
{
    case PAYMENT_REMINDER = 'PAYMENT_REMINDER';
    case FREE_TRIAL_REMINDER = 'FREE_TRIAL_REMINDER';
    case SYSTEM = 'SYSTEM';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::PAYMENT_REMINDER => 'Pengingat Pembayaran',
            self::FREE_TRIAL_REMINDER => 'Pengingat Free Trial',
            self::SYSTEM => 'Pemberitahuan Sistem',
            self::OTHER => 'Lainnya',
        };
    }
}
