<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            // Per-listing availability days (e.g. ["Tue","Thu"])
            $table->json('available_days')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            $table->dropColumn('available_days');
        });
    }
};
