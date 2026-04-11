<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Who is booking
            $table->foreignId('collector_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Who they are booking with
            $table->foreignId('technician_profile_id')
                  ->constrained('technician_profiles')
                  ->onDelete('cascade');

            // Which service listing they selected
            $table->foreignId('service_listing_id')
                  ->constrained('service_listings')
                  ->onDelete('restrict');

            // The date the collector requested
            $table->date('requested_date');

            // Device details submitted by collector
            $table->string('device_name');
            $table->text('device_description')->nullable();

            // Status lifecycle:
            // pending    → collector sent request, technician has not responded
            // confirmed  → technician accepted, payment triggered
            // rejected   → technician declined (with optional reason)
            // cancelled  → cancelled after confirmation (Twilio SMS fires if within 24h)
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();

            // Shipping deadline — set when booking is confirmed
            // Twilio SMS fires if cancellation happens within 24h of this
            $table->dateTime('shipping_deadline')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
