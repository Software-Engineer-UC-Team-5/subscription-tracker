<?php

namespace App\Services;

use App\Models\Reminder;
use App\Repositories\ReminderRepository;

/**
 * Service Layer: ReminderService
 * Mengelola aturan bisnis pengaturan pengingat jatuh tempo dan free trial (UC11 / FR-006).
 */
class ReminderService
{
    public function __construct(
        protected ReminderRepository $reminderRepository
    ) {
    }

    /**
     * Tolak akses dengan status 403 jika pengingat bukan milik pengguna.
     */
    public function ensureOwnership(Reminder $reminder, int $userId, string $message): void
    {
        abort_unless($reminder->user_id === $userId, 403, $message);
    }

    /**
     * Simpan pengaturan pengingat baru milik pengguna.
     */
    public function create(int $userId, array $data, bool $isActive): Reminder
    {
        $data['user_id'] = $userId;
        $data['is_active'] = $isActive;

        return $this->reminderRepository->create($data);
    }

    /**
     * Ubah status aktif/nonaktif pengingat dan kembalikan status barunya.
     */
    public function toggle(Reminder $reminder, int $userId): bool
    {
        $this->ensureOwnership($reminder, $userId, 'Anda tidak memiliki hak akses untuk mengubah status pengingat ini.');

        if ($reminder->is_active) {
            $reminder->deactivate();
        } else {
            $reminder->activate();
        }

        return $reminder->is_active;
    }

    /**
     * Hapus konfigurasi pengingat milik pengguna.
     */
    public function delete(Reminder $reminder, int $userId): void
    {
        $this->ensureOwnership($reminder, $userId, 'Anda tidak memiliki hak akses untuk menghapus pengingat ini.');

        $this->reminderRepository->delete($reminder);
    }
}
