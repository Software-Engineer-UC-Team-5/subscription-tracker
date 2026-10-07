<?php

namespace Database\Seeders;

use App\Enums\BillingPeriod;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Enums\ReminderType;
use App\Enums\SubscriptionStatus;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\FreeTrial;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Reminder;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data simulasi realistis untuk pengujian sistem (UC01-UC12 / FR-001-FR-006).
     */
    public function run(): void
    {
        // Akun Pengguna Demo Utama (Sesuai dokumentasi README.md)
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        // Kategori Default Sistem (user_id = null)
        $catEntertainment = Category::create(['name' => 'Hiburan & Streaming', 'icon' => 'tv', 'color' => '#ef4444', 'user_id' => null]);
        $catProductivity = Category::create(['name' => 'Produktivitas & Kerja', 'icon' => 'briefcase', 'color' => '#3b82f6', 'user_id' => null]);
        $catCloud = Category::create(['name' => 'Penyimpanan Cloud', 'icon' => 'cloud', 'color' => '#10b981', 'user_id' => null]);
        $catEducation = Category::create(['name' => 'Pendidikan & Kursus', 'icon' => 'academic-cap', 'color' => '#f59e0b', 'user_id' => null]);
        $catUtility = Category::create(['name' => 'Utilitas & Tools', 'icon' => 'wrench', 'color' => '#8b5cf6', 'user_id' => null]);

        // Metode Pembayaran Demo (Alias Tanpa Data Sensitif - NFR-004)
        $pmBca = PaymentMethod::create(['user_id' => $user->id, 'name' => 'BCA Debit']);
        $pmJenius = PaymentMethod::create(['user_id' => $user->id, 'name' => 'Jenius Visa']);
        $pmGopay = PaymentMethod::create(['user_id' => $user->id, 'name' => 'GoPay']);

        // Langganan Demo
        // Netflix Premium (Tagihan Mendatang dalam 3 hari)
        $subNetflix = Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catEntertainment->id,
            'payment_method_id' => $pmBca->id,
            'name' => 'Netflix Premium UHD',
            'price' => 186000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => now()->addDays(3)->toDateString(),
            'status' => SubscriptionStatus::ACTIVE,
            'is_free_trial' => false,
        ]);

        // Pengingat untuk Netflix
        Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subNetflix->id,
            'type' => ReminderType::PAYMENT_DUE,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        // Spotify Family
        $subSpotify = Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catEntertainment->id,
            'payment_method_id' => $pmGopay->id,
            'name' => 'Spotify Family Plan',
            'price' => 86900,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => now()->addDays(14)->toDateString(),
            'status' => SubscriptionStatus::ACTIVE,
            'is_free_trial' => false,
        ]);

        // YouTube Premium (Sedang Masa Free Trial - UC10 / FR-004)
        $subYoutube = Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catEntertainment->id,
            'payment_method_id' => $pmJenius->id,
            'name' => 'YouTube Premium',
            'price' => 59000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => now()->addDays(5)->toDateString(),
            'status' => SubscriptionStatus::TRIAL,
            'is_free_trial' => true,
        ]);

        FreeTrial::create([
            'subscription_id' => $subYoutube->id,
            'start_date' => now()->subDays(25)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'cancel_before_days' => 2,
            'is_converted' => false,
        ]);

        $remYoutube = Reminder::create([
            'user_id' => $user->id,
            'subscription_id' => $subYoutube->id,
            'type' => ReminderType::FREE_TRIAL_END,
            'notify_before_days' => 3,
            'is_active' => true,
        ]);

        // Google One 2TB (Billing Tahunan)
        Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catCloud->id,
            'payment_method_id' => $pmBca->id,
            'name' => 'Google One Cloud 2TB',
            'price' => 1350000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::YEARLY,
            'next_payment_date' => now()->addMonths(8)->toDateString(),
            'status' => SubscriptionStatus::ACTIVE,
            'is_free_trial' => false,
        ]);

        // ChatGPT Plus (Billing Bulanan)
        Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catProductivity->id,
            'payment_method_id' => $pmJenius->id,
            'name' => 'OpenAI ChatGPT Plus',
            'price' => 349000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => now()->addDays(22)->toDateString(),
            'status' => SubscriptionStatus::ACTIVE,
            'is_free_trial' => false,
        ]);

        // Disney+ Hotstar (Sudah Berhenti / CANCELLED)
        Subscription::create([
            'user_id' => $user->id,
            'category_id' => $catEntertainment->id,
            'payment_method_id' => $pmGopay->id,
            'name' => 'Disney+ Hotstar',
            'price' => 65000,
            'currency' => 'IDR',
            'billing_period' => BillingPeriod::MONTHLY,
            'next_payment_date' => now()->subDays(5)->toDateString(),
            'status' => SubscriptionStatus::CANCELLED,
            'is_free_trial' => false,
        ]);

        // Notifikasi Simulasi
        Notification::create([
            'user_id' => $user->id,
            'reminder_id' => null,
            'title' => 'Pengingat Tagihan: Netflix Premium UHD',
            'message' => 'Tagihan langganan Netflix Premium UHD sebesar Rp 186.000 akan jatuh tempo dalam 3 hari ke depan.',
            'type' => NotificationType::PAYMENT_REMINDER,
            'status' => NotificationStatus::SENT,
            'sent_at' => now()->subHours(2),
            'read_at' => null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'reminder_id' => $remYoutube->id,
            'title' => 'Pengingat Masa Uji Coba: YouTube Premium',
            'message' => 'Masa uji coba gratis (Free Trial) YouTube Premium Anda akan berakhir pada ' . now()->addDays(5)->format('d M Y') . '. Segera tentukan keputusan Anda sebelum autodebit dimulai.',
            'type' => NotificationType::FREE_TRIAL_REMINDER,
            'status' => NotificationStatus::PENDING,
            'sent_at' => null,
            'read_at' => null,
        ]);

        // Log Aktivitas Pengguna (Audit Trail - NFR-004)
        ActivityLog::record(
            userId: $user->id,
            action: 'LOGIN',
            entity: 'User',
            entityId: $user->id,
            description: 'Pengguna berhasil melakukan login ke aplikasi'
        );

        ActivityLog::record(
            userId: $user->id,
            action: 'CREATE',
            entity: 'Subscription',
            entityId: $subNetflix->id,
            description: "Menambahkan langganan baru: {$subNetflix->name}"
        );

        ActivityLog::record(
            userId: $user->id,
            action: 'CREATE',
            entity: 'Subscription',
            entityId: $subYoutube->id,
            description: "Menambahkan langganan dengan masa Free Trial: {$subYoutube->name}"
        );

        // Kategori demo dan referensi langganannya menggunakan salinan milik akun demo.
        app(CategoryRepository::class)->copyDefaultsForUser($user->id);
    }
}
