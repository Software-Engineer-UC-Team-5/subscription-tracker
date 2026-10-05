<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckRemindersCommand extends Command
{
    /**
     * Nama dan signature perintah konsol artisan.
     *
     * @var string
     */
    protected $signature = 'app:check-reminders';

    /**
     * Deskripsi fungsi perintah konsol.
     *
     * @var string
     */
    protected $description = 'Periksa pengingat aktif yang jatuh tempo dan kirim notifikasi/email kepada pengguna (UC12 / FR-006 / NFR-003)';

    /**
     * Eksekusi logika perintah konsol artisan.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Memulai pengecekan pengingat jatuh tempo...');

        $processed = $notificationService->checkDueReminders();

        $count = count($processed);
        $this->info("Pengecekan selesai. {$count} notifikasi berhasil diproses.");

        return Command::SUCCESS;
    }
}
