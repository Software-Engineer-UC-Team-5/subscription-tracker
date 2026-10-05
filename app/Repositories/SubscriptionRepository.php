<?php

namespace App\Repositories;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository
{
    /**
     * Ambil seluruh subscription milik user dengan opsi filter nama, kategori, status, dan metode pembayaran (FR-003).
     */
    public function getFilteredByUser(int $userId, array $filters = []): Collection
    {
        $query = Subscription::with(['category', 'paymentMethod', 'freeTrial', 'reminders'])
            ->where('user_id', $userId);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['payment_method_id'])) {
            $query->where('payment_method_id', $filters['payment_method_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('next_payment_date', 'asc')->get();
    }

    /**
     * Ambil subscription yang berstatus ACTIVE milik user (FR-005).
     */
    public function getActiveByUser(int $userId): Collection
    {
        return Subscription::with(['category', 'paymentMethod', 'freeTrial'])
            ->where('user_id', $userId)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->orderBy('next_payment_date', 'asc')
            ->get();
    }

    /**
     * Ambil tagihan yang akan datang dalam N hari ke depan (Upcoming Payments).
     */
    public function getUpcomingByUser(int $userId, int $days = 7): Collection
    {
        $today = Carbon::today();
        $target = Carbon::today()->addDays($days);

        return Subscription::with(['category', 'paymentMethod'])
            ->where('user_id', $userId)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->whereBetween('next_payment_date', [$today, $target])
            ->orderBy('next_payment_date', 'asc')
            ->get();
    }

    public function findByIdAndUser(int $id, int $userId): ?Subscription
    {
        return Subscription::with(['category', 'paymentMethod', 'freeTrial', 'reminders'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function update(Subscription $subscription, array $data): bool
    {
        return $subscription->update($data);
    }

    public function delete(Subscription $subscription): bool
    {
        return $subscription->delete();
    }
}
