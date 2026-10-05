<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('IDR');
            $table->string('billing_period', 20); // DAILY, WEEKLY, MONTHLY, QUARTERLY, YEARLY
            $table->date('next_payment_date');
            $table->string('status', 20)->default('ACTIVE'); // TRIAL, ACTIVE, PAUSED, CANCELLED, EXPIRED
            $table->boolean('is_free_trial')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('next_payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
