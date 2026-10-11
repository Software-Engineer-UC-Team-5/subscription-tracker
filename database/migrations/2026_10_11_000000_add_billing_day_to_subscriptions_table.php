<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Simpan tanggal tagihan asli (1-31) agar tagihan akhir bulan tidak bergeser permanen ke tanggal 28.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedTinyInteger('billing_day')->nullable();
        });

        // Isi data lama dari tanggal tagihan yang tersimpan saat ini; tanggal yang sudah bergeser tidak bisa dipulihkan.
        DB::table('subscriptions')->orderBy('id')->each(function (stdClass $subscription): void {
            DB::table('subscriptions')
                ->where('id', $subscription->id)
                ->update(['billing_day' => Carbon::parse($subscription->next_payment_date)->day]);
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('billing_day');
        });
    }
};
