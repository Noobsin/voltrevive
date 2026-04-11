<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('repair_job_id')
                  ->unique()
                  ->constrained('repair_jobs')
                  ->onDelete('cascade');

            $table->foreignId('collector_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('technician_profile_id')
                  ->constrained('technician_profiles')
                  ->onDelete('cascade');

            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
