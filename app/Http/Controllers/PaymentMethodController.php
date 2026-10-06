<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use App\Services\PaymentMethodService;
use Illuminate\Http\JsonResponse;
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

        return view('payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Tampilkan formulir tambah metode pembayaran baru.
     */
    public function create(): View
    {
        return view('payment_methods.create');
    }

    /**
     * Simpan metode pembayaran baru dan kembalikan redirect atau JSON untuk popup.
     */
    public function store(StorePaymentMethodRequest $request): RedirectResponse|JsonResponse
    {
        $this->paymentMethodService->create($request->user()->id, $request->validated());

        // Simpan pesan sukses di sesi agar tampil setelah popup mengarahkan ke daftar.
        $response = redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');

        return $request->expectsJson()
            ? response()->json(['redirect' => $response->getTargetUrl()])
            : $response;
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
     * Perbarui metode pembayaran dan kembalikan redirect atau JSON untuk popup.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse|JsonResponse
    {
        $this->paymentMethodService->update($paymentMethod, $request->user()->id, $request->validated());

        // Respons JSON menyertakan tujuan redirect tanpa mengambil halaman daftar terlebih dahulu.
        $response = redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');

        return $request->expectsJson()
            ? response()->json(['redirect' => $response->getTargetUrl()])
            : $response;
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
