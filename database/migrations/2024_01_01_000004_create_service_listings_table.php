<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('technician_profile_id')
                  ->constrained('technician_profiles')
                  ->onDelete('cascade');

            $table->string('title');                                // e.g. "Roland Juno-106 Full Restoration"
            $table->enum('category', [
                'Synthesizers',
                'Retro Gaming',
                'Hi-Fi Audio',
                'Vintage Keyboards',
                'Vintage Radio',
                'Vintage Computer',
                'Cameras',
                'Other',
            ]);
            $table->json('supported_models');                       // e.g. ["Roland Juno-106", "Korg MS-20"]
            $table->text('description')->nullable();

            // Price range
            $table->decimal('price_min', 10, 2);
            $table->decimal('price_max', 10, 2);

            // Mandatory Before/After portfolio images (stored in storage/app/public)
            $table->string('before_image');                        // relative path
            $table->string('after_image');                         // relative path

            // Admin must approve before this appears in search results
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->boolean('is_active')->default(true);           // technician can deactivate

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_listings');
    }
};
