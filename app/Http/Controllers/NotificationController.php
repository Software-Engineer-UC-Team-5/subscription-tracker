<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Notifikasi Pengguna
 * Controller untuk melihat riwayat dan status pengiriman notifikasi (UC12 / FR-006).
 */
class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Tampilkan riwayat notifikasi milik pengguna.
     */
    public function index(Request $request): View
    {
        $notifications = $this->notificationRepository->getByUser(auth()->id());

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah status notifikasi ini.');
        }

        $notification->markAsRead();

        return redirect()->back()
            ->with('success', 'Notifikasi ditandai sebagai telah dibaca.');
    }
}
