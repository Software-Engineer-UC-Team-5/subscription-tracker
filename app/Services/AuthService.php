<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Service Layer: AuthService
 * Mengelola aturan bisnis pendaftaran akun dan pencatatan aktivitas autentikasi.
 */
class AuthService
{
    /**
     * Siapkan repository untuk mengisi kategori awal milik akun baru.
     */
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Daftarkan akun baru beserta kategori awal dan log registrasi.
     */
    public function register(array $data): User
    {
        // Akun, kategori awal, dan log registrasi disimpan dalam satu transaksi.
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $this->categoryRepository->copyDefaultsForUser($user->id);

            ActivityLog::record(
                userId: $user->id,
                action: 'REGISTER',
                entity: 'User',
                entityId: $user->id,
                description: 'Pendaftaran akun baru berhasil'
            );

            return $user;
        });
    }

    /**
     * Catat log aktivitas login pengguna.
     */
    public function recordLogin(int $userId): void
    {
        ActivityLog::record(
            userId: $userId,
            action: 'LOGIN',
            entity: 'User',
            entityId: $userId,
            description: 'Pengguna berhasil masuk ke sistem'
        );
    }

    /**
     * Catat log aktivitas logout pengguna.
     */
    public function recordLogout(int $userId): void
    {
        ActivityLog::record(
            userId: $userId,
            action: 'LOGOUT',
            entity: 'User',
            entityId: $userId,
            description: 'Pengguna keluar dari sistem'
        );
    }
}
