<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum to include 'technician'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('collector', 'technician', 'admin') NOT NULL DEFAULT 'collector'");
    }

    public function down(): void
    {
        // Revert — any technician rows become collector first to avoid truncation
        DB::statement("UPDATE users SET role = 'collector' WHERE role = 'technician'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('collector', 'admin') NOT NULL DEFAULT 'collector'");
    }
};
