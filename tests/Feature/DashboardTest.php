<?php

namespace Tests\Feature;

use App\Enums\BillingPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian deteksi tagihan mendekati jatuh tempo pada dashboard (TC-09 / UC04 / FR-005).
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeSubscription(User $user, string $name, string $date, SubscriptionStatus $status = SubscriptionStatus::ACTIVE): Subscription
    {
        return Subscription::create([
            'user_id' => $user->id,
            'name' => $name,
            'price' => 50000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => $date,
            'status' => $status,
        ]);
    }

    /**
     * TC-09: Hanya tagihan aktif milik pengguna dalam 7 hari ke depan yang terdeteksi.
     */
    public function test_upcoming_payments_only_include_own_active_subscriptions_within_seven_days(): void
    {
        Carbon::setTestNow('2026-10-11 08:00:00');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->makeSubscription($user, 'Netflix H+3', '2026-10-14');
        $this->makeSubscription($user, 'Spotify H+7', '2026-10-18');
        $this->makeSubscription($user, 'Canva H+8', '2026-10-19');
        $this->makeSubscription($user, 'Disney Dibatalkan', '2026-10-14', SubscriptionStatus::CANCELLED);
        $this->makeSubscription($otherUser, 'Milik Orang Lain', '2026-10-14');

        $upcoming = app(DashboardService::class)->getUpcomingPayments($user->id);

        $this->assertSame(['Netflix H+3', 'Spotify H+7'], $upcoming->pluck('name')->all());
    }
}
