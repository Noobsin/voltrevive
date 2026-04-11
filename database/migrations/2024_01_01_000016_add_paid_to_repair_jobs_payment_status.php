<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'paid' to the payment_status ENUM on repair_jobs
        DB::statement("ALTER TABLE repair_jobs MODIFY payment_status ENUM('pending','held','released','refunded','paid') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE repair_jobs MODIFY payment_status ENUM('pending','held','released','refunded') DEFAULT 'pending'");
    }
};
