<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
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
        protected NotificationRepository $notificationRepository,
        protected NotificationService $notificationService
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
        $this->notificationService->markAsRead($notification, auth()->id());

        return redirect()->back()
            ->with('success', 'Notifikasi ditandai sebagai telah dibaca.');
    }
}
