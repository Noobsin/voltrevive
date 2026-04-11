<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();

            // One technician profile per user
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('application_id')
                  ->constrained('technician_applications')
                  ->onDelete('restrict');

            $table->text('bio')->nullable();
            $table->string('location')->nullable();                 // e.g. "Berlin, Germany"
            $table->string('specialisation');                       // e.g. "Synthesizers"
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->json('availability_windows');                   // copied from application on approval

            // Aggregate stats — updated by DB triggers / Eloquent observers
            $table->decimal('avg_rating', 3, 2)->default(0.00);    // e.g. 4.90
            $table->unsignedInteger('completed_jobs_count')->default(0);

            // Financial
            $table->decimal('withdrawable_balance', 10, 2)->default(0.00); // earnings in BDT/USD

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_profiles');
    }
};
