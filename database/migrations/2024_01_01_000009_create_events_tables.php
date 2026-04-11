<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Community events created by admin
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Admin who created it
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('restrict');

            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('location');

            $table->unsignedInteger('ticket_count');               // max capacity
            $table->unsignedInteger('attendee_count')->default(0); // increments on RSVP

            $table->timestamps();
        });

        // User RSVPs to events
        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Booking slip reference number e.g. "EVT-2026-00412"
            $table->string('reference')->unique();

            $table->timestamps();

            // One RSVP per user per event
            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_rsvps');
        Schema::dropIfExists('events');
    }
};
