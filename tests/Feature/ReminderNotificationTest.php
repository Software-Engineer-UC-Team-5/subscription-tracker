<?php

namespace Tests\Feature;

use App\Enums\BillingPeriod;
use App\Enums\NotificationStatus;
use App\Enums\ReminderType;
use App\Enums\SubscriptionStatus;
use App\Mail\SubscriptionReminderMail;
use App\Models\Notification;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Memastikan pengingat H-3 hanya mengirimkan notifikasi 1 kali per siklus penagihan,
     * tidak mengirim ulang di hari H-2 dan H-1 (mengatasi spam harian).
     */
    public function test_reminder_sends_notification_only_once_per_billing_cycle(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'user@example.com']);
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Spotify Premium',
            'price' => 54990,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => '2026-10-10',
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $reminder = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        $service = app(NotificationService::class);

        // Hari H-3 (2026-10-07)
        Carbon::setTestNow('2026-10-07 08:00:00');
        $processedDay3 = $service->checkDueReminders();
        $this->assertCount(1, $processedDay3);
        $this->assertEquals(1, Notification::where('reminder_id', $reminder->id)->count());
        Mail::assertSent(SubscriptionReminderMail::class, 1);

        // Hari H-2 (2026-10-08): Tidak boleh kirim ulang
        Carbon::setTestNow('2026-10-08 08:00:00');
        $processedDay2 = $service->checkDueReminders();
        $this->assertCount(0, $processedDay2);
        $this->assertEquals(1, Notification::where('reminder_id', $reminder->id)->count());
        Mail::assertSent(SubscriptionReminderMail::class, 1);

        // Hari H-1 (2026-10-09): Tidak boleh kirim ulang
        Carbon::setTestNow('2026-10-09 08:00:00');
        $processedDay1 = $service->checkDueReminders();
        $this->assertCount(0, $processedDay1);
        $this->assertEquals(1, Notification::where('reminder_id', $reminder->id)->count());
        Mail::assertSent(SubscriptionReminderMail::class, 1);
    }

    /**
     * Memastikan jika ada beberapa pengingat (contoh: H-3 dan H-1) pada langganan yang sama,
     * masing-masing pengingat terpicu tepat pada jadwalnya tanpa memicu duplikasi.
     */
    public function test_multiple_reminders_trigger_independently_without_duplication(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Netflix 4K',
            'price' => 186000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => '2026-10-10',
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $reminderH3 = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        $reminderH1 = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 1,
            'is_active' => true,
        ]);

        $service = app(NotificationService::class);

        // Hari H-3 (2026-10-07): Hanya reminder H-3 yang terpicu
        Carbon::setTestNow('2026-10-07 09:00:00');
        $processedH3 = $service->checkDueReminders();
        $this->assertCount(1, $processedH3);
        $this->assertEquals($reminderH3->id, $processedH3[0]->reminder_id);
        Mail::assertSent(SubscriptionReminderMail::class, 1);

        // Hari H-2 (2026-10-08): Tidak ada yang terpicu
        Carbon::setTestNow('2026-10-08 09:00:00');
        $processedH2 = $service->checkDueReminders();
        $this->assertCount(0, $processedH2);

        // Hari H-1 (2026-10-09): Hanya reminder H-1 yang terpicu (tepat 1 email baru)
        Carbon::setTestNow('2026-10-09 09:00:00');
        $processedH1 = $service->checkDueReminders();
        $this->assertCount(1, $processedH1);
        $this->assertEquals($reminderH1->id, $processedH1[0]->reminder_id);
        Mail::assertSent(SubscriptionReminderMail::class, 2);
    }

    /**
     * Memastikan jika server/cron sempat mati saat hari H-3, pengingat tetap melakukan
     * catch-up pengiriman saat aktif di hari H-2, lalu berhenti mengirim di H-1.
     */
    public function test_server_downtime_recovery_triggers_catch_up_once(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'name' => 'GitHub Copilot',
            'price' => 150000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => '2026-10-10',
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $reminder = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        $service = app(NotificationService::class);

        // Asumsikan server mati di 2026-10-07 (H-3), dan scheduler baru jalan di 2026-10-08 (H-2)
        Carbon::setTestNow('2026-10-08 10:00:00');
        $processedCatchUp = $service->checkDueReminders();
        $this->assertCount(1, $processedCatchUp);
        Mail::assertSent(SubscriptionReminderMail::class, 1);

        // Hari berikutnya H-1 (2026-10-09): Tidak boleh kirim ulang
        Carbon::setTestNow('2026-10-09 10:00:00');
        $processedNextDay = $service->checkDueReminders();
        $this->assertCount(0, $processedNextDay);
        Mail::assertSent(SubscriptionReminderMail::class, 1);
    }

    /**
     * Memastikan validasi menolak konfigurasi notify_before_days yang melebihi frekuensi langganan
     * (misalnya: langganan harian tidak boleh dipasang H-3).
     */
    public function test_validation_prevents_excessive_reminder_days_for_daily_subscription(): void
    {
        $user = User::factory()->create();

        $dailySub = Subscription::create([
            'user_id' => $user->id,
            'name' => 'Daily News Pass',
            'price' => 5000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::DAILY,
            'next_payment_date' => now()->addDay()->toDateString(),
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        // Coba pasang H-3 pada langganan harian -> harus ditolak
        $responseInvalid = $this->actingAs($user)->post('/reminders', [
            'subscription_id' => $dailySub->id,
            'type' => ReminderType::PAYMENT_DUE->value,
            'notify_before_days' => 3,
            'is_active' => 1,
        ]);

        $responseInvalid->assertSessionHasErrors('notify_before_days');
        $this->assertDatabaseMissing('reminders', [
            'subscription_id' => $dailySub->id,
            'notify_before_days' => 3,
        ]);

        // Pasang H-1 pada langganan harian -> valid
        $responseValid = $this->actingAs($user)->post('/reminders', [
            'subscription_id' => $dailySub->id,
            'type' => ReminderType::PAYMENT_DUE->value,
            'notify_before_days' => 1,
            'is_active' => 1,
        ]);

        $responseValid->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reminders', [
            'subscription_id' => $dailySub->id,
            'notify_before_days' => 1,
        ]);
    }
}
