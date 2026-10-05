<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Ambil kategori bawaan sistem (user_id IS NULL) + kategori kustom milik user.
     */
    public function getAvailableForUser(int $userId): Collection
    {
        return Category::whereNull('user_id')
            ->orWhere('user_id', $userId)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findByIdAndUser(int $id, int $userId): ?Category
    {
        return Category::where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id')->orWhere('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
