<?php

namespace App\Enums;

use Carbon\Carbon;

enum BillingPeriod: string
{
    case DAILY = 'DAILY';
    case WEEKLY = 'WEEKLY';
    case MONTHLY = 'MONTHLY';
    case QUARTERLY = 'QUARTERLY';
    case YEARLY = 'YEARLY';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Harian',
            self::WEEKLY => 'Mingguan',
            self::MONTHLY => 'Bulanan',
            self::QUARTERLY => '3 Bulanan (Quarterly)',
            self::YEARLY => 'Tahunan',
        };
    }

    /**
     * Hitung tanggal tagihan berikutnya satu periode setelah tanggal yang diberikan.
     * NoOverflow menjaga 31 Jan menjadi 28/29 Feb, bukan 2/3 Maret.
     */
    public function nextDateAfter(Carbon $date): Carbon
    {
        $date = $date->copy();

        return match ($this) {
            self::DAILY => $date->addDay(),
            self::WEEKLY => $date->addWeek(),
            self::MONTHLY => $date->addMonthNoOverflow(),
            self::QUARTERLY => $date->addMonthsNoOverflow(3),
            self::YEARLY => $date->addYearNoOverflow(),
        };
    }

    /**
     * Hitung bobot estimasi pengeluaran per bulan.
     */
    public function toMonthlyCost(float $price): float
    {
        return match ($this) {
            self::DAILY => $price * 30,
            self::WEEKLY => ($price * 52) / 12,
            self::MONTHLY => $price,
            self::QUARTERLY => $price / 3,
            self::YEARLY => $price / 12,
        };
    }

    /**
     * Hitung bobot estimasi pengeluaran per tahun.
     */
    public function toYearlyCost(float $price): float
    {
        return match ($this) {
            self::DAILY => $price * 365,
            self::WEEKLY => $price * 52,
            self::MONTHLY => $price * 12,
            self::QUARTERLY => $price * 4,
            self::YEARLY => $price,
        };
    }
}
