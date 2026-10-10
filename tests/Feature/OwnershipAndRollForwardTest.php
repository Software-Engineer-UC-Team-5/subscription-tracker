<?php

namespace Tests\Feature;

use App\Enums\BillingPeriod;
use App\Enums\ReminderType;
use App\Enums\SubscriptionStatus;
use App\Models\Category;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OwnershipAndRollForwardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeSubscription(User $user, array $overrides = []): Subscription
    {
        return Subscription::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Netflix',
            'price' => 186000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => '2026-10-10',
            'status' => SubscriptionStatus::ACTIVE,
        ], $overrides));
    }

    /**
     * User tidak boleh membuat pengingat untuk langganan milik user lain.
     */
    public function test_cannot_create_reminder_for_other_users_subscription(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $subscription = $this->makeSubscription($owner);

        $this->actingAs($intruder)->post('/reminders', [
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE->value,
            'notify_before_days' => 3,
        ])->assertSessionHasErrors('subscription_id');

        $this->assertEquals(0, Reminder::count());
    }

    /**
     * User tidak boleh memasang kategori atau metode bayar milik user lain ke langganannya.
     */
    public function test_cannot_attach_other_users_category_or_payment_method(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $category = Category::create(['user_id' => $owner->id, 'name' => 'Hiburan']);
        $paymentMethod = PaymentMethod::create(['user_id' => $owner->id, 'name' => 'GoPay']);

        $payload = [
            'name' => 'Spotify',
            'price' => 54990,
            'billing_period' => BillingPeriod::MONTHLY->value,
            'next_payment_date' => '2026-11-01',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
        ];

        $this->actingAs($intruder)->post('/subscriptions', $payload)
            ->assertSessionHasErrors(['category_id', 'payment_method_id']);
        $this->assertEquals(0, Subscription::count());

        $subscription = $this->makeSubscription($intruder);
        $this->actingAs($intruder)->put("/subscriptions/{$subscription->id}", $payload + ['status' => 'ACTIVE'])
            ->assertSessionHasErrors(['category_id', 'payment_method_id']);
        $this->assertNull($subscription->fresh()->category_id);
    }

    /**
     * Setelah jatuh tempo lewat, tanggal tagihan maju ke siklus berikutnya dan pengingat siklus itu terkirim lagi.
     */
    public function test_past_due_date_rolls_forward_and_next_cycle_reminder_is_sent(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscription = $this->makeSubscription($user);
        $reminder = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        // Siklus Oktober: pengingat H-3 terkirim
        Carbon::setTestNow('2026-10-07 08:00:00');
        $this->artisan('app:check-reminders')->assertSuccessful();
        $this->assertEquals(1, Notification::where('reminder_id', $reminder->id)->count());

        // Sehari setelah jatuh tempo: tanggal maju ke 10 November
        Carbon::setTestNow('2026-10-11 08:00:00');
        $this->artisan('app:check-reminders')->assertSuccessful();
        $this->assertEquals('2026-11-10', $subscription->fresh()->next_payment_date->toDateString());

        // Siklus November: pengingat H-3 terkirim lagi
        Carbon::setTestNow('2026-11-07 08:00:00');
        app(NotificationService::class)->checkDueReminders();
        $this->assertEquals(2, Notification::where('reminder_id', $reminder->id)->count());
    }

    /**
     * Tanggal yang tertinggal beberapa siklus langsung dimajukan ke siklus terdekat; langganan nonaktif tidak disentuh.
     */
    public function test_roll_forward_skips_multiple_cycles_and_ignores_inactive_subscriptions(): void
    {
        Carbon::setTestNow('2026-10-11 08:00:00');

        $user = User::factory()->create();
        $monthEnd = $this->makeSubscription($user, ['next_payment_date' => '2026-01-31']);
        $weekly = $this->makeSubscription($user, ['billing_period' => BillingPeriod::WEEKLY, 'next_payment_date' => '2026-10-01']);
        $cancelled = $this->makeSubscription($user, ['next_payment_date' => '2026-09-01', 'status' => SubscriptionStatus::CANCELLED]);

        $this->artisan('app:check-reminders')->assertSuccessful();

        // 31 Jan -> 28 Feb (tanpa meluap ke Maret) -> ... -> 28 Okt
        $this->assertEquals('2026-10-28', $monthEnd->fresh()->next_payment_date->toDateString());
        $this->assertEquals('2026-10-15', $weekly->fresh()->next_payment_date->toDateString());
        $this->assertEquals('2026-09-01', $cancelled->fresh()->next_payment_date->toDateString());
    }
}
