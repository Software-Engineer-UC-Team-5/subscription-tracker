<?php

namespace Tests\Unit;

use App\Enums\BillingPeriod;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Pengujian unit normalisasi biaya dan perhitungan tanggal tagihan per periode.
 */
class BillingPeriodTest extends TestCase
{
    /**
     * Setiap periode dikonversi ke biaya bulanan dengan bobot yang benar.
     */
    public function test_each_period_converts_to_monthly_cost(): void
    {
        $this->assertSame(30000.0, BillingPeriod::DAILY->toMonthlyCost(1000));
        $this->assertSame(52000.0, BillingPeriod::WEEKLY->toMonthlyCost(12000));
        $this->assertSame(100000.0, BillingPeriod::MONTHLY->toMonthlyCost(100000));
        $this->assertSame(100000.0, BillingPeriod::QUARTERLY->toMonthlyCost(300000));
        $this->assertSame(100000.0, BillingPeriod::YEARLY->toMonthlyCost(1200000));
    }

    /**
     * Setiap periode dikonversi ke biaya tahunan dengan bobot yang benar.
     */
    public function test_each_period_converts_to_yearly_cost(): void
    {
        $this->assertSame(365000.0, BillingPeriod::DAILY->toYearlyCost(1000));
        $this->assertSame(52000.0, BillingPeriod::WEEKLY->toYearlyCost(1000));
        $this->assertSame(1200000.0, BillingPeriod::MONTHLY->toYearlyCost(100000));
        $this->assertSame(1200000.0, BillingPeriod::QUARTERLY->toYearlyCost(300000));
        $this->assertSame(1200000.0, BillingPeriod::YEARLY->toYearlyCost(1200000));
    }

    /**
     * Tanggal 31 tidak meluap ke bulan berikutnya, dan kembali ke 31 bila tanggal asli diberikan.
     */
    public function test_next_date_handles_month_end_with_billing_day(): void
    {
        $jan31 = Carbon::parse('2026-01-31');
        $feb28 = Carbon::parse('2026-02-28');

        $this->assertSame('2026-02-28', BillingPeriod::MONTHLY->nextDateAfter($jan31)->toDateString());
        $this->assertSame('2026-03-28', BillingPeriod::MONTHLY->nextDateAfter($feb28)->toDateString());
        $this->assertSame('2026-03-31', BillingPeriod::MONTHLY->nextDateAfter($feb28, 31)->toDateString());
        $this->assertSame('2026-05-31', BillingPeriod::QUARTERLY->nextDateAfter($feb28, 31)->toDateString());
        $this->assertSame('2026-03-07', BillingPeriod::WEEKLY->nextDateAfter($feb28, 31)->toDateString());
        // Tanggal asal tidak boleh ikut berubah
        $this->assertSame('2026-01-31', $jan31->toDateString());
    }
}
