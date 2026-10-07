<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * Siapkan repository untuk akses data kategori.
     */
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Tolak perubahan kategori yang bukan milik pengguna yang sedang login.
     */
    public function ensureOwnership(Category $category, int $userId): void
    {
        abort_unless($category->user_id === $userId, 403);
    }

    /**
     * Buat kategori milik pengguna dan catat aktivitas dalam satu transaksi.
     */
    public function create(int $userId, array $data): Category
    {
        // Kepemilikan ditentukan dari akun aktif, bukan dari isian formulir.
        return DB::transaction(function () use ($userId, $data): Category {
            $category = $this->categoryRepository->create([
                'user_id' => $userId,
                'name' => $data['name'],
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            ActivityLog::record($userId, 'CREATE', 'Category', $category->id, "Menambahkan kategori: {$category->name}");

            return $category;
        });
    }

    /**
     * Perbarui kategori milik pengguna dan catat aktivitas dalam satu transaksi.
     */
    public function update(Category $category, int $userId, array $data): void
    {
        $this->ensureOwnership($category, $userId);

        // Pembaruan dan log aktivitas dibatalkan bersama jika salah satunya gagal.
        DB::transaction(function () use ($category, $userId, $data): void {
            $this->categoryRepository->update($category, [
                'name' => $data['name'],
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            ActivityLog::record($userId, 'UPDATE', 'Category', $category->id, "Memperbarui kategori: {$category->name}");
        });
    }

    /**
     * Hapus kategori yang tidak digunakan tanpa mengubah referensi langganan.
     *
     * @throws ValidationException Jika kategori masih digunakan oleh langganan.
     */
    public function delete(Category $category, int $userId): void
    {
        $this->ensureOwnership($category, $userId);

        // Lindungi langganan dari kehilangan kategori sebelum penghapusan dilakukan.
        DB::transaction(function () use ($category, $userId): void {
            if ($this->categoryRepository->hasSubscriptions($category)) {
                throw ValidationException::withMessages([
                    'category' => 'Kategori masih digunakan oleh langganan. '
                        .'Ubah kategori pada langganan tersebut sebelum menghapusnya.',
                ]);
            }

            $this->categoryRepository->delete($category);

            ActivityLog::record($userId, 'DELETE', 'Category', $category->id, "Menghapus kategori: {$category->name}");
        });
    }
}
