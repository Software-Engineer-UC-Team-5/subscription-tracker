<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reminder_id')->nullable()->constrained('reminders')->nullOnDelete();
            $table->string('title', 150);
            $table->text('message');
            $table->string('type', 30); // PAYMENT_REMINDER, FREE_TRIAL_REMINDER, SYSTEM, OTHER
            $table->string('status', 20)->default('PENDING'); // PENDING, SENT, FAILED, READ
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
