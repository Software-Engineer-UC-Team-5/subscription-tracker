<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use App\Services\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Panel Metode Pembayaran
 */
class PaymentMethodController extends Controller
{
    /**
     * Siapkan repository dan service untuk pengelolaan metode pembayaran.
     */
    public function __construct(
        protected PaymentMethodRepository $paymentMethodRepository,
        protected PaymentMethodService $paymentMethodService
    ) {
    }

    /**
     * Tampilkan halaman daftar metode pembayaran pengguna.
     */
    public function index(Request $request): View
    {
        $paymentMethods = $this->paymentMethodRepository->getByUser($request->user()->id);

        // Pulihkan formulir edit yang gagal validasi hanya dari metode pembayaran milik pengguna.
        $editingPaymentMethod = $paymentMethods->firstWhere('id', $request->old('payment_method_id'));

        return view('payment_methods.index', compact('paymentMethods', 'editingPaymentMethod'));
    }

    /**
     * Tampilkan formulir tambah metode pembayaran baru.
     */
    public function create(): View
    {
        return view('payment_methods.create');
    }

    /**
     * Simpan metode pembayaran baru dan arahkan kembali ke daftar dengan pesan sukses.
     */
    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $this->paymentMethodService->create($request->user()->id, $request->validated());

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit metode pembayaran milik pengguna yang sedang login.
     */
    public function edit(PaymentMethod $paymentMethod): View
    {
        $this->paymentMethodService->ensureOwnership($paymentMethod, auth()->id());

        return view('payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Perbarui metode pembayaran dan arahkan kembali ke daftar dengan pesan sukses.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->paymentMethodService->update($paymentMethod, $request->user()->id, $request->validated());

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Hapus metode pembayaran milik pengguna jika tidak digunakan oleh langganan.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->paymentMethodService->delete($paymentMethod, auth()->id());

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
