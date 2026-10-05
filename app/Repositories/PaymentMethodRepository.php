<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository
{
    /**
     * Ambil semua metode pembayaran milik pengguna yang sedang login.
     */
    public function getByUser(int $userId): Collection
    {
        return PaymentMethod::where('user_id', $userId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Cari metode pembayaran berdasarkan ID dan kepemilikan user.
     */
    public function findByIdAndUser(int $id, int $userId): ?PaymentMethod
    {
        return PaymentMethod::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Simpan data metode pembayaran baru (label alias saja - NFR-004).
     */
    public function create(array $data): PaymentMethod
    {
        return PaymentMethod::create($data);
    }

    /**
     * Perbarui data metode pembayaran.
     */
    public function update(PaymentMethod $paymentMethod, array $data): bool
    {
        return $paymentMethod->update($data);
    }

    /**
     * Hapus data metode pembayaran.
     */
    public function delete(PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->delete();
    }
}
