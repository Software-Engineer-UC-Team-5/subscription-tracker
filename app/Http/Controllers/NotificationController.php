<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Notifikasi Pengguna
 */
class NotificationController extends Controller
{
    /**
     * Tampilkan halaman inbox notifikasi pengguna.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil riwayat notifikasi milik pengguna yang sedang login
        return view('notifications.index');
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        // TODO: Perbarui status notifikasi menjadi READ
        return redirect()->back()
            ->with('success', 'Notifikasi ditandai sebagai telah dibaca.');
    }
}
