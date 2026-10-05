<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Pengaturan Pengingat (Reminder)
 */
class ReminderController extends Controller
{
    /**
     * Tampilkan halaman pengaturan pengingat.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil daftar pengingat milik user dan subscription aktif untuk dropdown form
        return view('reminders.index');
    }

    /**
     * Simpan pengaturan pengingat baru.
     */
    public function store(StoreReminderRequest $request): RedirectResponse
    {
        // TODO: Simpan konfigurasi pengingat baru ke database
        return redirect()->back()
            ->with('success', 'Pengingat berhasil diatur.');
    }

    /**
     * Hapus konfigurasi pengingat.
     */
    public function destroy(Reminder $reminder): RedirectResponse
    {
        // TODO: Hapus konfigurasi pengingat milik pengguna
        return redirect()->back()
            ->with('success', 'Pengingat berhasil dihapus.');
    }
}
