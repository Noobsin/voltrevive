<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();

            // Submitted by guests (no user_id required)
            $table->string('name');
            $table->string('email');
            $table->text('message');

            // Google ReCaptcha v3 score stored for audit purposes
            $table->decimal('recaptcha_score', 3, 2)->nullable();  // 0.0 to 1.0

            // Admin marks as read once reviewed
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};
