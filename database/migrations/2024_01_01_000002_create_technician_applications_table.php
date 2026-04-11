<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('specialisation');                       // e.g. "Synthesizers, Hi-Fi Audio"
            $table->unsignedTinyInteger('years_experience');
            $table->string('certifications_path')->nullable();      // uploaded file path
            $table->json('availability_windows');                   // e.g. {"days":["Tue","Wed","Thu"],"from":"09:00","to":"17:00"}

            // Admin reviews this
            $table->enum('status', ['under_review', 'approved', 'rejected'])
                  ->default('under_review');
            $table->text('admin_note')->nullable();                 // rejection reason

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_applications');
    }
};
