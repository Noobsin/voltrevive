<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('repair_job_id')
                  ->unique()                     // one payment per job
                  ->constrained('repair_jobs')
                  ->onDelete('restrict');

            $table->foreignId('collector_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            $table->decimal('amount', 10, 2);

            // Never store the full card number — last 4 digits only
            $table->string('card_last_four', 4);
            $table->string('cardholder_name');

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
