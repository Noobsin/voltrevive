<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE events ADD COLUMN event_type ENUM('Swap Meet','Repair Café','Exhibition','Workshop') NOT NULL DEFAULT 'Swap Meet' AFTER title");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE events DROP COLUMN event_type");
    }
};
