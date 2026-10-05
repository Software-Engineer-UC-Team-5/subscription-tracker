<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\Reminder;
use App\Repositories\ReminderRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Pengaturan Pengingat (Reminder)
 * Controller untuk mengelola pengingat jatuh tempo pembayaran dan free trial (UC11 / FR-006).
 */
class ReminderController extends Controller
{
    public function __construct(
        protected ReminderRepository $reminderRepository,
        protected SubscriptionRepository $subscriptionRepository
    ) {
    }

    /**
     * Tampilkan seluruh pengingat milik user dan opsi subscription aktif.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $reminders = $this->reminderRepository->getByUser($userId);
        $subscriptions = $this->subscriptionRepository->getActiveByUser($userId);

        return view('reminders.index', compact('reminders', 'subscriptions'));
    }

    /**
     * Simpan pengaturan pengingat baru.
     */
    public function store(StoreReminderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);

        $this->reminderRepository->create($data);

        return redirect()->back()
            ->with('success', 'Pengingat berhasil diatur.');
    }

    /**
     * Hapus konfigurasi pengingat.
     */
    public function destroy(Reminder $reminder): RedirectResponse
    {
        if ($reminder->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus pengingat ini.');
        }

        $this->reminderRepository->delete($reminder);

        return redirect()->back()
            ->with('success', 'Pengingat berhasil dihapus.');
    }
}
