<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // PAYMENT_DUE, FREE_TRIAL_END, OTHER
            $table->integer('notify_before_days')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
