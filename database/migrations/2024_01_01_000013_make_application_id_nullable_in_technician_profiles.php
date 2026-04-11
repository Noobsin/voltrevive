<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Technicians register directly — no separate application step.
     * application_id is therefore nullable; it can be populated later
     * if we add an application-upgrade flow in the future.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE technician_profiles MODIFY COLUMN application_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE technician_profiles MODIFY COLUMN application_id BIGINT UNSIGNED NOT NULL');
    }
};
