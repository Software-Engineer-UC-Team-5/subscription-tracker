<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\ActivityLog;
use App\Models\FreeTrial;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository
    ) {
    }

    /**
     * Tolak akses dengan status 403 jika langganan bukan milik pengguna.
     */
    public function ensureOwnership(Subscription $subscription, int $userId, string $message): void
    {
        abort_unless($subscription->user_id === $userId, 403, $message);
    }

    /**
     * Ambil data langganan terfilter (FR-003).
     */
    public function getFiltered(int $userId, array $filters = []): Collection
    {
        return $this->subscriptionRepository->getFilteredByUser($userId, $filters);
    }

    /**
     * Buat subscription baru. Jika is_free_trial true, buat juga record free_trials secara atomik (FR-002, FR-004).
     */
    public function createSubscription(int $userId, array $data): Subscription
    {
        return DB::transaction(function () use ($userId, $data) {
            $isFreeTrial = !empty($data['is_free_trial']);

            $subscription = $this->subscriptionRepository->create([
                'user_id' => $userId,
                'category_id' => $data['category_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'name' => $data['name'],
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'IDR',
                'billing_period' => $data['billing_period'],
                'next_payment_date' => $data['next_payment_date'],
                'status' => $data['status'] ?? 'ACTIVE',
                'is_free_trial' => $isFreeTrial,
            ]);

            // Jika ada opsi free trial, simpan masa percobaan gratis
            if ($isFreeTrial && !empty($data['trial_end_date'])) {
                FreeTrial::create([
                    'subscription_id' => $subscription->id,
                    'start_date' => $data['trial_start_date'] ?? now()->toDateString(),
                    'end_date' => $data['trial_end_date'],
                    'cancel_before_days' => $data['cancel_before_days'] ?? 1,
                    'is_converted' => false,
                ]);
            }

            // Catat log aktivitas (audit trail)
            ActivityLog::record(
                userId: $userId,
                action: 'CREATE',
                entity: 'Subscription',
                entityId: $subscription->id,
                description: "Menambahkan langganan baru: {$subscription->name}"
            );

            return $subscription;
        });
    }

    /**
     * Perbarui data langganan (FR-002).
     */
    public function updateSubscription(Subscription $subscription, array $data): bool
    {
        return DB::transaction(function () use ($subscription, $data) {
            // Tanggal tagihan asli hanya diganti jika user benar-benar mengubah tanggalnya,
            // agar 28 Feb hasil pergeseran otomatis tidak menimpa tanggal asli 31.
            if (!empty($data['next_payment_date'])) {
                $newDate = Carbon::parse($data['next_payment_date']);
                if (!$subscription->next_payment_date?->isSameDay($newDate)) {
                    $data['billing_day'] = $newDate->day;
                }
            }

            // Data trial disimpan ke tabel free_trials, bukan ke tabel subscriptions
            $trialFields = ['trial_start_date', 'trial_end_date', 'cancel_before_days'];
            $trialData = Arr::only($data, $trialFields);
            $data = Arr::except($data, $trialFields);

            if ($subscription->freeTrial && $trialData) {
                $subscription->freeTrial->update(array_filter([
                    'start_date' => $trialData['trial_start_date'] ?? null,
                    'end_date' => $trialData['trial_end_date'] ?? null,
                    'cancel_before_days' => $trialData['cancel_before_days'] ?? null,
                ], fn ($value) => $value !== null));
            }

            $wasTrial = $subscription->status === SubscriptionStatus::TRIAL;

            $updated = $this->subscriptionRepository->update($subscription, $data);

            if ($updated) {
                ActivityLog::record(
                    userId: $subscription->user_id,
                    action: 'UPDATE',
                    entity: 'Subscription',
                    entityId: $subscription->id,
                    description: "Memperbarui langganan: {$subscription->name}"
                );
            }

            // Status Masa Uji Coba menjadi Aktif berarti trial dilanjutkan sebagai langganan berbayar
            $freeTrial = $subscription->freeTrial;
            if ($updated && $wasTrial && $subscription->status === SubscriptionStatus::ACTIVE
                && $freeTrial && !$freeTrial->is_converted) {
                $freeTrial->markAsConverted();

                ActivityLog::record(
                    userId: $subscription->user_id,
                    action: 'UPDATE',
                    entity: 'FreeTrial',
                    entityId: $freeTrial->id,
                    description: "Masa uji coba {$subscription->name} dikonversi menjadi langganan berbayar"
                );
            }

            return $updated;
        });
    }

    /**
     * Majukan next_payment_date langganan aktif yang sudah lewat jatuh tempo ke siklus berikutnya,
     * agar pengingat siklus berikutnya tetap terkirim. Mengembalikan jumlah langganan yang dimajukan.
     */
    public function rollForwardPastDuePayments(): int
    {
        $rolled = 0;

        Subscription::where('status', SubscriptionStatus::ACTIVE)
            ->whereDate('next_payment_date', '<', today())
            ->eachById(function (Subscription $subscription) use (&$rolled) {
                $oldDate = $subscription->next_payment_date->format('d M Y');

                if ($subscription->rollForwardNextPaymentDate()) {
                    ActivityLog::record(
                        userId: $subscription->user_id,
                        action: 'UPDATE',
                        entity: 'Subscription',
                        entityId: $subscription->id,
                        description: "Tanggal tagihan {$subscription->name} dimajukan otomatis dari {$oldDate} ke " .
                            $subscription->next_payment_date->format('d M Y')
                    );
                    $rolled++;
                }
            });

        return $rolled;
    }

    /**
     * Hapus data langganan (FR-002 / UC08).
     */
    public function deleteSubscription(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $name = $subscription->name;
            $userId = $subscription->user_id;
            $id = $subscription->id;

            $deleted = $this->subscriptionRepository->delete($subscription);

            if ($deleted) {
                ActivityLog::record(
                    userId: $userId,
                    action: 'DELETE',
                    entity: 'Subscription',
                    entityId: $id,
                    description: "Menghapus langganan: {$name}"
                );
            }

            return $deleted;
        });
    }
}
