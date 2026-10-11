<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\Reminder;
use App\Repositories\ReminderRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\ReminderService;
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
        protected SubscriptionRepository $subscriptionRepository,
        protected ReminderService $reminderService
    ) {
    }

    /**
     * Tampilkan seluruh pengingat milik user dan opsi subscription aktif atau masa uji coba.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $reminders = $this->reminderRepository->getByUser($userId);
        $subscriptions = $this->subscriptionRepository->getRemindableByUser($userId);

        return view('reminders.index', compact('reminders', 'subscriptions'));
    }

    /**
     * Simpan pengaturan pengingat baru.
     */
    public function store(StoreReminderRequest $request): RedirectResponse
    {
        $this->reminderService->create(
            auth()->id(),
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()->back()
            ->with('success', 'Pengingat berhasil diatur.');
    }

    /**
     * Ubah status aktif/nonaktif pengingat.
     */
    public function toggle(Reminder $reminder): RedirectResponse
    {
        $pesan = $this->reminderService->toggle($reminder, auth()->id())
            ? 'Pengingat berhasil diaktifkan.'
            : 'Pengingat berhasil dinonaktifkan.';

        return redirect()->back()
            ->with('success', $pesan);
    }

    /**
     * Hapus konfigurasi pengingat.
     */
    public function destroy(Reminder $reminder): RedirectResponse
    {
        $this->reminderService->delete($reminder, auth()->id());

        return redirect()->back()
            ->with('success', 'Pengingat berhasil dihapus.');
    }
}
