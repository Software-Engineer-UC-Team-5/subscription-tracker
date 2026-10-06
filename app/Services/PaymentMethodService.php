<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentMethodService
{
    /**
     * Siapkan repository untuk akses data metode pembayaran.
     */
    public function __construct(
        protected PaymentMethodRepository $paymentMethodRepository
    ) {
    }

    /**
     * Tolak akses dengan status 403 jika metode pembayaran bukan milik pengguna.
     */
    public function ensureOwnership(PaymentMethod $paymentMethod, int $userId): void
    {
        abort_unless($paymentMethod->user_id === $userId, 403);
    }

    /**
     * Buat metode pembayaran milik pengguna dan catat aktivitas dalam satu transaksi.
     */
    public function create(int $userId, array $data): PaymentMethod
    {
        // Perubahan data dan log aktivitas harus berhasil atau dibatalkan bersama.
        return DB::transaction(function () use ($userId, $data): PaymentMethod {
            // NFR-004: Simpan nama yang sudah divalidasi tanpa menerima data sensitif perbankan.
            $paymentMethod = $this->paymentMethodRepository->create([
                'user_id' => $userId,
                'name' => $data['name'],
            ]);

            ActivityLog::record(
                $userId,
                'CREATE',
                'PaymentMethod',
                $paymentMethod->id,
                "Menambahkan metode pembayaran: {$paymentMethod->name}"
            );

            return $paymentMethod;
        });
    }

    /**
     * Perbarui nama metode pembayaran milik pengguna dan catat aktivitas dalam satu transaksi.
     */
    public function update(PaymentMethod $paymentMethod, int $userId, array $data): void
    {
        $this->ensureOwnership($paymentMethod, $userId);

        // Batalkan pembaruan nama jika pencatatan log aktivitas gagal.
        DB::transaction(function () use ($paymentMethod, $userId, $data): void {
            $this->paymentMethodRepository->update($paymentMethod, ['name' => $data['name']]);

            ActivityLog::record(
                $userId,
                'UPDATE',
                'PaymentMethod',
                $paymentMethod->id,
                "Memperbarui metode pembayaran: {$paymentMethod->name}"
            );
        });
    }

    /**
     * Hapus metode pembayaran yang tidak digunakan dan catat aktivitas dalam satu transaksi.
     *
     * @throws ValidationException Jika metode pembayaran masih digunakan oleh langganan.
     */
    public function delete(PaymentMethod $paymentMethod, int $userId): void
    {
        $this->ensureOwnership($paymentMethod, $userId);

        // Pemeriksaan penggunaan, penghapusan, dan pencatatan log dilakukan dalam satu transaksi.
        DB::transaction(function () use ($paymentMethod, $userId): void {
            if ($this->paymentMethodRepository->hasSubscriptions($paymentMethod)) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Metode pembayaran masih digunakan oleh langganan. '
                        .'Ubah metode pembayaran pada langganan tersebut sebelum menghapusnya.',
                ]);
            }

            $this->paymentMethodRepository->delete($paymentMethod);

            ActivityLog::record(
                $userId,
                'DELETE',
                'PaymentMethod',
                $paymentMethod->id,
                "Menghapus metode pembayaran: {$paymentMethod->name}"
            );
        });
    }
}
