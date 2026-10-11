<?php

namespace Tests\Unit;

use App\Enums\BillingPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use App\Services\DashboardService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Pengujian unit kalkulasi pengeluaran dashboard (TC-08 / UC04 / FR-005).
 * Repository diganti tiruan sehingga hanya logika DashboardService yang diuji, tanpa database.
 */
class DashboardServiceTest extends TestCase
{
    /**
     * Buat langganan aktif di memori tanpa menyimpannya ke database.
     */
    private function makeSubscription(float $price, BillingPeriod $period): Subscription
    {
        return new Subscription([
            'name' => 'Langganan',
            'price' => $price,
            'billing_period' => $period,
            'status' => SubscriptionStatus::ACTIVE,
        ]);
    }

    /**
     * Siapkan DashboardService dengan repository tiruan yang mengembalikan langganan aktif tertentu.
     */
    private function makeService(array $activeSubscriptions, int $expectedCalls = 1): DashboardService
    {
        $repository = $this->createMock(SubscriptionRepository::class);
        $repository->expects($this->exactly($expectedCalls))
            ->method('getActiveByUser')
            ->with(1)
            ->willReturn(new Collection($activeSubscriptions));
        $repository->method('getUpcomingByUser')->willReturn(new Collection());

        return new DashboardService($repository);
    }

    /**
     * TC-08: Rp 100.000 bulanan + Rp 1.200.000 tahunan menghasilkan Rp 200.000 per bulan.
     */
    public function test_monthly_expense_normalizes_yearly_subscriptions(): void
    {
        $service = $this->makeService([
            $this->makeSubscription(100000, BillingPeriod::MONTHLY),
            $this->makeSubscription(1200000, BillingPeriod::YEARLY),
        ]);

        $this->assertSame(200000.0, $service->calculateMonthlyExpense(1));
    }

    /**
     * Data yang sama menghasilkan Rp 2.400.000 per tahun.
     */
    public function test_yearly_expense_normalizes_monthly_subscriptions(): void
    {
        $service = $this->makeService([
            $this->makeSubscription(100000, BillingPeriod::MONTHLY),
            $this->makeSubscription(1200000, BillingPeriod::YEARLY),
        ]);

        $this->assertSame(2400000.0, $service->calculateYearlyExpense(1));
    }

    /**
     * Pengguna tanpa langganan aktif memiliki pengeluaran nol.
     */
    public function test_expenses_are_zero_without_active_subscriptions(): void
    {
        $service = $this->makeService([], expectedCalls: 2);

        $this->assertSame(0.0, $service->calculateMonthlyExpense(1));
        $this->assertSame(0.0, $service->calculateYearlyExpense(1));
    }

    /**
     * Ringkasan dashboard berisi total dan pengeluaran yang benar dengan satu kali pengambilan data.
     */
    public function test_summary_fetches_active_subscriptions_once(): void
    {
        $service = $this->makeService([
            $this->makeSubscription(100000, BillingPeriod::MONTHLY),
            $this->makeSubscription(1200000, BillingPeriod::YEARLY),
        ], expectedCalls: 1);

        $summary = $service->getSummary(1);

        $this->assertSame(2, $summary['total_active']);
        $this->assertSame(200000.0, $summary['monthly_expense']);
        $this->assertSame(2400000.0, $summary['yearly_expense']);
        $this->assertCount(0, $summary['upcoming_payments']);
    }
}
