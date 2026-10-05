<?php

namespace App\Services;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Enums\ReminderType;
use App\Mail\SubscriptionReminderMail;
use App\Models\Notification;
use App\Models\Reminder;
use App\Repositories\NotificationRepository;
use App\Repositories\ReminderRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service Layer: NotificationService
 * Mengelola alur pemrosesan Reminder menjadi Notification hingga pengiriman email (Bab 4.1.2).
 */
class NotificationService
{
    public function __construct(
        protected ReminderRepository $reminderRepository,
        protected NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Memeriksa seluruh Reminder aktif dan menentukan pengingat yang telah mencapai waktu pengiriman (UC12 / FR-006).
     */
    public function checkDueReminders(): array
    {
        $activeReminders = Reminder::with(['subscription.freeTrial', 'user'])
            ->where('is_active', true)
            ->get();

        $processed = [];

        foreach ($activeReminders as $reminder) {
            if ($reminder->isDue()) {
                // Cek apakah notifikasi untuk reminder ini sudah pernah dibuat hari ini (mencegah duplikasi spam)
                $alreadyCreatedToday = Notification::where('reminder_id', $reminder->id)
                    ->whereDate('created_at', today())
                    ->exists();

                if (!$alreadyCreatedToday) {
                    $notification = $this->createNotification($reminder->id);
                    $this->sendNotification($notification);
                    $processed[] = $notification;
                }
            }
        }

        return $processed;
    }

    /**
     * Membuat Notification baru berdasarkan Reminder yang jatuh tempo dengan status awal PENDING.
     */
    public function createNotification(int $reminderId): Notification
    {
        $reminder = Reminder::with(['subscription.freeTrial', 'user'])->findOrFail($reminderId);
        $subscription = $reminder->subscription;

        $title = 'Pengingat Berlangganan: ' . ($subscription->name ?? 'Layanan');
        $type = NotificationType::PAYMENT_REMINDER;
        $message = "Tagihan untuk layanan {$subscription->name} akan jatuh tempo pada " .
            ($subscription->next_payment_date ? $subscription->next_payment_date->format('d M Y') : 'segera') .
            " sebesar Rp " . number_format($subscription->price, 0, ',', '.') . ".";

        if ($reminder->type === ReminderType::FREE_TRIAL_END && $subscription->freeTrial) {
            $type = NotificationType::FREE_TRIAL_REMINDER;
            $title = 'Pengingat Masa Uji Coba: ' . $subscription->name;
            $message = "Masa uji coba gratis (Free Trial) untuk {$subscription->name} akan berakhir pada " .
                $subscription->freeTrial->end_date->format('d M Y') . ". Segera putuskan perpanjangan atau pembatalan.";
        }

        return $this->notificationRepository->create([
            'user_id' => $reminder->user_id,
            'reminder_id' => $reminder->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'status' => NotificationStatus::PENDING,
            'sent_at' => null,
            'read_at' => null,
        ]);
    }

    /**
     * Mengirimkan Notification kepada pengguna via email serta memperbarui status menjadi SENT atau FAILED.
     */
    public function sendNotification(Notification $notification): bool
    {
        try {
            $user = $notification->user;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new SubscriptionReminderMail($notification));
                $notification->markAsSent();
                return true;
            }

            $notification->markAsFailed();
            return false;
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email notifikasi ID {$notification->id}: " . $e->getMessage());
            $notification->markAsFailed();
            return false;
        }
    }
}
