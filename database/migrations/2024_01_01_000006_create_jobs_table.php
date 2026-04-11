<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named repair_jobs to avoid conflict with Laravel built-in jobs queue table
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                  ->unique()
                  ->constrained('bookings')
                  ->onDelete('restrict');

            $table->string('reference')->unique();

            $table->enum('status', [
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('confirmed');

            $table->string('jitsi_room_url')->nullable();

            $table->decimal('payment_amount', 10, 2);
            $table->enum('payment_status', [
                'pending',
                'held',
                'released',
                'refunded',
            ])->default('pending');
            $table->string('ssl_transaction_id')->nullable();
            $table->string('ssl_val_id')->nullable();

            $table->json('timeline_state')->nullable();

            $table->date('estimated_completion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_jobs');
    }
};
