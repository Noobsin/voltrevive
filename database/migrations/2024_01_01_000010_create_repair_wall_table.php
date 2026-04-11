<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Collector rescue appeals — phone number is stored on the user record
        // Technicians contact collectors directly via the phone modal on the repair wall
        // No flags table needed — we removed the "I Can Help" flag system
        Schema::create('repair_wall_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('device_name');
            $table->enum('category', [
                'Synthesizer',
                'Retro Gaming',
                'Hi-Fi Audio',
                'Vintage Keyboards',
                'Vintage Radio',
                'Vintage Computer',
                'Cameras',
                'Other',
            ]);
            $table->text('description');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_wall_posts');
    }
};
