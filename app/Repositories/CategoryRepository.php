<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    /**
     * Ambil seluruh kategori milik pengguna yang sedang login.
     */
    public function getAvailableForUser(int $userId): Collection
    {
        return Category::where('user_id', $userId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Cari kategori berdasarkan ID dan kepemilikan pengguna.
     */
    public function findByIdAndUser(int $id, int $userId): ?Category
    {
        return Category::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Salin kategori awal ke akun pengguna dan pindahkan referensi langganan lama ke salinan tersebut.
     */
    public function copyDefaultsForUser(int $userId): void
    {
        // Salinan dan referensi langganan harus berubah bersama tanpa menimpa kategori pengguna.
        DB::transaction(function () use ($userId): void {
            foreach (Category::whereNull('user_id')->orderBy('id')->get() as $defaultCategory) {
                $category = Category::firstOrCreate(
                    ['user_id' => $userId, 'name' => $defaultCategory->name],
                    ['icon' => $defaultCategory->icon, 'color' => $defaultCategory->color]
                );

                $defaultCategory->subscriptions()->where('user_id', $userId)
                    ->update(['category_id' => $category->id]);
            }
        });
    }

    /**
     * Simpan kategori kustom baru ke database.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Perbarui nama, ikon, dan warna kategori.
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Hapus kategori dari database.
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Periksa apakah kategori masih digunakan oleh langganan.
     */
    public function hasSubscriptions(Category $category): bool
    {
        return $category->subscriptions()->exists();
    }
}
