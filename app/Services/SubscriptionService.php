<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\FreeTrial;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository
    ) {
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

            return $updated;
        });
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
