<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository
    ) {}

    /**
     * Ambil seluruh langganan aktif milik user (UC04 / FR-005).
     */
    public function getActiveSubscriptions(int $userId): Collection
    {
        return $this->subscriptionRepository->getActiveByUser($userId);
    }

    /**
     * Ambil daftar tagihan yang akan jatuh tempo dalam rentang hari tertentu (default 7 hari).
     */
    public function getUpcomingPayments(int $userId, int $days = 7): Collection
    {
        return $this->subscriptionRepository->getUpcomingByUser($userId, $days);
    }

    /**
     * Hitung estimasi total pengeluaran langganan aktif per bulan.
     * Mengonversi seluruh periode (Harian, Mingguan, Bulanan, Quarterly, Tahunan) ke bobot bulanan.
     */
    public function calculateMonthlyExpense(int $userId): float
    {
        $activeSubs = $this->getActiveSubscriptions($userId);

        $total = 0.0;
        foreach ($activeSubs as $sub) {
            $total += $sub->getMonthlyCost();
        }

        return round($total, 2);
    }

    /**
     * Hitung estimasi total pengeluaran langganan aktif per tahun.
     */
    public function calculateYearlyExpense(int $userId): float
    {
        $activeSubs = $this->getActiveSubscriptions($userId);

        $total = 0.0;
        foreach ($activeSubs as $sub) {
            $total += $sub->getAnnualCost();
        }

        return round($total, 2);
    }

    /**
     * Ambil seluruh ringkasan statistik untuk dashboard dalam satu panggilan.
     */
    public function getSummary(int $userId): array
    {
        $activeSubs = $this->getActiveSubscriptions($userId);

        return [
            'total_active' => $activeSubs->count(),
            'monthly_expense' => $this->calculateMonthlyExpense($userId),
            'yearly_expense' => $this->calculateYearlyExpense($userId),
            'upcoming_payments' => $this->getUpcomingPayments($userId, 7),
        ];
    }
}
