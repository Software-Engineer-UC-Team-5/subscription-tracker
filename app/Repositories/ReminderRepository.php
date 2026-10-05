<?php

namespace App\Repositories;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Collection;

class ReminderRepository
{
    /**
     * Ambil semua pengingat milik user tertentu beserta data subscription terkait.
     */
    public function getByUser(int $userId): Collection
    {
        return Reminder::with('subscription')
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Ambil seluruh pengingat aktif di sistem untuk diproses oleh Scheduler / Cron.
     */
    public function getAllActive(): Collection
    {
        return Reminder::with(['subscription.freeTrial', 'subscription.user'])
            ->where('is_active', true)
            ->get();
    }

    /**
     * Cari pengingat berdasarkan ID dan user.
     */
    public function findByIdAndUser(int $id, int $userId): ?Reminder
    {
        return Reminder::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Simpan pengaturan pengingat baru.
     */
    public function create(array $data): Reminder
    {
        return Reminder::create($data);
    }

    /**
     * Perbarui konfigurasi pengingat.
     */
    public function update(Reminder $reminder, array $data): bool
    {
        return $reminder->update($data);
    }

    /**
     * Hapus konfigurasi pengingat.
     */
    public function delete(Reminder $reminder): bool
    {
        return $reminder->delete();
    }
}
