<?php

use App\Repositories\CategoryRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Berikan salinan kategori awal kepada akun lama tanpa kehilangan referensi langganan.
     */
    public function up(): void
    {
        $categoryRepository = app(CategoryRepository::class);

        // Proses bertahap agar tidak memuat seluruh akun ke memori sekaligus.
        DB::table('users')->orderBy('id')->each(function (stdClass $user) use ($categoryRepository): void {
            $categoryRepository->copyDefaultsForUser((int) $user->id);
        });
    }

    /**
     * Pertahankan kategori akun saat rollback agar perubahan pengguna dan referensinya tidak hilang.
     */
    public function down(): void
    {
        // Migrasi data ini tidak mengembalikan kategori akun menjadi kategori bersama.
    }
};
