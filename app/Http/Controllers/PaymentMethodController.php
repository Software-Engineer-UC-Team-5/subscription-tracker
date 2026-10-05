<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Panel Metode Pembayaran
 */
class PaymentMethodController extends Controller
{
    /**
     * Tampilkan halaman daftar metode pembayaran pengguna.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil seluruh metode pembayaran milik pengguna yang sedang login
        return view('payment_methods.index');
    }

    /**
     * Tampilkan formulir tambah metode pembayaran baru.
     */
    public function create(): View
    {
        // TODO: Tampilkan view formulir tambah metode pembayaran
        return view('payment_methods.create');
    }

    /**
     * Simpan metode pembayaran baru ke database.
     */
    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        // TODO: Simpan data nama alias metode pembayaran ke database (NFR-004: tanpa nomor kartu)
        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit nama metode pembayaran.
     */
    public function edit(PaymentMethod $paymentMethod): View
    {
        // TODO: Tampilkan view edit metode pembayaran dengan data $paymentMethod
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Perbarui data metode pembayaran di database.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        // TODO: Perbarui nama alias metode pembayaran di database
        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Hapus metode pembayaran dari database.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        // TODO: Hapus metode pembayaran dari database
        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
