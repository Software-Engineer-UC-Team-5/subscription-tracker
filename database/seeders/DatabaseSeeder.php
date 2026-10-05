<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database awal untuk pengguna demo dan kategori bawaan sistem.
     */
    public function run(): void
    {
        // Akun Pengguna Demo
        User::create([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        // Kategori Default Sistem (user_id = null)
        $defaultCategories = [
            ['name' => 'Hiburan & Streaming', 'icon' => 'tv', 'color' => '#ef4444', 'user_id' => null],
            ['name' => 'Produktivitas & Kerja', 'icon' => 'briefcase', 'color' => '#3b82f6', 'user_id' => null],
            ['name' => 'Penyimpanan Cloud', 'icon' => 'cloud', 'color' => '#10b981', 'user_id' => null],
            ['name' => 'Pendidikan & Kursus', 'icon' => 'academic-cap', 'color' => '#f59e0b', 'user_id' => null],
            ['name' => 'Utilitas & Tools', 'icon' => 'wrench', 'color' => '#8b5cf6', 'user_id' => null],
        ];

        foreach ($defaultCategories as $category) {
            Category::create($category);
        }
    }
}
