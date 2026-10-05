<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Repositories\CategoryRepository;
use App\Repositories\PaymentMethodRepository;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Manajemen Subscription & Free Trial
 * Controller untuk manajemen data langganan dan free trial (UC05-UC10 / FR-002, FR-003, FR-004).
 */
class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected CategoryRepository $categoryRepository,
        protected PaymentMethodRepository $paymentMethodRepository
    ) {
    }

    /**
     * Tampilkan daftar subscription aktif dan riwayat beserta filter (FR-002, FR-003 / UC06, UC09).
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $filters = $request->only(['search', 'category_id', 'payment_method_id', 'status']);

        $subscriptions = $this->subscriptionService->getFiltered($userId, $filters);
        $categories = $this->categoryRepository->getAvailableForUser($userId);
        $paymentMethods = $this->paymentMethodRepository->getByUser($userId);

        return view('subscriptions.index', compact('subscriptions', 'categories', 'paymentMethods', 'filters'));
    }

    /**
     * Tampilkan formulir tambah subscription baru (UC05).
     */
    public function create(): View
    {
        $userId = auth()->id();
        $categories = $this->categoryRepository->getAvailableForUser($userId);
        $paymentMethods = $this->paymentMethodRepository->getByUser($userId);

        return view('subscriptions.create', compact('categories', 'paymentMethods'));
    }

    /**
     * Simpan data subscription baru ke database (termasuk atomik free trial & activity log) (UC05, UC10).
     */
    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $this->subscriptionService->createSubscription(auth()->id(), $request->validated());

        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil ditambahkan.');
    }

    /**
     * Tampilkan rincian detail subscription beserta relasi (UC06).
     */
    public function show(Subscription $subscription): View
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data langganan ini.');
        }

        $subscription->load(['category', 'paymentMethod', 'freeTrial', 'reminders']);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Tampilkan formulir edit data subscription (UC07).
     */
    public function edit(Subscription $subscription): View
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data langganan ini.');
        }

        $userId = auth()->id();
        $categories = $this->categoryRepository->getAvailableForUser($userId);
        $paymentMethods = $this->paymentMethodRepository->getByUser($userId);

        return view('subscriptions.edit', compact('subscription', 'categories', 'paymentMethods'));
    }

    /**
     * Perbarui data subscription di database (UC07).
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui data langganan ini.');
        }

        $this->subscriptionService->updateSubscription($subscription, $request->validated());

        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil diperbarui.');
    }

    /**
     * Hapus data subscription dari database (UC08 / FR-002).
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data langganan ini.');
        }

        $this->subscriptionService->deleteSubscription($subscription);

        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil dihapus.');
    }
}
