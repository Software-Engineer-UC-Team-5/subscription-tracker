<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Manajemen Subscription & Free Trial
 */
class SubscriptionController extends Controller
{
    /**
     * Tampilkan daftar subscription aktif dan riwayat (FR-002, FR-003).
     */
    public function index(Request $request): View
    {
        // TODO: Ambil daftar subscription terfilter untuk pengguna yang sedang login
        return view('subscriptions.index');
    }

    /**
     * Tampilkan formulir tambah subscription baru.
     */
    public function create(): View
    {
        // TODO: Tampilkan view formulir tambah subscription (siapkan data categories & paymentMethods)
        return view('subscriptions.create');
    }

    /**
     * Simpan data subscription baru ke database (termasuk free trial).
     */
    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        // TODO: Simpan data subscription baru melalui SubscriptionService
        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil ditambahkan.');
    }

    /**
     * Tampilkan rincian detail subscription.
     */
    public function show(Subscription $subscription): View
    {
        // TODO: Tampilkan view detail subscription dengan relasi lengkap
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Tampilkan formulir edit data subscription.
     */
    public function edit(Subscription $subscription): View
    {
        // TODO: Tampilkan view formulir edit subscription
        return view('subscriptions.edit', compact('subscription'));
    }

    /**
     * Perbarui data subscription di database.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        // TODO: Perbarui data subscription melalui SubscriptionService
        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil diperbarui.');
    }

    /**
     * Hapus data subscription dari database (UC08 / FR-002).
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        // TODO: Hapus data subscription melalui SubscriptionService
        return redirect()->route('subscriptions.index')
            ->with('success', 'Data langganan berhasil dihapus.');
    }
}
