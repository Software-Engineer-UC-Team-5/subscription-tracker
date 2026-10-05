<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
    case READ = 'READ';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pengiriman',
            self::SENT => 'Terkirim',
            self::FAILED => 'Gagal Terkirim',
            self::READ => 'Telah Dibaca',
        };
    }
}
