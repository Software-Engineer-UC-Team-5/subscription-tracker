<?php

namespace App\Repositories;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    /**
     * Ambil riwayat notifikasi milik user diurutkan dari yang terbaru.
     */
    public function getByUser(int $userId, int $limit = 50): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil jumlah notifikasi yang belum dibaca.
     */
    public function countUnreadByUser(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('status', '!=', NotificationStatus::READ)
            ->count();
    }

    /**
     * Cari notifikasi berdasarkan ID dan kepemilikan user.
     */
    public function findByIdAndUser(int $id, int $userId): ?Notification
    {
        return Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Simpan entri notifikasi baru (status awal biasanya PENDING).
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * Perbarui status pengiriman notifikasi (SENT, FAILED, atau READ).
     */
    public function updateStatus(Notification $notification, NotificationStatus $status, array $extra = []): bool
    {
        return $notification->update(array_merge([
            'status' => $status,
        ], $extra));
    }
}
