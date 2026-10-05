<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengujian halaman login dapat diakses tamu (guest).
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Pengujian halaman register dapat diakses tamu.
     */
    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Pengujian pengguna dapat login dengan kredensial valid.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Pengujian pengguna dapat mendaftar akun baru.
     */
    public function test_user_can_register_new_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
        $this->assertAuthenticated();
    }

    /**
     * Pengujian pengguna terdaftar dapat mengakses dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Pengujian seluruh rute halaman utama dapat diakses tanpa galat.
     */
    public function test_authenticated_user_can_access_all_main_pages(): void
    {
        $user = User::factory()->create();

        $routes = [
            '/subscriptions',
            '/categories',
            '/payment-methods',
            '/reminders',
            '/notifications',
            '/activity-logs',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Pengujian pengguna dapat logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Pengujian pengguna dapat menambah langganan beserta masa free trial secara atomik (UC05, UC10).
     */
    public function test_user_can_create_subscription_with_free_trial(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/subscriptions', [
            'name' => 'Netflix Premium UHD',
            'price' => 186000,
            'currency' => 'IDR',
            'billing_period' => 'MONTHLY',
            'next_payment_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'TRIAL',
            'is_free_trial' => 1,
            'trial_start_date' => now()->format('Y-m-d'),
            'trial_end_date' => now()->addDays(14)->format('Y-m-d'),
            'cancel_before_days' => 2,
        ]);

        $response->assertRedirect('/subscriptions');
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'name' => 'Netflix Premium UHD',
            'is_free_trial' => 1,
        ]);
        $this->assertDatabaseHas('free_trials', [
            'cancel_before_days' => 2,
            'is_converted' => 0,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'CREATE',
            'entity' => 'Subscription',
        ]);
    }

    /**
     * Pengujian keamanan isolasi data: Pengguna tidak boleh mengakses subscription milik pengguna lain (NFR-004).
     */
    public function test_user_cannot_access_other_users_subscription(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $subscriptionA = \App\Models\Subscription::create([
            'user_id' => $userA->id,
            'name' => 'Private Sub A',
            'price' => 50000,
            'currency' => 'IDR',
            'billing_period' => \App\Enums\BillingPeriod::MONTHLY,
            'next_payment_date' => now()->addDays(10)->toDateString(),
            'status' => \App\Enums\SubscriptionStatus::ACTIVE,
        ]);

        $response = $this->actingAs($userB)->get("/subscriptions/{$subscriptionA->id}");
        $response->assertStatus(403);
    }

    /**
     * Pengujian indikator bubble badge notifikasi belum dibaca (UC12 / FR-006).
     */
    public function test_unread_notifications_badge_is_rendered_and_updated_properly(): void
    {
        $user = User::factory()->create();

        // 1. Awalnya belum ada notifikasi, unread count = 0
        $this->assertEquals(0, $user->unreadNotificationsCount());

        $responseNoBadge = $this->actingAs($user)->get('/dashboard');
        $responseNoBadge->assertStatus(200);
        $responseNoBadge->assertDontSee('bg-rose-500');

        // 2. Buat notifikasi baru untuk pengguna dengan status SENT
        $notification = \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => \App\Enums\NotificationType::PAYMENT_REMINDER,
            'title' => 'Tagihan Segera Tiba',
            'message' => 'Layanan Spotify Family akan jatuh tempo 3 hari lagi.',
            'status' => \App\Enums\NotificationStatus::SENT,
        ]);

        $this->assertEquals(1, $user->unreadNotificationsCount());

        // 3. Request dashboard sekarang harus menampilkan badge angka 1
        $responseWithBadge = $this->actingAs($user)->get('/dashboard');
        $responseWithBadge->assertStatus(200);
        $responseWithBadge->assertSee('bg-rose-500');

        // 4. Tandai notifikasi sebagai dibaca (PATCH /notifications/{id}/read)
        $this->actingAs($user)->patch("/notifications/{$notification->id}/read");

        $this->assertEquals(0, $user->unreadNotificationsCount());
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'status' => \App\Enums\NotificationStatus::READ->value,
        ]);
    }
}
